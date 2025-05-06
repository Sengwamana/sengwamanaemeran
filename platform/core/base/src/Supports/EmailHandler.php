<?php

namespace Botble\Base\Supports;

use Botble\Base\Events\SendMailEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Media\Facades\RvMedia;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Symfony\Component\ErrorHandler\ErrorRenderer\HtmlErrorRenderer;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Throwable;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Twig\Extension\DebugExtension;
use Twig\TwigFilter;

class EmailHandler
{
    protected string $type = 'plugins';

    protected ?string $module = null;

    protected ?string $template = null;

    protected array $templates = [];

    protected array $variableValues = [];

    protected array $coreVariableValues;

    protected TwigCompiler $twigCompiler;

    public function __construct()
    {
        $this->twigCompiler = new TwigCompiler([
            'autoescape' => false,
            'debug' => true,
        ]);

        $this->twigCompiler->addExtension(new DebugExtension());

        $this->twigCompiler->addFilter(
            new TwigFilter('icon_url', function (string $value): string {
                return $this->getIconVariables()[$value] ?? '';
            }),
        );
    }

    public function setModule(string $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template): self
    {
        $this->template = $template;

        return $this;
    }

    protected function initVariableValues(): void
    {
        $this->variableValues['core'] = $this->getCoreVariableValues();
    }

    public function getCoreVariables(): array
    {
        return [
            'site_title' => trans('core/base::base.email_template.site_title'),
            'site_url' => trans('core/base::base.email_template.site_url'),
            'site_logo' => trans('core/base::base.email_template.site_logo'),
            'site_email' => trans('core/base::base.email_template.site_email'),
            'site_copyright' => trans('core/base::base.email_template.site_copyright'),
            'site_social_links' => trans('core/base::base.email_template.site_social_links'),
            'header' => trans('core/base::base.email_template.header'),
            'footer' => trans('core/base::base.email_template.footer'),
            'css' => trans('core/base::base.email_template.email_css'),
            'date_time' => trans('core/base::base.email_template.date_time'),
            'date_year' => trans('core/base::base.email_template.date_year'),
            'html_attributes' => trans('core/base::base.email_template.html_attributes'),
            'body_attributes' => trans('core/base::base.email_template.body_attributes'),
        ];
    }

    public function getCoreVariableValues(): array
    {
        $language = Language::getCurrentLocale();

        return $this->coreVariableValues ??= [
            'html_attributes' => trim(Html::attributes([
                'lang' => $language['locale'],
            ])),
            'body_attributes' => trim(Html::attributes([
                'dir' => $language['is_rtl'] ? 'rtl' : 'ltr',
            ])),
            'now' => ($now = Carbon::now()),
            'header' => apply_filters(
                BASE_FILTER_EMAIL_TEMPLATE_HEADER,
                get_setting_email_template_content('core', 'base', 'header')
            ),
            'footer' => apply_filters(
                BASE_FILTER_EMAIL_TEMPLATE_FOOTER,
                get_setting_email_template_content('core', 'base', 'footer')
            ),
            'site_title' => setting('admin_title') ?: config('app.name'),
            'site_url' => url(''),
            'site_logo' => $this->getSiteLogo(),
            'date_time' => BaseHelper::formatDateTime($now),
            'date_year' => $now->year,
            'site_email' => $siteEmail = setting('email_template_email_contact', get_admin_email()->first() ?: 'demo@example.com'),
            'site_admin_email' => $siteEmail,
            'site_copyright' => $this->getSiteCopyright(),
            'site_social_links' => $this->getSiteSocialLinks(),
            'css' => $this->getCssContent(),
            'max_height_for_logo' => setting('email_template_max_height_for_logo', 40),
        ];
    }

    public function getCssContent(): string
    {
        $css = File::get(core_path('base/resources/email-templates/default.css'));

        if ($customCSS = setting('email_template_custom_css')) {
            $css .= $customCSS;
        }

        return (string) apply_filters('email_template_css', $css);
    }

    protected function getSiteLogo(): string
    {
        $siteLogo = setting('email_template_logo');
        $siteLogo = apply_filters('core_email_template_site_logo', $siteLogo);

        return $siteLogo
            ? RvMedia::getImageUrl($siteLogo)
            : (
                ($adminLogo = setting('admin_logo'))
                ? RvMedia::getImageUrl($adminLogo)
                : url(config('core.base.general.logo'))
            );
    }

    protected function getSiteSocialLinks(): array
    {
        return ($socialLinks = setting('email_template_social_links'))
            ? collect(json_decode($socialLinks, true))
                ->map(fn ($links) => collect($links)->pluck('value', 'key'))
                ->filter(function ($link) {
                    return ! empty($link['image']) || ! empty($link['url']);
                })
                ->map(function ($link) {
                    $link['image'] = RvMedia::getImageUrl($link['image']);

                    return $link;
                })
                ->toArray()
            : [];
    }

