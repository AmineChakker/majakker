<x-layouts.app :title="'Messages'" :subtitle="$user->name">
<div style="display:flex;flex-direction:column;height:100%">
  <div style="flex:1;overflow:auto;padding:20px 24px;display:flex;flex-direction:column;gap:12px" class="scroll">
    @foreach($messages as $msg)
    @php $mine = $msg->sender_id === auth()->id(); @endphp
    <div style="display:flex;justify-content:{{ $mine?'flex-end':'flex-start' }};gap:10px">
      @if(!$mine)<x-ui.avatar :name="$user->name" size="28"/>@endif
      <div style="max-width:60%;padding:10px 14px;border-radius:{{ $mine?'14px 14px 4px 14px':'14px 14px 14px 4px' }};background:{{ $mine?'var(--c-blue)':'var(--surface)' }};color:{{ $mine?'#fff':'var(--ink)' }};border:0.5px solid var(--line);font:400 13px/1.5 var(--f-ui)">
        {{ $msg->body }}
      </div>
    </div>
    @endforeach
  </div>
  <div style="border-top:0.5px solid var(--line);padding:14px 24px">
    <form action="{{ route('messages.send', $user) }}" method="POST" style="display:flex;gap:10px">
      @csrf
      <input name="body" placeholder="Votre message…" required style="flex:1;height:40px;padding:0 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1 var(--f-ui);outline:none"/>
      <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
  </div>
</div>
</x-layouts.app>
