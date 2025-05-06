<?php

namespace Botble\Base\Commands\Traits;

use Closure;
use Illuminate\Support\Facades\Validator;
use Throwable;

trait ValidateCommandInput
{
    protected function askWithValidate(string $message, string $rules, bool $secret = false): string
    {
        do {
            if ($secret) {
                try {
                    $input = $this->secret($message);
                } catch (Throwable) {
                    $input = $this->ask($message);
                }
            } else {
                $input = $this->ask($message);
            }

            $validator = Validator::make(compact('input'), ['input' => $rules]);

            if ($validator->fails()) {
                $this->components->error($validator->messages()->first());
            }
        } while ($validator->fails());

        return $input;
    }

    protected function validate(
        array|string $rules,
        array $messages = [],
        array $attributes = []
    ): Closure {
        return function (string $value) use ($rules, $messages, $attributes): string {
            return Validator::make(['value' => $value], ['value' => $rules], $messages, $attributes)
                ->errors()
                ->first();
        };
    }
}
