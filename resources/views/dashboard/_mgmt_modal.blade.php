{{-- Reusable CRUD modal. Props: title_create, title_edit, store_route, fields (partial path) --}}
<div x-show="modal.open" x-cloak
     style="position:fixed;inset:0;z-index:100;display:flex;align-items:center;justify-content:center;padding:20px"
     @click.self="modal.open=false">
  <div style="position:absolute;inset:0;background:rgba(0,0,0,.35);backdrop-filter:blur(4px)" @click="modal.open=false"></div>
  <div style="position:relative;width:100%;max-width:520px;background:var(--surface);border-radius:16px;border:0.5px solid var(--line);box-shadow:0 32px 80px -20px rgba(20,21,43,.22);overflow:hidden"
       x-transition:enter="transition ease-out duration-150"
       x-transition:enter-start="opacity-0 scale-95"
       x-transition:enter-end="opacity-100 scale-100">

    <div style="display:flex;align-items:center;gap:12px;padding:20px 22px;border-bottom:0.5px solid var(--line)">
      <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,#7E5BEF,#2563EB);display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round">
          <template x-if="!modal.editing"><path d="M10 4v12M4 10h12"/></template>
          <template x-if="modal.editing"><path d="M4 13l1.5-1.5L13 4l3 3-7.5 7.5L7 16H4v-3z"/></template>
        </svg>
      </div>
      <h3 style="font:400 20px/1 var(--f-display);margin:0" x-text="modal.editing ? '{{ $title_edit }}' : '{{ $title_create }}'"></h3>
      <button @click="modal.open=false" style="margin-left:auto;width:26px;height:26px;border-radius:7px;border:0.5px solid var(--line-2);background:var(--surface-2);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--ink-3)">
        <svg width="10" height="10" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M4 4l12 12M16 4L4 16"/></svg>
      </button>
    </div>

    <div style="padding:20px 22px 22px">
      <template x-if="!modal.editing">
        <form action="{{ $store_route }}" method="POST" style="display:flex;flex-direction:column;gap:14px">
          @csrf
          @include($fields)
          <div style="display:flex;gap:8px;margin-top:4px">
            <button type="submit" class="dir-btn-primary" style="flex:1;justify-content:center">Créer</button>
            <button type="button" @click="modal.open=false" class="dir-btn-ghost">Annuler</button>
          </div>
        </form>
      </template>
      <template x-if="modal.editing">
        <form :action="updateRoute()" method="POST" style="display:flex;flex-direction:column;gap:14px">
          @csrf
          <input type="hidden" name="_method" value="PATCH"/>
          @include($fields)
          <div style="display:flex;gap:8px;margin-top:4px">
            <button type="submit" class="dir-btn-primary" style="flex:1;justify-content:center">Enregistrer</button>
            <button type="button" @click="modal.open=false" class="dir-btn-ghost">Annuler</button>
          </div>
        </form>
      </template>
    </div>
  </div>
</div>
