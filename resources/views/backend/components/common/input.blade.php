@props([
    'label' => '',
    'name' => '',
    'value' => '',
    'placeholder' => '',
    'column' => '12',
    'required' => false
])
<div class="col-md-{{$column}} mb-4">
    @if($label)
        <label for="{{ $name }}" class="form-label"><strong>{{$label}}</strong> <span class='text-danger'>{{$required ? "*" : ''}} </span></label>
    @endif
    <input id="{{ $name }}" name="{{ $name }}" placeholder="{{ $placeholder }}" value="{{ old($name) ? old($name) : $value }}" {{$attributes->class(['form-control'])->merge(['type' => 'text'])}}>
    @error($name)
        <div class="valid-feedback">{{$message}}</div>
    @enderror
</div>
