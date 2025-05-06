<?php

namespace Botble\Installer\InstallerStep;

use Botble\Theme\Facades\Manager;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;

class InstallerStep
{
    /**
     * @var array<InstallerStepItem>
     */
    protected static array $steps = [];

    protected static array $themes = [];

    /**
     * @return array<InstallerStepItem>
     */
    public static function getItems(): array
    {
        self::$steps = [
            'welcome' => InstallerStepItem::make()
                ->label(fn () => trans('packages/installer::installer.welcome.title'))
                ->route('installers.welcome')
                ->priority(10),
            'requirements' => InstallerStepItem::make()
                ->label(fn () => trans('packages/installer::installer.requirements.title'))
                ->route('installers.requirements.index')
                ->priority(20),
            'environment' => InstallerStepItem::make()
                ->label(fn () => trans('packages/installer::installer.environment.wizard.title'))
                ->route('installers.environments.index')
                ->priority(30),
            'create-account' => InstallerStepItem::make()
                ->label(fn () => trans('packages/installer::installer.createAccount.title'))
                ->route('installers.accounts.index')
                ->priority(50),
            'license' => InstallerStepItem::make()
                ->label(fn () => trans('packages/installer::installer.license.title'))
                ->route('installers.licenses.index')
                ->priority(60),
            'final' => InstallerStepItem::make()
                ->label(fn () => trans('packages/installer::installer.final.title'))
                ->route('installers.final')
                ->priority(70),
        ];

        if (InstallerStep::hasMoreThemes()) {
            self::$steps['theme'] = InstallerStepItem::make()
                ->label(fn () => trans('packages/installer::installer.theme.title'))
                ->route('installers.themes.index')
                ->priority(40);
        }

        return collect(apply_filters('installer_steps', self::$steps))
            ->sortBy(fn (InstallerStepItem $item) => $item->getPriority())
            ->all();
    }

    public static function currentStep(): int
    {
        foreach (array_values(self::getItems()) as $key => $item) {
            if (Route::is($item->getRoute())) {
                return $key + 1;
            }
        }

        return 1;
    }

    public static function getThemes(): array
    {
        if (! class_exists(Manager::class)) {
            return [];
        }

        if (self::$themes) {
            return self::$themes;
        }

        $themes = collect(Manager::getThemes())->mapWithKeys(function ($theme, $key) {
            return [$key => [
                'label' => $theme['name'],
                'image' => Theme::getThemeScreenshot($key),
            ]];
        })->all();

        self::$themes = apply_filters('cms_installer_themes', $themes);

        return self::$themes;
    }

    public static function hasMoreThemes(): bool
    {
        return count(self::getThemes()) > 1;
    }
}
