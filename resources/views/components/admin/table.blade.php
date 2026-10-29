@props([
    'headers' => [],
])

<div class="adm-table-wrap">
    <table {{ $attributes->merge(['class' => 'adm-table']) }}>
        @if(!empty($headers))
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" @if(is_array($header) && !empty($header['align'])) class="adm-{{ $header['align'] }}" @endif>
                            {{ is_array($header) ? ($header['label'] ?? '') : $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody>
            {{ $slot }}
        </tbody>
        @isset($footer)
            <tfoot>
                {{ $footer }}
            </tfoot>
        @endisset
    </table>
</div>

