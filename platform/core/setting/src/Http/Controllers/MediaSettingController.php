<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFile;
use Botble\Setting\Forms\MediaSettingForm;
use Botble\Setting\Http\Requests\MediaSettingRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Throwable;

class MediaSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('core/setting::setting.media.title'));

        $form =  MediaSettingForm::create();

        return view('core/setting::media', compact('form'));
    }

    public function update(MediaSettingRequest $request): BaseHttpResponse
    {
        $this->saveSettings([
            ...$request->validated(),
            'media_folders_can_add_watermark' => $request->boolean('media_folders_can_add_watermark_all')
                ? []
                : $request->input('media_folders_can_add_watermark', []),
        ]);

        return $this
            ->httpResponse()
            ->withUpdatedSuccessMessage()
            ->setData(['files_count' => MediaFile::query()->count()]);
    }

    public function generateThumbnails(Request $request): BaseHttpResponse
    {
        $request->validate([
            'total' => ['required', 'numeric', 'min:0'],
            'offset' => ['required', 'numeric', 'min:0'],
            'limit' => ['required', 'numeric', 'min:1'],
        ]);

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        $totalFiles = $request->input('total');
        $offset = $request->input('offset', 0);
        $limit = $request->input('limit', RvMedia::getConfig('generate_thumbnails_chunk_limit'));

        /**
         * @var Collection<MediaFile> $files
         */
        $files = MediaFile::query()
            ->select(['url', 'mime_type', 'folder_id'])
            ->skip($offset)
            ->take($limit)
            ->get();

        $errors = [];

        if ($files->isNotEmpty()) {
            foreach ($files as $file) {
                try {
                    RvMedia::generateThumbnails($file);
                } catch (Throwable $exception) {
                    BaseHelper::logError($exception);
                    $errors[] = $file->url;
                }
            }

            $errors = array_map(fn ($item) => [$item], array_unique($errors));
        }

        if ($errors) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('core/setting::setting.generate_thumbnails_error', ['count' => count($errors)]))
                ->setData([
                    'total' => $totalFiles,
                    'next' => $offset + $limit,
                ]);
        }

        return $this
            ->httpResponse()
            ->setMessage(trans('core/setting::setting.generate_thumbnails_success', ['count' => $totalFiles]))
            ->setData([
                'total' => $totalFiles,
                'next' => $offset + $limit,
            ]);
    }
}
