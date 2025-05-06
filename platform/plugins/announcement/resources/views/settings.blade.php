@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="max-width-1200">
        <form
            action="{{ route('announcements.settings') }}"
            method="post"
        >
            @csrf

            <x-core-setting::section
                :title="trans('plugins/announcement::announcements.settings.name')"
                :description="trans('plugins/announcement::announcements.settings.description')"
            >
                <x-core-setting::select
                    name="announcement_placement"
                    :label="trans('plugins/announcement::announcements.placement')"
                    :options="\ArchiElite\Announcement\Enums\AnnouncePlacement::labels()"
                    :value="setting('announcement_placement', \ArchiElite\Announcement\Enums\AnnouncePlacement::TOP)"
                />

                <x-core-setting::select
                    name="announcement_text_alignment"
                    :label="trans('plugins/announcement::announcements.text_alignment')"
                    :options="\ArchiElite\Announcement\Enums\TextAlignment::labels()"
                    :value="setting(
                        'announcement_text_alignment',
                        \ArchiElite\Announcement\Enums\TextAlignment::CENTER,
                    )"
                />

                <x-core-setting::form-group>
                    <label
                        class="form-label"
                        for="announcement_max_width"
                    >{{ trans('plugins/announcement::announcements.max_width') }}</label>
                    <div class="input-group flex-nowrap">
                        <input
                            class="form-control"
                            name="announcement_max_width"
                            type="number"
                            value="{{ setting('announcement_max_width', '1200') }}"
                        >
                        {{ Form::customSelect('announcement_max_width_unit', \ArchiElite\Announcement\Enums\WidthUnit::labels(), setting('announcement_max_width_unit', 'px'), ['style' => 'width: 4rem;']) }}
                    </div>
                    {{ Form::helper(trans('plugins/announcement::announcements.max_width_help')) }}
                </x-core-setting::form-group>

                <x-core-setting::form-group>
                    <label
                        class="form-label"
                        for="announcement_background_color"
                    >{{ trans('plugins/announcement::announcements.background_color') }}</label>
                    {{ Form::customColor('announcement_background_color', setting('announcement_background_color', theme_option('primary_color', '#000'))) }}
                </x-core-setting::form-group>

                <x-core-setting::form-group>
                    <label
                        class="form-label"
                        for="announcement_text_color"
                    >{{ trans('plugins/announcement::announcements.text_color') }}</label>
                    {{ Form::customColor('announcement_text_color', setting('announcement_text_color', '#fff')) }}
                </x-core-setting::form-group>

                <x-core-setting::form-group>
                    <label
                        class="form-label"
                        for="announcement_max_width"
                    >{{ trans('plugins/announcement::announcements.font_size') }}</label>
                    <div class="input-group flex-nowrap">
                        <input
                            class="form-control"
                            name="announcement_font_size"
                            type="number"
                            value="{{ setting('announcement_font_size', '0.9') }}"
                        >
                        {{ Form::customSelect('announcement_font_size_unit', \ArchiElite\Announcement\Enums\FontSizeUnit::labels(), setting('announcement_font_size_unit', 'rem'), ['style' => 'width: 4rem;']) }}
                    </div>
                    {{ Form::helper(trans('plugins/announcement::announcements.font_size_help')) }}
                </x-core-setting::form-group>

                <x-core-setting::checkbox
                    name="announcement_dismissible"
                    :label="trans('plugins/announcement::announcements.dismissible_label')"
                    :checked="setting('announcement_dismissible', false)"
                />
            </x-core-setting::section>

            <div
                class="flexbox-annotated-section"
                style="border: none"
            >
                <div class="flexbox-annotated-section-annotation">&nbsp;</div>
                <div class="flexbox-annotated-section-content">
                    <button
                        class="btn btn-info"
                        type="submit"
                    >{{ trans('core/setting::setting.save_settings') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
