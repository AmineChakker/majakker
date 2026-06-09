@props(['groupId' => null])
@php
$user = auth()->user();
$placeholder = match($user->role) {
    'teacher'  => 'Annoncez un cours, un devoir, une ressource…',
    'director' => 'Parlez à toute l\'école…',
    default    => 'Partagez une question, une pensée, une découverte…',
};
@endphp

<div x-data="composer('{{ route('attachments.store') }}', '{{ route('posts.store') }}', {{ $groupId ?? 'null' }})"
     @dragover.prevent="dragging=true;expand()"
     @dragleave.prevent="dragging=false"
     @drop.prevent="handleDrop($event)"
     style="background:var(--surface);transition:background .18s"
     :style="dragging ? 'background:var(--c-blue-soft)' : ''">

  {{-- ── Form (reference only, submission is via doSubmit) ── --}}
  <form x-ref="form" action="{{ route('posts.store') }}" method="POST">
    @csrf
  </form>

  <div style="display:grid;grid-template-columns:34px 1fr;gap:12px;padding:14px 18px;">
    <x-ui.avatar :name="$user->name" size="34"/>

    <div style="display:flex;flex-direction:column;gap:10px">

      {{-- Textarea --}}
      <textarea x-model="text" @focus="expand()"
                placeholder="{{ $placeholder }}" rows="1"
                :rows="expanded ? 3 : 1"
                style="width:100%;resize:none;border:0;background:transparent;font:400 14px/1.6 var(--f-ui);color:var(--ink);outline:none;padding:0;min-height:38px;box-sizing:border-box;transition:rows .2s"></textarea>

      {{-- ── Post error ── --}}
      <div x-show="postError" x-cloak
           style="padding:10px 14px;border-radius:9px;background:rgba(220,38,38,.08);border:0.5px solid rgba(220,38,38,.2);color:#B91C1C;font:400 12.5px/1.4 var(--f-ui)"
           x-text="postError"></div>

      {{-- ── Attachment previews ── --}}
      <template x-if="attachments.length > 0">
        <div :style="attachments.filter(a=>a.isImage||a.isVideo).length > 1 ? 'display:grid;grid-template-columns:1fr 1fr;gap:6px' : 'display:flex;flex-direction:column;gap:6px'">
          <template x-for="att in attachments" :key="att.tempId">
            <div style="position:relative;border-radius:10px;overflow:hidden;border:0.5px solid var(--line)">

              {{-- Error state --}}
              <template x-if="att.error">
                <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 12px;background:rgba(220,38,38,.05)">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="#B91C1C" stroke-width="1.4" stroke-linecap="round" style="flex-shrink:0;margin-top:1px"><circle cx="10" cy="10" r="7"/><path d="M10 7v4M10 13.5v.5"/></svg>
                  <div style="flex:1;min-width:0">
                    <div style="font:500 11.5px/1.2 var(--f-ui);color:#B91C1C;word-break:break-word" x-text="att.error"></div>
                    <div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:3px" x-text="att.name"></div>
                  </div>
                </div>
              </template>

              {{-- Image preview --}}
              <template x-if="!att.error && att.isImage && att.url">
                <img :src="att.url" :alt="att.name" style="width:100%;max-height:240px;object-fit:cover;display:block"/>
              </template>

              {{-- Video preview --}}
              <template x-if="!att.error && att.isVideo && att.url">
                <video :src="att.url" controls preload="metadata" style="width:100%;max-height:240px;display:block;background:#000"></video>
              </template>

              {{-- File preview --}}
              <template x-if="!att.error && !att.isImage && !att.isVideo">
                <div style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--surface-2)">
                  <div style="width:36px;height:36px;border-radius:8px;background:var(--c-blue-soft);color:var(--c-blue);display:flex;align-items:center;justify-content:center;font:700 9.5px/1 var(--f-mono);flex-shrink:0"
                       x-text="(att.name.split('.').pop() || 'FILE').toUpperCase()"></div>
                  <div style="flex:1;min-width:0">
                    <div style="font:500 12px/1.2 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap" x-text="att.name"></div>
                    <div style="font:400 10px/1 var(--f-mono);color:var(--ink-3);margin-top:3px" x-text="att.size"></div>
                  </div>
                </div>
              </template>

              {{-- Upload spinner overlay --}}
              <template x-if="att.uploading">
                <div style="position:absolute;inset:0;background:rgba(0,0,0,.38);display:flex;align-items:center;justify-content:center">
                  <svg width="30" height="30" viewBox="0 0 30 30" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round"
                       style="animation:spin 1s linear infinite">
                    <circle cx="15" cy="15" r="12" stroke-opacity=".25"/>
                    <path d="M15 3 a12 12 0 0 1 12 12"/>
                  </svg>
                </div>
              </template>

              {{-- Remove button --}}
              <button type="button" @click="removeAttachment(att.tempId)"
                      :style="att.isImage||att.isVideo ? 'position:absolute;top:6px;right:6px;background:rgba(0,0,0,.5)' : 'position:absolute;top:8px;right:10px;background:transparent'"
                      style="width:22px;height:22px;border-radius:999px;border:0;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff">
                <svg width="10" height="10" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round">
                  <path d="M4 4l12 12M16 4L4 16"/>
                </svg>
              </button>
            </div>
          </template>
        </div>
      </template>

      {{-- Drag hint --}}
      <template x-if="dragging">
        <div style="display:flex;align-items:center;justify-content:center;gap:10px;padding:20px;border:2px dashed var(--c-blue);border-radius:10px;color:var(--c-blue);font:500 13px/1 var(--f-ui);background:var(--c-blue-soft)">
          <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M10 3v10M5 8l5-5 5 5"/><path d="M3 17h14"/></svg>
          Déposez vos fichiers ici
        </div>
      </template>

      {{-- ── Toolbar (expanded) ── --}}
      <template x-if="expanded">
        <div style="display:flex;align-items:center;gap:2px;margin-left:-7px">

          {{-- Photo --}}
          <button type="button" @click="triggerFile('image/*')"
                  style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:30px;padding:0 9px;border-radius:8px;color:var(--ink-3);cursor:pointer;font:500 11.5px/1 var(--f-ui);transition:all .14s"
                  @mouseenter="$el.style.background='var(--surface-2)';$el.style.color='var(--c-blue)'" @mouseleave="$el.style.background='';$el.style.color='var(--ink-3)'">
            <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
              <rect x="2" y="3" width="16" height="14" rx="2"/><circle cx="6.5" cy="7.5" r="1.5"/><path d="m2 14 4-4 3 3 2-2 4 4"/>
            </svg>
            Photo
          </button>

          {{-- Vidéo --}}
          <button type="button" @click="triggerFile('video/*')"
                  style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:30px;padding:0 9px;border-radius:8px;color:var(--ink-3);cursor:pointer;font:500 11.5px/1 var(--f-ui);transition:all .14s"
                  @mouseenter="$el.style.background='var(--surface-2)';$el.style.color='var(--c-terracotta)'" @mouseleave="$el.style.background='';$el.style.color='var(--ink-3)'">
            <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
              <rect x="2" y="5" width="12" height="10" rx="1.5"/><path d="M14 8l4-2v8l-4-2"/>
            </svg>
            Vidéo
          </button>

          {{-- Fichier --}}
          <button type="button" @click="triggerFile('.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip')"
                  style="appearance:none;border:0;background:transparent;display:inline-flex;align-items:center;gap:5px;height:30px;padding:0 9px;border-radius:8px;color:var(--ink-3);cursor:pointer;font:500 11.5px/1 var(--f-ui);transition:all .14s"
                  @mouseenter="$el.style.background='var(--surface-2)';$el.style.color='var(--c-atlas)'" @mouseleave="$el.style.background='';$el.style.color='var(--ink-3)'">
            <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
              <path d="M5 2h7l4 4v12a1 1 0 01-1 1H5a1 1 0 01-1-1V3a1 1 0 011-1z"/><path d="M12 2v5h5"/><path d="M7 9h6M7 12h6M7 15h4"/>
            </svg>
            Fichier
          </button>

          <span style="flex:1"></span>

          {{-- Char counter --}}
          <span x-show="charCount > 0"
                :style="charCount > 4500 ? 'color:#B91C1C' : charCount > 4000 ? 'color:#B45309' : 'color:var(--ink-4)'"
                style="font:400 10.5px/1 var(--f-mono);margin-right:8px"
                x-text="`${charCount}/5000`"></span>

          {{-- Upload indicator --}}
          <template x-if="uploading">
            <span style="display:inline-flex;align-items:center;gap:5px;font:400 11px/1 var(--f-ui);color:var(--ink-3);margin-right:8px">
              <svg width="12" height="12" viewBox="0 0 30 30" fill="none" stroke="var(--ink-3)" stroke-width="2.5" stroke-linecap="round"
                   style="animation:spin 1s linear infinite">
                <circle cx="15" cy="15" r="12" stroke-opacity=".25"/><path d="M15 3 a12 12 0 0 1 12 12"/>
              </svg>
              Chargement…
            </span>
          </template>

          {{-- Publish --}}
          <button type="button" @click="doSubmit()"
                  :disabled="!canSubmit"
                  :style="canSubmit ? 'opacity:1;cursor:pointer;background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff' : 'opacity:0.4;cursor:not-allowed;background:var(--surface-3);color:var(--ink-3)'"
                  style="height:32px;padding:0 18px;border-radius:9px;border:0;font:600 12.5px/1 var(--f-ui);transition:opacity .18s,filter .18s;display:inline-flex;align-items:center;gap:6px"
                  @mouseenter="canSubmit && ($el.style.filter='brightness(1.1)')" @mouseleave="$el.style.filter=''">
            <template x-if="posting">
              <svg width="12" height="12" viewBox="0 0 30 30" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                   style="animation:spin 1s linear infinite">
                <circle cx="15" cy="15" r="12" stroke-opacity=".3"/><path d="M15 3 a12 12 0 0 1 12 12"/>
              </svg>
            </template>
            Publier
          </button>
        </div>
      </template>

      {{-- ── Toolbar (collapsed) ── --}}
      <template x-if="!expanded">
        <div style="display:flex;align-items:center;gap:2px;margin-left:-7px">
          <button type="button" @click="triggerFile('image/*');expand()"
                  style="appearance:none;border:0;background:transparent;height:28px;width:32px;border-radius:7px;color:var(--ink-3);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .14s"
                  @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background=''">
            <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
              <rect x="2" y="3" width="16" height="14" rx="2"/><circle cx="6.5" cy="7.5" r="1.5"/><path d="m2 14 4-4 3 3 2-2 4 4"/>
            </svg>
          </button>
          <button type="button" @click="triggerFile('video/*');expand()"
                  style="appearance:none;border:0;background:transparent;height:28px;width:32px;border-radius:7px;color:var(--ink-3);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .14s"
                  @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background=''">
            <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
              <rect x="2" y="5" width="12" height="10" rx="1.5"/><path d="M14 8l4-2v8l-4-2"/>
            </svg>
          </button>
          <button type="button" @click="triggerFile('.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip');expand()"
                  style="appearance:none;border:0;background:transparent;height:28px;width:32px;border-radius:7px;color:var(--ink-3);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .14s"
                  @mouseenter="$el.style.background='var(--surface-2)'" @mouseleave="$el.style.background=''">
            <svg width="15" height="15" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round">
              <path d="M5 2h7l4 4v12a1 1 0 01-1 1H5a1 1 0 01-1-1V3a1 1 0 011-1z"/><path d="M12 2v5h5"/>
            </svg>
          </button>
          <span style="flex:1"></span>
          <button type="button" @click="doSubmit()" :disabled="!canSubmit"
                  :style="canSubmit ? 'opacity:1;cursor:pointer' : 'opacity:0.38;cursor:not-allowed'"
                  style="height:28px;padding:0 16px;border-radius:8px;border:0;background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff;font:600 12px/1 var(--f-ui)">
            Publier
          </button>
        </div>
      </template>

    </div>
  </div>
</div>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
