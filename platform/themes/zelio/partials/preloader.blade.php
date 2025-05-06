@php
    $style = (int) theme_option('preloader_style', 1);
    $style = in_array($style, range(0, 3)) ? $style : 1;
@endphp

@if($style > 0)
    <div id="preloader">
        <div class="loader-cover">
            <div class="loader-container">
                <div @class(['loader-icon' => $style == 1, 'loader-icon-2' => $style == 2, 'loader-icon-3' => $style == 3])></div>
            </div>
        </div>
    </div>
@endif
