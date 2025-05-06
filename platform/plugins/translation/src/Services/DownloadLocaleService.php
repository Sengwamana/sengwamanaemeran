<?php

namespace Botble\Translation\Services;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\PclZip as Zip;
use Botble\Media\Facades\RvMedia;
use Illuminate\Support\Facades\File;
use ZipArchive;

class DownloadLocaleService
{
    public function handle(string $locale): string
    {
        $file = RvMedia::getUploadPath() . '/locale-' . $locale . '.zip';

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        if (class_exists('ZipArchive', false)) {
            $zip = new ZipArchive();
            if ($zip->open($file, ZipArchive::CREATE) !== true) {
                File::delete($file);
            }
        } else {
            $zip = new Zip($file);
        }

        $source = lang_path($locale);

        $arrSource = explode(DIRECTORY_SEPARATOR, str_replace('/' . $locale, '', $source));
        $pathLength = strlen(implode(DIRECTORY_SEPARATOR, $arrSource) . DIRECTORY_SEPARATOR);

        // Add each file in the file list to the archive
        $this->recurseZip($source, $zip, $pathLength);

        $jsonFile = lang_path($locale . '.json');

        $arrSource = explode(DIRECTORY_SEPARATOR, File::dirname($jsonFile));
        $pathLength = strlen(implode(DIRECTORY_SEPARATOR, $arrSource) . DIRECTORY_SEPARATOR);

        $this->recurseZip($jsonFile, $zip, $pathLength);

        foreach (File::directories(lang_path('vendor')) as $module) {
            foreach (File::directories($module) as $item) {
                $source = $item . '/' . $locale;

                if (File::isDirectory($source)) {
                    $arrSource = explode(
                        DIRECTORY_SEPARATOR,
                        str_replace(
                            '/vendor/' . File::basename($module) . '/' . File::basename($item) . '/' . $locale,
                            '',
                            $source
                        )
                    );
                    $pathLength = strlen(implode(DIRECTORY_SEPARATOR, $arrSource) . DIRECTORY_SEPARATOR);

                    $this->recurseZip($source, $zip, $pathLength);

                    continue;
                }

                $source = $item . '/' . $locale . '.json';

                if (File::exists($source)) {
                    $arrSource = explode(
                        DIRECTORY_SEPARATOR,
                        str_replace(
                            '/vendor/' . File::basename($module) . '/' . File::basename($item) . '/' . $locale . '.json',
                            '',
                            $source
                        )
                    );
                    $pathLength = strlen(implode(DIRECTORY_SEPARATOR, $arrSource) . DIRECTORY_SEPARATOR);

                    $this->recurseZip($source, $zip, $pathLength);
                }
            }
        }

        if (class_exists('ZipArchive', false)) {
            $zip->close();
        }

        if (File::exists($file)) {
            chmod($file, 0755);
        }

        return $file;
    }

    protected function recurseZip($src, &$zip, $pathLength): void
    {
        if (File::isDirectory($src)) {
            $files = BaseHelper::scanFolder($src);
        } else {
            $files = [File::basename($src)];
            $src = File::dirname($src);
        }

        foreach ($files as $file) {
            if (File::isDirectory($src . DIRECTORY_SEPARATOR . $file)) {
                $this->recurseZip($src . DIRECTORY_SEPARATOR . $file, $zip, $pathLength);
            } elseif (File::isFile($src . DIRECTORY_SEPARATOR . $file)) {
                if (class_exists('ZipArchive', false)) {
                    $zip->addFile($src . DIRECTORY_SEPARATOR . $file, substr($src . DIRECTORY_SEPARATOR . $file, $pathLength));
                } else {
                    /**
                     * @var Zip $zip
                     */
                    $zip->add(
                        $src . DIRECTORY_SEPARATOR . $file,
                        PCLZIP_OPT_REMOVE_PATH,
                        substr($src . DIRECTORY_SEPARATOR . $file, $pathLength)
                    );
                }
            }
        }
    }
}
