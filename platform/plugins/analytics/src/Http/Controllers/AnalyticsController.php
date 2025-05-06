<?php

namespace Botble\Analytics\Http\Controllers;

use Botble\Analytics\Exceptions\InvalidConfiguration;
use Botble\Analytics\Facades\Analytics;
use Botble\Analytics\Http\Requests\AnalyticsRequest;
use Botble\Analytics\Period;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

class AnalyticsController extends BaseController
{
    public function getGeneral(AnalyticsRequest $request)
    {
        try {
            $period = $this->getPeriodFromRequest($request);

            $dimensions = $this->getDimensionFromRequest($request);

            $chartStats = $this->getChartStats($period, $dimensions);

            $countryStats = $this->getCountryStats($period, 'countryIsoCode');

            [$sessions, $totalUsers, $screenPageViews, $bounceRate] = $this->getTotalStats($period, $dimensions);

            return $this
                ->httpResponse()
                ->setData(
                    view(
                        'plugins/analytics::widgets.general',
                        compact('chartStats', 'countryStats', 'sessions', 'totalUsers', 'screenPageViews', 'bounceRate')
                    )->render()
                );
        } catch (InvalidConfiguration $exception) {
            return $this->handleInvalidConfigException($exception);
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    protected function getTotalStats(Period $period, string $dimensions): array
    {
        if ($dimensions === 'hour') {
            $dimensions = 'date';
        }

        $sessions = 0;
        $totalUsers = 0;
        $screenPageViews = 0;
        $bounceRate = 0;

        $totalQuery = Analytics::performQuery($period, ['sessions', 'totalUsers', 'screenPageViews', 'bounceRate'], $dimensions)->toArray();

        foreach ($totalQuery as $item) {
            $sessions += $item['sessions'];
            $totalUsers += $item['totalUsers'];
            $screenPageViews += $item['screenPageViews'];
            $bounceRate += Arr::get($item, 'bounceRate', 0);
        }

        return [$sessions, $totalUsers, $screenPageViews, $bounceRate];
    }

    protected function getCountryStats(Period $period, string $dimensions): array
    {
        $countryStats = Analytics::performQuery($period, 'sessions', $dimensions)->toArray();

        foreach ($countryStats as $key => $item) {
            $countryStats[$key] = array_values($item);
        }

        return $countryStats;
    }

    protected function getChartStats(Period $period, string $dimensions): Collection
    {
        $visitorData = [];

        $queryRows = Analytics::performQuery($period, ['totalUsers', 'screenPageViews'], $dimensions)->toArray();

        foreach ($queryRows as $dateRow) {
            $dateRow = array_values($dateRow);

            $visitorData[$dateRow[0]] = [
                'axis' => $this->getAxisByDimensions($dateRow[0], $dimensions),
                'visitors' => $dateRow[1],
                'pageViews' => $dateRow[2],
            ];
        }

        ksort($visitorData);

        if ($dimensions === 'hour') {
            for ($index = 0; $index < 24; $index++) {
                if (! isset($visitorData[$index])) {
                    $visitorData[$index] = [
                        'axis' => $index . 'h',
                        'visitors' => 0,
                        'pageViews' => 0,
                    ];
                }
            }
        }

        return collect($visitorData);
    }

    protected function getAxisByDimensions(string $dateRow, string $dimensions = 'hour'): string
    {
        return match ($dimensions) {
            'date' => BaseHelper::formatDate($dateRow),
            'yearMonth' => Carbon::createFromFormat('Ym', $dateRow)->format('M Y'),
            default => (int) $dateRow . 'h',
        };
    }

    public function getTopVisitPages(AnalyticsRequest $request)
    {
        try {
            $period = $this->getPeriodFromRequest($request);

            $query = Analytics::fetchMostVisitedPages($period, 10);

            $pages = [];

            $schema = $request->getScheme() . '://';

            foreach ($query as $item) {
                if (empty($item['fullPageUrl'])) {
                    continue;
                }

                $pageUrl = $item['fullPageUrl'];

                if (! Str::startsWith($pageUrl, $schema)) {
                    $pageUrl = $schema . $pageUrl;
                }

                $pages[] = [
                    'pageTitle' => $item['pageTitle'],
                    'url' => $pageUrl,
                    'pageViews' => $item['screenPageViews'] ?? $item['pageViews'],
                ];
            }

            return $this
                ->httpResponse()
                ->setData(view('plugins/analytics::widgets.page', compact('pages'))->render());
        } catch (InvalidConfiguration $exception) {
            return $this->handleInvalidConfigException($exception);
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getTopBrowser(AnalyticsRequest $request)
    {
        try {
            $period = $this->getPeriodFromRequest($request);

            $browsers = Analytics::fetchTopBrowsers($period);

            return $this
                ->httpResponse()
                ->setData(view('plugins/analytics::widgets.browser', compact('browsers'))->render());
        } catch (InvalidConfiguration $exception) {
            return $this->handleInvalidConfigException($exception);
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getTopReferrer(AnalyticsRequest $request)
    {
        try {
            $period = $this->getPeriodFromRequest($request);

            $query = Analytics::fetchTopReferrers($period, 10);

            $referrers = [];

            foreach ($query as $item) {
                $referrers[] = [
                    'url' => $item['sessionSource'] ?? $item['url'],
                    'pageViews' => $item['screenPageViews'] ?? $item['pageViews'],
                ];
            }

            return $this
                ->httpResponse()
                ->setData(view('plugins/analytics::widgets.referrer', compact('referrers'))->render());
        } catch (InvalidConfiguration $exception) {
            return $this->handleInvalidConfigException($exception);
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    protected function getPeriodFromRequest(AnalyticsRequest $request): Period
    {
        $dashboardInstance = new DashboardWidgetInstance();
        $predefinedRangeFound = $dashboardInstance->getFilterRange($request->input('predefined_range'));
        if ($request->input('changed_predefined_range')) {
            $dashboardInstance->saveSettings(
                'widget_analytics_general',
                ['predefined_range' => $predefinedRangeFound['key']]
            );
        }

        $startDate = $predefinedRangeFound['startDate'];
        $endDate = $predefinedRangeFound['endDate'];

        return Period::create($startDate, $endDate);
    }

    protected function getDimensionFromRequest(AnalyticsRequest $request): string
    {
        $predefinedRangeFound = (new DashboardWidgetInstance())->getFilterRange($request->input('predefined_range'));

        return Arr::get([
            'this_week' => 'date',
            'last_7_days' => 'date',
            'this_month' => 'date',
            'last_30_days' => 'date',
            'this_year' => 'yearMonth',
        ], $predefinedRangeFound['key'], 'hour');
    }

    protected function handleInvalidConfigException(InvalidConfiguration $exception): BaseHttpResponse
    {
        $message = $exception->getMessage() ?: trans('plugins/analytics::analytics.wrong_configuration');

        return $this
            ->httpResponse()
            ->setError()
            ->setData(view('plugins/analytics::widgets.empty-state', compact('message'))->render())
            ->setMessage($message);
    }
}
