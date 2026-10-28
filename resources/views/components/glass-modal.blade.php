@props([
    'id',
    'title' => null,
    'subtitle' => null,
    'icon' => null,
])

<div id="{{ $id }}" class="glass-modal-backdrop" role="dialog" aria-modal="true" {{ $attributes }}>
    <div class="glass-modal p-6">
        @if($title)
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/10">
                <div class="flex items-center gap-3">
                    @if($icon)
                        <div class="w-8 h-8 rounded-lg bg-[#0071E3]/20 flex items-center justify-center text-[#0071E3]">
                            <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="font-semibold text-white text-base">{{ $title }}</h3>
                        @if($subtitle)
                            <p class="text-xs text-[#8E8E93]">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
                <button type="button" data-modal-close class="p-1.5 rounded-lg text-[#8E8E93] hover:text-white hover:bg-white/10 transition-colors" aria-label="Tutup">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        @endif

        <div>
            {{ $slot }}
        </div>
    </div>
</div>
