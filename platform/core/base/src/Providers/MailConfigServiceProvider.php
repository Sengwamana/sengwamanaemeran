<?php

namespace Botble\Base\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Hooks\EmailSettingHooks;
use Botble\Base\Supports\ServiceProvider;
use Botble\Setting\Supports\SettingStore;
use Illuminate\Mail\MailManager;

class MailConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->booted(function (): void {
            add_filter(
                BASE_FILTER_AFTER_SETTING_EMAIL_CONTENT,
                [EmailSettingHooks::class, 'addEmailTemplateSettings'],
                99
            );

            $config = $this->app->make('config');

            if (! $config->get('core.base.general.enable_email_configuration_from_admin_panel', true)) {
                return;
            }

            $this->app->resolving(MailManager::class, function () use ($config): void {
                $setting = $this->app->make(SettingStore::class);

                $defaultMailDriver = function_exists('proc_open') ? 'sendmail' : 'smtp';

                $mailDriver = $setting->get('email_driver', BaseHelper::hasDemoModeEnabled() ? $config->get('mail.default') : $defaultMailDriver);

                $config->set([
                    'mail' => array_merge($config->get('mail'), [
                        'default' => $mailDriver,
                        'from' => [
                            'address' => $setting->get('email_from_address', $config->get('mail.from.address')),
                            'name' => $setting->get('email_from_name', $config->get('mail.from.name')),
                        ],
                        'stream' => [
                            'ssl' => [
                                'allow_self_signed' => true,
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                            ],
                        ],
                    ]),
                ]);

                switch ($mailDriver) {
                    case 'smtp':
                        $config->set([
                            'mail.mailers.smtp' => array_merge($config->get('mail.mailers.smtp'), [
                                'transport' => 'smtp',
                                'schema' => $config->get('mail.mailers.smtp.schema'),
                                'host' => $setting->get('email_host', $config->get('mail.mailers.smtp.host')),
                                'port' => (int) $setting->get('email_port', $config->get('mail.mailers.smtp.port')),
                                'username' => $setting->get('email_username', $config->get('mail.mailers.smtp.username')),
                                'password' => $setting->get('email_password', $config->get('mail.mailers.smtp.password')),
                                'auth_mode' => null,
                                'verify_peer' => false,
                            ]),
                        ]);

                        break;
                    case 'mailgun':
                        $config->set([
                            'services.mailgun' => [
                                'domain' => $setting->get('email_mail_gun_domain'),
                                'secret' => $setting->get('email_mail_gun_secret'),
                                'endpoint' => $setting->get('email_mail_gun_endpoint', 'api.mailgun.net'),
                                'scheme' => 'https',
                            ],
                        ]);

                        break;
                    case 'sendmail':
                        $config->set([
                            'mail.mailers.sendmail.path' => $setting->get(
                                'email_sendmail_path',
                                $config->get('mail.mailers.sendmail.path')
                            ),
                        ]);

                        break;
                    case 'postmark':
                        $config->set([
                            'services.postmark' => [
                                'token' => $setting->get('email_postmark_token', $config->get('services.postmark.token')),
                            ],
                        ]);

                        break;
                    case 'ses':
                        $config->set([
                            'services.ses' => [
                                'key' => $setting->get('email_ses_key', $config->get('services.ses.key')),
                                'secret' => $setting->get('email_ses_secret', $config->get('services.ses.secret')),
                                'region' => $setting->get('email_ses_region', $config->get('services.ses.region')),
                            ],
                        ]);

                        break;
                    case 'log':
                        $config->set([
                            'mail.mailers.log.channel' => $setting->get(
                                'email_log_channel',
                                $config->get('mail.mailers.log.channel')
                            ),
                        ]);

                        break;

                    default:
                        do_action('core_email_setup_mailer', $mailDriver);

                        break;
                }
            });
        });
    }
}
