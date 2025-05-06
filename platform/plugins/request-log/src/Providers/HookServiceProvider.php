<?php

namespace Botble\RequestLog\Providers;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\AlertFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\AlertField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\RequestLog\Events\RequestHandlerEvent;
use Botble\Setting\Enums\DataRetentionPeriod;
use Botble\Setting\Forms\GeneralSettingForm;
use Botble\Setting\Http\Requests\GeneralSettingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action(BASE_ACTION_SITE_ERROR, [$this, 'handleSiteError'], 125);

        $this->app['events']->listen(RenderingDashboardWidgets::class, function (): void {
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 125, 2);
        });

        GeneralSettingForm::extend(callback: function (GeneralSettingForm $form): void {
            $form
                ->add(
                    'request_log_data_retention_period',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(trans('plugins/request-log::request-log.clear_old_data'))
                        ->helperText(trans('plugins/request-log::request-log.clear_old_data_helper'))
                        ->choices(DataRetentionPeriod::labels())
                        ->selected(setting('request_log_data_retention_period', DataRetentionPeriod::ONE_MONTH))
                )
                ->when(! setting('cronjob_last_run_at') && ! $form->has('cronjob_warning'), function (GeneralSettingForm $form): void {
                    $form->add(
                        'cronjob_warning',
                        AlertField::class,
                        AlertFieldOption::make()
                            ->type('warning')
                            ->content(trans('plugins/audit-log::history.cronjob_warning', [
                                'link' => route('system.cronjob'),
                            ])),
                    );
                });
        });

        add_filter('core_request_rules', function (array $rules, $request) {
            if (! $request instanceof GeneralSettingRequest) {
                return $rules;
            }

            return [
                ...$rules,
                'request_log_data_retention_period' => ['required', 'string', Rule::in(DataRetentionPeriod::values())],
            ];
        }, 999, 2);
    }

    public function handleSiteError(int $code): void
    {
        event(new RequestHandlerEvent($code));
    }

    public function registerDashboardWidgets(array $widgets, Collection $widgetSettings): array
    {
        if (! Auth::guard()->user()->hasPermission('request-log.index')) {
            return $widgets;
        }

        Assets::addScriptsDirectly(['vendor/core/plugins/request-log/js/request-log.js']);

        return (new DashboardWidgetInstance())
            ->setPermission('request-log.index')
            ->setKey('widget_request_errors')
            ->setTitle(trans('plugins/request-log::request-log.widget_request_errors'))
            ->setRoute(route('request-log.widget.request-errors'))
            ->setColumn('col-md-6 col-sm-6')
            ->init($widgets, $widgetSettings);
    }
}