    protected function getSiteCopyright(): ?string
    {
        $copyright = apply_filters('email_template_copyright_text', setting('email_template_copyright_text'));

        return $copyright
            ? str_replace(
                '%Y',
                Carbon::now()->format('Y'),
                apply_filters('email_template_copyright_text', setting('email_template_copyright_text'))
            )
            : $copyright;
    }

    public function getIconVariables(): array
    {
        $iconPath = 'vendor/core/core/base/images/email-icons';

        $path = public_path($iconPath);

        if (! File::isDirectory($path)) {
            return [];
        }

        $files = BaseHelper::scanFolder($path);

        $variables = [];

        foreach ($files as $file) {
            $fileExtension = strtolower(File::extension($file));

            if (! in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'], true)) {
                continue;
            }

            $variables[File::name($file)] = asset($iconPath . '/' . $file);
        }

        return $variables;
    }

    public function setVariableValue(string $variable, string|array|null $value, ?string $module = null): self
    {
        Arr::set($this->variableValues, ($module ?: $this->module) . '.' . $variable, $value);

        return $this;
    }

    public function getVariableValues(?string $module = null): array
    {
        $values = apply_filters('cms_email_variable_values', $this->variableValues, $this->template);

        if ($module) {
            return Arr::get($values, $module, []);
        }

        return $values;
    }

    public function setVariableValues(array $data, ?string $module = null): self
    {
        foreach ($data as $name => $value) {
            $this->setVariableValue($name, $value, $module);
        }

        return $this;
    }

    public function addTemplateSettings(string $module, ?array $data, string $type = 'plugins'): self
    {
        if (empty($data)) {
            return $this;
        }

        $this->module = $module;

        Arr::set($this->templates, $type . '.' . $module, $data);

        foreach ($data['templates'] as $key => &$template) {
            if (! isset($template['variables'])) {
                $this->templates[$type][$module]['templates'][$key]['variables'] = Arr::get($data, 'variables', []);
            }

            $this->templates[$type][$module]['templates'][$key]['path'] = platform_path(
                $type . '/' . $module . '/resources/email-templates/' . $key . '.tpl'
            );
        }

        return $this;
    }

