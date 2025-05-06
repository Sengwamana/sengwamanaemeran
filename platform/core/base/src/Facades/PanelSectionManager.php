<?php

namespace Botble\Base\Facades;

use Botble\Base\Contracts\PanelSections\Manager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static static group(string $groupId)
 * @method static static setGroupId(string $groupId)
 * @method static string getGroupId()
 * @method static static setGroupName(string $name)
 * @method static string getGroupName()
 * @method static static default()
 * @method static static moveGroup(string $from, string $to)
 * @method static static register(array|string|\Closure $panelSections)
 * @method static array getAllSections()
 * @method static array getSections()
 * @method static static registerItem(string $section, \Closure $item)
 * @method static static registerItems(string $section, \Closure $items)
 * @method static array getItems(string $section)
 * @method static static ignoreItemId(string $id)
 * @method static static ignoreItemIds(array $ids)
 * @method static bool isIgnoredItemIds(string $id)
 * @method static static beforeRendering(\Closure|callable $callback, int $priority = 100)
 * @method static static afterRendering(\Closure|callable $callback, int $priority = 100)
 * @method static string toHtml()
 * @method static string render()
 *
 * @see \Botble\Base\Contracts\PanelSections\Manager
 * @see \Botble\Base\PanelSections\Manager
 */
class PanelSectionManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
