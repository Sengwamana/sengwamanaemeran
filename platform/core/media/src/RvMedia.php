<?php

namespace Botble\Media;

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Media\Events\MediaFileRenamed;
use Botble\Media\Events\MediaFileRenaming;
use Botble\Media\Events\MediaFileUploaded;
use Botble\Media\Events\MediaFolderRenamed;
use Botble\Media\Events\MediaFolderRenaming;
use Botble\Media\Http\Resources\FileResource;
use Botble\Media\Models\MediaFile;
use Botble\Media\Models\MediaFolder;
use Botble\Media\Services\ThumbnailService;
use Botble\Media\Services\UploadsManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File as ValidationFile;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

class RvMedia
{
    protected array $permissions = [];

    public function __construct(protected UploadsManager $uploadManager, protected ThumbnailService $thumbnailService)
    {
        $this->permissions = $this->getConfig('permissions', []);
    }

    public function renderHeader(): string
    {
        $urls = $this->getUrls();

        return view('core/media::header', compact('urls'))->render();
    }

    public function getUrls(): array
    {
        return [
            'base_url' => url(''),
            'base' => route('media.index'),
            'get_media' => route('media.list'),
            'create_folder' => route('media.folders.create'),
            'popup' => route('media.popup'),
            'download' => route('media.download'),
            'upload_file' => route('media.files.upload'),
            'get_breadcrumbs' => route('media.breadcrumbs'),
            'global_actions' => route('media.global_actions'),
            'media_upload_from_editor' => route('media.files.upload.from.editor'),
            'download_url' => route('media.download_url'),
        ];
    }

    public function renderFooter(): string
    {
        return view('core/media::footer')->render();
    }

    public function renderContent(): string
    {
        $sorts = [
            'name-asc' => [
                'label' => trans('core/media::media.file_name_asc'),
                'icon' => 'ti ti-sort-ascending-letters',
            ],
            'name-desc' => [
                'label' => trans('core/media::media.file_name_desc'),
                'icon' => 'ti ti-sort-descending-letters',
            ],
            'created_at-asc' => [
                'label' => trans('core/media::media.uploaded_date_asc'),
                'icon' => 'ti ti-sort-ascending-numbers',
            ],
            'created_at-desc' => [
                'label' => trans('core/media::media.uploaded_date_desc'),
                'icon' => 'ti ti-sort-descending-numbers',
            ],
            'size-asc' => [
                'label' => trans('core/media::media.size_asc'),
                'icon' => 'ti ti-sort-ascending-2',
            ],
            'size-desc' => [
                'label' => trans('core/media::media.size_desc'),
                'icon' => 'ti ti-sort-descending-2',
            ],
        ];

        return view('core/media::content', compact('sorts'))->render();
    }

    public function responseSuccess(array $data, ?string $message = null): JsonResponse
    {
        return response()->json([
            'error' => false,
            'data' => $data,
            'message' => $message,
        ]);
    }

