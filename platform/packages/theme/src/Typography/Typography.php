<?php

namespace Botble\Theme\Typography;

use Botble\Base\Facades\BaseHelper;
use Botble\Support\Http\Requests\Request;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Botble\Theme\Facades\ThemeOption;
use Botble\Theme\Http\Requests\UpdateOptionsRequest;
use Botble\Theme\ThemeOption\Fields\GoogleFontsField;
use Botble\Theme\ThemeOption\Fields\NumberField;
use Botble\Theme\ThemeOption\ThemeOptionSection;
use Illuminate\Support\Facades\Event;

class Typography
{
    /**
     * @var array<TypographyItem>
     */
    protected array $fontFamilies = [];

    /**
     * @var array<TypographyItem>
     */
    protected array $fontSizes = [];

    public function registerFontFamily(TypographyItem $fontFamily): static
    {
        $this->fontFamilies[$fontFamily->getName()] = $fontFamily;

        return $this;
    }

    public function removeFontFamilies(array|string $fontFamilies): static
    {
        $fontFamilies = is_array($fontFamilies) ? $fontFamilies : [$fontFamilies];

        $this->fontFamilies = array_filter(
            $this->fontFamilies,
            fn (TypographyItem $fontFamily) => ! in_array($fontFamily->getName(), $fontFamilies)
        );

        return $this;
    }

    /**
     * @param  array<TypographyItem>  $fontFamilies
     * @return $this
     */
    public function registerFontFamilies(array $fontFamilies): static
    {
        foreach ($fontFamilies as $fontFamily) {
            $this->registerFontFamily($fontFamily);
        }

        return $this;
    }

    public function registerFontSize(TypographyItem $fontSize): static
    {
        $this->fontSizes[$fontSize->getName()] = $fontSize;

        return $this;
    }

    public function removeFontSizes(array|string $fontSizes): static
    {
        $fontSizes = is_array($fontSizes) ? $fontSizes : [$fontSizes];

        $this->fontSizes = array_filter(
            $this->fontSizes,
            fn (TypographyItem $fontSize) => ! in_array($fontSize->getName(), $fontSizes)
        );

        return $this;
    }

    /**
     * @param  array<TypographyItem>  $fontSizes
     * @return $this
     */
    public function registerFontSizes(array $fontSizes): static
    {
        foreach ($fontSizes as $fontSize) {
            $this->registerFontSize($fontSize);
        }

        return $this;
    }

    public function getFontFamilies(): array
    {
        return $this->fontFamilies;
    }

    public function getFontSizes(): array
    {
        return $this->fontSizes;
    }

    public function renderCssVariables(): string
    {
        if (empty($this->fontFamilies)) {
            $fontFamily = new TypographyItem('primary', __('Primary'), theme_option('primary_font', 'Inter'));

            $this->fontFamilies[$fontFamily->getName()] = $fontFamily;
        }

        $fontFamilies = $this->getFontFamilies();

        $fontFaces = '';
        $styles = '<style>:root{';

        $renderedFonts = [];

        foreach ($fontFamilies as $fontFamily) {
            $value = theme_option("tp_{$fontFamily->getName()}_font");

            if (! $value) {
                $value = theme_option("{$fontFamily->getName()}_font");
            }

            if (! $value) {
                $value = $fontFamily->getDefault();
            }

            if (! in_array($value, $renderedFonts) && $fontFamily->isGoogleFont()) {
                $fontWeights = $fontFamily->getFontWeights() ?: ['300', '400', '500', '600', '700'];

                $fontFaces .= BaseHelper::googleFonts('https://fonts.googleapis.com/' . sprintf(
                    'css2?family=%s:wght@%s&display=swap',
                    urlencode($value),
                    implode(';', $fontWeights)
                ));

                $renderedFonts[] = $value;
            }

            $styles .= sprintf(
                '--%s-font: "%s", sans-serif;',
                $fontFamily->getName(),
                $value
            );
        }

        $fontSizes = $this->getFontSizes();

        foreach ($fontSizes as $fontSize) {
            $styles .= sprintf(
                '--%s-size: %spx;',
                $fontSize->getName(),
                theme_option("tp_{$fontSize->getName()}_size", $fontSize->getDefault())
            );
        }

        if ($fontSizes) {
            foreach (['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'body'] as $tag) {
                if (! isset($fontSizes[$tag])) {
                    continue;
                }

                $fontSize = $fontSizes[$tag];

                $styles .= sprintf(
                    '%s{font-size: var(--%s-size);}',
                    $tag,
                    $fontSize->getName()
                );
            }
        }

        $styles .= '}</style>';

        return $fontFaces . $styles;
    }

    public function renderThemeOptions(): void
    {
        Event::listen(RenderingThemeOptionSettings::class, function (): void {
            if (empty($this->fontFamilies) && empty($this->fontSizes)) {
                return;
            }

            $fields = [];

            foreach ($this->fontFamilies as $fontFamily) {
                $fields[] = GoogleFontsField::make()
                    ->name("tp_{$fontFamily->getName()}_font")
                    ->label(__(':name font family', ['name' => $fontFamily->getLabel()]))
                    ->defaultValue($fontFamily->getDefault());
            }

            foreach ($this->fontSizes as $fontSize) {
                $fields[] = NumberField::make()
                    ->name("tp_{$fontSize->getName()}_size")
                    ->label(__(':name font size', ['name' => $fontSize->getLabel()]))
                    ->defaultValue($fontSize->getDefault())
                    ->helperText(__('The font size in pixels (px). Default is :default', [
                        'default' => "<code>{$fontSize->getDefault()}</code>",
                    ]));
            }

            ThemeOption::setSection(
                ThemeOptionSection::make('opt-text-subsection-typography')
                    ->title(trans('packages/theme::theme.typography'))
                    ->icon('ti ti-typography')
                    ->priority(10)
                    ->fields($fields)
            );
        });

        add_filter('core_request_rules', function (array $rules, Request $request) {
            if (! $request instanceof UpdateOptionsRequest) {
                return $rules;
            }

            foreach ($this->fontFamilies as $fontFamily) {
                $rules["tp_{$fontFamily->getName()}_font"] = ['sometimes', 'required', 'string'];
            }

            foreach ($this->fontSizes as $fontSize) {
                $rules["tp_{$fontSize->getName()}_size"] = ['sometimes', 'required', 'numeric', 'gt:0'];
            }

            return $rules;
        }, 999, 2);

        add_filter('core_request_attributes', function (array $attributes, Request $request) {
            if (! $request instanceof UpdateOptionsRequest) {
                return $attributes;
            }

            foreach ($this->fontFamilies as $fontFamily) {
                $attributes["tp_{$fontFamily->getName()}_font"] = $fontFamily->getLabel();
            }

            foreach ($this->fontSizes as $fontSize) {
                $attributes["tp_{$fontSize->getName()}_size"] = $fontSize->getLabel();
            }

            return $attributes;
        }, 999, 2);
    }
}
