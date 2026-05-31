<style>
@keyframes dir-fadeup{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:translateY(0)}}
.dir-eyebrow{font:500 11px/1 var(--f-mono);letter-spacing:.22em;text-transform:uppercase;color:var(--ink-3);display:inline-flex;align-items:center;gap:12px}
.dir-eyebrow::before{content:'';width:28px;height:0.5px;background:var(--ink-3)}
.dir-card{background:var(--surface);border:0.5px solid var(--line);border-radius:14px;padding:18px;display:flex;flex-direction:column;gap:12px;transition:border-color .2s,box-shadow .2s}
.dir-card:hover{border-color:rgba(126,91,239,.22);box-shadow:0 8px 28px -12px rgba(20,21,43,.12)}
.dir-table{width:100%;border-collapse:collapse}
.dir-table th{text-align:left;padding:11px 14px;font:500 10px/1 var(--f-mono);letter-spacing:.12em;text-transform:uppercase;color:var(--ink-3);background:var(--surface-2);border-bottom:0.5px solid var(--line)}
.dir-table td{padding:12px 14px;border-bottom:0.5px solid var(--line);font:400 13px/1.2 var(--f-ui);color:var(--ink-2)}
.dir-table tr:hover td{background:rgba(126,91,239,.03)}
.dir-table tr:hover .row-actions{opacity:1!important}
.dir-input{width:100%;height:40px;padding:0 12px;border-radius:9px;border:0.5px solid var(--line-2);background:var(--surface-2);font:400 13.5px/1 var(--f-ui);color:var(--ink);outline:none;box-sizing:border-box;transition:border-color .18s,box-shadow .18s}
.dir-input:focus{border-color:#7E5BEF;box-shadow:0 0 0 3px rgba(126,91,239,.12)}
.dir-label{font:500 11.5px/1 var(--f-ui);color:var(--ink-2);display:block;margin-bottom:5px}
.dir-btn-primary{display:inline-flex;align-items:center;gap:7px;height:38px;padding:0 16px;border-radius:9px;background:linear-gradient(135deg,#7E5BEF,#2563EB);color:#fff;border:0;cursor:pointer;font:500 12.5px/1 var(--f-ui);box-shadow:0 4px 14px -4px rgba(94,57,224,.4);transition:transform .18s,filter .18s;text-decoration:none}
.dir-btn-primary:hover{transform:translateY(-1px);filter:brightness(1.08)}
.dir-btn-ghost{display:inline-flex;align-items:center;gap:6px;height:36px;padding:0 14px;border-radius:8px;border:0.5px solid var(--line-2);background:var(--surface);font:500 12px/1 var(--f-ui);color:var(--ink-2);cursor:pointer;transition:all .15s;text-decoration:none}
.dir-btn-ghost:hover{border-color:rgba(126,91,239,.3);color:#7E5BEF}
.dir-btn-danger{display:inline-flex;align-items:center;height:36px;padding:0 14px;border-radius:8px;border:0.5px solid rgba(220,38,38,.2);background:rgba(220,38,38,.05);font:500 12px/1 var(--f-ui);color:#B91C1C;cursor:pointer;transition:all .15s}
.dir-btn-danger:hover{background:rgba(220,38,38,.1)}
</style>
