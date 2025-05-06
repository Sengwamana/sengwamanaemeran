@php
    use Botble\Base\Enums\SystemUpdaterStepEnum;

    $changelog = isset($latestUpdate) && $latestUpdate && $latestUpdate->changelog ? trim(str_replace(PHP_EOL . PHP_EOL, PHP_EOL, strip_tags(str_replace(['<li>', '</li>', '<ul>'], ['<li>- ', '</li>' . PHP_EOL, PHP_EOL . '<ul>'], $latestUpdate->changelog)))) : ''
@endphp

@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div style="max-width: 900px;" class="m-auto">
        <x-core::alert
            type="warning"
            title="Important notes:"
            :important="false"
        >
            <ul class="mt-3 mb-0 ps-2">
                <li class="mb-2">Please back up your database and script files before upgrading.</li>
                <li class="mb-2">You need to activate your license before doing upgrade.</li>
                <li class="mb-2">If you don't need this 1-click update, you can disable it in <strong>.env</strong> by
                    adding
                    <strong>CMS_ENABLE_SYSTEM_UPDATER=false</strong>
                </li>
                <li>It will override all files in <strong>platform/core</strong>, <strong>platform/packages</strong>,
                    all
                    plugins developed by us in <strong>platform/plugins</strong> and theme developed by us in
                    <strong>platform/themes</strong>.
                </li>
            </ul>
        </x-core::alert>

        @if (($memoryLimit && $memoryLimit != -1 && $memoryLimit < $requiredMemoryLimit) || ($maximumExecutionTime && $maximumExecutionTime < $requiredMaximumExecutionTime))
            <x-core::alert
                type="warning"
                title="Warning"
                :important="false"
            >
                <div class="mt-3 mb-0">
                    Your server does not meet the minimum requirements for this update, hence you may not be able to
                    update to the latest version. Please enhance the following:

                    <ul class="mt-3 mb-0 ps-2">
                        @if ($memoryLimit && $memoryLimit < $requiredMemoryLimit)
                            <li class="mb-2"><code>memory_limit</code>: <code>{{ $memoryLimit }}M</code> =>
                                <code>{{ $requiredMemoryLimit }}M</code></li>
                        @endif

                        @if ($maximumExecutionTime && $maximumExecutionTime < $requiredMaximumExecutionTime)
                            <li class="mb-2"><code>max_execution_time</code>: <code>{{ $maximumExecutionTime }}</code>
                                => <code>{{ $requiredMaximumExecutionTime }}</code></li>
                         @endif
                    </ul>

                    <p class="mt-3 mb-0">
                        <span>Need help editing the <code>memory_limit</code> or <code>max_execution_time</code> config?</span>
                        <span>Check out the <a href="https://support.cpanel.net/hc/en-us/articles/360062361214-How-to-set-or-increase-PHP-INI-memory-limit-or-other-values-" rel="noreferer" target="_blank">PHP Memory Limit Configuration Guide (CPanel)</a> for step-by-step instructions.</span>
                        <span>Or, find helpful tutorials by searching 'edit memory_limit config in PHP' online.</span>
                    </p>
                </div>
            </x-core::alert>
        @endif

        @if (! $activated)
            <x-core::alert
                type="warning"
                title="You haven't activated your license yet!"
                :important="true"
            >
                <p class="mt-3 mb-0">
                    We are required to activate your license before doing upgrade.
                    Please go to <a href="{{ route('settings.general') }}" class="fw-bold text-white">settings</a> page
                    to activate your license.
                </p>
            </x-core::alert>
        @endif

        @if($isOutdated && $latestUpdate)
            <x-core::card class="mb-3">
                <x-core::card.header>
                    <x-core::card.title>
                        Latest changelog ({{ BaseHelper::formatDate($latestUpdate->releasedDate) }})
                    </x-core::card.title>
                </x-core::card.header>
                <x-core::card.body dir="ltr">
                    <h3 class="text-success mb-4">
                        {{ __('A new version (:version / released on :date) is available to update!', ['version' => $latestUpdate->version, 'date' => BaseHelper::formatDate($latestUpdate->releasedDate)]) }}
                    </h3>

                    <pre>{!! $changelog !!}</pre>
                </x-core::card.body>
            </x-core::card>
        @endif

        @if(request()->query('no-ajax') != 1)
            <x-core::card class="mb-3">
                <x-core::card.header>
                    <x-core::card.title>OneClick System Updater</x-core::card.title>
                </x-core::card.header>

                <x-core::card.body dir="ltr">
                    @if (!empty($latestUpdate))
                        <system-update-component
                            update-url="{{ route('system.updater.post') }}"
                            update-id="{{ $latestUpdate->updateId }}"
                            version="{{ $latestUpdate->version }}"
                            first-step="{{ SystemUpdaterStepEnum::firstStep() }}"
                            first-step-message="{{ SystemUpdaterStepEnum::DOWNLOAD()->message() }}"
                            last-step="{{ SystemUpdaterStepEnum::lastStep() }}"
                            :is-outdated="{{ Js::encode($isOutdated) }}"
                            :is-activated="{{ Js::encode($activated) }}"
                            v-slot="{ performUpdate }"
                        >
                            @if (!$isOutdated)
                                <h3 class="text-success">{{ __('The system is up-to-date. There are no new versions to update!') }}</h3>
                            @endif

                            @if (! $activated)
                                <x-core::modal.action
                                    id="system-updater-confirm-modal"
                                    type="warning"
                                    title="Are you sure?"
                                    description="Your license has not been activated yet! You might not receive the latest updates."
                                    submit-button-label="Yes, update it!"
                                    :submit-button-attrs="['@click' => 'performUpdate']"
                                    :cancel-button="true"
                                ></x-core::modal.action>
                            @endif
                        </system-update-component>

                        @if(! $isOutdated)
                            <p class="mt-3">
                                This won't touch or reset your data - it just reinstall the latest version of the system.
                            </p>
                        @endif
                    @else
                        <p class="mb-0 text-success">{{ __('The system is up-to-date. There are no new versions to update!') }}</p>
                    @endif
                </x-core::card.body>
            </x-core::card>
        @endif

        @if($latestUpdate)
            <x-core::card class="mb-3">
                <x-core::card.header>
                    <x-core::card.title>Manual System Updater</x-core::card.title>
                </x-core::card.header>
                <x-core::card.body dir="ltr">
                    <x-core::alert type="info" class="mb-3">
                        Having trouble with the One-Click System Updater? No worries! Check out the manual updater below - it's easy, just follow the steps!
                    </x-core::alert>

                    <form
                        action="{{ route('system.updater') }}?no-ajax=1&update_id={{ $latestUpdate->updateId }}&version={{ $latestUpdate->version }}"
                        method="POST"
                    >
                        <x-core::step :vertical="true" :counter="true">
                            @foreach (SystemUpdaterStepEnum::labels() as $step => $label)
                                @break($step === SystemUpdaterStepEnum::lastStep())

                                <x-core::step.item>
                                    <div class="h4 mb-2">{{ $label }}</div>
                                    <x-core::button
                                        class="mb-3"
                                        type="submit"
                                        name="step_name"
                                        value="{{ $step }}"
                                        color="primary"
                                        data-updating-text="Updating..."
                                    >
                                        {{ __('Run') }}
                                    </x-core::button>
                                </x-core::step.item>
                            @endforeach
                        </x-core::step>
                        @csrf
                    </form>
                </x-core::card.body>
            </x-core::card>
        @endif

        @if (isset($isOutdated) && isset($latestUpdate) && !$isOutdated && $latestUpdate)
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>
                        Latest changelog ({{ BaseHelper::formatDate($latestUpdate->releasedDate) }})
                    </x-core::card.title>
                </x-core::card.header>
                <x-core::card.body>
                    <pre>{!! $changelog !!}</pre>
                </x-core::card.body>
            </x-core::card>
        @endif
    </div>
@endsection
