<?php

namespace Botble\Analytics\Facades;

use Botble\Analytics\Abstracts\AnalyticsAbstract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getCredentials()
 * @method static \Google\Analytics\Data\V1beta\BetaAnalyticsDataClient getClient()
 * @method static \Botble\Analytics\AnalyticsResponse get()
 * @method static \Illuminate\Support\Collection fetchMostVisitedPages(\Botble\Analytics\Period $period, int $maxResults = 20)
 * @method static \Illuminate\Support\Collection fetchTopReferrers(\Botble\Analytics\Period $period, int $maxResults = 20)
 * @method static \Illuminate\Support\Collection fetchTopBrowsers(\Botble\Analytics\Period $period, int $maxResults = 10)
 * @method static \Illuminate\Support\Collection performQuery(\Botble\Analytics\Period $period, array|string $metrics, array|string $dimensions = [])
 * @method static string getPropertyId()
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static \Botble\Analytics\Analytics dateRange(\Botble\Analytics\Period $period)
 * @method static \Botble\Analytics\Analytics dateRanges(\Botble\Analytics\Period ...$items)
 * @method static \Botble\Analytics\Analytics metric(string $name)
 * @method static \Botble\Analytics\Analytics metrics(array|string $items)
 * @method static \Botble\Analytics\Analytics dimension(string $name)
 * @method static \Botble\Analytics\Analytics dimensions(array|string $items)
 * @method static \Botble\Analytics\Analytics orderByMetric(string $name, string $order = 'ASC')
 * @method static \Botble\Analytics\Analytics orderByMetricDesc(string $name)
 * @method static \Botble\Analytics\Analytics orderByDimension(string $name, string $order = 'ASC')
 * @method static \Botble\Analytics\Analytics orderByDimensionDesc(string $name)
 * @method static \Botble\Analytics\Analytics metricAggregation(int $value)
 * @method static \Botble\Analytics\Analytics metricAggregations(int ...$items)
 * @method static \Botble\Analytics\Analytics whereDimension(string $name, int $matchType, $value, bool $caseSensitive = false)
 * @method static \Botble\Analytics\Analytics whereDimensionIn(string $name, array $values, bool $caseSensitive = false)
 * @method static \Botble\Analytics\Analytics whereMetric(string $name, int $operation, $value)
 * @method static \Botble\Analytics\Analytics whereMetricBetween(string $name, $from, $to)
 * @method static \Botble\Analytics\Analytics keepEmptyRows(bool $keepEmptyRows = false)
 * @method static \Botble\Analytics\Analytics limit(int|null $limit = null)
 * @method static \Botble\Analytics\Analytics offset(int|null $offset = null)
 *
 * @see \Botble\Analytics\Analytics
 */
class Analytics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AnalyticsAbstract::class;
    }
}
