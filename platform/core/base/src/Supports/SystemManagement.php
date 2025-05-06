<?php

namespace Botble\Base\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\Media\Facades\RvMedia;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class SystemManagement
{
    public static function getComposerArray(): array
    {
        return BaseHelper::getFileData(base_path('composer.json'));
    }

    public static function getPackagesAndDependencies(array $packagesArray): array
    {
        $packages = [];
        foreach ($packagesArray as $key => $value) {
            $packageFile = base_path('vendor/' . $key . '/composer.json');

            if ($key === 'php' || ! File::exists($packageFile)) {
                continue;
            }

            $composer = BaseHelper::getFileData($packageFile);

            $packages[] = [
                'name' => $key,
                'version' => $value,
                'dependencies' => Arr::get($composer, 'require', 'No dependencies'),
                'dev-dependencies' => Arr::get($composer, 'require-dev', 'No dependencies'),
            ];
        }

        return $packages;
    }

    public static function getSystemEnv(): array
    {
        $app = app();

        return [
            'version' => $app->version(),
            'timezone' => $app['config']->get('app.timezone'),
            'debug_mode' => $app->hasDebugModeEnabled(),
            'storage_dir_writable' => File::isWritable($app->storagePath()),
            'cache_dir_writable' => File::isReadable($app->bootstrapPath('cache')),
            'app_size' => 'N/A',
        ];
    }

    protected static function calculateAppSize(string $directory): int
    {
        $size = 0;

        foreach (File::glob(rtrim($directory, '/') . '/*', GLOB_NOSORT) as $each) {
            $size += File::isFile($each) ? File::size($each) : self::calculateAppSize($each);
        }

        return $size;
    }

    public static function getServerEnv(): array
    {
        return [
            'version' => phpversion(),
            'memory_limit' => @ini_get('memory_limit'),
            'max_execution_time' => @ini_get('max_execution_time'),
            'server_software' => Request::server('SERVER_SOFTWARE'),
            'server_os' => function_exists('php_uname') ? php_uname() : 'N/A',
            'database_connection_name' => DB::getDefaultConnection(),
            'ssl_installed' => request()->isSecure(),
            'cache_driver' => Cache::getDefaultDriver(),
            'session_driver' => Session::getDefaultDriver(),
            'queue_connection' => Queue::getDefaultDriver(),
            'allow_url_fopen_enabled' => @ini_get('allow_url_fopen'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'curl' => extension_loaded('curl'),
            'exif' => extension_loaded('exif'),
            'pdo' => extension_loaded('pdo'),
            'fileinfo' => extension_loaded('fileinfo'),
            'tokenizer' => extension_loaded('tokenizer'),
            'imagick_or_gd' => (extension_loaded('imagick') || extension_loaded('gd')) && extension_loaded(RvMedia::getImageProcessingLibrary()),
            'zip' => extension_loaded('zip'),
            'iconv' => extension_loaded('iconv'),
        ];
    }

    public static function getMemoryLimitAsMegabyte(): int
    {
        $memoryLimit = @ini_get('memory_limit') ?: 0;

        if (! $memoryLimit) {
            return 0;
        }

        if (preg_match('/^(\d+)(.)$/', $memoryLimit, $matches)) {
            if ($matches[2] === 'M') {
                return (int) $matches[1];
            }

            if ($matches[2] === 'K') {
                return (int) ((int) $matches[1] / 1024);
            }

            if ($matches[2] === 'G') {
                return (int) ((int) $matches[1] * 1024);
            }
        }

        return (int) $memoryLimit;
    }

    public static function getMaximumExecutionTime(): int
    {
        return (int) (@ini_get('max_execution_time') ?: -1);
    }

    public static function getAppSize(): int
    {
        return self::calculateAppSize(app()->basePath());
    }
}
