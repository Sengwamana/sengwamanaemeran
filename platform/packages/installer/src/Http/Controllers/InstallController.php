<?php

namespace Botble\Installer\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Services\DownloadLocaleService;
use Botble\Base\Supports\Language;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Throwable;

class InstallController extends BaseController
{
    public function index(): View|RedirectResponse
    {
        $languages = collect(Language::getLocales())->mapWithKeys(fn ($item, $key) => [$key => "{$item} - {$key}"]);

        return view('packages/installer::welcome', compact('languages'));
    }

    public function next(
        Request $request,
        DownloadLocaleService $downloadLocaleService
    ): RedirectResponse {
        $request->validate([
            'language' => ['required', 'string'],
        ]);

        $language = $request->input('language');

        if ($language === 'en') {
            return $this->redirectToNextStep();
        }

        try {
            $downloadLocaleService->handle($language);
        } catch (Throwable $e) {
            BaseHelper::logError($e);
        }

        Session::put('site-locale', $language);

        return $this->redirectToNextStep();
    }

    protected function redirectToNextStep()
    {
        return redirect()->to(
            URL::signedRoute(
                'installers.requirements.index',
                expiration: Carbon::now()->addMinutes(30)
            )
        );
    }
}
