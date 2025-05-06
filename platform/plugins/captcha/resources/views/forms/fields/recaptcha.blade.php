@switch(setting('captcha_type'))
    @case('v2')
        <x-core::form-group>
            {!! Captcha::display() !!}
        </x-core::form-group>
    @break

    @case('v3')
        {!! Captcha::display() !!}
    @break
@endswitch