    public function responseError(
        string $message,
        array $data = [],
        ?int $code = null,
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'error' => true,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $status);
    }

    public function getAllImageSizes(?string $url): array
    {
        $images = [];
        foreach ($this->getSizes() as $size) {
            $readableSize = explode('x', $size);
            $images = $this->getImageUrl($url, $readableSize);
        }

        return $images;
    }

    public function getSizes(): array
    {
        $sizes = $this->getConfig('sizes', []);

        foreach ($sizes as $name => $size) {
            $size = explode('x', $size);

            $settingName = 'media_sizes_' . $name;

            $width = setting($settingName . '_width', $size[0]);

            $height = setting($settingName . '_height', $size[1]);

            if (! $width && ! $height) {
                unset($sizes[$name]);

                continue;
            }

            if (! $width) {
                $width = 'auto';
            }

            if (! $height) {
                $height = 'auto';
            }

            $sizes[$name] = $width . 'x' . $height;
        }

        return $sizes;
    }

    public function getImageUrl(
        ?string $url,
        $size = null,
        bool $relativePath = false,
        $default = null
    ): ?string {
        if (empty($url)) {
            return $default;
        }

        $url = trim($url);

        if (empty($url)) {
            return $default;
        }

        if (Str::startsWith($url, ['data:image/png;base64,', 'data:image/jpeg;base64,', 'http://', 'https://'])) {
            return $url;
        }

        if (empty($size) || $url == '__value__') {
            if ($relativePath) {
                return $url;
            }

            return $this->url($url);
        }

        if ($url == $this->getDefaultImage(false, $size)) {
            return url($url);
        }

        if (
            setting('media_enable_thumbnail_sizes', true) &&
            array_key_exists($size, $this->getSizes()) &&
            $this->canGenerateThumbnails($this->getMimeType($this->getRealPath($url)))
        ) {
            $fileName = File::name($url);
            $fileExtension = File::extension($url);

            $url = str_replace(
                $fileName . '.' . $fileExtension,
                $fileName . '-' . $this->getSize($size) . '.' . $fileExtension,
                $url
            );
        }

        if ($relativePath) {
            return $url;
        }

        if ($url == '__image__') {
            return $this->url($default);
        }

        return $this->url($url);
    }

    public function url(?string $path): string
    {
        $path = $path ? trim($path) : $path;

        if (Str::contains($path, ['http://', 'https://'])) {
            return $path;
        }

        if ($this->getMediaDriver() === 'do_spaces' && (int) setting('media_do_spaces_cdn_enabled')) {
            $customDomain = setting('media_do_spaces_cdn_custom_domain');

            if ($customDomain) {
                return $customDomain . '/' . ltrim($path, '/');
            }

            return str_replace('.digitaloceanspaces.com', '.cdn.digitaloceanspaces.com', Storage::url($path));
        }

        return Storage::url($path);
    }

    public function getDefaultImage(bool $relative = false, ?string $size = null): string
    {
        $default = $this->getConfig('default_image');

        if ($placeholder = setting('media_default_placeholder_image')) {
            $filename = pathinfo($placeholder, PATHINFO_FILENAME);

            if ($size && $size = $this->getSize($size)) {
                $placeholder = str_replace($filename, $filename . '-' . $size, $placeholder);
            }

            return Storage::url($placeholder);
        }

        if ($relative) {
            return $default;
        }

        return $default ? url($default) : $default;
    }

    public function getSize(string $name): ?string
    {
        return Arr::get($this->getSizes(), $name);
    }

    public function deleteFile(MediaFile $file): bool
    {
        $this->deleteThumbnails($file);

        if (! $this->isUsingCloud() && ! Storage::exists($file->url)) {
            return false;
        }

        return Storage::delete($file->url);
    }

    public function deleteThumbnails(MediaFile $file): bool
    {
        if (! $file->canGenerateThumbnails()) {
            return false;
        }

        $filename = pathinfo($file->url, PATHINFO_FILENAME);

        $files = [];
        foreach ($this->getSizes() as $size) {
            $files[] = str_replace($filename, $filename . '-' . $size, $file->url);
        }

        return Storage::delete($files);
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }

    public function removePermission(string $permission): void
    {
        Arr::forget($this->permissions, $permission);
    }

    public function addPermission(string $permission): void
    {
        $this->permissions[] = $permission;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions);
    }

    public function hasAnyPermission(array $permissions): bool
    {
        $hasPermission = false;
        foreach ($permissions as $permission) {
            if (in_array($permission, $this->permissions)) {
                $hasPermission = true;

                break;
            }
        }

        return $hasPermission;
    }

    public function addSize(string $name, int|string $width, int|string $height = 'auto'): self
    {
        if (! $width) {
            $width = 'auto';
        }

        if (! $height) {
            $height = 'auto';
        }

        config(['core.media.media.sizes.' . $name => $width . 'x' . $height]);

        return $this;
    }

    public function removeSize(string $name): self
    {
        $sizes = $this->getSizes();
        Arr::forget($sizes, $name);

        config(['core.media.media.sizes' => $sizes]);

        return $this;
    }

    public function uploadFromEditor(
        Request $request,
        int|string|null $folderId = 0,
        $folderName = null,
        string $fileInput = 'upload'
    ) {
        $validator = Validator::make($request->all(), [
            'upload' => $this->imageValidationRule(),
        ]);

        if ($validator->fails()) {
            return response('<script>alert("' . trans('core/media::media.can_not_detect_file_type') . '")</script>')
                ->header('Content-Type', 'text/html');
        }

        $folderName = $folderName ?: $request->input('upload_type');

        $result = $this->handleUpload($request->file($fileInput), $folderId, $folderName);

        if (! $result['error']) {
            $file = $result['data'];
            if (! $request->input('CKEditorFuncNum')) {
                return response()->json([
                    'fileName' => File::name($this->url($file->url)),
                    'uploaded' => 1,
                    'url' => $this->url($file->url),
                ]);
            }

            return response(
                '<script>window.parent.CKEDITOR.tools.callFunction("' . $request->input('CKEditorFuncNum') .
                '", "' . $this->url($file->url) . '", "");</script>'
            )
                ->header('Content-Type', 'text/html');
        }

        return response('<script>alert("' . Arr::get($result, 'message') . '")</script>')
            ->header('Content-Type', 'text/html');
    }

    public function handleUpload(
        ?UploadedFile $fileUpload,
        int|string|null $folderId = 0,
        ?string $folderSlug = null,
        bool $skipValidation = false,
        string $visibility = 'public'
    ): array {
        $fileUpload = apply_filters('core_media_file_upload', $fileUpload);

        $request = request();

        if ($uploadPath = $request->input('path')) {
            $folderId = $this->handleTargetFolder($folderId, $uploadPath);
        }

        if (! $fileUpload) {
            return [
                'error' => true,
                'message' => trans('core/media::media.can_not_detect_file_type'),
            ];
        }

        $allowedMimeTypes = $this->getConfig('allowed_mime_types');

        if (! $this->isChunkUploadEnabled()) {
            if (! $skipValidation) {
                $validator = Validator::make(['uploaded_file' => $fileUpload], [
                    'uploaded_file' => ['required', ValidationFile::types(explode(',', $allowedMimeTypes))],
                ]);

                if ($validator->fails()) {
                    return [
                        'error' => true,
                        'message' => $validator->getMessageBag()->first(),
                    ];
                }
            }

            $maxUploadFilesizeAllowed = setting('max_upload_filesize');

            if (
                $maxUploadFilesizeAllowed
                && ($fileUpload->getSize() / 1024) / 1024 > (float) $maxUploadFilesizeAllowed
            ) {
                return [
                    'error' => true,
                    'message' => trans('core/media::media.file_too_big_readable_size', [
                        'size' => BaseHelper::humanFilesize($maxUploadFilesizeAllowed * 1024 * 1024),
                    ]),
                ];
            }

            $maxSize = $this->getServerConfigMaxUploadFileSize();

            if ($fileUpload->getSize() / 1024 > (int) $maxSize) {
                return [
                    'error' => true,
                    'message' => trans('core/media::media.file_too_big_readable_size', [
                        'size' => BaseHelper::humanFilesize($maxSize),
                    ]),
                ];
            }
        }

        $extraValidation = apply_filters('core_media_extra_validation', [], $fileUpload);

        if ($extraValidation && Arr::get($extraValidation, 'error')) {
            return [
                'error' => true,
                'message' => $extraValidation['message'],
            ];
        }

        try {
            $fileExtension = $fileUpload->getClientOriginalExtension() ?: $fileUpload->guessExtension();

            if (! $skipValidation && ! in_array(strtolower($fileExtension), explode(',', $allowedMimeTypes))) {
                return [
                    'error' => true,
                    'message' => trans('core/media::media.can_not_detect_file_type'),
                ];
            }

            if ($folderId == 0 && ! empty($folderSlug)) {
                if (str_contains($folderSlug, '/')) {
                    $paths = array_filter(explode('/', $folderSlug));
                    foreach ($paths as $folder) {
                        $folderId = $this->createFolder($folder, $folderId, true);
                    }
                } else {
                    $folderId = $this->createFolder($folderSlug, $folderId, true);
                }
            }

            $file = new MediaFile();

            $fileName = apply_filters('core_media_upload_filename', File::name($fileUpload->getClientOriginalName()), $fileUpload);

            $file->name = MediaFile::createName($fileName, $folderId);

            $folderPath = MediaFolder::getFullPath($folderId);

            $fileName = MediaFile::createSlug(
                $file->name,
                $fileExtension,
                Storage::path($folderPath ?: '')
            );

            $filePath = $fileName;

            if ($folderPath) {
                $filePath = $folderPath . '/' . $filePath;
            }

            if ($this->canGenerateThumbnails($fileUpload->getMimeType())) {
                if (setting('media_keep_original_file_size_and_quality')) {
                    $content = File::get($fileUpload->getRealPath());
                } else {
                    try {
                        $encoder = new AutoEncoder();

                        if (
                            in_array($fileExtension, ['jpg', 'jpeg', 'png'])
                            && setting('media_convert_image_to_webp', false)
                        ) {
                            $encoder = new WebpEncoder();

                            $filePath = File::dirname($filePath) . '/' . File::name($filePath) . '.webp';
                        }

                        $image = $this->imageManager()->read($fileUpload->getRealPath());

                        if (
                            in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp'])
                            && setting('media_reduce_large_image_size', false)
                        ) {
                            $maxWith = setting('media_image_max_width');

                            $maxHeight = setting('media_image_max_height');

                            if ($maxWith || $maxHeight) {
                                $image->scaleDown($maxWith, $maxHeight);
                            }
                        }

                        $content = $image->encode($encoder);
                    } catch (Throwable) {
                        $content = File::get($fileUpload->getRealPath());

                        $filePath = File::dirname($filePath) . '/' . File::name($filePath) . '.' . $fileExtension;
                    }
                }
            } else {
                $content = File::get($fileUpload->getRealPath());
            }

            $this->uploadManager->saveFile($filePath, $content, $fileUpload, $visibility);

            $data = $this->uploadManager->fileDetails($filePath);

            $file->url = $data['url'];
            $file->alt = $file->name;
            $file->size = $data['size'] ?: $fileUpload->getSize();

            $file->mime_type = $data['mime_type'];
            $file->folder_id = $folderId;
            $file->user_id = Auth::guard()->check() ? Auth::guard()->id() : 0;
            $file->options = $request->input('options', []);

            $file->visibility = $visibility;

            $file->save();

            MediaFileUploaded::dispatch($file);

            do_action('core_media_file_uploaded', $file);

            $customizedGeneratedThumbnails = apply_filters('core_media_customized_generate_thumbnails_function', null);

            if (! $customizedGeneratedThumbnails) {
                $this->generateThumbnails($file, $fileUpload);
            }

            return [
                'error' => false,
                'data' => new FileResource($file),
            ];
        } catch (UnableToWriteFile $exception) {
            $message = $exception->getMessage();

            if (! $this->isUsingCloud()) {
                $message = trans('core/media::media.unable_to_write', ['folder' => $this->getUploadPath()]);
            }

            return [
                'error' => true,
                'message' => $message,
            ];
        } catch (Throwable $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size
     */
    public function getServerConfigMaxUploadFileSize(): float
    {
        // Start with post_max_size.
        $maxSize = $this->parseSize(@ini_get('post_max_size'));

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $uploadMax = $this->parseSize(@ini_get('upload_max_filesize'));
        if ($uploadMax > 0 && $uploadMax < $maxSize) {
            $maxSize = $uploadMax;
        }

        return $maxSize;
    }

    public function parseSize(int|string $size): float
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = (int) preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }

        return round($size);
    }

    public function generateThumbnails(MediaFile $file, ?UploadedFile $fileUpload = null): bool
    {
        if (! $file->canGenerateThumbnails()) {
            return false;
        }

        if (! $this->isUsingCloud() && ! File::exists($this->getRealPath($file->url))) {
            return false;
        }

        $folderIds = json_decode(setting('media_folders_can_add_watermark', ''), true);

        if (
            empty($folderIds) ||
            in_array($file->folder_id, $folderIds) ||
            ! empty(array_intersect($file->folder->parents->pluck('id')->all(), $folderIds))
        ) {
            $this->insertWatermark($file->url);
        }

        if (! setting('media_enable_thumbnail_sizes', true)) {
            return false;
        }

        foreach ($this->getSizes() as $size) {
            $readableSize = explode('x', $size);

            if (! $fileUpload || $this->isChunkUploadEnabled()) {
                $fileUpload = $this->getRealPath($file->url);

                if ($this->isUsingCloud()) {
                    $fileUpload = @file_get_contents($fileUpload);

                    if (! $fileUpload) {
                        continue;
                    }
                }
            }

            $thumbnailPath = File::name($file->url) . '-' . $size . '.' . File::extension($file->url);

            if (! $this->isUsingCloud() && Storage::exists($thumbnailPath)) {
                continue;
            }

            $this->thumbnailService
                ->setImage($fileUpload)
                ->setSize($readableSize[0], $readableSize[1])
                ->setDestinationPath(File::dirname($file->url))
                ->setFileName($thumbnailPath)
                ->save();
        }

        return true;
    }

    public function insertWatermark(string $image): bool
    {
        if (! $image || ! setting('media_watermark_enabled', $this->getConfig('watermark.enabled'))) {
            return false;
        }

        $watermarkImage = setting('media_watermark_source', $this->getConfig('watermark.source'));

        if (! $watermarkImage) {
            return false;
        }

        $watermarkPath = $this->getRealPath($watermarkImage);

        if ($this->isUsingCloud()) {
            $watermark = $this->imageManager()->read(file_get_contents($watermarkPath));

            $imageSource = $this->imageManager()->read(file_get_contents($this->getRealPath($image)));
        } else {
            if (! File::exists($watermarkPath)) {
                return false;
            }

            $watermark = $this->imageManager()->read($watermarkPath);

            $imageSource = $this->imageManager()->read($this->getRealPath($image));
        }

        // 10% less than an actual image (play with this value)
        // Watermark will be 10 less than the actual width of the image
        $watermarkSize = (int) round(
            $imageSource->width() * ((int) setting(
                'media_watermark_size',
                $this->getConfig('watermark.size')
            ) / 100),
            2
        );

        // Resize watermark width keep height auto
        $watermark->scale($watermarkSize);

        $imageSource->place(
            $watermark,
            setting('media_watermark_position', $this->getConfig('watermark.position')),
            (int) setting('media_watermark_position_x', setting('watermark_position_x') ?: $this->getConfig('watermark.x')),
            (int) setting('media_watermark_position_y', setting('watermark_position_y') ?: $this->getConfig('watermark.y')),
            (int) setting('media_watermark_opacity', setting('watermark_opacity') ?: $this->getConfig('watermark.opacity'))
        );

        $destinationPath = sprintf(
            '%s/%s',
            trim(File::dirname($image), '/'),
            File::name($image) . '.' . File::extension($image)
        );

        $this->uploadManager->saveFile($destinationPath, $imageSource->encode(new AutoEncoder()));

        return true;
    }

    public function getRealPath(?string $url): string
    {
        $path = $this->isUsingCloud()
            ? Storage::url($url)
            : Storage::path($url);

        return Arr::first(explode('?v=', $path));
    }

    public function isImage(string $mimeType): bool
    {
        return Str::startsWith($mimeType, 'image/');
    }

    public function isUsingCloud(): bool
    {
        return ! in_array($this->getMediaDriver(), ['local', 'public']);
    }

    public function uploadFromUrl(
        string $url,
        int|string $folderId = 0,
        ?string $folderSlug = null,
        ?string $defaultMimetype = null
    ): ?array {
        if (empty($url)) {
            return [
                'error' => true,
                'message' => trans('core/media::media.url_invalid'),
            ];
        }

        $info = pathinfo($url);

        try {
            $response = Http::withoutVerifying()->get($url);

            if ($response->failed() || ! $response->body()) {
                return [
                    'error' => true,
                    'message' => $response->reason() ?: trans('core/media::media.unable_download_image_from', ['url' => $url]),
                ];
            }

            $contents = $response->body();
        } catch (Throwable $exception) {
            return [
                'error' => true,
                'message' => $exception->getMessage(),
            ];
        }

        $path = '/tmp';
        File::ensureDirectoryExists($path);

        $path = $path . '/' . Str::limit($info['basename'], 50, '');
        file_put_contents($path, $contents);

        $fileUpload = $this->newUploadedFile($path, $defaultMimetype);

        $result = $this->handleUpload($fileUpload, $folderId, $folderSlug);

        File::delete($path);

        return $result;
    }

    public function uploadFromPath(
        string $path,
        int|string $folderId = 0,
        ?string $folderSlug = null,
        ?string $defaultMimetype = null
    ): array {
        if (empty($path)) {
            return [
                'error' => true,
                'message' => trans('core/media::media.path_invalid'),
            ];
        }

        $fileUpload = $this->newUploadedFile($path, $defaultMimetype);

        return $this->handleUpload($fileUpload, $folderId, $folderSlug);
    }

    public function uploadFromBlob(
        UploadedFile $path,
        ?string $fileName = null,
        int|string $folderId = 0,
        ?string $folderSlug = null,
    ): array {
        $fileUpload = new UploadedFile($path, $fileName ?: Str::uuid());

        return $this->handleUpload($fileUpload, $folderId, $folderSlug, true);
    }

    protected function newUploadedFile(string $path, ?string $defaultMimeType = null): UploadedFile
    {
        $mimeType = $this->getMimeType($path);

        if (empty($mimeType)) {
            $mimeType = $defaultMimeType;
        }

        $fileName = File::name($path);
        $fileExtension = File::extension($path);

        if (empty($fileExtension) && $mimeType) {
            $mimeTypeDetection = (new MimeTypes())->getExtensions($mimeType);

            $fileExtension = Arr::first($mimeTypeDetection);
        }

        return new UploadedFile($path, $fileName . '.' . $fileExtension, $mimeType, null, true);
    }

    public function getUploadPath(): string
    {
        $customFolder = $this->getConfig('default_upload_folder');

        if (setting('media_customize_upload_path')) {
            $customFolder = trim(setting('media_upload_path'), '/');
        }

        if ($customFolder) {
            return public_path($customFolder);
        }

        return is_link(public_path('storage')) ? storage_path('app/public') : public_path('storage');
    }

    public function getUploadURL(): string
    {
        $uploadUrl = $this->getConfig('default_upload_url') ?: asset('storage');

        if (setting('media_customize_upload_path')) {
            $uploadUrl = trim(asset(setting('media_upload_path')), '/');
        }

        return str_replace('/index.php', '', $uploadUrl);
    }

    public function setUploadPathAndURLToPublic(): static
    {
        add_action('init', function (): void {
            config([
                'filesystems.disks.public.root' => $this->getUploadPath(),
                'filesystems.disks.public.url' => $this->getUploadURL(),
            ]);
        }, 124);

        return $this;
    }

    public function getMimeType(string $url): ?string
    {
        if (! $url) {
            return null;
        }

        try {
            $realPath = $this->getRealPath($url);

            $fileExtension = File::extension($realPath);

            if (! $fileExtension) {
                return null;
            }

            if ($fileExtension == 'jfif') {
                return 'image/jpeg';
            }

            $mimeTypeDetection = new MimeTypes();

            return Arr::first($mimeTypeDetection->getMimeTypes($fileExtension));
        } catch (UnableToRetrieveMetadata) {
            return null;
        }
    }

    public function canGenerateThumbnails(?string $mimeType): bool
    {
        if (! $this->getConfig('generate_thumbnails_enabled')) {
            return false;
        }

        if (! $mimeType) {
            return false;
        }

        return $this->isImage($mimeType) && ! in_array($mimeType, ['image/svg+xml', 'image/x-icon']);
    }

    public function createFolder(string $folderSlug, int|string|null $parentId = 0, bool $force = false): int|string
    {
        $folder = MediaFolder::query()
            ->where([
                'slug' => $folderSlug,
                'parent_id' => $parentId,
            ])
            ->first();

        if (! $folder) {
            if ($force) {
                MediaFolder::query()
                    ->where([
                        'slug' => $folderSlug,
                        'parent_id' => $parentId,
                    ])
                    ->each(fn (MediaFolder $folder) => $folder->forceDelete());
            }

            $folder = MediaFolder::query()->create([
                'user_id' => Auth::guard()->check() ? Auth::guard()->id() : 0,
                'name' => MediaFolder::createName($folderSlug, $parentId),
                'slug' => MediaFolder::createSlug($folderSlug, $parentId),
                'parent_id' => $parentId,
            ]);
        }

        return $folder->id;
    }

    public function handleTargetFolder(int|string|null $folderId = 0, string $filePath = ''): string
    {
        if (str_contains($filePath, '/')) {
            $paths = array_filter(explode('/', $filePath));
            array_pop($paths);
            foreach ($paths as $folder) {
                $folderId = $this->createFolder($folder, $folderId, true);
            }
        }

        return $folderId;
    }

    public function isChunkUploadEnabled(): bool
    {
        return (bool) setting('media_chunk_enabled', (int) $this->getConfig('chunk.enabled') == 1);
    }

    public function getConfig(?string $key = null, string|null|array $default = null)
    {
        $configs = config('core.media.media');

        if (! $key) {
            return $configs;
        }

        return Arr::get($configs, $key, $default);
    }

    public function imageValidationRule(): string
    {
        return 'required|image|mimes:jpg,jpeg,png,webp,gif,bmp';
    }

    public function turnOffAutomaticUrlTranslationIntoLatin(): bool
    {
        return (int) setting('media_turn_off_automatic_url_translation_into_latin', 0) == 1;
    }

    public function getImageProcessingLibrary(): string
    {
        return setting('media_image_processing_library') ?: 'gd';
    }

    public function getMediaDriver(): string
    {
        return setting('media_driver', 'public');
    }

    public function setS3Disk(array $config): void
    {
        if (
            ! $config['key'] ||
            ! $config['secret'] ||
            ! $config['region'] ||
            ! $config['bucket'] ||
            ! $config['url']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.s3' => [
                'driver' => 's3',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'url' => $config['url'],
                'endpoint' => $config['endpoint'],
                'use_path_style_endpoint' => (bool) $config['use_path_style_endpoint'],
            ],
        ]);
    }

    public function setR2Disk(array $config): void
    {
        if (
            ! $config['key'] ||
            ! $config['secret'] ||
            ! $config['bucket'] ||
            ! $config['endpoint']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.r2' => [
                'driver' => 's3',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => 'auto',
                'bucket' => $config['bucket'],
                'url' => $config['url'],
                'endpoint' => $config['endpoint'],
                'use_path_style_endpoint' => (bool) $config['use_path_style_endpoint'],
            ],
        ]);
    }

    public function setDoSpacesDisk(array $config): void
    {
        if (
            ! $config['key'] ||
            ! $config['secret'] ||
            ! $config['region'] ||
            ! $config['bucket'] ||
            ! $config['endpoint']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.do_spaces' => [
                'driver' => 's3',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'endpoint' => $config['endpoint'],
                'use_path_style_endpoint' => (bool) $config['use_path_style_endpoint'],
            ],
        ]);
    }

    public function setWasabiDisk(array $config): void
    {
        if (
            ! $config['key'] ||
            ! $config['secret'] ||
            ! $config['region'] ||
            ! $config['bucket']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.wasabi' => [
                'driver' => 'wasabi',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'root' => $config['root'] ?: '/',
            ],
        ]);
    }

    public function setBunnyCdnDisk(array $config): void
    {
        if (
            ! $config['hostname'] ||
            ! $config['storage_zone'] ||
            ! $config['api_key']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.bunnycdn' => [
                'driver' => 'bunnycdn',
                'visibility' => 'public',
                'throw' => true,
                'hostname' => $config['hostname'],
                'storage_zone' => $config['storage_zone'],
                'api_key' => $config['api_key'],
                'region' => $config['region'],
            ],
        ]);
    }

    public function setBackblazeDisk(array $config): void
    {
        if (
            ! $config['key'] ||
            ! $config['secret'] ||
            ! $config['region'] ||
            ! $config['bucket'] ||
            ! $config['endpoint']
        ) {
            return;
        }

        config()->set([
            'filesystems.disks.backblaze' => [
                'driver' => 's3',
                'visibility' => 'public',
                'throw' => true,
                'key' => $config['key'],
                'secret' => $config['secret'],
                'region' => $config['region'],
                'bucket' => $config['bucket'],
                'url' => $config['url'],
                'endpoint' => str_starts_with($config['endpoint'], 'https://') ? $config['endpoint'] : 'https://' . $config['endpoint'],
                'use_path_style_endpoint' => (bool) $config['use_path_style_endpoint'],
            ],
        ]);
    }

    public function image(
        ?string $url,
        ?string $alt = null,
        ?string $size = null,
        bool $useDefaultImage = true,
        array $attributes = [],
        ?bool $secure = null,
        ?bool $lazy = true
    ): HtmlString {
        if (! isset($attributes['loading'])) {
            $attributes['loading'] = 'lazy';
        }

        $defaultImageUrl = $this->getDefaultImage(false, $size);

        if (! $url) {
            $url = $defaultImageUrl;
        }

        $url = $this->getImageUrl($url, $size, false, $useDefaultImage ? $defaultImageUrl : null);

        if ($alt) {
            $alt = BaseHelper::clean(strip_tags($alt));
        }

        $attributes = [
            'data-bb-lazy' => $lazy ? 'true' : 'false',
            ...$attributes,
        ];

        if (Str::startsWith($url, ['data:image/png;base64,', 'data:image/jpeg;base64,', 'data:image/jpg;base64,'])) {
            return Html::tag('img', '', [...$attributes, 'src' => $url, 'alt' => $alt]);
        }

        return apply_filters('core_media_image', Html::image($url, $alt, $attributes, $secure), $url, $alt, $attributes, $secure);
    }

    public function getFileSize(?string $path): ?string
    {
        try {
            if (! $path || (! $this->isUsingCloud() && ! Storage::exists($path))) {
                return null;
            }

            $size = Storage::size($path);

            if ($size == 0) {
                return '0kB';
            }
        } catch (Throwable) {
            return null;
        }

        return BaseHelper::humanFilesize($size);
    }

    public function renameFile(MediaFile $file, string $newName, bool $renameOnDisk = true): void
    {
        MediaFileRenaming::dispatch($file, $newName, $renameOnDisk);

        $file->name = MediaFile::createName($newName, $file->folder_id);

        if ($renameOnDisk) {
            $filePath = $this->getRealPath($file->url);

            if (File::exists($filePath)) {
                $newFilePath = str_replace(
                    File::name($file->url),
                    File::name($file->name),
                    $file->url
                );

                File::move($filePath, $this->getRealPath($newFilePath));

                $this->deleteFile($file);

                $file->url = str_replace(
                    File::name($file->url),
                    File::name($file->name),
                    $file->url
                );

                $this->generateThumbnails($file);
            }
        }

        $file->save();

        MediaFileRenamed::dispatch($file);
    }

    public function renameFolder(MediaFolder $folder, string $newName, bool $renameOnDisk = true): void
    {
        MediaFolderRenaming::dispatch($folder, $newName, $renameOnDisk);

        $folder->name = MediaFolder::createName($newName, $folder->parent_id);

        if ($renameOnDisk) {
            $folderPath = MediaFolder::getFullPath($folder->id);

            if (Storage::exists($folderPath)) {
                $newFolderName = MediaFolder::createSlug($newName, $folder->parent_id);

                $newFolderPath = str_replace(
                    File::name($folderPath),
                    $newFolderName,
                    $folderPath
                );

                Storage::move($folderPath, $newFolderPath);

                $folder->slug = $newFolderName;

                $folderPath = "$folderPath/";

                MediaFile::query()
                    ->where('url', 'LIKE', "$folderPath%")
                    ->update([
                        'url' => DB::raw(
                            sprintf(
                                'CONCAT(%s, SUBSTRING(url, LOCATE(%s, url) + LENGTH(%s)))',
                                DB::escape("$newFolderPath/"),
                                DB::escape($folderPath),
                                DB::escape($folderPath)
                            )
                        ),
                    ]);
            }
        }

        $folder->save();

        MediaFolderRenamed::dispatch($folder);
    }

    public function refreshCache(): void
    {
        setting()->forceSet('media_random_hash', md5((string) time()))->save();
    }

    public function getFolderColors(): array
    {
        return $this->getConfig('folder_colors', []);
    }

    public function imageManager(?string $driver = null): ImageManager
    {
        if (! $driver) {
            $driver = GdDriver::class;

            if ($this->getImageProcessingLibrary() === 'imagick' && extension_loaded('imagick')) {
                $driver = ImagickDriver::class;
            }
        }

        return new ImageManager($driver);
    }

    public function canOnlyViewOwnMedia(): bool
    {
        return setting('user_can_only_view_own_media', false)
            && AdminHelper::isInAdmin(true)
            && ! App::runningInConsole()
            && auth()->check()
            && ! auth()->user()->isSuperUser();
    }
}
