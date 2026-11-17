<?php extract(collect($attributes->getAttributes())->mapWithKeys(function ($value, $key) { return [Illuminate\Support\Str::camel(str_replace([':', '.'], ' ', $key)) => $value]; })->all(), EXTR_SKIP); ?>

<x-tabler-3d-cube-sphere  {{ $attributes }}>

{{ $slot ?? "" }}
</x-tabler-3d-cube-sphere>