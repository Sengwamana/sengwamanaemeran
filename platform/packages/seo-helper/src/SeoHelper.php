<?php

namespace Botble\SeoHelper;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\MetaBox;
use Botble\Media\Facades\RvMedia;
use Botble\SeoHelper\Contracts\SeoHelperContract;
use Botble\SeoHelper\Contracts\SeoMetaContract;
use Botble\SeoHelper\Contracts\SeoOpenGraphContract;
use Botble\SeoHelper\Contracts\SeoTwitterContract;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SeoHelper implements SeoHelperContract
{
    public function __construct(
        protected SeoMetaContract $seoMeta,
        protected SeoOpenGraphContract $seoOpenGraph,
        protected SeoTwitterContract $seoTwitter
    ) {
        $this->openGraph()->addProperty('type', 'website');
    }

    public function setSeoMeta(SeoMetaContract $seoMeta): static
    {
        $this->seoMeta = $seoMeta;

        return $this;
    }

    public function setSeoOpenGraph(SeoOpenGraphContract $seoOpenGraph): static
    {
        $this->seoOpenGraph = $seoOpenGraph;

        return $this;
    }

    public function setSeoTwitter(SeoTwitterContract $seoTwitter): static
    {
        $this->seoTwitter = $seoTwitter;

        return $this;
    }

    public function openGraph(): SeoOpenGraphContract
    {
        return $this->seoOpenGraph;
    }

    public function setTitle(?string $title, ?string $siteName = null, ?string $separator = null): static
    {
        $this->meta()->setTitle($title, $siteName, $separator);
        $this->openGraph()->setTitle($title);
        if ($siteName) {
            $this->openGraph()->setSiteName($siteName);
        }
        $this->twitter()->setTitle($title);

        return $this;
    }

    public function setImage(?string $image): SeoHelperContract
    {
        $this->openGraph()->setImage($image);

        return $this;
    }

    public function meta(): SeoMetaContract
    {
        return $this->seoMeta;
    }

    public function twitter(): SeoTwitterContract
    {
        return $this->seoTwitter;
    }

    public function getTitle(): ?string
    {
        return $this->meta()->getTitle();
    }

    public function getTitleOnly(): ?string
    {
        return $this->meta()->getTitleOnly();
    }

    public function getDescription(): ?string
    {
        return $this->meta()->getDescription();
    }

    public function setDescription($description): static
    {
        if ($description) {
            $description = Str::limit(strip_tags(BaseHelper::cleanShortcodes($description)), 250);
        }

        $this->meta()->setDescription($description);
        $this->openGraph()->setDescription($description);
        $this->twitter()->setDescription($description);

        return $this;
    }

    public function __toString()
    {
        return $this->render();
    }

    public function render()
    {
        return implode(
            PHP_EOL,
            array_filter([
                $this->meta()->render(),
                $this->openGraph()->render(),
                $this->twitter()->render(),
            ])
        );
    }

    public function saveMetaData(string $screen, Request $request, Model $object): bool
    {
        if (
            in_array(get_class($object), config('packages.seo-helper.general.supported', [])) &&
            $request->has('seo_meta')
        ) {
            try {
                if (empty($request->input('seo_meta'))) {
                    MetaBox::deleteMetaData($object, 'seo_meta');

                    return false;
                }

                $seoMeta = $request->input('seo_meta', []);

                if ($request->hasFile('seo_meta_image_input')) {
                    $uploadFolder = $object->upload_folder ?: Str::plural(Str::slug(class_basename($this)));

                    $result = RvMedia::handleUpload($request->file('seo_meta_image_input'), 0, $uploadFolder);

                    if (! $result['error']) {
                        $request->merge(['seo_meta_image' => $result['data']->url]);
                    }
                }

                $seoMeta['seo_image'] = $request->input('seo_meta_image');

                Arr::forget($seoMeta, 'seo_meta_image');
                Arr::forget($seoMeta, 'seo_meta_image_input');

                if (! Arr::get($seoMeta, 'seo_title')) {
                    Arr::forget($seoMeta, 'seo_title');
                }

                if (! Arr::get($seoMeta, 'seo_description')) {
                    Arr::forget($seoMeta, 'seo_description');
                }

                if (! Arr::get($seoMeta, 'seo_image')) {
                    Arr::forget($seoMeta, 'seo_image');
                }

                if (! empty($seoMeta)) {
                    MetaBox::saveMetaBoxData($object, 'seo_meta', $seoMeta);
                } else {
                    MetaBox::deleteMetaData($object, 'seo_meta');
                }

                return true;
            } catch (Exception) {
                return false;
            }
        }

        return false;
    }

    public function deleteMetaData(string $screen, Model $object): bool
    {
        try {
            if (in_array(get_class($object), config('packages.seo-helper.general.supported', []))) {
                MetaBox::deleteMetaData($object, 'seo_meta');
            }

            return true;
        } catch (Exception) {
            return false;
        }
    }

    public function supportedModules(): array
    {
        return config('packages.seo-helper.general.supported', []);
    }

    public function registerModule(array|string $model): static
    {
        if (! is_array($model)) {
            $model = [$model];
        }

        config([
            'packages.seo-helper.general.supported' => array_merge($this->supportedModules(), $model),
        ]);

        return $this;
    }

    public function removeModule(array|string $model): static
    {
        if (! is_array($model)) {
            $model = [$model];
        }

        $supported = collect($this->supportedModules())->reject(fn ($item) => in_array($item, $model))->toArray();

        config()->set('packages.seo-helper.general.supported', $supported);

        return $this;
    }
}
