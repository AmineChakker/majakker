@props(['name' => '??', 'avatar' => null, 'size' => 28, 'ring' => false, 'status' => null])

@php
    [$bg, $fg] = avatarTones($name);
    $initials  = avatarInitials($name);
    $fontSize  = max(9, round($size * 0.36));
    $dotSize   = round($size * 0.28);
    $boxShadow = $ring ? 'box-shadow: 0 0 0 2px var(--bg), 0 0 0 2.5px var(--c-blue);' : '';
@endphp

<span class="avatar"
      style="width:{{ $size }}px;height:{{ $size }}px;position:relative;overflow:hidden;border-radius:999px;display:flex;align-items:center;justify-content:center;{{ $boxShadow }}">

    @if($avatar)
        <img src="{{ $avatar }}"
             alt=""
             style="width:100%;height:100%;object-fit:cover;">
    @else
        <span style="background:{{ $bg }};color:{{ $fg }};font-size:{{ $fontSize }}px;display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
            {{ $initials }}
        </span>
    @endif

    @if($status)
        <span style="position:absolute;bottom:0;right:0;width:{{ $dotSize }}px;height:{{ $dotSize }}px;border-radius:999px;background:{{ $status === 'online' ? 'var(--c-atlas)' : 'var(--ink-3)' }};box-shadow:0 0 0 1.5px var(--bg)"></span>
    @endif
</span>