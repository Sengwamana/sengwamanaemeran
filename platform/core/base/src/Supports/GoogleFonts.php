<?php

namespace Botble\Base\Supports;

use Botble\Media\Facades\RvMedia;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GoogleFonts
{
    protected Filesystem $files;

    protected string $path = 'fonts';

    protected bool $inline = true;

    protected string $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0.3 Safari/605.1.15';

    public function setDisk(): void
    {
        config(['filesystems.disks.fonts' => [
            'driver' => 'local',
            'root' => RvMedia::getUploadPath(),
            'url' => RvMedia::getUploadURL(),
            'visibility' => 'public',
            'throw' => false,
        ]]);

        $this->files = Storage::disk('fonts');
    }

    public function load(string $font, ?string $nonce = null, bool $forceDownload = false): ?Fonts
    {
        ['font' => $font, 'nonce' => $nonce] = $this->parseOptions($font, $nonce);

        $url = $font;

        try {
            if ($forceDownload) {
                return $this->fetch($url, $nonce);
            }

            $fonts = $this->loadLocal($url, $nonce);

            if (! $fonts) {
                return $this->fetch($url, $nonce);
            }

            return $fonts;
        } catch (Exception $exception) {
            if (App::hasDebugModeEnabled()) {
                throw $exception;
            }

            return new Fonts(googleFontsUrl: $url, nonce: $nonce);
        }
    }

    protected function loadLocal(string $url, ?string $nonce): ?Fonts
    {
        $this->setDisk();

        if (! $this->files->exists($this->path($url, 'fonts.css'))) {
            return null;
        }

        $fontCssPath = $this->path($url, 'fonts.css');

        $localizedCss = $this->files->get($fontCssPath);

        if (str_contains($localizedCss, '<!DOCTYPE html>')) {
            $this->files->delete($fontCssPath);

            return null;
        }

        if (! str_contains($localizedCss, $this->files->url('fonts'))) {
            $uploadFolder = 'storage';

            if (setting('media_customize_upload_path')) {
                $uploadFolder = trim(setting('media_upload_path'), '/');
            }

            $localizedCss = preg_replace(
                '/(http|https):\/\/.*?\/' . $uploadFolder . '\/fonts\//i',
                $this->files->url('fonts/'),
                $localizedCss
            );

            $this->files->put($fontCssPath, $localizedCss);
        }

        return new Fonts(
            googleFontsUrl: $url,
            localizedUrl: $this->files->url($fontCssPath),
            localizedCss: $localizedCss,
            nonce: $nonce,
            preferInline: $this->inline,
        );
    }

    protected function fetch(string $url, ?string $nonce): ?Fonts
    {
        $response = Http::withHeaders(['User-Agent' => $this->userAgent])
            ->timeout(300)
            ->withoutVerifying()
            ->get($url);

        if ($response->failed()) {
            return null;
        }

        $localizedCss = $response->body();

        try {
            $extractedFonts = $this->extractFontUrls($response);
        } catch (Exception) {
            return null;
        }

        $this->setDisk();

        foreach ($extractedFonts as $fontUrl) {
            $localizedFontUrl = $this->localizeFontUrl($fontUrl);

            $storedFontPath = $this->path($url, $localizedFontUrl);

            if (! $this->files->exists($storedFontPath)) {
                $this->files->put(
                    $storedFontPath,
                    Http::withoutVerifying()->get($fontUrl)->body(),
                );
            }

            $localizedCss = str_replace(
                $fontUrl,
                $this->files->url($storedFontPath),
                $localizedCss,
            );
        }

        $this->files->put($this->path($url, 'fonts.css'), $localizedCss);

        return new Fonts(
            googleFontsUrl: $url,
            localizedUrl: $this->files->url($this->path($url, 'fonts.css')),
            localizedCss: $localizedCss,
            nonce: $nonce,
            preferInline: $this->inline,
        );
    }

    protected function extractFontUrls(string $css): array
    {
        $matches = [];
        preg_match_all('/url\((https:\/\/fonts.gstatic.com\/[^)]+)\)/', $css, $matches);

        return $matches[1];
    }

    protected function localizeFontUrl(string $path): string
    {
        [$path, $extension] = explode('.', str_replace('https://fonts.gstatic.com/', '', $path));

        return implode('.', [Str::slug($path), $extension]);
    }

    protected function path(string $url, string $path = ''): string
    {
        $segments = collect([
            $this->path,
            substr(md5($url), 0, 10),
            $path,
        ]);

        return $segments->filter()->join('/');
    }

    protected function parseOptions(string $font, ?string $nonce = null): array
    {
        return [
            'font' => $font,
            'nonce' => $nonce,
        ];
    }

    public static function getFonts(): array
    {
        $path = core_path('base/resources/data/google-fonts.json');

        try {
            if (! File::exists($path)) {
                return [];
            }

            return File::json($path);
        } catch (Exception) {
            return [];
        }
    }
}
