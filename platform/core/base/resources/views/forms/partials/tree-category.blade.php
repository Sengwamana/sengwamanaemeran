@php
    if (!isset($groupedCategories)) {
        $groupedCategories = $categories->groupBy('parent_id');
    }

    $currentCategories = $groupedCategories->get($parentId = $parentId ?? 0);
@endphp

@if ($currentCategories)
    <ol @class(['list-group dd-list', $className ?? null])>
        @foreach ($currentCategories as $category)
            @php
                $hasChildren = $groupedCategories->has($category->id);
            @endphp
            <li class="dd-item" data-id="{{ $category->id }}" data-name="{{ $category->name }}">
                @if($updateTreeRoute)
                    <div class="dd-handle dd3-handle"></div>
                @endif
                <div @class(['dd3-content d-flex align-items-center gap-2', 'ps-3' => !$updateTreeRoute])>
                    <div class="d-flex align-items-center gap-1" style="width: 90%;">
                        <x-core::icon :name="$hasChildren ? 'ti ti-folder' : 'ti ti-file'" />
                        <span
                            class="fetch-data text-truncate"
                            role="button"
                            data-href="{{ $canEdit && $editRoute ? route($editRoute, $category->id) : '' }}"
                            title="ID: {{ $category->id }}"
                            data-bs-toggle="tooltip"
                        >
                            {{ $category->name }}
                        </span>

                        @if($category->badge_with_count)
                            {{ $category->badge_with_count }}
                        @endif

                        @if ($canDelete)
                            <span
                                data-bs-toggle="modal"
                                data-bs-target=".modal-confirm-delete"
                                data-url="{{ route($deleteRoute, $category->id)}}"
                                class="ms-2"
                            >
                            <x-core::button
                                type="button"
                                color="danger"
                                size="sm"
                                class="delete-button"
                                icon="ti ti-trash"
                                :icon-only="true"
                                :tooltip="trans('core/base::tree-category.delete_button')"
                                data-bs-placement="right"
                            />
                        </span>
                        @endif
                    </div>
                </div>
                @if ($hasChildren)
                    @include('core/base::forms.partials.tree-category', [
                        'groupedCategories' => $groupedCategories,
                        'parentId' => $category->id,
                        'className' => '',
                    ])
                @endif
            </li>
        @endforeach
    </ol>
@endif
