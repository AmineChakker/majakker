@props(['name', 'size' => 16, 'style' => ''])
@php
$stroke = 'fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"';
$paths = [
    'home'        => '<path d="M3 10.5 L10 4 L17 10.5 V16 a1 1 0 0 1-1 1h-3v-5h-4v5H4 a1 1 0 0 1-1-1z" '.$stroke.'/>',
    'feed'        => '<path d="M3 5h14M3 10h14M3 15h9" '.$stroke.'/>',
    'classes'     => '<path d="M3 6 L10 3 L17 6 L10 9 Z M5 8 V13 a5 5 0 0 0 10 0 V8" '.$stroke.'/>',
    'clubs'       => '<circle cx="7" cy="8" r="3" '.$stroke.'/><circle cx="13" cy="8" r="3" '.$stroke.'/><path d="M3 17a4 4 0 0 1 7-2.6 a4 4 0 0 1 7 2.6" '.$stroke.'/>',
    'msg'         => '<path d="M3 5 h14 v9 H8 l-3 3 V14 H3 z" '.$stroke.'/>',
    'bell'        => '<path d="M5 14 L5 9 a5 5 0 0 1 10 0 v5 l1.5 2 H3.5 z M8 17 a2 2 0 0 0 4 0" '.$stroke.'/>',
    'search'      => '<circle cx="9" cy="9" r="5" '.$stroke.'/><path d="m13 13 4 4" '.$stroke.'/>',
    'plus'        => '<path d="M10 4v12 M4 10h12" '.$stroke.'/>',
    'heart'       => '<path d="M10 16 C 4 12 2 9 2 6.5 A 3.5 3.5 0 0 1 10 5 A 3.5 3.5 0 0 1 18 6.5 C 18 9 16 12 10 16 z" '.$stroke.'/>',
    'spark'       => '<path d="M10 3 L11 8 L16 9 L11 10 L10 15 L9 10 L4 9 L9 8 z" '.$stroke.'/>',
    'cmt'         => '<path d="M3 5 h14 v9 H10 l-4 3 V14 H3 z" '.$stroke.'/>',
    'share'       => '<path d="M14 4 L17 7 L14 10 M17 7 H7 a4 4 0 0 0-4 4 v3" '.$stroke.'/>',
    'book'        => '<path d="M3 4 a3 1.5 0 0 1 7 0 a3 1.5 0 0 1 7 0 v11 a3 1.5 0 0 0-7 0 a3 1.5 0 0 0-7 0 z M10 4 v11" '.$stroke.'/>',
    'chart'       => '<path d="M3 17 V3 M3 17 H17 M6 14 V9 M10 14 V6 M14 14 V11" '.$stroke.'/>',
    'grid'        => '<rect x="3" y="3" width="6" height="6" '.$stroke.'/><rect x="11" y="3" width="6" height="6" '.$stroke.'/><rect x="3" y="11" width="6" height="6" '.$stroke.'/><rect x="11" y="11" width="6" height="6" '.$stroke.'/>',
    'settings'    => '<circle cx="10" cy="10" r="2.5" '.$stroke.'/><path d="M10 2 v2 M10 16 v2 M2 10 h2 M16 10 h2 M4.3 4.3 l1.4 1.4 M14.3 14.3 l1.4 1.4 M4.3 15.7 l1.4-1.4 M14.3 5.7 l1.4-1.4" '.$stroke.'/>',
    'chevron'     => '<path d="M7 5 L12 10 L7 15" '.$stroke.'/>',
    'chevronDown' => '<path d="M5 7 L10 12 L15 7" '.$stroke.'/>',
    'pin'         => '<path d="M10 3 L13 6 L17 7 L13 11 L13 17 L10 14 L7 17 L7 11 L3 7 L7 6 z" '.$stroke.'/>',
    'paperclip'   => '<path d="M14 6 L8 12 a2 2 0 0 0 3 3 L17 9 a4 4 0 0 0-6-6 L4 11 a6 6 0 0 0 8 8 L17 14" '.$stroke.'/>',
    'poll'        => '<path d="M4 16 V11 M9 16 V6 M14 16 V13" '.$stroke.'/>',
    'img'         => '<rect x="3" y="4" width="14" height="12" rx="2" '.$stroke.'/><circle cx="7" cy="8" r="1.4" '.$stroke.'/><path d="m4 14 4-4 3 3 2-2 3 3" '.$stroke.'/>',
    'smile'       => '<circle cx="10" cy="10" r="7" '.$stroke.'/><circle cx="7.5" cy="9" r=".5" fill="currentColor"/><circle cx="12.5" cy="9" r=".5" fill="currentColor"/><path d="M7 12.5 a3 2 0 0 0 6 0" '.$stroke.'/>',
    'check'       => '<path d="M4 10 L8 14 L16 6" '.$stroke.'/>',
    'dot'         => '<circle cx="10" cy="10" r="2" fill="currentColor"/>',
    'moon'        => '<path d="M16 11 A6 6 0 1 1 9 4 a5 5 0 0 0 7 7 z" '.$stroke.'/>',
    'flag'        => '<path d="M5 17 V3 M5 4 h10 l-2 3 l2 3 H5" '.$stroke.'/>',
    'award'       => '<circle cx="10" cy="8" r="5" '.$stroke.'/><path d="M7 12 l-2 5 l3-1 2 2 2-2 3 1-2-5" '.$stroke.'/>',
    'users'       => '<circle cx="8" cy="7" r="3" '.$stroke.'/><circle cx="14" cy="6" r="2.2" '.$stroke.'/><path d="M2 16 a4 4 0 0 1 12 0 M14 11 a4 4 0 0 1 4 5" '.$stroke.'/>',
    'arrow'       => '<path d="M4 10 H16 M12 6 L16 10 L12 14" '.$stroke.'/>',
    'sparkle'     => '<path d="M10 2 L11 8 L17 9 L11 10 L10 17 L9 10 L3 9 L9 8 z M16 3 L16.5 5 L18 5.5 L16.5 6 L16 8 L15.5 6 L14 5.5 L15.5 5 z" '.$stroke.'/>',
    'moderation'  => '<path d="M10 3 L17 6 V11 a7 7 0 0 1-7 6 a7 7 0 0 1-7-6 V6 z" '.$stroke.'/><path d="M7 10 L9 12 L13 8" '.$stroke.'/>',
    'calendar'    => '<rect x="3" y="5" width="14" height="12" rx="1.5" '.$stroke.'/><path d="M3 9 H17 M7 3 V6 M13 3 V6" '.$stroke.'/>',
    'logout'      => '<path d="M15 4 L19 10 L15 16 M19 10 H6 M4 4 h2 a2 2 0 0 1 2 2 v8 a2 2 0 0 1-2 2H4" '.$stroke.'/>',
];
@endphp
<svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 20 20" aria-hidden="true"@if($style) style="{{ $style }}"@endif>
    {!! $paths[$name] ?? '' !!}
</svg>
