@php
$user = auth()->user();
$placeholder = match($user->role) {
    'teacher'  => 'Annoncez un cours, un devoir, une ressource…',
    'director' => 'Parlez à toute l\'école Majakker…',
    'admin'    => 'Note interne ou annonce système…',
    default    => 'Partagez une question, une pensée…',
};
$visLabel = match($user->role) {
    'director' => 'VISIBLE PAR TOUTE L\'ÉCOLE',
    'teacher'  => 'VISIBLE PAR VOS CLASSES',
    default    => 'VISIBLE PAR VOTRE PROMO',
};
@endphp

<div style="display:grid;grid-template-columns:32px 1fr;gap:12px;padding:14px 18px;border-bottom:0.5px solid var(--line);background:var(--surface);"
     x-data="{ text: '', submitting: false }">
    <x-ui.avatar :name="$user->name" :avatar="$user->avatar_path ? Storage::url($user->avatar_path) : null" size="28"/>
    <div style="display:flex;flex-direction:column;gap:8px">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <textarea name="body" x-model="text" placeholder="{{ $placeholder }}" rows="2"
                      style="width:100%;resize:none;appearance:none;border:0;background:transparent;font:400 13.5px/1.55 var(--f-ui);color:var(--ink);outline:none;padding:0;min-height:40px"
                      required></textarea>
            <div style="display:flex;align-items:center;gap:4px;margin-left:-6px">
                <button type="button" class="btn btn-ghost" style="height:26px;padding:0 8px"><x-ui.icon name="img" size="14"/></button>
                <button type="button" class="btn btn-ghost" style="height:26px;padding:0 8px"><x-ui.icon name="paperclip" size="14"/></button>
                <button type="button" class="btn btn-ghost" style="height:26px;padding:0 8px"><x-ui.icon name="poll" size="14"/></button>
                <button type="button" class="btn btn-ghost" style="height:26px;padding:0 8px"><x-ui.icon name="smile" size="14"/></button>
                <span style="flex:1"></span>
                <span style="font:400 10.5px/1 var(--f-mono);color:var(--ink-4);margin-right:8px">{{ $visLabel }}</span>
                <button type="submit" class="btn" :class="text.trim() ? 'btn-primary' : ''"
                        :disabled="!text.trim() || submitting" :style="text.trim() ? 'opacity:1' : 'opacity:0.5'"
                        @click="submitting=true">Publier</button>
            </div>
        </form>
    </div>
</div>
