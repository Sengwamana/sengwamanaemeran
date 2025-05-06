@once
    <script>
        var lazyLoadShortcodeBlocks = function () {
            document.querySelectorAll('.shortcode-lazy-loading').forEach(function (element) {
                var name = element.getAttribute('data-name');
                var attributes = JSON.parse(element.getAttribute('data-attributes'));

                const url = '{{ route('public.ajax.render-ui-block') }}';
                const csrfToken = '{{ csrf_token() }}';

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        name,
                        attributes: {
                            ...attributes
                        }
                    })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(({ error, data }) => {
                        if (error) {
                            return;
                        }

                        element.outerHTML = data;

                        document.dispatchEvent(new CustomEvent('shortcode.loaded', {
                            detail: {
                                name,
                                attributes,
                                html: data
                            }
                        }));

                        if (typeof Theme !== 'undefined' && typeof Theme.lazyLoadInstance !== 'undefined') {
                            Theme.lazyLoadInstance.update()
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                    });
            });
        };

        window.addEventListener('load', function () {
            lazyLoadShortcodeBlocks();
        });
    </script>
@endonce
