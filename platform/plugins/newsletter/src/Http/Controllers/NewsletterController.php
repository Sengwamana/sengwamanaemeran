<?php

namespace Botble\Newsletter\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Newsletter\Models\Newsletter;
use Botble\Newsletter\Tables\NewsletterTable;

class NewsletterController extends BaseController
{
    public function index(NewsletterTable $dataTable)
    {
        $this->pageTitle(trans('plugins/newsletter::newsletter.name'));

        return $dataTable->renderTable();
    }

    public function destroy(Newsletter $newsletter)
    {
        return DeleteResourceAction::make($newsletter);
    }
}
