{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td class="bb-content" align="center">
                                    <table class="bb-mb-lg" cellspacing="0" cellpadding="0">
                                        <tbody>
                                            <tr>
                                                <td valign="middle" align="center">
                                                    <img src="{{ site_url }}/vendor/core/plugins/newsletter/images/newsletter.png" alt="Icon" height="160" class="bb-img-illustration" />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <h1 class="bb-text-center bb-m-0">New Subscriber</h1>

                                    <p class="bb-text-center bb-mt-sm bb-mb-0 bb-text-muted">New user has been subscribed your newsletter:
                                        <a href="mailto:{{ newsletter_email }}">{{ newsletter_email}}</a>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
