<?php

namespace Botble\Table\Columns\Concerns;

enum CopyableAction: string
{
    case Copy = 'copy';

    case Cut = 'cut';
}
