<x-layouts.guest :title="'Majakker — La place publique numérique des écoles marocaines'">
<style>
@keyframes lp-fadeup{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
@keyframes lp-marquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
@keyframes lp-breathe{0%,100%{opacity:.55}50%{opacity:.85}}
@keyframes lp-float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}
.lp-root{--mj-purple:#7E5BEF;--mj-blue:#2563EB;--mj-ink:#14152B;--mj-ink-2:#3F4360;--mj-ink-3:#7A7E96;--mj-bg:#FAFAFC;--mj-gradient:linear-gradient(135deg,#7E5BEF 0%,#2563EB 100%);background:var(--mj-bg);color:var(--mj-ink);font-family:var(--f-ui);}
.mj-grad-text{background:var(--mj-gradient);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;font-style:italic}
.lp-nav{position:sticky;top:0;z-index:60;display:flex;align-items:center;gap:28px;padding:16px 56px;background:color-mix(in oklab,var(--mj-bg) 72%,transparent);backdrop-filter:saturate(180%) blur(18px);border-bottom:0.5px solid var(--line)}
.lp-cta{display:inline-flex;align-items:center;gap:8px;height:46px;padding:0 22px;border-radius:999px;background:var(--mj-gradient);color:#fff;border:0;cursor:pointer;font:500 13.5px/1 var(--f-ui);box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 8px 24px -6px rgba(94,57,224,.45);transition:transform .25s,filter .25s}
.lp-cta:hover{transform:translateY(-1px);filter:brightness(1.08)}
.lp-cta-ghost{background:var(--surface);color:var(--ink);border:0.5px solid var(--line-2);box-shadow:none}
.lp-cta-ghost:hover{border-color:var(--mj-purple);color:var(--mj-purple)}
.lp-marquee-wrap{overflow:hidden;-webkit-mask-image:linear-gradient(90deg,transparent,#000 10%,#000 90%,transparent);mask-image:linear-gradient(90deg,transparent,#000 10%,#000 90%,transparent)}
.lp-marquee-track{display:flex;gap:56px;white-space:nowrap;width:max-content;animation:lp-marquee 55s linear infinite}
.lp-card{position:relative;overflow:hidden;background:var(--surface);border:0.5px solid var(--line);border-radius:22px;padding:28px;display:flex;flex-direction:column;transition:transform .45s cubic-bezier(.2,.7,.3,1),box-shadow .45s,border-color .45s}
.lp-card:hover{transform:translateY(-4px);box-shadow:var(--sh-lg);border-color:var(--line-2)}
.lp-eyebrow{font:500 11px/1 var(--f-mono);letter-spacing:.22em;text-transform:uppercase;color:var(--ink-3);display:inline-flex;align-items:center;gap:12px}
.lp-eyebrow::before{content:'';width:28px;height:0.5px;background:var(--ink-3)}
.lp-display{font-family:var(--f-display);font-weight:400;letter-spacing:-.035em;line-height:.97}
.lp-float-a{animation:lp-float 7s ease-in-out infinite}
.lp-float-b{animation:lp-float 9s ease-in-out infinite .8s}
.lp-float-c{animation:lp-float 8s ease-in-out infinite .3s}
</style>

<div class="lp-root">
{{-- Nav --}}
<nav class="lp-nav">
    <a href="/" style="display:flex;align-items:center">
        <img src="{{ asset('images/logo.png') }}" alt="Majakker" style="height:42px;width:auto"/>
    </a>
    <div style="display:flex;gap:26px;margin-left:28px">
        @foreach(['Plateforme','Écoles','Élèves','Manifeste','Tarifs'] as $link)
        <a href="#" style="font:500 13px/1 var(--f-ui);color:var(--ink-2);text-decoration:none">{{ $link }}</a>
        @endforeach
    </div>
    <span style="flex:1"></span>
    <a href="{{ route('login') }}" style="font:500 13px/1 var(--f-ui);color:var(--ink-2);text-decoration:none">Connexion</a>
    <a href="{{ route('login') }}" class="lp-cta" style="height:40px;padding:0 18px;font-size:13px">Demander une démo <x-ui.icon name="arrow" size="13"/></a>
</nav>

{{-- Hero --}}
<section style="padding:110px 56px 110px;display:grid;grid-template-columns:1.15fr 1fr;gap:60px;align-items:center;position:relative;max-width:1440px;margin:0 auto">
    <div style="animation:lp-fadeup .8s cubic-bezier(.2,.7,.3,1) backwards .1s">
        <div class="lp-eyebrow" style="margin-bottom:20px">Majakker · Learn · Connect · Grow</div>
        <h1 class="lp-display" style="font-size:clamp(56px,8.4vw,108px);margin:0 0 28px">
            La place publique<br>numérique des<br>écoles <span class="mj-grad-text">marocaines</span>.
        </h1>
        <p style="font:400 17px/1.55 var(--f-ui);color:var(--ink-2);max-width:520px;margin:0 0 36px">
            Majakker réunit élèves, enseignants et directions dans un espace social calme et productif. Pensé au Maroc, hébergé au Maroc.
        </p>
        <div style="display:flex;gap:12px;margin-bottom:56px">
            <a href="{{ route('login') }}" class="lp-cta">Demander une démo <x-ui.icon name="arrow" size="13"/></a>
            <a href="#" class="lp-cta lp-cta-ghost">Voir une école en direct</a>
        </div>
        <div style="display:flex;gap:36px;padding-top:28px;border-top:0.5px solid var(--line)">
            @foreach([['142','écoles partenaires'],['86 K','élèves actifs'],['4 villes','Casa · Rabat · Marrakech · Tanger']] as [$n,$l])
            <div>
                <div class="lp-display" style="font-size:28px">{{ $n }}</div>
                <div style="font:400 11.5px/1.4 var(--f-ui);color:var(--ink-3);margin-top:6px">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Floating UI preview --}}
    <div style="position:relative;height:580px;animation:lp-fadeup .8s cubic-bezier(.2,.7,.3,1) backwards .2s">
        <div class="lp-float-a card" style="position:absolute;top:60px;left:0;width:360px;height:440px;border-radius:18px;box-shadow:var(--sh-lg);overflow:hidden">
            <div style="padding:10px 14px;border-bottom:0.5px solid var(--line);display:flex;align-items:center;gap:8px">
                <span style="width:8px;height:8px;border-radius:999px;background:var(--c-terracotta)"></span>
                <span style="width:8px;height:8px;border-radius:999px;background:var(--c-saffron)"></span>
                <span style="width:8px;height:8px;border-radius:999px;background:var(--c-atlas)"></span>
                <span style="flex:1"></span>
                <span class="eyebrow" style="font-size:9px">FIL · MAJAKKER</span>
            </div>
            <div style="padding:14px;display:flex;flex-direction:column;gap:14px">
                @foreach($previewPosts as $p)
                <div style="display:grid;grid-template-columns:26px 1fr;gap:10px">
                    <x-ui.avatar :name="$p->user->name" size="26"/>
                    <div>
                        <div style="font:600 11.5px/1 var(--f-ui)">{{ $p->user->name }}</div>
                        @if($p->title)<div style="font:600 12px/1.3 var(--f-ui);margin-top:3px">{{ $p->title }}</div>@endif
                        <div style="font:400 11.5px/1.5 var(--f-ui);color:var(--ink-2);margin-top:3px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">{{ $p->body }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="lp-float-b card-glass" style="position:absolute;top:0;right:30px;padding:14px 16px;border-radius:16px;width:200px">
            <span class="eyebrow" style="font-size:9.5px">Engagement · S20</span>
            <div style="display:flex;align-items:baseline;gap:6px;margin-top:6px">
                <span class="lp-display" style="font-size:32px">72%</span>
                <span style="font:500 10px/1 var(--f-mono);color:var(--c-atlas)">+4 PTS</span>
            </div>
        </div>
        <div class="lp-float-c" style="position:absolute;bottom:0;right:0;width:210px;height:400px;border-radius:30px;background:var(--ink);padding:6px;box-shadow:var(--sh-lg)">
            <div style="width:100%;height:100%;border-radius:24px;background:var(--surface-2);overflow:hidden;display:flex;flex-direction:column">
                <div style="padding:28px 14px 8px;font:500 11px/1 var(--f-ui)">14:21</div>
                <div style="padding:0 14px;flex:1">
                    <div class="serif" style="font:400 22px/1.05 var(--f-display)">Bonjour<br>Yasmine.</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- School marquee --}}
<section style="padding:32px 0;border-top:0.5px solid var(--line);border-bottom:0.5px solid var(--line);background:var(--surface)">
    <div style="text-align:center;margin-bottom:22px"><span class="lp-eyebrow">Présent dans 142 écoles à travers le Royaume</span></div>
    <div class="lp-marquee-wrap">
        <div class="lp-marquee-track">
            @foreach(array_merge($schools->toArray(),$schools->toArray()) as $i => $school)
            <span class="lp-display" style="font-size:22px;color:{{ $i%5===2 ? 'var(--c-blue)' : 'var(--ink-3)' }}">{{ $school }}</span>
            @endforeach
        </div>
    </div>
</section>

{{-- Features --}}
<section style="padding:80px 56px;max-width:1440px;margin:0 auto">
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px">
        @foreach([['Calme par défaut','Pas d\'algorithme à scroller à l\'infini. Le fil suit la cadence de l\'école.','moon','blue'],['Pédagogique d\'abord','Devoirs, polycopiés, sondages, fichiers de classe. Tous les outils dans un seul fil.','book','saffron'],['Modération IA bilingue','Une IA entraînée sur l\'arabe marocain. Comprend les nuances, repère les abus.','moderation','atlas']] as [$t,$b,$icon,$tone])
        <div class="lp-card">
            <div style="width:44px;height:44px;border-radius:12px;background:var(--c-{{ $tone }}-soft);color:var(--c-{{ $tone }});display:flex;align-items:center;justify-content:center;margin-bottom:18px">
                <x-ui.icon name="{{ $icon }}" size="20"/>
            </div>
            <h3 class="lp-display" style="font-size:28px;margin:0 0 12px">{{ $t }}</h3>
            <p style="font:400 13px/1.55 var(--f-ui);color:var(--ink-2);margin:0">{{ $b }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- Final CTA --}}
