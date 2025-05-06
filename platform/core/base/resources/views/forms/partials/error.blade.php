@if (!empty($errors) && $errors->has($name))
    <div class="invalid-feedback">
        <small>{{ $errors->first($name) }}</small>
    </div>
@endif
