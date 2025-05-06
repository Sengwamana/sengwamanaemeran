<?php

namespace Botble\Base\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Supports\Core;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UnlicensedController extends BaseController
{
    public function __construct(private readonly Core $core)
    {
    }

    public function index(Request $request): View|RedirectResponse
    {
        $this->pageTitle('Requires License Activation');

        $this->validateRedirectUrl($request);

        if ($this->core->verifyLicense(true)) {
            return redirect()->route('dashboard.index');
        }

        Assets::removeStyles(['fontawesome', 'select2', 'datepicker', 'spectrum'])
            ->removeScripts([
                'spectrum',
                'jquery-waypoints',
                'stickytableheaders',
                'cookie',
                'select2',
                'datepicker',
                'modernizr',
                'ie8-fix',
                'excanvas',
            ]);

        $redirectUrl = $request->query('redirect_url');

        return view('core/base::system.unlicensed', compact('redirectUrl'));
    }

    public function postSkip(Request $request): RedirectResponse
    {
        $this->validateRedirectUrl($request);

        $this->core->skipLicenseReminder();

        return $request->filled('redirect_url')
            ? redirect()->to($request->input('redirect_url'))
            : redirect()->route('dashboard.index');
    }

    protected function validateRedirectUrl(Request $request): void
    {
        $request->validate(['redirect_url' => ['nullable', 'string', 'url']]);
    }
}
