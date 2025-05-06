<?php

namespace Botble\Menu\Forms;

use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Menu\Models\MenuNode;

class MenuNodeForm extends FormAbstract
{
    public function setup(): void
    {
        $this->model(MenuNode::class);

        $id = $this->model->id ?? 'new';

        $this
            ->contentOnly()
            ->add(
                'menu_id',
                'hidden',
                TextFieldOption::make()
                    ->value($this->request->route('menu'))
                    ->attributes(['class' => 'menu_id'])
            )
            ->add(
                'title',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('packages/menu::menu.title'))
                    ->labelAttributes([
                        'data-update' => 'title',
                        'for' => 'menu-node-title-' . $id,
                    ])
                    ->placeholder(trans('packages/menu::menu.title_placeholder'))
                    ->attributes([
                        'data-old' => $this->model->title,
                        'id' => 'menu-node-title-' . $id,
                    ])
            );

        if (! $this->model->reference_id) {
            $this
                ->add(
                    'url',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(trans('packages/menu::menu.url'))
                        ->labelAttributes([
                            'data-update' => 'custom-url',
                            'for' => 'menu-node-url-' . $id,
                        ])
                        ->placeholder(trans('packages/menu::menu.url_placeholder'))
                        ->attributes([
                            'data-old' => $this->model->url,
                            'id' => 'menu-node-url-' . $id,
                        ])
                );
        }

        $this
            ->add(
                'icon_font',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('packages/menu::menu.icon'))
                    ->labelAttributes([
                        'data-update' => 'icon',
                        'for' => 'menu-node-icon-font-' . $id,
                    ])
                    ->placeholder(trans('packages/menu::menu.icon_placeholder'))
                    ->attributes([
                        'data-old' => $this->model->icon_font,
                        'id' => 'menu-node-icon-font-' . $id,
                    ])
            )
            ->add(
                'css_class',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('packages/menu::menu.css_class'))
                    ->labelAttributes([
                        'data-update' => 'css_class',
                        'for' => 'menu-node-css-class-' . $id,
                    ])
                    ->placeholder(trans('packages/menu::menu.css_class_placeholder'))
                    ->attributes([
                        'data-old' => $this->model->css_class,
                        'id' => 'menu-node-css-class-' . $id,
                    ])
            )
            ->add(
                'target',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('packages/menu::menu.target'))
                    ->labelAttributes([
                        'data-update' => 'target',
                        'for' => 'menu-node-target-' . $id,
                    ])
                    ->choices([
                        '_self' => trans('packages/menu::menu.self_open_link'),
                        '_blank' => trans('packages/menu::menu.blank_open_link'),
                    ])
                    ->attributes([
                        'data-old' => $this->model->target,
                        'id' => 'menu-node-target-' . $id,
                    ])
            );
    }
}
