<?php

namespace Botble\Base\Exceptions;

use Botble\Base\Contracts\Exceptions\IgnoringReport;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Media\Facades\RvMedia;
use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected BaseHttpResponse $baseHttpResponse;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->ignore(PhpSpreadsheetException::class);

        $this->baseHttpResponse = new BaseHttpResponse();
    }

    public function render($request, Throwable $e)
    {
        if (! app()->isDownForMaintenance() && $e instanceof HttpExceptionInterface) {
            do_action(BASE_ACTION_SITE_ERROR, $e->getStatusCode());
        }

        if ($e instanceof ModelNotFoundException || $e instanceof MethodNotAllowedHttpException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        switch (true) {
            case $e instanceof DisabledInDemoModeException:
            case $e instanceof MethodNotAllowedHttpException:
            case $e instanceof TokenMismatchException:
                return $this->baseHttpResponse
                    ->setError()
                    ->setCode($e->getCode())
                    ->setMessage($e->getMessage());
            case $e instanceof PostTooLargeException:
                if (! empty($request->allFiles())) {
                    return RvMedia::responseError(
                        trans('core/media::media.upload_failed', [
                            'size' => BaseHelper::humanFilesize(RvMedia::getServerConfigMaxUploadFileSize()),
                        ])
                    );
                }

                break;
            case $e instanceof TransportException:
                if (is_in_admin(true)) {
                    $message = trans('core/base::errors.error_when_sending_email');
                } else {
                    $message = trans('core/base::errors.error_when_sending_email_guest');
                }

                return $this->baseHttpResponse
                    ->setError()
                    ->setCode($e->getCode())
                    ->setMessage($message);
            case $e instanceof NotFoundHttpException:
                if (setting('redirect_404_to_homepage', 0) == 1) {
                    return redirect(url('/'));
                }

                break;
            case $e instanceof HttpExceptionInterface:
                $code = $e->getStatusCode();

                if ($request->expectsJson()) {
                    return match ($code) {
                        401 => $this->baseHttpResponse
                            ->setError()
                            ->setMessage(trans('core/acl::permissions.access_denied_message'))
                            ->setCode($code)
                            ->toResponse($request),
                        403 => $this->baseHttpResponse
                            ->setError()
                            ->setMessage(trans('core/acl::permissions.action_unauthorized'))
                            ->setCode($code)
                            ->toResponse($request),
                        404 => $this->baseHttpResponse
                            ->setError()
                            ->setMessage(trans('core/base::errors.not_found'))
                            ->setCode(404)
                            ->toResponse($request),
                        default => $this->baseHttpResponse
                            ->setError()
                            ->setMessage($e->getMessage())
                            ->setCode($code)
                            ->toResponse($request),
                    };
                }
        }

        return parent::render($request, $e);
    }

    public function report(Throwable $e)
    {
        if ($e instanceof IgnoringReport
            || $this->shouldntReport($e)
            || $this->isExceptionFromBot()) {
            return;
        }

        if (! app()->has('cache')) {
            parent::report($e);

            return;
        }

        $key = 'send_error_exception';

        if (Cache::has($key)) {
            return;
        }

        Cache::put($key, 1, Carbon::now()->addMinutes(5));

        if (! app()->isLocal() && ! app()->runningInConsole() && ! app()->isDownForMaintenance()) {
            if (
                setting('enable_send_error_reporting_via_email', false)
                && setting('email_driver', Mail::getDefaultDriver())
            ) {
                EmailHandler::sendErrorException($e);
            }

            if (config('core.base.general.error_reporting.via_slack', false)) {
                $request = request();

                $previous = $e->getPrevious();

                $inputs = $request->except([
                    'current_password',
                    'password',
                    'password_confirmation',
                    'token',
                    'remember_token',
                ]);

                $inputs = $inputs ? BaseHelper::jsonEncodePrettify($inputs) : null;

                logger()->channel('slack')->critical(
                    str_replace(base_path(), '', $e->getMessage()) . ($previous ? '(' . $previous . ')' : null),
                    [
                        'Request URL' => $request->fullUrl(),
                        'Request IP' => $request->ip(),
                        'Request Referer' => $request->header('referer'),
                        'Request Method' => $request->method(),
                        'Request Form Data' => $inputs,
                        'Exception Type' => $e::class,
                        'File Path' => $this->getErrorFilePath($e),
                        'Previous File Path' => $previous ? $this->getErrorFilePath($previous) : null,
                    ]
                );
            }
        }

        parent::report($e);
    }

    protected function getErrorFilePath(Throwable $e): string
    {
        return ltrim(str_replace(base_path(), '', $e->getFile()), '/') . ':' . $e->getLine();
    }

    protected function isExceptionFromBot(): bool
    {
        $ignoredBots = config('core.base.general.error_reporting.ignored_bots', []);
        $agent = strtolower(request()->userAgent());

        if (empty($agent)) {
            return false;
        }

        foreach ($ignoredBots as $bot) {
            if (str_contains($agent, $bot)) {
                return true;
            }
        }

        return false;
    }

    protected function getHttpExceptionView(HttpExceptionInterface $e)
    {
        $defaultView = parent::getHttpExceptionView($e);

        if (app()->runningInConsole() || request()->wantsJson() || request()->expectsJson()) {
            return $defaultView;
        }

        if (is_in_admin(true) && view()->exists($view = 'core/base::errors.' . $e->getStatusCode())) {
            return $view;
        }

        return apply_filters('get_http_exception_view', $defaultView, $e);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->wantsJson() || $request->expectsJson()) {
            return $this
                ->baseHttpResponse
                ->setError()
                ->setMessage($exception->getMessage())
                ->setCode(401)
                ->toResponse($request);
        }

        if (array_filter($exception->guards())) {
            $defaultException = redirect()
                ->guest($exception->redirectTo($request) ?? (Route::has('login') ? route('login') : url('login')));

            return apply_filters('cms_unauthenticated_response', $defaultException, $request, $exception);
        }

        return redirect()->guest(route('access.login'));
    }
}
