<?php

namespace Botble\Base\GlobalSearch\Providers;

use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\GlobalSearch\GlobalSearchableProvider;
use Botble\Base\GlobalSearch\GlobalSearchableResult;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PanelSectionProvider extends GlobalSearchableProvider
{
    protected array $cachedGroupNames = [];

    public function search(string $keyword): Collection
    {
        $items = [];

        foreach (PanelSectionManager::getAllSections() as $group => $sections) {
            foreach ($sections as $section) {
                $children = $section->getItems();

                if (! empty($children)) {
                    foreach ($children as $item) {
                        if (
                            (
                                $this->stringContains($item->getTitle(), $keyword)
                                || $this->stringContains($item->getDescription(), $keyword)
                            ) && ! empty($item->getUrl())
                        ) {
                            $key = Str::slug("{$item->getTitle()}-{$item->getUrl()}");

                            if (array_key_exists($key, $items)) {
                                continue;
                            }

                            $items[$key] = new GlobalSearchableResult(
                                title: $item->getTitle(),
                                description: $item->getDescription(),
                                parents: [
                                    $this->getGroupName($group),
                                    $section->getTitle(),
                                ],
                                url: $item->getUrl(),
                            );
                        }
                    }
                }
            }
        }

        return collect($items);
    }

    protected function getGroupName(string $groupId): string
    {
        return $this->cachedGroupNames[$groupId] ??= PanelSectionManager::group($groupId)->getGroupName();
    }
}
