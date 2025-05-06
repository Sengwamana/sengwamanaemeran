@php
    Theme::layout('has-heading');
@endphp

<section class="section-blog-list">
    @php
        $style = theme_option('post_item_style', 1);
        $itemsPerRow = theme_option('post_item_per_row', 3);
    @endphp

    <div @class(['row mt-8', "row-cols-1 row-cols-md-{$itemsPerRow}"])>
        @forelse($posts as $post)
            @include(Theme::getThemeNamespace('views.templates.post-item.index'))
        @empty
            <div class="col-lg-12 py-60">
                <div class="alert alert-warning text-center">
                    {{ __('No post found.') }}
                </div>
            </div>
        @endforelse
    </div>

    @if($posts->hasPages())
        <div class="row pt-150">
            {{ $posts->links(Theme::getThemeNamespace('partials.pagination')) }}
        </div>
    @endif
</section>
