<?php

use Botble\Base\Facades\BaseHelper;
use Botble\Shortcode\View\View;
use Botble\Theme\Theme;

return [
    'inherit' => null,

    'events' => [
        'beforeRenderTheme' => function (Theme $theme): void {
            $version = get_cms_version() . '.8';

            $themeMode = theme_option('default_theme_color_mode', 'dark');

            if ($themeMode === 'light') {
                $theme->addHtmlAttributes(['data-bs-theme' => 'light']);
            } else {
                $theme->addHtmlAttributes(['data-bs-theme' => 'dark']);
            }

            $theme->addBodyAttributes(['class' => 'home-page-' . get_header_style()]);

            if (BaseHelper::isRtlEnabled()) {
                $theme->asset()->usePath()->add('bootstrap', 'css/vendors/bootstrap.rtl.min.css');
            } else {
                $theme->asset()->usePath()->add('bootstrap', 'css/vendors/bootstrap.min.css');
            }

            $theme->asset()->usePath()->add('swiper', 'css/vendors/swiper-bundle.min.css');
            $theme->asset()->usePath()->add('aos', 'css/vendors/aos.css');
            $theme->asset()->usePath()->add('aos', 'css/vendors/aos.css');
            $theme->asset()->usePath()->add('odometer', 'css/vendors/odometer.css', version: $version);
            $theme->asset()->usePath()->add('carouselTicker', 'css/vendors/carouselTicker.css', version: $version);
            $theme->asset()->usePath()->add('slick-css', 'css/vendors/slick.min.css', version: $version);
            $theme->asset()->usePath()->add('magnific-popup', 'css/vendors/magnific-popup.css', version: $version);
            $theme->asset()->usePath()->add('remixicon', 'fonts/remixicon/remixicon.css', version: $version);
            $theme->asset()->usePath()->add('main', 'css/main.css', version: $version);
            $theme->asset()->usePath()->add('theme', 'css/theme.css', version: $version);

            $theme->asset()->container('footer')->usePath()->add('jquery', 'js/vendors/jquery-3.7.1.min.js');
            $theme->asset()->container('footer')->usePath()->add('bootstrap', 'js/vendors/bootstrap.bundle.min.js');
            $theme->asset()->container('footer')->usePath()->add('swiper', 'js/vendors/swiper-bundle.min.js');
            $theme->asset()->container('footer')->usePath()->add('aos', 'js/vendors/aos.js');
            $theme->asset()->container('footer')->usePath()->add('wow', 'js/vendors/wow.min.js');
            $theme->asset()->container('footer')->usePath()->add('headhesive', 'js/vendors/headhesive.min.js');
            $theme->asset()->container('footer')->usePath()->add('smart-stick-nav', 'js/vendors/smart-stick-nav.js');
            $theme->asset()->container('footer')->usePath()->add('magnific-popup', 'js/vendors/jquery.magnific-popup.min.js');
            $theme->asset()->container('footer')->usePath()->add('gsap', 'js/vendors/gsap.min.js');
            $theme->asset()->container('footer')->usePath()->add('imagesloaded', 'js/vendors/imagesloaded.pkgd.min.js');
            $theme->asset()->container('footer')->usePath()->add('isotope', 'js/vendors/isotope.pkgd.min.js');
            $theme->asset()->container('footer')->usePath()->add('ScrollToPlugin', 'js/vendors/ScrollToPlugin.min.js');
            $theme->asset()->container('footer')->usePath()->add('ScrollTrigger', 'js/vendors/ScrollTrigger.min.js');
            $theme->asset()->container('footer')->usePath()->add('carouselTicker', 'js/vendors/jquery.carouselTicker.min.js');
            $theme->asset()->container('footer')->usePath()->add('slick-js', 'js/vendors/slick.min.js');
            $theme->asset()->container('footer')->usePath()->add('appear', 'js/vendors/jquery.appear.js', version: $version);
            $theme->asset()->container('footer')->usePath()->add('gsap-custom', 'js/vendors/gsap-custom.js', version: $version);
            $theme->asset()->container('footer')->usePath()->add('imageRevealHover', 'js/imageRevealHover.js', version: $version);
            $theme->asset()->container('footer')->usePath()->add('aat', 'js/vendors/aat.min.js');
            $theme->asset()->container('footer')->usePath()->add('color-modes', 'js/vendors/color-modes.js', version: $version);
            $theme->asset()->container('footer')->usePath()->add('main', 'js/main.js', ['jquery'], version: $version);
            $theme->asset()->container('footer')->usePath()->add('theme', 'js/theme.js', ['jquery'], version: $version);

            if (function_exists('shortcode')) {
                $theme->composer(
                    [
                        'page',
                        'post',
                        'portfolio.package',
                        'portfolio.project',
                        'portfolio.service',
                    ],
                    fn (View $view) => $view->withShortcodes()
                );
            }
        },
    ],
];
