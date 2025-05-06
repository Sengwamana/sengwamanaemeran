<?php

namespace Botble\Portfolio\Forms;

use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Portfolio\Enums\QuoteStatus;
use Botble\Portfolio\Models\Quote;

class QuoteForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Quote())
            ->add('status', SelectField::class, StatusFieldOption::make()->choices(QuoteStatus::labels())->toArray())
            ->setBreakFieldPoint('status')
            ->addMetaBoxes([
                'information' => [
                    'title' => trans('plugins/portfolio::portfolio.quotation_request.information'),
                    'content' => view('plugins/portfolio::quotation-requests.show', ['quote' => $this->getModel()])->render(),
                ],
            ]);
    }
}
