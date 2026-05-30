<x-layouts.app title="Calendrier" :subtitle="now()->locale('fr')->isoFormat('MMMM YYYY')">
<style>
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:1px;background:var(--line)}
.cal-cell{background:var(--bg);min-height:96px;padding:6px 8px;display:flex;flex-direction:column;gap:3px;position:relative}
.cal-cell.other-month{background:var(--surface-2)}
.cal-cell.today .cal-day-num{background:var(--c-blue);color:#fff;border-radius:999px;width:22px;height:22px;display:flex;align-items:center;justify-content:center}
.cal-event-chip{padding:2px 6px;border-radius:4px;font:500 10px/1.4 var(--f-ui);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;cursor:default}
.cal-day-num{font:500 12px/1 var(--f-mono);color:var(--ink-3);width:22px;height:22px;display:flex;align-items:center;justify-content:center;flex-shrink:0}
</style>

<div style="padding:24px 32px 60px;height:100%;overflow:auto" class="scroll">

  {{-- Header row --}}
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
    <div>
      <span class="eyebrow">Calendrier scolaire</span>
      <h2 class="serif" style="font:400 34px/1.05 var(--f-display);margin:6px 0 0">
        {{ now()->locale('fr')->isoFormat('MMMM YYYY') }}
      </h2>
    </div>
    @if(auth()->user()->isDirector() || auth()->user()->isTeacher())
    <button class="btn btn-primary" x-data @click="document.getElementById('new-event-modal').showModal()">
      <x-ui.icon name="plus" size="12"/> Créer un événement
    </button>
    @endif
  </div>

  {{-- Day-of-week header --}}
  <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:1px;margin-bottom:1px">
    @foreach(['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'] as $d)
    <div style="padding:8px;text-align:center;font:500 10px/1 var(--f-mono);letter-spacing:.1em;text-transform:uppercase;color:var(--ink-3)">{{ $d }}</div>
    @endforeach
  </div>

  {{-- Month grid --}}
  @php
    $today     = now()->startOfDay();
    $startOfMonth = now()->startOfMonth();
    $endOfMonth   = now()->endOfMonth();
    // Monday-based grid: pad to previous Monday
    $gridStart = $startOfMonth->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
    $gridEnd   = $endOfMonth->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

    // Index events by date
    $eventsByDay = $events->groupBy(fn($e) => $e->starts_at->format('Y-m-d'));
  @endphp

  <div class="cal-grid" style="border:0.5px solid var(--line);border-radius:var(--r-md);overflow:hidden">
    @for($day = $gridStart->copy(); $day->lte($gridEnd); $day->addDay())
    @php
      $key      = $day->format('Y-m-d');
      $isToday  = $day->isSameDay($today);
      $isCurrent= $day->month === now()->month;
      $dayEvents= $eventsByDay[$key] ?? collect();
    @endphp
    <div class="cal-cell {{ !$isCurrent ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}">
      <div class="cal-day-num">{{ $day->day }}</div>
      @foreach($dayEvents->take(3) as $event)
      <div class="cal-event-chip"
           style="background:var(--c-{{ $event->color }}-soft);color:var(--c-{{ $event->color }})"
           title="{{ $event->title }}{{ $event->location ? ' · '.$event->location : '' }}">
        {{ $event->title }}
      </div>
      @endforeach
      @if($dayEvents->count() > 3)
      <div style="font:400 9.5px/1 var(--f-mono);color:var(--ink-3)">+{{ $dayEvents->count()-3 }} autres</div>
      @endif
    </div>
    @endfor
  </div>

  {{-- Upcoming events list --}}
  @php $upcoming = $events->filter(fn($e) => $e->starts_at->gte(now()))->sortBy('starts_at')->take(5); @endphp
  @if($upcoming->count())
  <div style="margin-top:28px">
    <span class="eyebrow" style="margin-bottom:12px;display:block">Prochains événements</span>
    <div style="display:flex;flex-direction:column;gap:10px;max-width:640px">
      @foreach($upcoming as $event)
      <div style="display:flex;gap:14px;padding:14px;border-radius:var(--r-md);background:var(--surface);border:0.5px solid var(--line);border-left:3px solid var(--c-{{ $event->color }})">
        <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;width:46px;height:46px;border-radius:10px;background:var(--c-{{ $event->color }}-soft);flex-shrink:0">
          <span style="font:600 17px/1 var(--f-ui);color:var(--c-{{ $event->color }})">{{ $event->starts_at->format('d') }}</span>
          <span style="font:500 8.5px/1 var(--f-mono);color:var(--c-{{ $event->color }});margin-top:2px;letter-spacing:.08em">{{ strtoupper($event->starts_at->locale('fr')->isoFormat('MMM')) }}</span>
        </div>
        <div style="flex:1;min-width:0">
          <div style="font:600 13.5px/1.3 var(--f-ui)">{{ $event->title }}</div>
          @if($event->location || $event->starts_at)
          <div style="font:400 11.5px/1 var(--f-ui);color:var(--ink-3);margin-top:4px">
            {{ $event->starts_at->format('H:i') }}{{ $event->location ? ' · '.$event->location : '' }}
          </div>
          @endif
          @if($event->description)<div style="font:400 12.5px/1.5 var(--f-ui);color:var(--ink-2);margin-top:6px">{{ $event->description }}</div>@endif
        </div>
        @if(auth()->user()->isDirector() || auth()->user()->id === $event->created_by)
        <form action="{{ route('events.destroy', $event) }}" method="POST" style="align-self:flex-start">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-ghost" style="height:26px;padding:0 8px;font-size:11px;color:var(--ink-3)">Supprimer</button>
        </form>
        @endif
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- Create event modal --}}
  @if(auth()->user()->isDirector() || auth()->user()->isTeacher())
  <dialog id="new-event-modal" style="border-radius:16px;border:0.5px solid var(--line);padding:28px;width:100%;max-width:480px;box-shadow:var(--sh-lg)">
    <h3 class="serif" style="font:400 24px/1 var(--f-display);margin:0 0 20px">Nouvel événement</h3>
    <form action="{{ route('events.store') }}" method="POST" style="display:flex;flex-direction:column;gap:14px">
      @csrf
      @if($errors->any())
      <div style="padding:10px 14px;border-radius:8px;background:var(--c-terracotta-soft);color:var(--c-terracotta);font:400 12.5px/1.5 var(--f-ui)">
        {{ $errors->first() }}
      </div>
      @endif
      <input name="title" placeholder="Titre de l'événement" required value="{{ old('title') }}"
             style="height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1 var(--f-ui);outline:none"/>
      <input name="starts_at" type="datetime-local" required value="{{ old('starts_at') }}"
             style="height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1 var(--f-ui);outline:none"/>
      <input name="location" placeholder="Lieu (optionnel)" value="{{ old('location') }}"
             style="height:42px;padding:0 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1 var(--f-ui);outline:none"/>
      <textarea name="description" placeholder="Description (optionnel)" rows="2"
                style="padding:10px 14px;border-radius:10px;border:0.5px solid var(--line-2);background:var(--surface);font:400 14px/1.5 var(--f-ui);resize:none">{{ old('description') }}</textarea>
      <div style="display:flex;gap:6px">
        @foreach(['blue'=>'Bleu','saffron'=>'Safran','atlas'=>'Vert','terracotta'=>'Terre'] as $val=>$lbl)
        <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font:400 12px/1 var(--f-ui);color:var(--ink-2)">
          <input type="radio" name="color" value="{{ $val }}" {{ old('color','blue')===$val?'checked':'' }} style="accent-color:var(--c-{{ $val }})"/>
          {{ $lbl }}
        </label>
        @endforeach
      </div>
      <div style="display:flex;gap:8px">
        <button type="submit" class="btn btn-primary">Créer</button>
        <button type="button" class="btn btn-ghost" onclick="document.getElementById('new-event-modal').close()">Annuler</button>
      </div>
    </form>
  </dialog>
  @endif
</div>
</x-layouts.app>
