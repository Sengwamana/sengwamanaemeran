<table cellspacing="0" cellpadding="0">
    <tbody>
        <tr>
            <td class="bb-py-xl">
                <table class="bb-text-center bb-text-muted" cellspacing="0" cellpadding="0">
                    <tbody>
                    {% if social_links %}
                        <tr>
                            <td align="center" class="bb-pb-md">
                                <table class="bb-w-auto" cellspacing="0" cellpadding="0">
                                    <tbody>
                                        <tr>
                                            {% for social_link in site_social_links %}
                                                <td class="bb-px-sm">
                                                    <a title="{{ social_link.name }}" href="{{ social_link.url }}">
                                                        <img src="{{ social_link.image }}" class="bb-va-middle" width="24" height="24" alt="{{ social_link.name }}" />
                                                    </a>
                                                </td>
                                            {% endfor %}
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    {% endif %}

                    <tr>
                        <td class="bb-px-lg">
                            {{ site_copyright }}
                        </td>
                    </tr>

                    {% if site_email %}
                        <tr>
                            <td class="bb-pt-md">
                                If you have any questions, feel free to message us at <a href="mailto:{{ site_email }}">{{ site_email }}</a>.
                            </td>
                        </tr>
                    {% endif %}
                    </tbody>
                </table>
            </td>
        </tr>
    </tbody>
</table>
</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>
</center>
</body>

</html>

