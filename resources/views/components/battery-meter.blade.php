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
        $fillColor = '#34C759';
    } elseif ($pct >= 80) {
        $tierClass = 'battery-normal';
        $healthStatus = 'Normal';
        $fillColor = '#FF9F0A';
    } else {
        $tierClass = 'battery-service';
        $healthStatus = 'Servis';
        $fillColor = '#FF453A';
    }
@endphp

<div {{ $attributes->merge(['class' => 'battery-meter-container ' . $tierClass]) }} title="Battery Health: {{ $pct }}% (Kondisi {{ $healthStatus }})">
    <!-- Visual Apple Battery Icon -->
    <div class="battery-icon-shell">
        <div class="battery-fill-bar" style="width: {{ $pct }}%;"></div>
    </div>
    
    <!-- Percentage & Health Status -->
    <span class="battery-text font-bold text-xs tracking-tight">
        {{ $pct }}%
    </span>

    @if($showLabel)
        <span class="text-[10px] text-[#8E8E93] font-medium hidden sm:inline">
            BH {{ $healthStatus }}
        </span>
    @endif
</div>
