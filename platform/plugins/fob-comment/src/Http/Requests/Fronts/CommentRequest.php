<?php

namespace FriendsOfBotble\Comment\Http\Requests\Fronts;

use Botble\Captcha\Facades\Captcha;
use Botble\Support\Http\Requests\Request;
use FriendsOfBotble\Comment\Http\Requests\CommentRequest as BaseCommentRequest;
use FriendsOfBotble\Comment\Support\CommentHelper;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class CommentRequest extends Request
{
    protected function prepareForValidation(): void
    {
        $preparedData = CommentHelper::preparedDataForFill();

        $this->merge($preparedData);
    }

    public function rules(): array
    {
        $rules = [
            'reference_id' => [Rule::when($this->has('reference_type'), 'required', 'nullable'), 'string'],
            'reference_type' => [Rule::when($this->has('reference_id'), 'required', 'nullable'), 'string'],
            'reference_url' => [Rule::when(! $this->has('reference_id') && ! $this->has('reference_type'), 'required', 'nullable'), 'string'],
            ...Arr::except((new BaseCommentRequest())->rules(), 'status'),
        ];

        if (CommentHelper::isEnableReCaptcha()) {
            $rules = [...$rules, ...Captcha::rules()];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = [
            'name' => __('Name'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'content' => __('Content'),
            'website' => __('Website'),
        ];

        if (CommentHelper::isEnableReCaptcha()) {
            $attributes = [...$attributes, ...Captcha::attributes()];
        }

        return $attributes;
    }
}
