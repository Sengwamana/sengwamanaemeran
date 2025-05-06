<?php
/**
 * Date: 22/07/2015
 * Time: 8:11 PM
 */

return [
    'offline' => env('ASSETS_OFFLINE', true),
    'enable_version' => env('ASSETS_ENABLE_VERSION', true),
    'version' => env('ASSETS_VERSION'),
    'scripts' => [
        'core-ui',
        'excanvas',
        'ie8-fix',
        'modernizr',
        'select2',
        'datepicker',
        'cookie',
        'core',
        'app',
        'toastr',
        'custom-scrollbar',
        'stickytableheaders',
        'jquery-waypoints',
        'spectrum',
        'fancybox',
        'fslightbox',
    ],
    'styles' => [
        'fontawesome',
        'select2',
        'toastr',
        'custom-scrollbar',
        'datepicker',
        'spectrum',
        'fancybox',
    ],
    'resources' => [
        'scripts' => [
            'core-ui' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/js/core-ui.js',
                ],
            ],
            'core' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/js/core.js',
                ],
            ],
            'app' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => [
                        '/vendor/core/core/base/libraries/jquery.min.js',
                        '/vendor/core/core/base/js/app.js',
                    ],
                ],
            ],
            'vue' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => [
                        '/vendor/core/core/base/libraries/vue.global.min.js',
                    ],
                ],
            ],
            'vue-app' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/js/vue-app.js',
                ],
            ],
            'bootstrap' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => [
                        '/vendor/core/core/base/libraries/bootstrap.bundle.min.js',
                    ],
                ],
            ],
            'modernizr' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/modernizr/modernizr.min.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.3/modernizr.js',
                ],
            ],
            'excanvas' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/excanvas.min.js',
                ],
            ],
            'ie8-fix' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/ie8.fix.min.js',
                ],
            ],
            'counterup' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => [
                        '/vendor/core/core/base/libraries/counterup/jquery.counterup.min.js',
                    ],
                ],
            ],
            'blockui' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery.blockui.min.js',
                ],
            ],
            'jquery-ui' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-ui/jquery-ui.min.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js',
                ],
            ],
            'cookie' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-cookie/jquery.cookie.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js',
                ],
            ],
            'dropzone' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/dropzone/dropzone.js',
                ],
            ],
            'jqueryTree' => [
                'use_cdn' => false,
                'location' => 'footer',
                'include_style' => true,
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-tree/jquery.tree.min.js',
                ],
            ],
            'jqueryTreeView' => [
                'use_cdn' => false,
                'location' => 'footer',
                'include_style' => true,
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-treeview/jquery.treeview.min.js',
                ],
            ],
            'bootstrap-editable' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/bootstrap3-editable/js/bootstrap-editable.min.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/js/bootstrap-editable.min.js',
                ],
            ],
            'toastr' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/toastr/toastr.min.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.2/toastr.min.js',
                ],
            ],
            'fancybox' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/fancybox/jquery.fancybox.min.js',
                    'cdn' => '//fastly.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js',
                ],
            ],
            'fslightbox' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/fslightbox.js',
                    'cdn' => '//fastly.jsdelivr.net/npm/fslightbox@3.4.1/index.min.js',
                ],
            ],
            'datatables' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => [
                        '/vendor/core/core/base/libraries/datatables/media/js/jquery.dataTables.min.js',
                        '/vendor/core/core/base/libraries/datatables/media/js/dataTables.bootstrap.min.js',
                        '/vendor/core/core/base/libraries/datatables/extensions/Buttons/js/dataTables.buttons.min.js',
                        '/vendor/core/core/base/libraries/datatables/extensions/Buttons/js/buttons.bootstrap.min.js',
                        '/vendor/core/core/base/libraries/datatables/extensions/Responsive/js/dataTables.responsive.min.js',
                    ],
                ],
            ],
            'raphael' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => [
                        '/vendor/core/core/base/libraries/raphael-min.js',
                    ],
                ],
            ],
            'morris' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/morris/morris.min.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js',
                ],
            ],
            'select2' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/select2/js/select2.min.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
            ],
            'cropper' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/cropper/cropper.min.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js',
                ],
            ],
            'datepicker' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/flatpickr/flatpickr.min.js',
                    'cdn' => '//fastly.jsdelivr.net/npm/flatpickr',
                ],
            ],
            'sortable' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/sortable/sortable.min.js',
                ],
            ],
            'jquery-nestable' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-nestable/jquery.nestable.min.js',
                ],
            ],
            'custom-scrollbar' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/mcustom-scrollbar/jquery.mCustomScrollbar.js',
                ],
            ],
            'stickytableheaders' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/stickytableheaders/jquery.stickytableheaders.js',
                ],
            ],
            'are-you-sure' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery.are-you-sure/jquery.are-you-sure.js',
                ],
            ],
            'moment' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/moment-with-locales.min.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.1/moment-with-locales.min.js',
                ],
            ],
            'datetimepicker' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js',
                ],
            ],
            'jquery-waypoints' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-waypoints/jquery.waypoints.min.js',
                ],
            ],
            'colorpicker' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js',
                ],
            ],
            'timepicker' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/bootstrap-timepicker/js/bootstrap-timepicker.min.js',
                ],
            ],
            'spectrum' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/spectrum/spectrum.js',
                ],
            ],
            'input-mask' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-inputmask/jquery.inputmask.bundle.min.js',
                ],
            ],
            'form-validation' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/js-validation/js/js-validation.js',
                ],
            ],
            'apexchart' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/apexchart/apexcharts.min.js',
                ],
            ],
            'coloris' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/coloris/coloris.min.js',
                    'cdn' => '//fastly.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.js',
                ],
            ],
            'tagify' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/tagify/tagify.js',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/tagify/4.16.4/tagify.min.js',
                ],
            ],
        ],
        'styles' => [
            'core' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/css/core.css',
                ],
            ],
            'fontawesome' => [
                'use_cdn' => true,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/font-awesome/css/fontawesome.min.css',
                    'cdn' => '//use.fontawesome.com/releases/v6.1.1/css/all.css',
                ],
            ],
            'dropzone' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/dropzone/dropzone.css',
                ],
            ],
            'jqueryTree' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-tree/jquery.tree.min.css',
                ],
            ],
            'jqueryTreeView' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-treeview/jquery.treeview.min.css',
                ],
            ],
            'jquery-ui' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-ui/jquery-ui.min.css',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.theme.css',
                ],
            ],
            'toastr' => [
                'use_cdn' => true,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/toastr/toastr.min.css',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.2/toastr.min.css',
                ],
            ],
            'kendo' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/kendo/kendo.min.css',
                ],
            ],
            'datatables' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => [
                        '/vendor/core/core/base/libraries/datatables/media/css/dataTables.bootstrap.min.css',
                        '/vendor/core/core/base/libraries/datatables/extensions/Buttons/css/buttons.bootstrap.min.css',
                        '/vendor/core/core/base/libraries/datatables/extensions/Responsive/css/responsive.bootstrap.min.css',
                    ],
                ],
            ],
            'bootstrap-editable' => [
                'use_cdn' => true,
                'location' => 'footer',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/bootstrap3-editable/css/bootstrap-editable.css',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/css/bootstrap-editable.css',
                ],
            ],
            'morris' => [
                'use_cdn' => true,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/morris/morris.css',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css',
                ],
            ],
            'cropper' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/cropper/cropper.min.css',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css',
                ],
            ],
            'datepicker' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/flatpickr/flatpickr.min.css',
                    'cdn' => '//fastly.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
                ],
            ],
            'jquery-nestable' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/jquery-nestable/jquery.nestable.min.css',
                ],
            ],
            'select2' => [
                'use_cdn' => true,
                'location' => 'header',
                'src' => [
                    'local' => [
                        '/vendor/core/core/base/libraries/select2/css/select2.min.css',
                        '/vendor/core/core/base/css/libraries/select2.css',
                    ],
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css',
                ],
            ],
            'fancybox' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/fancybox/jquery.fancybox.min.css',
                    'cdn' => '//fastly.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css',
                ],
            ],
            'custom-scrollbar' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/mcustom-scrollbar/jquery.mCustomScrollbar.css',
                ],
            ],
            'datetimepicker' => [
                'use_cdn' => true,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css',
                ],
            ],
            'colorpicker' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css',
                ],
            ],
            'timepicker' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/bootstrap-timepicker/css/bootstrap-timepicker.min.css',
                ],
            ],
            'spectrum' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/spectrum/spectrum.css',
                ],
            ],
            'apexchart' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/apexchart/apexcharts.css',
                ],
            ],
            'coloris' => [
                'use_cdn' => true,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/coloris/coloris.min.css',
                    'cdn' => '//fastly.jsdelivr.net/gh/mdbassit/Coloris@latest/dist/coloris.min.css',
                ],
            ],
            'tagify' => [
                'use_cdn' => true,
                'location' => 'header',
                'src' => [
                    'local' => '/vendor/core/core/base/libraries/tagify/tagify.css',
                    'cdn' => '//cdnjs.cloudflare.com/ajax/libs/tagify/4.16.4/tagify.css',
                ],
            ],
        ],
    ],
];
