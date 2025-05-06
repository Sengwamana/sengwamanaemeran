<?php

namespace Botble\PluginManagement\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Breadcrumb;
use Botble\PluginManagement\Services\MarketplaceService;
use Botble\PluginManagement\Services\PluginService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class MarketplaceController extends BaseController
{
    public function __construct(
        protected MarketplaceService $marketplaceService,
        protected PluginService $pluginService,
    ) {
    }

    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('packages/plugin-management::plugin.plugins'), route('plugins.index'))
            ->add(trans('packages/plugin-management::plugin.plugins_add_new'), route('plugins.new'));
    }

    public function index(): View
    {
        $this->pageTitle(trans('packages/plugin-management::plugin.plugins_add_new'));

        Assets::usingVueJS()
            ->addScriptsDirectly('vendor/core/packages/plugin-management/js/marketplace.js');

        return view('packages/plugin-management::marketplace');
    }

    public function list(Request $request): array|BaseHttpResponse
    {
        $request->merge(['type' => 'plugin']);

        $response = $this->marketplaceService->callApi('get', '/products', $request->input());

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
        } else {
            $data = $response->json();
        }

        if (isset($data['error']) && $data['error']) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($data['message']);
        }

        $coreVersion = get_core_version();

        foreach ($data['data'] as $key => $item) {
            $data['data'][$key]['version_check'] = version_compare($coreVersion, $item['minimum_core_version'], '>=');
            $data['data'][$key]['humanized_last_updated_at'] = Carbon::parse($item['last_updated_at'])->diffForHumans();
        }

        return $data;
    }

    public function detail(string $id): JsonResponse|array|null
    {
        $response = $this->marketplaceService->callApi('get', '/products/' . $id);

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $response->json();
    }

    public function iframe(string $id): JsonResponse|string
    {
        $response = $this->marketplaceService->callApi('get', '/products/' . $id . '/iframe');

        if ($response instanceof JsonResponse) {
            return $response;
        }

        return $response->body();
    }

    public function install(string $id): BaseHttpResponse
    {
        $detail = $this->detail($id);

        $version = $detail['data']['minimum_core_version'];
        if (version_compare($version, get_core_version(), '>')) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('packages/plugin-management::marketplace.minimum_core_version_error', compact('version')));
        }

        $name = Str::afterLast($detail['data']['package_name'], '/');

        try {
            $this->marketplaceService->beginInstall($id, $name);
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }

        return $this
            ->httpResponse()
            ->setMessage(trans('packages/plugin-management::marketplace.install_success'))
            ->setData([
                'name' => $name,
                'id' => $id,
            ]);
    }

    public function update(string $id, ?string $name = null): JsonResponse
    {
        $detail = $this->detail($id);

        if (! $name) {
            $name = Str::afterLast($detail['data']['package_name'], '/');
        }

        try {
            $this->marketplaceService->beginInstall($id, $name);
        } catch (Throwable $exception) {
            return response()->json([
                'error' => true,
                'message' => $exception->getMessage(),
            ]);
        }

        $this->pluginService->runMigrations($name);

        $published = $this->pluginService->publishAssets($name);

        if ($published['error']) {
            return response()->json([
                'error' => true,
                'message' => $published['message'],
            ]);
        }

        $this->pluginService->publishTranslations($name);

        return response()->json([
            'error' => false,
            'message' => trans('packages/plugin-management::marketplace.update_success'),
            'data' => [
                'name' => $name,
                'id' => $id,
            ],
        ]);
    }

    public function checkUpdate(): JsonResponse|array|null
    {
        $installedPlugins = $this->pluginService->getInstalledPluginIds();

        if (! $installedPlugins) {
            return response()->json();
        }

        $response = $this->marketplaceService->callApi('post', '/products/check-update', [
            'products' => $installedPlugins,
        ]);

        return $response instanceof JsonResponse ? $response : $response->json();
    }
}
