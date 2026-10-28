@props([
    'percentage' => 100,
    'showLabel' => true,
    'size' => 'sm',
])

@php
    $pct = max(0, min(100, (int) $percentage));
    
    if ($pct >= 90) {
        $tierClass = 'battery-optimal';
        $healthStatus = 'Prima';
    } elseif ($pct >= 80) {
        $tierClass = 'battery-normal';
        $healthStatus = 'Normal';
    } else {
        $tierClass = 'battery-service';
        $healthStatus = 'Servis';
    }
@endphp

<div {{ $attributes->merge(['class' => 'battery-meter-container ' . $tierClass]) }} title="Battery Health: {{ $pct }}% (Kondisi {{ $healthStatus }})">
    <!-- Visual Apple Battery Icon -->
    <div class="battery-icon-shell">
        <div class="battery-fill-bar" style="width: {{ $pct }}%;"></div>
    </div>
    
    <!-- Percentage & Health Status -->
    <span class="battery-text font-bold text-[11px] tracking-tight">
        BH {{ $pct }}%
    </span>

    @if($showLabel)
        <span class="text-[10px] text-[#6E6E73] font-medium hidden sm:inline">
            ({{ $healthStatus }})
        </span>
    @endif
</div>
