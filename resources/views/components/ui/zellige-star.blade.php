@props(['size' => 32, 'color' => 'currentColor', 'opacity' => 0.5])
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 64 64" style="opacity:{{ $opacity }}">
    <g fill="none" stroke="{{ $color }}" stroke-width="0.8">
        <path d="M32 6 L40 22 L56 22 L44 34 L48 50 L32 42 L16 50 L20 34 L8 22 L24 22 Z"/>
        <path d="M32 14 L37 24 L48 24 L40 32 L42 42 L32 36 L22 42 L24 32 L16 24 L27 24 Z"/>
        <circle cx="32" cy="28" r="4"/>
    </g>
</svg>
