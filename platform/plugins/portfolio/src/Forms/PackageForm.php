<?php

namespace Botble\Portfolio\Forms;

use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\SortOrderFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Portfolio\Enums\PackageDuration;
use Botble\Portfolio\Http\Requests\PackageRequest;
use Botble\Portfolio\Models\Package;

class PackageForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Package::class)
            ->setValidatorClass(PackageRequest::class)
            ->columns()
            ->add('name', TextField::class, NameFieldOption::make()->required()->colspan(2))
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->colspan(2))
            ->add('content', EditorField::class, ContentFieldOption::make()->allowedShortcodes()->colspan(2))
            ->add(
                'duration',
                SelectField::class,
                SelectFieldOption::make()
                    ->required()
                    ->choices(PackageDuration::labels())
                    ->label(trans('plugins/portfolio::portfolio.duration'))
                    ->colspan(2)
            )
            ->add(
                'price',
                TextField::class,
                TextFieldOption::make()
                    ->required()
                    ->label(trans('plugins/portfolio::portfolio.price'))
                    ->placeholder(trans('plugins/portfolio::portfolio.form.price_placeholder'))
            )
            ->add(
                'annual_price',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/portfolio::portfolio.annual_price'))
                    ->placeholder(trans('plugins/portfolio::portfolio.form.price_placeholder'))
                    ->helperText(trans('plugins/portfolio::portfolio.form.packages_switch_pricing_plan'))
            )
            ->add(
                'features',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->required()
                    ->placeholder(trans('plugins/portfolio::portfolio.form.features_placeholder'))
                    ->helperText(trans('plugins/portfolio::portfolio.form.features_help_block'))
                    ->label(trans('plugins/portfolio::portfolio.form.features'))
                    ->colspan(2)
            )
            ->add(
                'action_label',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/portfolio::portfolio.form.action_label'))
                    ->toArray()
            )
            ->add(
                'action_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/portfolio::portfolio.form.action_url'))
                    ->toArray()
            )
            ->add('is_popular', OnOffField::class, OnOffFieldOption::make()->label(trans('plugins/portfolio::portfolio.is_popular')))
            ->add('order', NumberField::class, SortOrderFieldOption::make())
            ->add('status', SelectField::class, StatusFieldOption::make()->toArray())
            ->setBreakFieldPoint('status');
    }
}
