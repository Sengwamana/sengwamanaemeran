{{ header }}

<table width="100%">
    <tbody>
    <tr>
        <td class="wrapper" width="700" align="center">
            <table class="section" cellpadding="0" cellspacing="0" width="700" bgcolor="#f8f8f8">
                <tr>
                    <td class="column" align="left">
                        <table>
                            <tbody>
                            <tr>
                                <td align="left" style="padding: 20px 50px;">
                                    <p>
                                        Hello, you have a new quotation request from <strong>{{ contact_name }} ({{ contact_email }})</strong> at <strong>{{ site_name }}</strong>.
                                    </p>

                                    <p>
                                        <img src="{{ site_url }}/vendor/core/core/base/images/emails/message.png" alt="Message" width="20" style="margin-right: 10px;" />
                                        {{ contact_message }}
                                    </p>

                                    {% if fields %}
                                    <p>
                                        Below are the details of our requirements:
                                    </p>

                                    <ul>
                                        {% for key, value in fields %}
                                        <li><strong>{{ key }}</strong>: {{ value }}</li>
                                        {% endfor %}
                                    </ul>
                                    {% endif %}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td class="wrapper" width="700" align="center">
            <table class="section main" cellpadding="0" cellspacing="0" width="700">
                <tr>
                    <td class="column" align="center">
                        <table>
                            <tbody>
                            <tr>
                                <td align="center">
                                    <p>You can reply an email to {{ contact_email }} by clicking on below button.</p> <br />
                                    <a href="mailto:{{ contact_email }}" class="action-button">Answer</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>

{{ footer }}
