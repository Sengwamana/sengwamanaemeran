{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content bb-pb-0" align="center">
                    <table class="bb-icon bb-icon-lg bb-bg-blue" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td valign="middle" align="center">
                                    <img src="{{ 'mail' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h1 class="bb-text-center bb-m-0 bb-mt-md">Thank You for Contacting Us!</h1>
                </td>
            </tr>
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td class="bb-content">
                                    <p>Dear Admin,</p>

                                    <p>Thank you for reaching out to us! We have received your message and our team will review it shortly. One of our representatives will get back to you as soon as possible.</p>

                                    <h4>Here are the details of your submission:</h4>

                                    <table class="bb-table" cellspacing="0" cellpadding="0">
                                        <thead>
                                            <tr>
                                                <th width="80px"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {% if contact_name %}
                                                <tr>
                                                    <td>Name:</td>
                                                    <td class="bb-font-strong bb-text-left"> {{ contact_name }} </td>
                                                </tr>
                                            {% endif %}
                                            {% if contact_subject %}
                                                <tr>
                                                    <td>Subject:</td>
                                                    <td class="bb-font-strong bb-text-left"> {{ contact_subject }} </td>
                                                </tr>
                                            {% endif %}
                                            {% if contact_email %}
                                                <tr>
                                                    <td>Email:</td>
                                                    <td class="bb-font-strong bb-text-left"> {{ contact_email }} </td>
                                                </tr>
                                            {% endif %}
                                            {% if contact_address %}
                                                <tr>
                                                    <td>Address:</td>
                                                    <td class="bb-font-strong bb-text-left"> {{ contact_address }} </td>
                                                </tr>
                                            {% endif %}
                                            {% if contact_phone %}
                                                <tr>
                                                    <td>Phone:</td>
                                                    <td class="bb-font-strong bb-text-left"> {{ contact_phone }} </td>
                                                </tr>
                                            {% endif %}
                                            {% for key, value in contact_custom_fields %}
                                            <tr>
                                                <td>{{ key }}:</td>
                                                <td class="bb-font-strong bb-text-left"> {{ value }} </td>
                                            </tr>
                                            {% endfor %}
                                            {% if contact_content %}
                                                <tr>
                                                    <td colspan="2">Content:</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="bb-font-strong"><i>{{ contact_content }}</i></td>
                                                </tr>
                                            {% endif %}
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="bb-content bb-text-center bb-pt-0 bb-pb-xl" align="center">
                                    <p>If you have any additional information or questions, feel free to reply to this email.</p>

                                    <p>We appreciate your interest and will be in touch soon!</p>
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
