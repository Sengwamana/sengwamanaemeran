@foreach (RvMedia::getConfig('libraries.javascript', []) as $js)
    <script
        src="{{ asset($js) }}?v={{ get_cms_version() }}"
        type="text/javascript"
    ></script>
@endforeach
