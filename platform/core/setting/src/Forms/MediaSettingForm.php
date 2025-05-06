<?php

namespace Botble\Setting\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\AlertFieldOption;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\AlertField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFolder;
use Botble\Setting\Http\Requests\MediaSettingRequest;

class MediaSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        Assets::addScriptsDirectly('vendor/core/core/setting/js/media.js');

        $folders = MediaFolder::query()
            ->where('parent_id', 0)
            ->pluck('name', 'id')
            ->all();
        $folderIds = old($key = 'media_folders_can_add_watermark', json_decode((string) setting($key), true));

        $this
            ->setSectionTitle(trans('core/setting::setting.media.title'))
            ->setSectionDescription(trans('core/setting::setting.media.description'))
            ->setUrl(route('settings.media.update'))
            ->when(setting('media_enable_thumbnail_sizes', true), function (): void {
                $this->setActionButtons(
                    view('core/setting::partials.media.action-buttons', ['form' => $this->getFormOption('id')])->render()
                );
            })
            ->setValidatorClass(MediaSettingRequest::class)
            ->contentOnly()
            ->columns(6)
            ->add(
                'media_driver',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/setting::setting.media.driver'))
                    ->choices(apply_filters('core_media_drivers', [
                        'public' => trans('core/setting::setting.media.local_disk'),
                        's3' => 'Amazon S3',
                        'r2' => 'Cloudflare R2',
                        'do_spaces' => 'DigitalOcean Spaces',
                        'wasabi' => 'Wasabi',
                        'bunnycdn' => 'BunnyCDN',
                        'backblaze' => 'Backblaze B2',
                    ]))
                    ->selected($mediaDriver = RvMedia::getMediaDriver())
                    ->colspan(6)
            )
            ->add(
                'open_media_drivers',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('<div class="col-lg-12">')
            )
            ->addOpenCollapsible('media_driver', 's3', $mediaDriver === 's3')
            ->add(
                'media_aws_access_key_id',
                TextField::class,
                TextFieldOption::make()
                ->label(trans('core/setting::setting.media.aws_access_key_id'))
                ->value(setting('media_aws_access_key_id'))
                ->placeholder('Ex: AKIAIKYXBSNBXXXXXX')
            )
            ->add(
                'media_aws_secret_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.aws_secret_key'))
                    ->value(setting('media_aws_secret_key'))
                    ->placeholder('Ex: +fivlGCeTJCVVnzpM2WfzzrFIMLHGhxxxxxxx')
            )
            ->add(
                'media_aws_default_region',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.aws_default_region'))
                    ->value(setting('media_aws_default_region'))
                    ->placeholder('Ex: ap-southeast-1')
            )
            ->add(
                'media_aws_bucket',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.aws_bucket'))
                    ->value(setting('media_aws_bucket'))
                    ->placeholder('Ex: botble')
            )
            ->add(
                'media_aws_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.aws_url'))
                    ->value(setting('media_aws_url'))
                    ->placeholder('Ex: https://s3-ap-southeast-1.amazonaws.com/botble')
            )
            ->add(
                'media_aws_endpoint',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.aws_endpoint'))
                    ->value(setting('media_aws_endpoint'))
                    ->placeholder(trans('core/setting::setting.media.optional'))
            )
            ->add(
                'media_aws_use_path_style_endpoint',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/setting::setting.media.use_path_style_endpoint'))
                    ->choices([
                        0 => trans('core/base::base.no'),
                        1 => trans('core/base::base.yes'),
                    ])
                    ->selected(setting('media_aws_use_path_style_endpoint'))
            )
            ->addCloseCollapsible('media_driver', 's3')
            ->addOpenCollapsible('media_driver', 'r2', $mediaDriver === 'r2')
            ->add(
                'media_r2_access_key_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.r2_access_key_id'))
                    ->value(setting('media_r2_access_key_id'))
                    ->placeholder('Ex: AKIAIKYXBSNBXXXXXX')
            )
            ->add(
                'media_r2_secret_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.r2_secret_key'))
                    ->value(setting('media_r2_secret_key'))
                    ->placeholder('Ex: +fivlGCeTJCVVnzpM2WfzzrFIMLHGhxxxxxxx')
            )
            ->add(
                'media_r2_bucket',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.r2_bucket'))
                    ->value(setting('media_r2_bucket'))
                    ->placeholder('Ex: botble')
            )
            ->add(
                'media_r2_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.r2_url'))
                    ->value(setting('media_r2_url'))
                    ->placeholder('Ex: https://pub-f70218cc331a40689xxx.r2.dev')
            )
            ->add(
                'media_r2_endpoint',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.r2_endpoint'))
                    ->value(setting('media_r2_endpoint'))
                    ->placeholder('Ex: https://xxx.r2.cloudflarestorage.com')
            )
            ->add(
                'media_r2_use_path_style_endpoint',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/setting::setting.media.use_path_style_endpoint'))
                    ->choices([
                        0 => trans('core/base::base.no'),
                        1 => trans('core/base::base.yes'),
                    ])
                    ->selected(setting('media_r2_use_path_style_endpoint'))
            )
            ->addCloseCollapsible('media_driver', 'r2')
            ->addOpenCollapsible('media_driver', 'do_spaces', $mediaDriver === 'do_spaces')
            ->add(
                'media_do_spaces_access_key_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.do_spaces_access_key_id'))
                    ->value(setting('media_do_spaces_access_key_id'))
                    ->placeholder('Ex: AKIAIKYXBSNBXXXXXX')
            )
            ->add(
                'media_do_spaces_secret_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.do_spaces_secret_key'))
                    ->value(setting('media_do_spaces_secret_key'))
                    ->placeholder('Ex: +fivlGCeTJCVVnzpM2WfzzrFIMLHGhxxxxxxx')
            )
            ->add(
                'media_do_spaces_default_region',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.do_spaces_default_region'))
                    ->value(setting('media_do_spaces_default_region'))
                    ->placeholder('Ex: SGP1')
            )
            ->add(
                'media_do_spaces_bucket',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.do_spaces_bucket'))
                    ->value(setting('media_do_spaces_bucket'))
                    ->placeholder('Ex: botble')
            )
            ->add(
                'media_do_spaces_endpoint',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.do_spaces_endpoint'))
                    ->value(setting('media_do_spaces_endpoint'))
                    ->placeholder('Ex: https://botble.sfo2.digitaloceanspaces.com')
            )
            ->add(
                'media_do_spaces_cdn_enabled',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('core/setting::setting.media.do_spaces_cdn_enabled'))
                    ->checked((bool) setting('media_do_spaces_cdn_enabled'))
            )
            ->add(
                'media_do_spaces_cdn_custom_domain',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.media_do_spaces_cdn_custom_domain'))
                    ->value(setting('media_do_spaces_cdn_custom_domain'))
                    ->placeholder(trans('core/setting::setting.media.media_do_spaces_cdn_custom_domain_placeholder'))
            )
            ->add(
                'media_do_spaces_use_path_style_endpoint',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/setting::setting.media.use_path_style_endpoint'))
                    ->choices([
                        0 => trans('core/base::base.no'),
                        1 => trans('core/base::base.yes'),
                    ])
                    ->selected(setting('media_do_spaces_use_path_style_endpoint'))
            )
            ->addCloseCollapsible('media_driver', 'do_spaces')
            ->addOpenCollapsible('media_driver', 'wasabi', $mediaDriver === 'wasabi')
            ->add(
                'media_wasabi_access_key_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.wasabi_access_key_id'))
                    ->value(setting('media_wasabi_access_key_id'))
                    ->placeholder('Ex: AKIAIKYXBSNBXXXXXX')
            )
            ->add(
                'media_wasabi_secret_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.wasabi_secret_key'))
                    ->value(setting('media_wasabi_secret_key'))
                    ->placeholder('Ex: +fivlGCeTJCVVnzpM2WfzzrFIMLHGhxxxxxxx')
            )
            ->add(
                'media_wasabi_default_region',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.wasabi_default_region'))
                    ->value(setting('media_wasabi_default_region'))
                    ->placeholder('Ex: us-east-1')
            )
            ->add(
                'media_wasabi_bucket',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.wasabi_bucket'))
                    ->value(setting('media_wasabi_bucket'))
                    ->placeholder('Ex: botble')
            )
            ->add(
                'media_wasabi_root',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.wasabi_root'))
                    ->value(setting('media_wasabi_root'))
                    ->placeholder('Default: /')
                    ->helperText(trans('core/setting::setting.media.wasabi_root_helper'))
            )
            ->addCloseCollapsible('media_driver', 'wasabi')
            ->addOpenCollapsible('media_driver', 'bunnycdn', $mediaDriver === 'bunnycdn')
            ->add(
                'media_bunnycdn_zone',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.bunnycdn_zone'))
                    ->value(setting('media_bunnycdn_zone'))
                    ->placeholder('Ex: botble')
            )
            ->add(
                'media_bunnycdn_hostname',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.bunnycdn_hostname'))
                    ->value(setting('media_bunnycdn_hostname'))
                    ->placeholder('Ex: botble.b-cdn.net')
            )
            ->add(
                'media_bunnycdn_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.bunnycdn_key'))
                    ->value(setting('media_bunnycdn_key'))
                    ->placeholder('Ex: 9a734df7-844b-...')
            )
            ->add(
                'media_bunnycdn_region',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/setting::setting.media.bunnycdn_region'))
                    ->choices([
                        'de' => 'Falkenstein',
                        'ny' => 'New York',
                        'la' => 'Los Angeles',
                        'sg' => 'Singapore',
                        'syd' => 'Sydney',
                        'uk' => 'United Kingdom',
                    ])
                    ->selected(setting('media_bunnycdn_region'))
            )
            ->addCloseCollapsible('media_driver', 'bunnycdn')
            ->addOpenCollapsible('media_driver', 'backblaze', $mediaDriver === 'backblaze')
            ->add(
                'media_backblaze_access_key_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.backblaze_access_key_id'))
                    ->value(setting('media_backblaze_access_key_id'))
                    ->placeholder('Ex: 005febe473bdd490000xxxxxxx')
            )
            ->add(
                'media_backblaze_secret_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.backblaze_secret_key'))
                    ->value(setting('media_backblaze_secret_key'))
                    ->placeholder('Ex: K005C3JkwgUkUSh+4bZLoTkiBxxxxxx')
            )
            ->add(
                'media_backblaze_default_region',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.backblaze_default_region'))
                    ->value(setting('media_backblaze_default_region'))
                    ->placeholder('Ex: eu-central-003')
            )
            ->add(
                'media_backblaze_bucket',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.backblaze_bucket'))
                    ->value(setting('media_backblaze_bucket'))
                    ->placeholder('Ex: botble')
            )
            ->add(
                'media_backblaze_endpoint',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.backblaze_endpoint'))
                    ->value(setting('media_backblaze_endpoint'))
                    ->placeholder('Ex: https://s3.eu-central-003.backblazeb2.com')
            )
            ->add(
                'media_backblaze_use_path_style_endpoint',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/setting::setting.media.use_path_style_endpoint'))
                    ->choices([
                        0 => trans('core/base::base.no'),
                        1 => trans('core/base::base.yes'),
                    ])
                    ->selected(setting('media_backblaze_use_path_style_endpoint'))
            )
            ->addCloseCollapsible('media_driver', 'backblaze')
            ->add(
                'close_media_drivers',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('</div>')
            )
            ->add(
                'media_use_original_name_for_file_path',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.media.use_original_name_for_file_path'))
                    ->value(setting('media_use_original_name_for_file_path'))
                    ->colspan(6)
            )
            ->add(
                'media_convert_file_name_to_uuid',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.media.convert_file_name_to_uuid'))
                    ->helperText(trans('core/setting::setting.media.convert_file_name_to_uuid_helper'))
                    ->value(setting('media_convert_file_name_to_uuid'))
                    ->colspan(6)
            )
            ->add(
                'media_keep_original_file_size_and_quality',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.media.keep_original_file_size_and_quality'))
                    ->value(setting('media_keep_original_file_size_and_quality'))
                    ->colspan(6)
            )
            ->add(
                'media_turn_off_automatic_url_translation_into_latin',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.media.turn_off_automatic_url_translation_into_latin'))
                    ->value(RvMedia::turnOffAutomaticUrlTranslationIntoLatin())
                    ->colspan(6)
            )
            ->add(
                'user_can_only_view_own_media',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.media.user_can_only_view_own_media'))
                    ->value(setting('user_can_only_view_own_media', false))
                    ->helperText(trans('core/setting::setting.media.user_can_only_view_own_media_helper'))
                    ->colspan(6)
            )
            ->add(
                'media_convert_image_to_webp',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.media.convert_image_to_webp'))
                    ->value(setting('media_convert_image_to_webp', false))
                    ->helperText(trans('core/setting::setting.media.convert_image_to_webp_helper'))
                    ->colspan(6)
            )
            ->add('media_default_placeholder_image', MediaImageField::class, [
                'label' => trans('core/setting::setting.media.default_placeholder_image'),
                'value' => setting('media_default_placeholder_image'),
                'colspan' => 6,
            ])
            ->add(
                'max_upload_filesize',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('core/setting::setting.media.max_upload_filesize'))
                    ->value(setting('max_upload_filesize'))
                    ->placeholder(trans('core/setting::setting.media.max_upload_filesize_placeholder', [
                        'size' => ($maxSize = BaseHelper::humanFilesize(RvMedia::getServerConfigMaxUploadFileSize())),
                    ]))
                    ->step(0.01)
                    ->helperText(trans('core/setting::setting.media.max_upload_filesize_helper', ['size' => $maxSize]))
                    ->colspan(6)
            )
            ->add(
                'media_reduce_large_image_size',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.media.reduce_large_image_size'))
                    ->value($reduceLargeImageSize = setting('media_reduce_large_image_size', false))
                    ->helperText(trans('core/setting::setting.media.reduce_large_image_size_helper'))
                    ->colspan(6)
            )
            ->add(
                'media_customize_upload_path',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('core/setting::setting.media.customize_upload_path'))
                    ->value($customizeUploadPath = setting('media_customize_upload_path', false))
                    ->helperText(trans('core/setting::setting.media.customize_upload_path_helper', [
                        'path' => str_replace(base_path(), '', is_link(public_path('storage')) ? storage_path('app/public') : public_path('storage')),
                    ]))
                    ->colspan(6)
            )
            ->add(
                'open_media_customize_upload_path',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('<div class="col-lg-12">')
            )
            ->addOpenCollapsible('media_customize_upload_path', '1', $customizeUploadPath === '1')
            ->add(
                'open_row_media_customize_upload_path',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('<div class="row">')
            )
            ->add(
                'media_upload_path',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/setting::setting.media.upload_path'))
                    ->value(setting('media_upload_path', 'storage'))
                    ->placeholder(trans('core/setting::setting.media.upload_path_placeholder'))
                    ->helperText(trans('core/setting::setting.media.upload_path_helper', ['folder' => 'storage']))
                    ->colspan(6)
            )
            ->add(
                'media_upload_path_warning',
                AlertField::class,
                AlertFieldOption::make()
                    ->type('warning')
                    ->content(trans('core/setting::setting.media.upload_path_warning'))
                    ->colspan(6)
            )
            ->add(
                'close_row_media_customize_upload_path',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('</div>')
            )
            ->addCloseCollapsible('media_customize_upload_path', '1')
            ->add(
                'close_media_customize_upload_path',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('</div>')
            )
            ->add(
                'open_media_reduce_large_image_size',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('<div class="col-lg-12">')
            )
            ->addOpenCollapsible('media_reduce_large_image_size', '1', $reduceLargeImageSize === '1')
            ->add(
                'open_row_media_reduce_large_image_size',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('<div class="row">')
            )
            ->add(
                'media_image_max_width',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('core/setting::setting.media.image_max_width'))
                    ->value(setting('media_image_max_width'))
                    ->placeholder(trans('core/setting::setting.media.image_max_width_placeholder'))
                    ->helperText(trans('core/setting::setting.media.image_max_width_helper'))
                    ->colspan(3)
            )
            ->add(
                'media_image_max_height',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('core/setting::setting.media.image_max_height'))
                    ->value(setting('media_image_max_height'))
                    ->placeholder(trans('core/setting::setting.media.image_max_height_placeholder'))
                    ->helperText(trans('core/setting::setting.media.image_max_height_helper'))
                    ->colspan(3)
            )
            ->add(
                'close_row_media_reduce_large_image_size',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('</div>')
            )
            ->addCloseCollapsible('media_reduce_large_image_size', '1')
            ->add(
                'close_media_reduce_large_image_size',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content('</div>')
            )
            ->add(
                'chunk_size_upload_file',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('core/setting::partials.media.chunk-size-upload-field')->render())
                    ->colspan(6)
            )
            ->add(
                $mediaWatermarkEnabled = 'media_watermark_enabled',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('core/setting::setting.media.enable_watermark'))
                    ->value($mediaWatermarkEnabledValue = setting('media_watermark_enabled'))
                    ->colspan(6)
            )
            ->addOpenCollapsible($mediaWatermarkEnabled, '1', $mediaWatermarkEnabledValue, ['class' => 'form-fieldset col-lg-12'])
            ->add(
                'media_folders_can_add_watermark_field',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->view('core/setting::partials.media.media-folders-can-add-watermark-field', compact('folders', 'folderIds'))
            )
            ->add('row_1', HtmlField::class, HtmlFieldOption::make()->content('<div class="row">'))
            ->add(
                'media_watermark_source',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(trans('core/setting::setting.media.watermark_source'))
                    ->value(setting('media_watermark_source'))
                    ->colspan(6)
            )
            ->add('media_watermark_size', NumberField::class, [
                'label' => trans('core/setting::setting.media.watermark_size'),
                'value' => setting('media_watermark_size', RvMedia::getConfig('watermark.size')),
                'attr' => [
                    'placeholder' => trans('core/setting::setting.media.watermark_size_placeholder'),
                ],
                'colspan' => 3,
            ])
            ->add('media_watermark_opacity', NumberField::class, [
                'label' => trans('core/setting::setting.media.watermark_opacity'),
                'value' => setting(
                    'media_watermark_opacity',
                    setting('watermark_opacity') ?: RvMedia::getConfig('watermark.opacity')
                ),
                'attr' => [
                    'placeholder' => trans('core/setting::setting.media.watermark_opacity_placeholder'),
                ],
                'colspan' => 3,
            ])
            ->add('media_watermark_position', SelectField::class, [
                'label' => trans('core/setting::setting.media.watermark_position'),
                'selected' => setting(
                    'media_watermark_position',
                    RvMedia::getConfig('watermark.position')
                ),
                'choices' => [
                    'top-left' => trans('core/setting::setting.media.watermark_position_top_left'),
                    'top-right' => trans('core/setting::setting.media.watermark_position_top_right'),
                    'bottom-left' => trans(
                        'core/setting::setting.media.watermark_position_bottom_left'
                    ),
                    'bottom-right' => trans(
                        'core/setting::setting.media.watermark_position_bottom_right'
                    ),
                    'center' => trans('core/setting::setting.media.watermark_position_center'),
                ],
                'colspan' => 2,
            ])
            ->add('media_watermark_position_x', NumberField::class, [
                'label' => trans('core/setting::setting.media.watermark_position_x'),
                'value' => setting(
                    'media_watermark_position_x',
                    setting('watermark_position_x') ?: RvMedia::getConfig('watermark.x')
                ),
                'attr' => [
                    'placeholder' => trans('core/setting::setting.media.watermark_position_x'),
                ],
                'colspan' => 2,
            ])
            ->add('media_watermark_position_y', NumberField::class, [
                'label' => trans('core/setting::setting.media.watermark_position_y'),
                'value' => setting(
                    'media_watermark_position_y',
                    setting('watermark_position_y') ?: RvMedia::getConfig('watermark.y')
                ),
                'attr' => [
                    'placeholder' => trans('core/setting::setting.media.watermark_position_y'),
                ],
                'colspan' => 2,
            ])
            ->add(
                'media_watermark_warning',
                AlertField::class,
                AlertFieldOption::make()
                    ->type('warning')
                    ->content(trans('core/setting::setting.watermark_description'))
                    ->colspan(6)
            )
            ->add('row_1_close', HtmlField::class, HtmlFieldOption::make()->content('</div>'))
            ->addCloseCollapsible($mediaWatermarkEnabled, '1')
            ->add('media_image_processing_library', 'customRadio', [
                'label' => trans('core/setting::setting.media.image_processing_library'),
                'selected' => RvMedia::getImageProcessingLibrary(),
                'choices' => array_merge(
                    ['gd' => 'GD Library'],
                    extension_loaded('imagick')
                        ? ['imagick' => 'Imagick']
                        : [],
                ),
                'colspan' => 6,
            ])
            ->add(
                'media_enable_thumbnail_sizes',
                OnOffCheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('core/setting::setting.media.enable_thumbnail_sizes'))
                    ->helperText(trans('core/setting::setting.media.enable_thumbnail_sizes_helper'))
                    ->value($enabledThumbnailSizes = setting('media_enable_thumbnail_sizes', true))
                    ->colspan(6)
            )
            ->addOpenCollapsible('media_enable_thumbnail_sizes', '1', $enabledThumbnailSizes, ['class' => 'form-fieldset col-lg-12'])
            ->add('open_row_2', HtmlField::class, HtmlFieldOption::make()->content('<div class="row row-cols-lg-6">'))
            ->add(
                'title_media_size',
                HtmlField::class,
                HtmlFieldOption::make()
                ->content('<h4>' . trans('core/setting::setting.media.sizes') . ':</h4>')
                ->colspan(6)
            );

        foreach (RvMedia::getSizes() as $name => $size) {
            $sizeExploded = explode('x', $size);

            $this->add(
                sprintf('media_size_%s_label', $name),
                HtmlField::class,
                HtmlFieldOption::make()
                    ->view('core/setting::includes.form-media-size-label', compact('name'))
                    ->colspan(6)
            );

            if (count($sizeExploded) === 2) {
                $this
                    ->add($nameWidth = sprintf('media_sizes_%s_width', $name), NumberField::class, [
                        'label' => false,
                        'value' => setting($nameWidth, $sizeExploded[0]),
                        'attr' => [
                            'placeholder' => 0,
                        ],
                        'colspan' => 3,
                    ])
                    ->add($nameHeight = sprintf('media_sizes_%s_height', $name), NumberField::class, [
                        'label' => false,
                        'value' => setting($nameHeight, $sizeExploded[1]),
                        'attr' => [
                            'placeholder' => 0,
                        ],
                        'colspan' => 3,
                    ]);
            }
        }

        $this
            ->add(
                'media_thumbnail_crop_position',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/setting::setting.media.thumbnail_crop_position'))
                    ->helperText(trans('core/setting::setting.media.thumbnail_crop_position_helper'))
                    ->selected(setting('media_thumbnail_crop_position', 'center'))
                    ->choices([
                        'left' => trans('core/setting::setting.media.thumbnail_crop_position_left'),
                        'right' => trans('core/setting::setting.media.thumbnail_crop_position_right'),
                        'top' => trans('core/setting::setting.media.thumbnail_crop_position_top'),
                        'bottom' => trans('core/setting::setting.media.thumbnail_crop_position_bottom'),
                        'center' => trans('core/setting::setting.media.thumbnail_crop_position_center'),
                    ])
                    ->colspan(6)
            )
            ->add(
                'media_sizes_helper',
                AlertField::class,
                AlertFieldOption::make()
                    ->content(trans('core/setting::setting.media.media_sizes_helper'))
                    ->addAttribute('class', 'mb-0')
                    ->colspan(6)
            )
            ->add(
                'update_thumbnail_sizes_warning',
                AlertField::class,
                AlertFieldOption::make()
                    ->type('warning')
                    ->content(trans('core/setting::setting.media.update_thumbnail_sizes_warning', ['button_text' => trans('core/setting::setting.generate_thumbnails')]))
                    ->colspan(6)
            )
            ->add('close_row_2', HtmlField::class, HtmlFieldOption::make()->content('</div>'))
            ->addCloseCollapsible('media_enable_thumbnail_sizes', '1');
    }
}
