<x-layouts.guest :title="'UNIVERCONNECT — Connexion'">
<style>
@keyframes lg-fadeup{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
.lg-root{--mj-purple:#7E5BEF;--mj-blue:#2563EB;--mj-ink:#14152B;--mj-ink-2:#3F4360;--mj-ink-3:#7A7E96;--mj-bg:#FAFAFC;--mj-surface:#FFFFFF;--mj-line:rgba(20,21,43,.08);--mj-line-2:rgba(20,21,43,.14);--mj-gradient:linear-gradient(135deg,#7E5BEF 0%,#2563EB 100%);display:grid;grid-template-columns:1fr 1fr;height:100vh;min-height:100vh;background:var(--mj-bg)}
.lg-hero{position:relative;background:#14152B;display:flex;flex-direction:column;padding:48px 56px;overflow:hidden}
.lg-form-pane{position:relative;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px;background:var(--mj-bg);overflow:auto}
.lg-form{width:100%;max-width:420px;display:flex;flex-direction:column;gap:20px;animation:lg-fadeup .7s cubic-bezier(.2,.7,.3,1) backwards .15s}
.lg-title{font:400 52px/.97 var(--f-display);letter-spacing:-.03em;color:var(--mj-ink);margin:0}
.mj-grad-text{background:var(--mj-gradient);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;font-style:italic}
.lg-sso-btn{display:inline-flex;align-items:center;justify-content:center;gap:10px;height:42px;padding:0 16px;border-radius:10px;background:var(--mj-surface);border:0.5px solid var(--mj-line-2);font:500 13px/1 var(--f-ui);color:var(--mj-ink);cursor:pointer;transition:all .18s;width:100%}
.lg-sso-btn:hover{border-color:var(--mj-purple);color:var(--mj-purple);transform:translateY(-1px)}
.lg-or{display:grid;grid-template-columns:1fr auto 1fr;align-items:center;gap:14px;font:400 11px/1 var(--f-mono);color:var(--mj-ink-3);letter-spacing:.14em;text-transform:uppercase}
.lg-or::before,.lg-or::after{content:'';height:0.5px;background:var(--mj-line-2)}
.lg-input{width:100%;height:46px;padding:0 14px 0 42px;border-radius:10px;border:0.5px solid var(--mj-line-2);background:var(--mj-surface);font:400 14px/1 var(--f-ui);color:var(--mj-ink);outline:none;transition:border-color .2s,box-shadow .2s;box-sizing:border-box}
.lg-input:focus{border-color:var(--mj-purple);box-shadow:0 0 0 3px rgba(126,91,239,.12)}
.lg-submit{width:100%;height:50px;border-radius:12px;background:var(--mj-gradient);color:#fff;border:none;cursor:pointer;font:500 14px/1 var(--f-ui);display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 1px 0 rgba(255,255,255,.22) inset,0 8px 24px -6px rgba(94,57,224,.45);transition:transform .2s,filter .2s}
.lg-submit:hover{transform:translateY(-1px);filter:brightness(1.08)}
.lg-input-wrap{position:relative}
.lg-input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--mj-ink-3);pointer-events:none;display:flex;align-items:center}
@media(max-width:860px){.lg-root{grid-template-columns:1fr}.lg-hero{display:none}}
</style>

<div class="lg-root">
{{-- Left hero --}}
<aside class="lg-hero">
    <div style="position:absolute;top:-140px;right:-140px;width:480px;height:480px;background:linear-gradient(135deg,#7E5BEF,#2563EB);border-radius:50%;filter:blur(60px);opacity:.55;pointer-events:none"></div>
    <div style="position:absolute;bottom:-80px;left:-80px;width:360px;height:360px;background:linear-gradient(135deg,#8B5CF6,#0EA5E9);border-radius:50%;filter:blur(60px);opacity:.32;pointer-events:none"></div>

    <a href="/" style="position:relative;display:flex;align-items:center;text-decoration:none">
        <img src="{{ asset('images/logo.png') }}" alt="UNIVERCONNECT" style="height:44px;width:auto"/>
    </a>

    <div style="position:relative;flex:1;display:flex;flex-direction:column;justify-content:center;max-width:480px;animation:lg-fadeup .8s cubic-bezier(.2,.7,.3,1) backwards .1s">
        <div style="font:500 11px/1 var(--f-mono);letter-spacing:.22em;text-transform:uppercase;color:rgba(250,247,242,.55);display:inline-flex;align-items:center;gap:12px;margin-bottom:22px">
            <span style="width:28px;height:0.5px;background:rgba(250,247,242,.4)"></span>
            UNIVERCONNECT · La plateforme scolaire marocaine
        </div>
        <h1 style="font:400 clamp(44px,5.8vw,72px)/.97 var(--f-display);letter-spacing:-.03em;margin:0 0 28px;color:#fff">
            L'école <span class="mj-grad-text">enfin</span><br>connectée.
        </h1>
        <p style="font:400 15px/1.65 var(--f-ui);color:rgba(250,247,242,.68);margin:0 0 48px;max-width:420px">
            Fil d'actualité, cours, clubs, devoirs — tout l'espace numérique de votre école, pensé au Maroc.
        </p>
        <div style="display:flex;gap:32px;padding-top:28px;border-top:0.5px solid rgba(250,247,242,.14)">
            @foreach([['142','écoles'],['86 K','élèves actifs'],['4 villes','au Maroc']] as [$n,$l])
            <div>
                <div style="font:400 28px/1 var(--f-display);letter-spacing:-.03em;color:#fff">{{ $n }}</div>
                <div style="font:400 11.5px/1.4 var(--f-ui);color:rgba(250,247,242,.55);margin-top:6px">{{ $l }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <div style="position:relative;margin-top:60px;padding:28px 32px;background:rgba(255,255,255,.06);border:0.5px solid rgba(255,255,255,.12);border-radius:18px;animation:lg-fadeup .9s cubic-bezier(.2,.7,.3,1) backwards .3s">
        <div style="font:400 80px/.7 var(--f-display);font-style:italic;background:linear-gradient(135deg,#7E5BEF,#2563EB);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;position:absolute;top:12px;left:24px;pointer-events:none">«</div>
        <p style="font:400 17px/1.5 var(--f-display);font-style:italic;color:#fff;margin:28px 0 22px">
            Pour la première fois, l'école parle d'<span style="background:linear-gradient(135deg,#C7B6FF,#93C5FD);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent">une seule voix</span>.
        </p>
        <div style="display:flex;align-items:center;gap:12px">
            <x-ui.avatar name="Najat Tazi" size="36"/>
            <div>
                <div style="font:500 12.5px/1.3 var(--f-ui);color:#fff">Najat Tazi</div>
                <div style="font:400 11px/1.3 var(--f-ui);color:rgba(250,247,242,.62)">Directrice — Lycée Majakker, Casablanca</div>
            </div>
        </div>
    </div>
</aside>

{{-- Right form --}}
<main class="lg-form-pane">
    <form class="lg-form" method="POST" action="{{ route('login') }}">
        @csrf
        <div style="display:flex;flex-direction:column;gap:8px">
            <span style="font:500 11px/1 var(--f-mono);letter-spacing:.22em;text-transform:uppercase;color:var(--mj-ink-3);display:inline-flex;align-items:center;gap:10px"><span style="width:22px;height:0.5px;background:var(--mj-ink-3)"></span>Connexion · Sécurisée</span>
            <h1 class="lg-title">Bon <span class="mj-grad-text">retour</span>.</h1>
            <p style="font:400 14px/1.55 var(--f-ui);color:var(--mj-ink-2);margin:0">Connectez-vous à votre espace UNIVERCONNECT.</p>
        </div>

        @if(session('status'))
        <div style="padding:10px 14px;border-radius:8px;background:var(--c-atlas-soft);color:#2D6B61;font:500 13px/1.4 var(--f-ui)">{{ session('status') }}</div>
        @endif

        {{-- <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <button type="button" class="lg-sso-btn">
                <svg width="17" height="17" viewBox="0 0 18 18"><path d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844a4.14 4.14 0 0 1-1.796 2.716v2.259h2.908C16.658 14.376 17.64 12.069 17.64 9.2z" fill="#4285F4"/><path d="M9 18c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z" fill="#34A853"/><path d="M3.964 10.71A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.71V4.958H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.042l3.007-2.332z" fill="#FBBC05"/><path d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.958L3.964 7.29C4.672 5.163 6.656 3.58 9 3.58z" fill="#EA4335"/></svg>
                Google
            </button>
            <button type="button" class="lg-sso-btn">
                <svg width="16" height="16" viewBox="0 0 21 21"><rect x="1" y="1" width="9" height="9" fill="#F25022"/><rect x="11" y="1" width="9" height="9" fill="#7FBA00"/><rect x="1" y="11" width="9" height="9" fill="#00A4EF"/><rect x="11" y="11" width="9" height="9" fill="#FFB900"/></svg>
                Microsoft
            </button>
        </div> 

        <div class="lg-or">ou par email</div>--}}

        <div style="display:flex;flex-direction:column;gap:8px">
            <label style="font:500 12.5px/1 var(--f-ui);color:var(--mj-ink-2)" for="email">Adresse email</label>
            <div class="lg-input-wrap">
                <span class="lg-input-icon"><svg width="16" height="16" viewBox="0 0 20 20" fill="none"><rect x="2" y="4" width="16" height="12" rx="2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.5 5.5 L10 11.5 L17.5 5.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="prenom.nom@domaine.ma" class="lg-input"/>
            </div>
            @error('email')<p style="font:400 12px/1 var(--f-ui);color:var(--c-terracotta);margin:4px 0 0">{{ $message }}</p>@enderror
        </div>

        <div style="display:flex;flex-direction:column;gap:8px">
            <div style="display:flex;align-items:center;justify-content:space-between">
                <label style="font:500 12.5px/1 var(--f-ui);color:var(--mj-ink-2)" for="password">Mot de passe</label>
                @if(Route::has('password.request'))<a href="{{ route('password.request') }}" style="font:500 12px/1 var(--f-ui);color:var(--mj-purple,#7E5BEF);text-decoration:none">Mot de passe oublié ?</a>@endif
            </div>
            <div class="lg-input-wrap" x-data="{show:false}">
                <span class="lg-input-icon"><svg width="16" height="16" viewBox="0 0 20 20" fill="none"><rect x="4" y="9" width="12" height="9" rx="2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 9V6.5a3 3 0 0 1 6 0V9" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="13.5" r="1.2" fill="currentColor"/></svg></span>
                <input id="password" name="password" :type="show?'text':'password'" required autocomplete="current-password" placeholder="••••••••••" class="lg-input" style="padding-right:48px"/>
                <button type="button" @click="show=!show" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:var(--mj-ink-3);display:flex;align-items:center">
                    <svg x-show="!show" width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M2 10s3-5 8-5 8 5 8 5-3 5-8 5-8-5-8-5z" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="10" r="2.5" stroke="currentColor" stroke-width="1.4"/></svg>
                    <svg x-show="show" width="16" height="16" viewBox="0 0 20 20" fill="none"><path d="M3 3 L17 17M8.5 8.6A2.5 2.5 0 0 0 11.5 11.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
            @error('password')<p style="font:400 12px/1 var(--f-ui);color:var(--c-terracotta);margin:4px 0 0">{{ $message }}</p>@enderror
        </div>

        <label style="display:flex;align-items:center;gap:10px;font:400 13px/1 var(--f-ui);color:var(--mj-ink-2);cursor:pointer">
            <input name="remember" type="checkbox" style="width:16px;height:16px;border-radius:5px;accent-color:#7E5BEF"/>
            Rester connecté pendant 30 jours
        </label>

        <button type="submit" class="lg-submit">
            Se connecter
            <svg width="14" height="14" viewBox="0 0 20 20" fill="none"><path d="M4 10 H16 M12 6 L16 10 L12 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

        {{-- <div style="text-align:center;font:400 13px/1 var(--f-ui);color:var(--mj-ink-3)">
            Pas encore de compte ? <a href="{{ route('register') }}" style="font:500 13px/1 var(--f-ui);color:#7E5BEF;text-decoration:none">Créer un compte →</a>
        </div> --}}
    </form>
    <div style="margin-top:auto;padding-top:32px;font:400 10.5px/1.4 var(--f-mono);letter-spacing:.1em;color:var(--mj-ink-3);text-transform:uppercase;text-align:center">
        Hébergé à Rabat · Loi 09-08 · Données personnelles
    </div>
</main>
</div>
</x-layouts.guest>
