<div
    data-announcement-id="{{ $announcement->getKey() }}"
    style="display: none;"
    @class([
        'ae-anno-announcement',
        'ae-anno-announcement__bottom' => \ArchiElite\Announcement\AnnouncementHelper::isBottomPlacement(),
    ])
>
    <div class="ae-anno-announcement__content">
        <p class="ae-anno-announcement__text">
            {!! BaseHelper::clean($announcement->formatted_content) !!}
        </p>

        @if ($announcement->has_action)
            <a
                class="ae-anno-announcement__button"
                href="{{ $announcement->action_url }}"
                @if ($announcement->action_open_new_tab) target="_blank" @endif
            >
                {!! BaseHelper::clean($announcement->action_label) !!}
                <span aria-hidden="true">&rarr;</span>
            </a>
        @endif
    </div>
</div>
