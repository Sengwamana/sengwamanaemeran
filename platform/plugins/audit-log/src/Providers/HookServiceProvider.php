<?php

namespace Botble\AuditLog\Providers;

use Botble\AuditLog\AuditLog;
use Botble\AuditLog\Events\AuditHandlerEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\AlertFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\AlertField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Setting\Enums\DataRetentionPeriod;
use Botble\Setting\Forms\GeneralSettingForm;
use Botble\Setting\Http\Requests\GeneralSettingRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_action(AUTH_ACTION_AFTER_LOGOUT_SYSTEM, [$this, 'handleLogout'], 45, 2);

        add_action(USER_ACTION_AFTER_UPDATE_PASSWORD, [$this, 'handleUpdatePassword'], 45, 3);
        add_action(USER_ACTION_AFTER_UPDATE_PASSWORD, [$this, 'handleUpdateProfile'], 45, 3);

        if (defined('BACKUP_ACTION_AFTER_BACKUP')) {
            add_action(BACKUP_ACTION_AFTER_BACKUP, [$this, 'handleBackup'], 45);
            add_action(BACKUP_ACTION_AFTER_RESTORE, [$this, 'handleRestore'], 45);
        }

        $this->app['events']->listen(RenderingDashboardWidgets::class, function (): void {
            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this, 'registerDashboardWidgets'], 28, 2);
        });

        GeneralSettingForm::extend(callback: function (GeneralSettingForm $form): void {
            $form
                ->add(
                    'audit_log_data_retention_period',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(trans('plugins/audit-log::history.clear_old_data'))
                        ->helperText(trans('plugins/audit-log::history.clear_old_data_helper'))
                        ->choices(DataRetentionPeriod::labels())
                        ->selected(setting('audit_log_data_retention_period', DataRetentionPeriod::ONE_MONTH))
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
                'audit_log_data_retention_period' => ['required', 'string', Rule::in(DataRetentionPeriod::values())],
            ];
        }, 999, 2);
    }

    public function handleLogout(Request $request, Model $data): void
    {
        event(new AuditHandlerEvent(
            'of the system',
            'logged out',
            $data->getKey(),
            $data->name,
            'info'
        ));
    }

    public function handleUpdateProfile(string $screen, Request $request, Model $data): void
    {
        event(new AuditHandlerEvent(
            $screen,
            'updated profile',
            $data->getKey(),
            AuditLog::getReferenceName($screen, $data),
            'info'
        ));
    }

    public function handleUpdatePassword(string $screen, Request $request, Model $data): void
    {
        event(new AuditHandlerEvent(
            $screen,
            'changed password',
            $data->getKey(),
            AuditLog::getReferenceName($screen, $data),
            'danger'
        ));
    }

    public function handleBackup(string $screen): void
    {
        event(new AuditHandlerEvent($screen, 'created', 0, '', 'info'));
    }

    public function handleRestore(string $screen): void
    {
        event(new AuditHandlerEvent($screen, 'restored', 0, '', 'info'));
    }

    public function registerDashboardWidgets(array $widgets, Collection $widgetSettings): array
    {
        if (! Auth::guard()->user()->hasPermission('audit-log.index')) {
            return $widgets;
        }

        Assets::addScriptsDirectly('vendor/core/plugins/audit-log/js/audit-log.js');

        return (new DashboardWidgetInstance())
            ->setPermission('audit-log.index')
            ->setKey('widget_audit_logs')
            ->setTitle(trans('plugins/audit-log::history.widget_audit_logs'))
            ->setIcon('fas fa-history')
            ->setColor('cyan')
            ->setRoute(route('audit-log.widget.activities'))
            ->setBodyClass('')
            ->setColumn('col-md-6 col-sm-6')
            ->init($widgets, $widgetSettings);
    }
}
