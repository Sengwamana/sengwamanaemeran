<?php

namespace Botble\Base\Http\Actions;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Closure;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteResourceAction implements Responsable
{
    protected Closure $beforeDeleting;

    protected Closure $deleteUsing;

    protected Closure $afterDeleting;

    protected bool $silent = false;

    public function __construct(
        protected Model $model,
        protected Request $request,
        protected BaseHttpResponse $httpResponse
    ) {
    }

    public static function make(Model $model): static
    {
        return app(static::class, compact('model'));
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getHttpResponse(): BaseHttpResponse
    {
        return $this->httpResponse;
    }

    public function beforeDeleting(Closure $callback): static
    {
        $this->beforeDeleting = $callback;

        return $this;
    }

    public function deleteUsing(Closure $callback): static
    {
        $this->deleteUsing = $callback;

        return $this;
    }

    public function afterDeleting(Closure $callback): static
    {
        $this->afterDeleting = $callback;

        return $this;
    }

    protected function dispatchBeforeDeleting(): void
    {
        if (! isset($this->beforeDeleting)) {
            return;
        }

        call_user_func($this->beforeDeleting, $this);
    }

    protected function dispatchDelete(): void
    {
        if (! isset($this->deleteUsing)) {
            if (! $this->model->exists) {
                $this->httpResponse->setError()->setMessage(trans('core/base::notices.delete_failed_message'));

                return;
            }

            $this->model->delete();

            DeletedContentEvent::dispatch($this->model::class, $this->request, $this->model);

            $this->httpResponse->withDeletedSuccessMessage();

            return;
        }

        call_user_func($this->deleteUsing, $this);
    }

    protected function dispatchAfterDeleting(): void
    {
        if (! isset($this->afterDeleting)) {
            return;
        }

        call_user_func($this->afterDeleting, $this);
    }

    public function toResponse($request): BaseHttpResponse
    {
        try {
            DB::beginTransaction();

            $this->dispatchBeforeDeleting();

            $this->dispatchDelete();

            $this->dispatchAfterDeleting();

            DB::commit();

            return $this->httpResponse;
        } catch (Throwable $exception) {
            DB::rollBack();

            return $this
                ->httpResponse
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
