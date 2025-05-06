<div
    data-bb-value="smtp"
    class="email-fields"
    @style(['display: none;' => $mailer !== 'smtp'])
>
    <x-core::form.text-input
        name="email_port"
        :label="trans('core/setting::setting.email.port')"
        type="number"
        data-counter="10"
        :value="old('email_port', setting('email_port', config('mail.mailers.smtp.port')))"
        :placeholder="trans('core/setting::setting.email.port_placeholder')"
    />

    <x-core::form.text-input
        name="email_host"
        :label="trans('core/setting::setting.email.host')"
        type="text"
        data-counter="255"
        :value="old('email_host', setting('email_host', config('mail.mailers.smtp.host')))"
        :placeholder="trans('core/setting::setting.email.host_placeholder')"
    />

    <x-core::form.text-input
        name="email_username"
        :label="trans('core/setting::setting.email.username')"
        type="text"
        data-counter="120"
        :value="old('email_username', setting('email_username', config('mail.mailers.smtp.username')))"
        :placeholder="trans('core/setting::setting.email.username_placeholder')"
    />

    <x-core::form.text-input
        name="email_password"
        :label="trans('core/setting::setting.email.password')"
        type="password"
        data-counter="255"
        :value="old('email_password', setting('email_password', config('mail.mailers.smtp.password')))"
        :placeholder="trans('core/setting::setting.email.password_placeholder')"
    />

    <x-core::form.text-input
        name="email_local_domain"
        :label="trans('core/setting::setting.email.local_domain')"
        data-counter="20"
        :value="old('email_local_domain', setting('email_local_domain', config('mail.mailers.smtp.local_domain')))"
        :placeholder="trans('core/setting::setting.email.local_domain_placeholder')"
    />
</div>

<div
    data-bb-value="mailgun"
    class="email-fields"
    @style(['display: none;' => $mailer !== 'mailgun'])
>
    <x-core::form.text-input
        name="email_mail_gun_domain"
        :label="trans('core/setting::setting.email.mail_gun_domain')"
        data-counter="120"
        :value="old('email_mail_gun_domain', setting('email_mail_gun_domain'))"
        :placeholder="trans('core/setting::setting.email.mail_gun_domain_placeholder')"
    />

    @if (!BaseHelper::hasDemoModeEnabled())
        <x-core::form.text-input
            name="email_mail_gun_secret"
            :label="trans('core/setting::setting.email.mail_gun_secret')"
            data-counter="120"
            :value="old('email_mail_gun_secret', setting('email_mail_gun_secret'))"
            :placeholder="trans('core/setting::setting.email.mail_gun_secret_placeholder')"
        />
    @endif

    <x-core::form.text-input
        name="email_mail_gun_endpoint"
        :label="trans('core/setting::setting.email.mail_gun_endpoint')"
        data-counter="120"
        :value="old('email_mail_gun_endpoint', setting('email_mail_gun_endpoint', 'api.mailgun.net'))"
        :placeholder="trans('core/setting::setting.email.mail_gun_endpoint_placeholder')"
    />
</div>

<div
    data-bb-value="ses"
    class="email-fields"
    @style(['display: none;' => $mailer !== 'ses'])
>
    <x-core::form.text-input
        name="email_ses_key"
        :label="trans('core/setting::setting.email.ses_key')"
        data-counter="120"
        :value="old('email_ses_key', setting('email_ses_key', config('services.ses.key')))"
        :placeholder="trans('core/setting::setting.email.ses_key_placeholder')"
    />

    @if (!BaseHelper::hasDemoModeEnabled())
        <x-core::form.text-input
            name="email_ses_secret"
            :label="trans('core/setting::setting.email.ses_secret')"
            data-counter="120"
            :value="old('email_ses_secret', setting('email_ses_secret', config('services.ses.secret')))"
            :placeholder="trans('core/setting::setting.email.ses_secret_placeholder')"
        />
    @endif

    <x-core::form.text-input
        name="email_ses_region"
        :label="trans('core/setting::setting.email.ses_region')"
        data-counter="120"
        :value="old('email_ses_region', setting('email_ses_region', config('services.ses.region')))"
        :placeholder="trans('core/setting::setting.email.ses_region_placeholder')"
    />
</div>

<div
    data-bb-value="postmark"
    class="email-fields"
    @style(['display: none;' => $mailer !== 'postmark'])
>
    @if (!BaseHelper::hasDemoModeEnabled())
        <x-core::form.text-input
            name="email_postmark_token"
            :label="trans('core/setting::setting.email.postmark_token')"
            data-counter="120"
            :value="old('email_postmark_token', setting('email_postmark_token', config('services.postmark.token')))"
            :placeholder="trans('core/setting::setting.email.postmark_token_placeholder')"
        />
    @endif
</div>

<div
    data-bb-value="sendmail"
    class="email-fields"
    @style(['display: none;' => old('email_driver', $mailer) !== 'sendmail'])
>
    <x-core::form.text-input
        name="email_sendmail_path"
        :label="trans('core/setting::setting.email.sendmail_path')"
        data-counter="120"
        :value="old('email_sendmail_path', setting('email_sendmail_path', config('mail.mailers.sendmail.path')))"
        :placeholder="trans('core/setting::setting.email.sendmail_path')"
        :helper-text="trans('core/setting::setting.email.default') .': <code>' . config('mail.mailers.sendmail.path') . '</code>'"
    />
</div>

<div
    data-bb-value="log"
    class="email-fields"
    @style(['display: none;' => $mailer !== 'log'])
>
    <x-core::form.select
        name="email_log_channel"
        :label="trans('core/setting::setting.email.log_channel')"
        :options="array_combine(
            $logChannels = array_keys(config('logging.channels', [])),
            $logChannels,
        )"
        :value="old('email_log_channel', setting('email_log_channel', config('mail.mailers.log.channel')))"
    />
</div>
