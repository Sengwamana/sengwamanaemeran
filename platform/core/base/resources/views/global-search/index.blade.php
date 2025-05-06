@if ($results->isEmpty())
    <div class="text-center text-muted">
        {{ trans('core/base::base.global_search.no_result') }}
    </div>
@else
    <div class="list-group list-group-hoverable fw-bold">
        @foreach($results as $item)
            <a href="{{ $item->getUrl() }}" aria-selected="false" class="text-truncate list-group-item list-group-item-action">
                <span class="gs-prefix text-primary me-3">#</span>

                @if (! empty($parents = $item->getParents()))
                    @foreach($parents as $parent)
                        @if(! $loop->first)
                            <x-core::icon name="ti ti-chevron-right" class="text-muted" />
                        @endif

                        <span>{{ $parent }}</span>
                    @endforeach

                    <x-core::icon name="ti ti-chevron-right" class="text-muted" />
                @endif

                <span>{{ $item->getTitle() }}</span>
            </a>
        @endforeach
    </div>
@endif