<section style="padding:0 56px 80px;max-width:1440px;margin:0 auto">
    <div style="position:relative;background:var(--mj-ink,#14152B);color:#fff;border-radius:28px;padding:84px 64px;overflow:hidden">
        <div style="position:absolute;top:-120px;right:-120px;width:460px;height:460px;background:var(--mj-gradient);border-radius:50%;filter:blur(60px);opacity:.6;pointer-events:none"></div>
        <div style="position:relative;display:grid;grid-template-columns:1.4fr 1fr;gap:56px;align-items:center">
            <div>
                <span class="lp-eyebrow" style="color:rgba(250,247,242,.6)">Pour les directions</span>
                <h2 class="lp-display" style="font-size:clamp(40px,5vw,64px);margin:20px 0 22px">
                    Donnez à votre école<br>un <span style="font-style:italic;background:linear-gradient(135deg,#C7B6FF,#93C5FD);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">espace numérique</span><br>digne d'elle.
                </h2>
                <p style="font:400 15px/1.65 var(--f-ui);color:rgba(250,247,242,.72);max-width:520px;margin:0">Aucune carte bancaire. Migration accompagnée. Démo en 30 minutes.</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px">
                <a href="{{ route('login') }}" class="lp-cta" style="height:52px;font-size:14px;background:#fff;color:var(--mj-ink,#14152B)">Réserver une démo <x-ui.icon name="arrow" size="14"/></a>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer style="padding:60px 56px 40px;border-top:0.5px solid var(--line);max-width:1440px;margin:0 auto">
    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:24px;border-top:0.5px solid var(--line)">
        <span style="font:400 11px/1 var(--f-mono);letter-spacing:.08em;color:var(--ink-3)">MAJAKKER © {{ date('Y') }} — RABAT, MAROC</span>
        <span style="font:400 11px/1 var(--f-mono);letter-spacing:.08em;color:var(--ink-4)">v.2026.05 · BETA</span>
    </div>
</footer>
</div>
</x-layouts.guest>
