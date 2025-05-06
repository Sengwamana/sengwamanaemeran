<?php

namespace Botble\Base\Forms\FieldOptions;

use Botble\Base\Traits\FieldOptions\HasAspectRatio;
use Botble\Base\Traits\FieldOptions\HasNumberItemsPerRow;

class UiSelectorFieldOption extends SelectFieldOption
{
    use HasAspectRatio;
    use HasNumberItemsPerRow;

    public const RATIO_16_9 = '16:9';

    public const RATIO_9_16 = '9:16';

    public const RATIO_4_3 = '4:3';

    public const RATIO_3_4 = '3:4';

    public const RATIO_16_10 = '16:10';

    public const RATIO_10_16 = '10:16';

    public const RATIO_SQUARE = '1:1';

    public function toArray(): array
    {
        $data = parent::toArray();

        if (isset($this->ratio)) {
            $data['attr']['ratio'] = $this->ratio;
        }

        if (isset($this->numberItemsPerRow)) {
            $data['attr']['number_items_per_row'] = $this->numberItemsPerRow;
        }

        $data['attr']['without_aspect_ratio'] = $this->withoutAspectRatio;

        return $data;
    }
}
