@props([
    'hex' => '#9A958E',
    'name' => '',
    'active' => false,
])

<button type="button" 
        {{ $attributes->merge(['class' => 'cust-swatch ' . ($active ? 'active' : '')]) }}
        style="background-color: {{ $hex }};"
        data-color-name="{{ $name }}"
        title="{{ $name }}"
        aria-label="Varian warna {{ $name }}">
</button>

