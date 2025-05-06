<?php

namespace ArchiElite\Announcement\Http\Controllers;

use ArchiElite\Announcement\AnnouncementHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;

class PublicController extends BaseController
{
    public function ajaxGetAnnouncements(): BaseHttpResponse
    {
        return $this
            ->httpResponse()
            ->setData(AnnouncementHelper::render());
    }
}
