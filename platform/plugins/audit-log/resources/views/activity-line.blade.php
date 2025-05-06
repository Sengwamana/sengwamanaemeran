<div class="row">
    <div class="col-auto">
        @if ($history->user->id)
            <img
                src="{{ $history->user->avatar_url }}"
                class="avatar"
                alt="{{ $history->user->name }}"
            />
        @else
            <img
                src="{{ setting('admin_favicon') ? RvMedia::getImageUrl(setting('admin_favicon')) : asset(RvMedia::getDefaultImage()) }}"
                class="avatar"
                alt="{{ trans('plugins/audit-log::history.system') }}"
            />
        @endif
    </div>
    <div class="col">
        <div class="text-truncate">
            <strong>
                @if ($history->user->id)
                    <a href="{{ Auth::guard()->user()->url }}">{{ $history->user->name }}</a>
                @else
                    {{ trans('plugins/audit-log::history.system') }}
                @endif
            </strong>

            @if (Lang::has("plugins/audit-log::history.$history->action"))
                {{ trans("plugins/audit-log::history.$history->action") }}
            @else
                {{ $history->action }}
            @endif

            @if ($history->module != 'user' || empty($history->user) || $history->user->id != Auth::guard()->id())
                @if (Lang::has("plugins/audit-log::history.$history->module"))
                    {{ trans("plugins/audit-log::history.$history->module") }}
                @else
                    {{ $history->module }}
                @endif
            @endif

            @if ($history->reference_name && (empty($history->user) || $history->user->name != $history->reference_name))
                <span title="{{ $history->reference_name }}">"{{ Str::limit($history->reference_name, 40) }}"</span>
            @endif
        </div>
        <div class="text-muted">
            {{ $history->created_at->diffForHumans() }}
            (<a
                href="https://ipinfo.io/{{ $history->ip_address }}"
                target="_blank"
                title="{{ $history->ip_address }}"
                rel="nofollow"
            >{{ $history->ip_address }}</a>)
        </div>
    </div>
</div>