    public function removeTemplateSettings(string $module, string $type = 'plugins'): self
    {
        Arr::forget($this->templates, $type . '.' . $module);

        return $this;
    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function getTemplateData(string $type, string $module, string $name): string|array|null
    {
        return Arr::get($this->templates, $type . '.' . $module . '.templates.' . $name);
    }

    public function getFunctions(): array
    {
        return $this->getTwigFunctions();
    }

    protected function getTwigFunctions(): array
    {
        return [
            'apply' => [
                'label' => trans('core/base::base.email_template.twig.tag.apply'),
                'sample' => "{% apply upper %}\n\tThis text becomes uppercase\n{% endapply %}",
            ],
            'for' => [
                'label' => trans('core/base::base.email_template.twig.tag.for'),
                'sample' => "{% for user in users %}\n\t{{ user.username|e }}\n{% endfor %}",
            ],
            'if' => [
                'label' => trans('core/base::base.email_template.twig.tag.if'),
                'sample' => "{% if online == false %}\n\t<p>Our website is in maintenance mode. Please, come back later.</p>\n{% endif %}",
            ],
        ];
    }

    public function getVariables(string $type, string $module, string $name): array
    {
        $this->template = $name;

        return $this->getCoreVariables() + Arr::get($this->getTemplateData($type, $module, $name), 'variables', []);
    }

    public function getVariableValue(string $variable, string $module, string $default = ''): string|array|null
    {
        $values = $this->getVariableValues();

        $value = Arr::get($values, $module . '.' . $variable, $default);

        if (! $value) {
            $value = Arr::get($values, 'core.' . $variable, $default);
        }

        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        return $value;
    }

    protected function replaceVariableValue(array $variables, string $module, string $content): string
    {
        do_action('email_variable_value');

        $data = [];

        foreach ($variables as $variable) {
            $data[$variable] = $this->getVariableValue($variable, $module);
        }

        $coreData = $this->getCoreVariableValues();
        $data = [...$coreData, ...$data];
        $variables = [...array_keys($coreData), ...$variables];

        $twigCompiler = apply_filters('cms_twig_compiler', $this->twigCompiler);

        foreach ($data as $key => $value) {
            try {
                $data[$key] = $value && is_string($value) ? $twigCompiler->compile($value, $data) : $value;
            } catch (Throwable) {
                $data[$key] = $value;
            }
        }

        if (empty($data) || empty($content)) {
            return $content;
        }

        try {
            return $twigCompiler->compile($content, $data);
        } catch (Throwable $throwable) {
            BaseHelper::logError($throwable);

            foreach ($variables as $variable) {
                $keys = [
                    '{{ ' . $variable . ' }}',
                    '{{' . $variable . '}}',
                    '{{ ' . $variable . '}}',
                    '{{' . $variable . ' }}',
                    '<?php echo e(' . $variable . '); ?>',
                ];

                foreach ($keys as $key) {
                    $value = $data[$variable] ?? '';

                    if (is_string($value)) {
                        $content = str_replace($key, $value, $content);
                    }
                }
            }

            $content .= Html::tag('p', 'Complied error: ' . $throwable->getMessage(), ['style' => 'color: red; font-weight: bold']);

            return $content;
        }
    }

    public function sendUsingTemplate(
        string $template,
        string|array|null $email = null,
        array $args = [],
        bool $debug = false,
        string $type = 'plugins',
        $subject = null
    ): bool {
        if (! $this->templateEnabled($template)) {
            return false;
        }

        $this->type = $type;
        $this->template = $template;

        if (! $subject) {
            $subject = $this->getSubject();
        }

        $this->send($this->getContent(), $subject, $email, $args, $debug);

        return true;
    }

    public function templateEnabled(string $template, string $type = 'plugins'): bool
    {
        return (bool) get_setting_email_status($type, $this->module, $template);
    }

    public function send(
        string $content,
        string $title,
        string|array|null $to = null,
        array $args = [],
        bool $debug = false
    ): void {
        try {
            if (empty($to)) {
                $to = get_admin_email()->toArray();
                if (empty($to)) {
                    $to = setting('email_from_address', config('mail.from.address'));
                }
            }

            $content = $this->prepareData($content);
            $title = $this->prepareData($title);

            event(new SendMailEvent($content, $title, $to, $args, $debug));
        } catch (Throwable $throwable) {
            if ($debug) {
                throw $throwable;
            }

            BaseHelper::logError($throwable);

            $this->sendErrorException($throwable);
        }
    }

    public function prepareData(string $content): string
    {
        $this->initVariableValues();

        if (! empty($content)) {
            $variables = $this->getCoreVariables();

            if ($this->module && $this->template) {
                $variables = $this->getVariables($this->type ?: 'plugins', $this->module, $this->template);
            }

            $variables = Arr::except($variables, array_keys($this->getCoreVariableValues()));

            $content = $this->replaceVariableValue(array_keys($variables), $this->module, $content);
        }

        return apply_filters(BASE_FILTER_EMAIL_TEMPLATE, $content);
    }

    public function sendErrorException(Throwable $throwable): void
    {
        try {
            $ex = FlattenException::createFromThrowable($throwable);

            $url = URL::full();
            $error = $this->renderException($throwable);

            $this->send(
                view('core/base::emails.error-reporting', compact('url', 'ex', 'error'))->render(),
                $throwable->getFile(),
                ! empty(config('core.base.general.error_reporting.to')) ?
                    config('core.base.general.error_reporting.to') :
                    get_admin_email()->toArray()
            );
        } catch (Throwable $throwable) {
            BaseHelper::logError($throwable);
        }
    }

    protected function renderException(Throwable $throwable): string
    {
        $renderer = new HtmlErrorRenderer(true);

        $throwable = $renderer->render($throwable);

        if (! headers_sent()) {
            http_response_code($throwable->getStatusCode());

            foreach ($throwable->getHeaders() as $name => $value) {
                header($name . ': ' . $value, false);
            }
        }

        return $throwable->getAsString();
    }

    public function getTemplateContent(string $template, string $type = 'plugins'): ?string
    {
        $this->template = $template;
        $this->type = $type;

        return get_setting_email_template_content($type, $this->module, $template);
    }

    public function getTemplateSubject(string $template, string $type = 'plugins'): string
    {
        return (string) setting(
            get_setting_email_subject_key($type, $this->module, $template),
            trans(
                config(
                    $type . '.' . $this->module . '.email.templates.' . $template . '.subject',
                    ''
                )
            )
        );
    }

    public function getContent(): string
    {
        $content = $this->prepareData(get_setting_email_template_content($this->type, $this->module, $this->template));

        $inlineCss = new CssToInlineStyles();

        return $inlineCss->convert($content, $this->getCssContent());
    }

    public function getSubject(): string
    {
        return $this->prepareData($this->getTemplateSubject($this->template, $this->type));
    }
}
