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
        <label class="form-label"><strong>{{$label}}</strong> <span class='text-danger'>{{$required ? "*" : ''}} </span></label>
    @endif
    <input name="{{ $name }}" placeholder="{{$placeholder}}" {{$attributes->class(['form-control datepicker flatpickr-input'])->merge(['type' => 'text'])}} value="{{ $value }}" readonly="readonly">
    @error($name)
        <div class="valid-feedback">{{$message}}</div>
    @enderror
</div>
