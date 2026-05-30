@props(['poll'])
@php $userId = auth()->id(); $hasVoted = $poll->userHasVoted($userId); $total = $poll->total_votes; @endphp

<div x-data="{
    voted: {{ $hasVoted ? 'true' : 'false' }},
    options: {{ $poll->options->map(fn($o) => ['id'=>$o->id,'label'=>$o->label,'pct'=>$o->percentage,'mine'=>$o->isVotedByUser(auth()->id()),'votes'=>$o->votes_count])->toJson() }},
    async vote(optionId) {
        if(this.voted) return;
        const r = await fetch('{{ route('polls.vote', $poll) }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({option_id: optionId})
        });
        const d = await r.json();
        this.options = d.options;
        this.voted = true;
    }
}" style="width:100%;padding:12px;border-radius:10px;border:0.5px solid var(--line);background:var(--surface);display:flex;flex-direction:column;gap:6px;">
    <div style="font:600 12px/1.3 var(--f-ui);margin-bottom:2px">{{ $poll->question }}</div>
    <template x-for="o in options" :key="o.id">
        <div @click="vote(o.id)" style="position:relative;display:flex;align-items:center;padding:7px 10px;border-radius:8px;background:var(--surface-2);cursor:default;">
            <div style="position:absolute;inset:0;border-radius:8px;transition:width 0.4s cubic-bezier(0.2,0.7,0.3,1)"
                 :style="{width: o.pct+'%', background: o.mine ? 'var(--c-blue-soft)' : 'var(--surface-3)'}"></div>
            <span style="position:relative;font:500 11.5px/1 var(--f-ui)" :style="{color: o.mine ? '#2A3FB8' : 'var(--ink-2)'}">
                <span x-text="o.label"></span>
            </span>
            <span style="position:relative;margin-left:auto;font:500 11px/1 var(--f-mono);color:var(--ink-3)" x-text="o.pct+'%'"></span>
        </div>
    </template>
    <div style="font:400 10.5px/1 var(--f-ui);color:var(--ink-3);margin-top:4px">{{ $total }} votes · {{ $poll->days_remaining }}</div>
</div>
