<?php

namespace Botble\Installer\Http\Controllers;

use Botble\Base\Exceptions\LicenseInvalidException;
use Botble\Base\Exceptions\LicenseIsAlreadyActivatedException;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Core;
use Botble\Setting\Facades\Setting;
use Botble\Setting\Http\Requests\LicenseSettingRequest;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class LicenseController extends BaseController
{
    public function index(): View|RedirectResponse
    {
        return view('packages/installer::license');
    }

    public function store(LicenseSettingRequest $request, Core $core): RedirectResponse
    {
        $buyer = $request->input('buyer');

        if (filter_var($buyer, FILTER_VALIDATE_URL)) {
            $username = Str::afterLast($buyer, '/');

            throw ValidationException::withMessages([
                'buyer' => sprintf('Envato username must not a URL. Please try with username "%s".', $username),
            ]);
        }

        try {
            $licenseKey = $request->input('purchase_code');

            $core->activateLicense($licenseKey, $buyer);

            Setting::forceSet('licensed_to', $buyer)->save();

            $finalUrl = URL::temporarySignedRoute('installers.final', Carbon::now()->addMinutes(30));

            return redirect()->to($finalUrl);
        } catch (LicenseInvalidException|LicenseIsAlreadyActivatedException $exception) {
            throw ValidationException::withMessages([
                'purchase_code' => [$exception->getMessage()],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'purchase_code' => ['Something went wrong. Please try again later.'],
            ]);
        }
    }

    public function skip(): RedirectResponse
    {
        Core::make()->skipLicenseReminder();

        return redirect()->to(URL::temporarySignedRoute('installers.final', Carbon::now()->addMinutes(30)));
    }
}
