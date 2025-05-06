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
                    <h1 class="bb-text-center bb-m-0 bb-mt-md">New Contact Message</h1>
                </td>
            </tr>
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td class="bb-content">
                                    <p>Dear Admin,</p>

                                    <h4>Message details</h4>

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
                                    <p>You can reply an email to {{ contact_email }} by clicking on below button.</p> <br />
                                    <a href="mailto:{{ contact_email }}" class="bb-btn bb-bg-blue bb-border-blue">Answer</a>
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
