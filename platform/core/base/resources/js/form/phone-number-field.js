class PhoneNumberField {
    init() {
        $(document)
            .find('.js-phone-number-mask')
            .each(function (index, element) {
                window.intlTelInput(element, {
                    // allowDropdown: false,
                    // autoHideDialCode: false,
                    // autoPlaceholder: "off",
                    // dropdownContainer: document.body,
                    // excludeCountries: ["us"],
                    // formatOnDisplay: false,
                    geoIpLookup: function (callback) {
                        $httpClient
                            .make()
                            .withCredentials(false)
                            .get('https://ipinfo.io')
                            .then(function ({ data }) {
                                callback(data && data.country ? data.country : '')
                            })
                    },
                    // hiddenInput: "full_number",
                    initialCountry: 'auto',
                    // localizedCountries: { 'de': 'Deutschland' },
                    // nationalMode: false,
                    // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
                    // placeholderNumberType: "MOBILE",
                    // preferredCountries: ['cn', 'jp'],
                    // separateDialCode: true,
                    utilsScript: '/vendor/core/core/base/libraries/intl-tel-input/js/utils.js',
                })
            })
    }
}

$(() => {
    new PhoneNumberField().init()

    document.addEventListener('payment-form-reloaded', function () {
        new PhoneNumberField().init()
    })
})
