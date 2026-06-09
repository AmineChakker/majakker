<style>
/* ── Post card ── */
.post-card{padding:18px 20px;border-bottom:0.5px solid var(--line);
  transition:background .18s;position:relative}
.post-card:hover{background:rgba(244,239,230,.4)}
.post-card.is-pinned{background:linear-gradient(175deg,rgba(212,160,74,.10) 0%,transparent 60%)}
.post-card.is-announcement{border-left:2.5px solid var(--ann-color)}
.pc-grid{display:grid;grid-template-columns:36px 1fr;gap:13px;align-items:start}

/* ── Action bar ── */
.act-btn{appearance:none;border:0;background:0;display:inline-flex;align-items:center;
  gap:5px;height:30px;padding:0 9px;border-radius:8px;font:500 12px/1 var(--f-ui);
  cursor:pointer;transition:all .14s;color:var(--ink-3)}
.act-btn:hover{background:var(--surface-2);color:var(--ink-2)}
.act-btn.is-liked{color:var(--c-terracotta)}
.act-btn.is-sparked{color:var(--c-saffron)}
.act-btn.is-active-cmt{color:var(--c-blue);background:var(--c-blue-soft)}
@keyframes pop{0%,100%{transform:scale(1)}50%{transform:scale(1.4)}}
.btn-pop{animation:pop .22s cubic-bezier(.2,.7,.3,1)}

/* ── Comments ── */
.cmt-section{border-top:0.5px solid var(--line);padding-top:12px;margin-top:4px;
  display:flex;flex-direction:column;gap:9px}
@keyframes cmt-in{from{opacity:0;transform:translateY(-5px)}to{opacity:1;transform:translateY(0)}}
.cmt-section{animation:cmt-in .18s ease-out}

/* ── Image grid ── */
.img-wrap{border-radius:12px;overflow:hidden;margin-top:2px}
.img-single img{width:100%;max-height:360px;object-fit:cover;display:block;
  transition:transform .4s cubic-bezier(.2,.7,.3,1)}
.img-pair{display:grid;grid-template-columns:1fr 1fr;gap:2px}
.img-triple{display:grid;grid-template-columns:1fr 1fr;gap:2px}
.img-triple .img-main{grid-row:span 2}
.img-quad{display:grid;grid-template-columns:1fr 1fr;gap:2px}
.img-wrap img{width:100%;height:190px;object-fit:cover;display:block;
  transition:transform .4s cubic-bezier(.2,.7,.3,1);cursor:zoom-in}
.img-single img{height:auto;max-height:360px}
.img-wrap img:hover{transform:scale(1.025)}

/* ── Enter animation ── */
@keyframes fd-up{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
.post-card{animation:fd-up .38s cubic-bezier(.2,.7,.3,1) both}
.post-card:nth-child(1){animation-delay:.04s}
.post-card:nth-child(2){animation-delay:.08s}
.post-card:nth-child(3){animation-delay:.12s}
.post-card:nth-child(4){animation-delay:.16s}
.post-card:nth-child(5){animation-delay:.20s}

/* ── Composer spin ── */
@keyframes spin{to{transform:rotate(360deg)}}
</style>
