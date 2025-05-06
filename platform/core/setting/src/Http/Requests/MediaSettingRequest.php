<?php

namespace Botble\Setting\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\Media\Facades\RvMedia;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class MediaSettingRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'media_driver' => ['required', 'string', 'in:public,s3,r2,do_spaces,wasabi,bunnycdn,backblaze'],
            'media_aws_access_key_id' => ['nullable', 'string', 'required_if:media_driver,s3'],
            'media_aws_secret_key' => ['nullable', 'string', 'required_if:media_driver,s3'],
            'media_aws_default_region' => ['nullable', 'string', 'required_if:media_driver,s3'],
            'media_aws_bucket' => ['nullable', 'string', 'required_if:media_driver,s3'],
            'media_aws_url' => ['nullable', 'string', 'required_if:media_driver,s3'],
            'media_aws_endpoint' => ['nullable', 'string'],
            'media_aws_use_path_style_endpoint' => $onOffRule = new OnOffRule(),

            'media_r2_access_key_id' => ['nullable', 'string', 'required_if:media_driver,r2'],
            'media_r2_secret_key' => ['nullable', 'string', 'required_if:media_driver,r2'],
            'media_r2_bucket' => ['nullable', 'string', 'required_if:media_driver,r2'],
            'media_r2_endpoint' => ['nullable', 'string', 'required_if:media_driver,r2'],
            'media_r2_url' => ['nullable', 'string', 'required_if:media_driver,r2'],
            'media_r2_use_path_style_endpoint' => $onOffRule,

            'media_wasabi_access_key_id' => ['nullable', 'string', 'required_if:media_driver,wasabi'],
            'media_wasabi_secret_key' => ['nullable', 'string', 'required_if:media_driver,wasabi'],
            'media_wasabi_default_region' => ['nullable', 'string', 'required_if:media_driver,wasabi'],
            'media_wasabi_bucket' => ['nullable', 'string', 'required_if:media_driver,wasabi'],
            'media_wasabi_root' => ['nullable', 'string'],

            'media_do_spaces_access_key_id' => ['nullable', 'string', 'required_if:media_driver,do_spaces'],
            'media_do_spaces_secret_key' => ['nullable', 'string', 'required_if:media_driver,do_spaces'],
            'media_do_spaces_default_region' => ['nullable', 'string', 'size:4', 'required_if:media_driver,do_spaces,in:NYC1,NYC2,NYC3,SFO1,SFO2,SFO3,TOR1,LON1,AMS2,AMS3,FRA1,SGP1,BLR1,SYD1'],
            'media_do_spaces_bucket' => ['nullable', 'string', 'required_if:media_driver,do_spaces'],
            'media_do_spaces_endpoint' => ['nullable', 'string', 'required_if:media_driver,do_spaces'],
            'media_do_spaces_cdn_enabled' => $onOffRule,
            'media_do_spaces_cdn_custom_domain' => ['nullable', 'url', 'required_if:media_driver,do_spaces'],
            'media_do_spaces_use_path_style_endpoint' => $onOffRule,

            'media_bunnycdn_hostname' => ['nullable', 'string', 'required_if:media_driver,bunnycdn'],
            'media_bunnycdn_zone' => ['nullable', 'string', 'required_if:media_driver,bunnycdn'],
            'media_bunnycdn_key' => ['nullable', 'string', 'required_if:media_driver,bunnycdn'],
            'media_bunnycdn_region' => ['nullable', 'string', 'max:200'],

            'media_backblaze_access_key_id' => ['nullable', 'string', 'required_if:media_driver,backblaze'],
            'media_backblaze_secret_key' => ['nullable', 'string', 'required_if:media_driver,backblaze'],
            'media_backblaze_bucket' => ['nullable', 'string', 'required_if:media_driver,backblaze'],
            'media_backblaze_default_region' => ['nullable', 'string', 'required_if:media_driver,backblaze'],
            'media_backblaze_endpoint' => ['nullable', 'string', 'required_if:media_driver,backblaze'],
            'media_backblaze_url' => ['nullable', 'url'],
            'media_backblaze_use_path_style_endpoint' => $onOffRule,

            'media_turn_off_automatic_url_translation_into_latin' => $onOffRule,
            'media_use_original_name_for_file_path' => $onOffRule,
            'media_keep_original_file_size_and_quality' => $onOffRule,
            'media_default_placeholder_image' => ['nullable', 'string'],
            'max_upload_filesize' => ['nullable', 'numeric', 'min:0'],

            'media_chunk_enabled' => $onOffRule,
            'media_chunk_size' => ['required', 'numeric', 'min:0'],
            'media_max_file_size' => ['required', 'numeric', 'min:0'],

            'media_folders_can_add_watermark' => ['nullable', 'array'],
            'media_folders_can_add_watermark.*' => ['nullable', 'string'],

            'media_watermark_enabled' => $onOffRule,
            'media_image_processing_library' => ['nullable', 'in:gd,imagick'],
            'media_watermark_source' => ['nullable', 'string', 'required_if:media_watermark_enabled,1'],
            'media_watermark_size' => ['nullable', 'numeric', 'min:0', 'required_if:media_watermark_enabled,1'],
            'media_watermark_opacity' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_if:media_watermark_enabled,1'],
            'media_watermark_position' => [
                'nullable',
                Rule::in(['top-left', 'top-right', 'bottom-left', 'bottom-right', 'center']),
                'required_if:media_watermark_enabled,1',
            ],
            'media_watermark_position_x' => ['nullable', 'numeric', 'min:0', 'required_if:media_watermark_enabled,1'],
            'media_watermark_position_y' => ['nullable', 'numeric', 'min:0', 'required_if:media_watermark_enabled,1'],
            'media_thumbnail_crop_position' => [
                'nullable',
                Rule::in(['left', 'right', 'bottom', 'top', 'center']),
            ],
            'user_can_only_view_own_media' => [$onOffRule],
            'media_convert_image_to_webp' => [$onOffRule],
            'media_enable_thumbnail_sizes' => [$onOffRule],
            'media_reduce_large_image_size' => [$onOffRule],
            'media_image_max_width' => ['nullable', 'numeric', 'min:200'],
            'media_image_max_height' => ['nullable', 'numeric', 'min:200'],
            'media_customize_upload_path' => [$onOffRule],
            'media_upload_path' => ['required', 'string', 'max:255'],
            'media_convert_file_name_to_uuid' => [$onOffRule],
        ];

        foreach (array_keys(RvMedia::getSizes()) as $size) {
            $rules['media_sizes_' . $size . '_width'] = ['required', 'numeric', 'min:0'];
            $rules['media_sizes_' . $size . '_height'] = ['required', 'numeric', 'min:0'];
        }

        return apply_filters('cms_media_settings_validation_rules', $rules);
    }

    public function attributes(): array
    {
        $attributes = [];

        foreach (array_keys(RvMedia::getSizes()) as $size) {
            $attributes['media_sizes_' . $size . '_width'] = trans('core/setting::setting.media_size_width', ['size' => ucfirst($size)]);
            $attributes['media_sizes_' . $size . '_height'] = trans('core/setting::setting.media_size_height', ['size' => ucfirst($size)]);
        }

        return $attributes;
    }
}
