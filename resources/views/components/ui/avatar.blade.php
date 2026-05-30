@props(['name' => '??', 'size' => 28, 'ring' => false, 'status' => null])
@php
    [$bg, $fg] = avatarTones($name);
    $initials  = avatarInitials($name);
    $fontSize  = max(9, round($size * 0.36));
    $dotSize   = round($size * 0.28);
    $boxShadow = $ring ? 'box-shadow: 0 0 0 2px var(--bg), 0 0 0 2.5px var(--c-blue);' : '';
@endphp
<span class="avatar" style="width:{{ $size }}px;height:{{ $size }}px;background:{{ $bg }};color:{{ $fg }};font-size:{{ $fontSize }}px;{{ $boxShadow }}">
    {{ $initials }}
    @if($status)
        <span style="position:absolute;bottom:0;right:0;width:{{ $dotSize }}px;height:{{ $dotSize }}px;border-radius:999px;background:{{ $status === 'online' ? 'var(--c-atlas)' : 'var(--ink-3)' }};box-shadow:0 0 0 1.5px var(--bg)"></span>
    @endif
</span>
