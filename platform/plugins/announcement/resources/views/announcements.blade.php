@if($announcements->isNotEmpty())
    <div
        @class(['ae-anno-announcement-wrapper', 'hide-on-mobile' => \ArchiElite\Announcement\AnnouncementHelper::isHideOnMobile()])
        style="
            --background-color: {{ setting('announcement_background_color', theme_option('primary_color', '#000')) }};
            --text-color: {{ setting('announcement_text_color', '#fff') }};
            --font-size: {{ \ArchiElite\Announcement\AnnouncementHelper::getFontSize() }};
        "
        @if ($autoPlay = setting('announcement_autoplay', false))
            data-announcement-autoplay="{{ $autoPlay }}"
            data-announcement-autoplay-delay="{{ setting('announcement_autoplay_delay', 5000) }}"
        @endif
    >
        <div
            class="ae-anno-announcement__items"
            style="
            justify-content: {{ \ArchiElite\Announcement\AnnouncementHelper::getTextAlignment() }};
            @if (setting('announcement_text_alignment') === \ArchiElite\Announcement\Enums\TextAlignment::CENTER) text-align: center; @endif
           max-width: {{ \ArchiElite\Announcement\AnnouncementHelper::getMaxWidth() }};
        "
        >
            @if ($announcements->count() > 1)
                @include('plugins/announcement::partials.controls')
            @endif

            @each('plugins/announcement::partials.item', $announcements, 'announcement')
        </div>

        @if (setting('announcement_dismissible', false))
            @include('plugins/announcement::partials.dismiss', [
                'announcementIds' => $announcements->pluck('id')->toJson(),
            ])
        @endif
    </div>
@endif
