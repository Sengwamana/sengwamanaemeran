<div
    class="debug-badge"
    role="button"
    data-bs-toggle="modal"
    data-bs-target="#debug-mode-modal"
>Debug Mode</div>

<x-core::modal.action
    id="debug-mode-modal"
    type="info"
    title="Debug Mode"
    size="md"
    :submit-button-label="__('Fix it for me')"
    :submit-button-attrs="['data-bs-toggle' => 'modal', 'data-bs-target' => '#debug-mode-turn-off-confirmation-modal']"
    submit-button-color="warning"
>
    <div class="text-start">
        <p>
            By default, this option is set to respect the value of the <code class="text-danger">APP_DEBUG</code>
            environment variable, which is stored in your <code class="text-danger">.env</code> file.
        </p>
        <p>
            For local development, you should set the <code class="text-danger">APP_DEBUG</code> environment variable to
            <code class="text-danger">true</code>. In your production environment, this value should always be <code
                class="text-danger"
            >false</code>. If the variable is set to <code class="text-danger">true</code> in production, you risk
            exposing sensitive configuration values to your application's end users.
        </p>
    </div>
</x-core::modal.action>

<x-core::modal.action
    id="debug-mode-turn-off-confirmation-modal"
    type="warning"
    :title="__('Are you sure?')"
    :description="__('Are you sure you want to turn off the debug mode? This action cannot be undone.')"
    :submit-button-label="__('Yes, turn off')"
    :submit-button-attrs="['id' => 'debug-mode-turn-off-form-submit', 'data-url' => route('system.debug-mode.turn-off')]"
    :cancel-button="true"
></x-core::modal.action>
