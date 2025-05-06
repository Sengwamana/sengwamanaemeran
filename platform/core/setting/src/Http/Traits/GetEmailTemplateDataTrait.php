<?php

namespace Botble\Setting\Http\Traits;

use Botble\Base\Facades\EmailHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait GetEmailTemplateDataTrait
{
    public function getData(Request $request, string $type, string $module, string $template): array
    {
        $emailHandler = EmailHandler::setModule($module)
            ->setType($type)
            ->setTemplate($template);

        $variables = $emailHandler->getVariables($type, $module, $template);

        $coreVariables = $emailHandler->getCoreVariables();

        Arr::forget($variables, array_keys($coreVariables));

        $inputData = $request->only(array_keys($variables));

        return [$inputData, $variables, $emailHandler];
    }
}
