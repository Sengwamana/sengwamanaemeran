<script>
    window.themeIcons = window.themeIcons || {!! json_encode(Theme::getThemeIcons()) !!}
</script>

{!! apply_filters('theme_icon_js_code', null) !!}
