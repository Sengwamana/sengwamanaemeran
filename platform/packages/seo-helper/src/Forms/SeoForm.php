<?php

namespace Botble\SeoHelper\Forms;

use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;

class SeoForm extends FormAbstract
{
    public function setup(): void
    {
        $meta = $this->getModel();

        $this
            ->contentOnly()
            ->add(
                'seo_meta[seo_title]',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('packages/seo-helper::seo-helper.seo_title'))
                    ->placeholder(trans('packages/seo-helper::seo-helper.seo_title'))
                    ->maxLength(70)
                    ->allowOverLimit()
                    ->value(old('seo_meta.seo_title', $meta['seo_title']))
            )
            ->add(
                'seo_meta[seo_description]',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('packages/seo-helper::seo-helper.seo_description'))
                    ->placeholder(trans('packages/seo-helper::seo-helper.seo_description'))
                    ->rows(3)
                    ->maxLength(160)
                    ->allowOverLimit()
                    ->value(old('seo_meta.seo_description', $meta['seo_description']))
            )
            ->add(
                'meta_keywords',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('packages/theme::partials.no-meta-keywords')->render())
            )
            ->add(
                'seo_meta_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(trans('packages/seo-helper::seo-helper.seo_image'))
                    ->value(old('seo_meta_image', $meta['seo_image']))
            )
            ->add(
                'seo_meta[index]',
                RadioField::class,
                RadioFieldOption::make()
                    ->label(trans('packages/seo-helper::seo-helper.index'))
                    ->selected(old('seo_meta.index', $meta['index']))
                    ->choices([
                        'index' => trans('packages/seo-helper::seo-helper.index'),
                        'noindex' => trans('packages/seo-helper::seo-helper.noindex'),
                    ])
            );
    }
}
