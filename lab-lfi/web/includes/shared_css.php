<?php /* shared_css.php */ ?>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{font-size:15px;scroll-behavior:smooth}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',system-ui,sans-serif;background:#080b13;color:#dde4ef;min-height:100vh;line-height:1.6;-webkit-font-smoothing:antialiased}
a{text-decoration:none;color:inherit}
:root{
  --bg:#080b13;--surface:#0e1420;--card:#111827;--el:#161e2e;
  --bd:#1d2b3a;--bd2:#263446;
  --red:#e63946;--rbg:rgba(230,57,70,.1);--rbdr:rgba(230,57,70,.2);
  --green:#22c55e;--gbg:rgba(34,197,94,.08);--gbdr:rgba(34,197,94,.2);
  --orange:#f59e0b;--obg:rgba(245,158,11,.08);--obdr:rgba(245,158,11,.2);
  --blue:#38bdf8;--bbg:rgba(56,189,248,.08);--bbdr:rgba(56,189,248,.2);
  --purple:#a78bfa;--pbg:rgba(167,139,250,.08);--pbdr:rgba(167,139,250,.2);
  --t1:#dde4ef;--t2:#7b8fa8;--t3:#3d5168;
  --mono:'Courier New',monospace;--r:8px;--r2:12px
}
/* NAV */
.nav{position:sticky;top:0;z-index:100;background:rgba(8,11,19,.97);backdrop-filter:blur(16px);border-bottom:1px solid var(--bd);height:60px;display:flex;align-items:center;padding:0 40px;gap:24px}
.nav-logo img{height:28px;display:block}
.nav-menu{display:flex;align-items:center;gap:2px;margin:0 auto}
.nav-menu a{font-size:.79rem;font-weight:600;letter-spacing:.01em;color:var(--t2);padding:6px 14px;border-radius:6px;transition:all .15s;white-space:nowrap}
.nav-menu a:hover{color:var(--t1);background:var(--el)}
.nav-menu a.on{color:#fff;background:var(--red)}
.nav-sep{width:1px;height:18px;background:var(--bd);margin:0 4px;flex-shrink:0}
.nav-pill{font-size:.65rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--red);border:1px solid var(--rbdr);border-radius:20px;padding:4px 12px;white-space:nowrap}
/* PAGE HEADER */
.phdr{background:var(--surface);border-bottom:1px solid var(--bd);padding:30px 40px}
.phdr-in{max-width:1020px;margin:0 auto}
.bc{display:flex;align-items:center;gap:6px;font-size:.7rem;color:var(--t3);font-family:var(--mono);margin-bottom:10px;flex-wrap:wrap}
.bc a{color:var(--t3);transition:color .15s}.bc a:hover{color:var(--red)}
.bc-sep{color:var(--t3)}
.phdr h1{font-size:1.45rem;font-weight:800;letter-spacing:-.02em;margin-bottom:8px;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.phdr-desc{font-size:.87rem;color:var(--t2);line-height:1.75;max-width:700px}
/* TAGS */
.tag{font-size:.6rem;font-weight:700;letter-spacing:.09em;padding:3px 10px;border-radius:20px;font-family:var(--mono);border:1px solid;white-space:nowrap;display:inline-block}
.tag.g{color:var(--green);background:var(--gbg);border-color:var(--gbdr)}
.tag.o{color:var(--orange);background:var(--obg);border-color:var(--obdr)}
.tag.r{color:var(--red);background:var(--rbg);border-color:var(--rbdr)}
.tag.b{color:var(--blue);background:var(--bbg);border-color:var(--bbdr)}
.tag.p{color:var(--purple);background:var(--pbg);border-color:var(--pbdr)}
/* WRAP */
.wrap{max-width:1020px;margin:0 auto;padding:28px 40px 72px}
/* BOX */
.box{background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);padding:22px 24px;margin-bottom:14px}
.box-t{font-size:.66rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);margin-bottom:18px;display:flex;align-items:center;gap:8px}
.box-t::before{content:'';width:3px;height:12px;background:var(--red);border-radius:2px;flex-shrink:0}
/* PROSE */
.prose{font-size:.87rem;color:var(--t2);line-height:1.85}
.prose strong{color:var(--t1);font-weight:600}
.prose .hl-g{color:var(--green);font-weight:600}
.prose .hl-r{color:var(--red);font-weight:600}
.prose .hl-o{color:var(--orange);font-weight:600}
.prose .hl-b{color:var(--blue);font-weight:600}
/* OBJECTIVES */
.obj-list{list-style:none;display:flex;flex-direction:column;gap:10px}
.obj-list li{display:flex;align-items:flex-start;gap:12px;font-size:.86rem;color:var(--t2);line-height:1.55}
.obj-n{min-width:22px;height:22px;border-radius:50%;background:var(--rbg);border:1px solid var(--rbdr);color:var(--red);font-size:.64rem;font-weight:700;font-family:var(--mono);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px}
/* FORM */
.fg{margin-bottom:16px}
.fl{display:block;font-size:.66rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--t3);margin-bottom:6px;font-family:var(--mono)}
.fi{width:100%;padding:10px 14px;background:var(--bg);border:1px solid var(--bd);border-radius:var(--r);color:var(--t1);font-size:.88rem;font-family:var(--mono);outline:none;transition:border-color .15s}
.fi:focus{border-color:var(--red)}
.frow{display:flex;gap:10px;align-items:flex-end}
.frow .fg{flex:1;margin-bottom:0}
.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border:none;border-radius:var(--r);cursor:pointer;font-weight:700;font-size:.82rem;font-family:inherit;transition:all .15s;white-space:nowrap}
.btn-r{background:var(--red);color:#fff}.btn-r:hover{background:#c1121f}
.btn-g{background:var(--el);color:var(--t2);border:1px solid var(--bd)}.btn-g:hover{color:var(--t1);border-color:var(--bd2)}
/* CODE BOX */
.qbox{background:var(--bg);border:1px solid var(--bd);border-left:3px solid var(--red);border-radius:var(--r);padding:14px 16px;font-family:var(--mono);font-size:.79rem;color:#a8c4e0;word-break:break-all;line-height:1.75;white-space:pre-wrap;margin-bottom:14px}
.ql{font-size:.6rem;letter-spacing:.12em;text-transform:uppercase;color:var(--t3);margin-bottom:8px;font-weight:700}
.ic{background:var(--bg);border:1px solid var(--bd);padding:1px 7px;border-radius:4px;color:var(--green);font-family:var(--mono);font-size:.78rem;white-space:nowrap}
.kw{color:var(--red)}.val{color:var(--blue)}.str{color:var(--orange)}.cm{color:var(--t3)}
/* FILE OUTPUT */
.file-out{background:#040608;border:1px solid var(--bd);border-left:3px solid var(--green);border-radius:var(--r);padding:16px 18px;font-family:var(--mono);font-size:.78rem;color:#a8c4e0;line-height:1.75;white-space:pre-wrap;word-break:break-all;max-height:420px;overflow-y:auto;margin-bottom:14px}
.file-out .flag{color:var(--green);font-weight:700}
/* ALERTS */
.alert{padding:12px 16px;border-radius:var(--r);font-size:.84rem;border:1px solid;margin-bottom:14px;line-height:1.6}
.a-ok{background:var(--gbg);border-color:var(--gbdr);color:var(--green)}
.a-err{background:var(--rbg);border-color:var(--rbdr);color:var(--red)}
.a-warn{background:var(--obg);border-color:var(--obdr);color:var(--orange)}
.a-info{background:var(--bbg);border-color:var(--bbdr);color:var(--blue)}
/* TABLE */
.tbl-wrap{overflow-x:auto;border-radius:var(--r2);border:1px solid var(--bd)}
.tbl{width:100%;border-collapse:collapse;font-size:.82rem}
.tbl th{background:var(--el);color:var(--t3);font-size:.64rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:10px 14px;text-align:left;border-bottom:1px solid var(--bd)}
.tbl td{padding:9px 14px;border-bottom:1px solid var(--bd);color:var(--t2);vertical-align:top;font-family:var(--mono);font-size:.8rem}
.tbl tr:last-child td{border-bottom:none}
.tbl tr:hover td{background:var(--el);color:var(--t1)}
/* HINTS */
.hint{background:var(--el);border:1px solid var(--bd);border-radius:var(--r);padding:13px 16px;margin-bottom:8px}
.hint summary{cursor:pointer;font-size:.84rem;font-weight:600;color:var(--t2);list-style:none;display:flex;align-items:center;gap:8px;user-select:none}
.hint summary::-webkit-details-marker{display:none}
.hint summary::before{content:'▶';font-size:.58rem;color:var(--red);transition:transform .15s;flex-shrink:0}
.hint[open] summary::before{transform:rotate(90deg)}
.hint-body{margin-top:12px;padding-top:12px;border-top:1px solid var(--bd);font-size:.83rem;color:var(--t2);line-height:1.85}
/* FOOTER */
footer{border-top:1px solid var(--bd);padding:22px 40px}
.foot{max-width:1020px;margin:0 auto;display:flex;align-items:center;gap:16px}
.foot img{height:20px;opacity:.35}
.foot p{font-size:.72rem;color:var(--t3);font-family:var(--mono)}
@media(max-width:820px){
  .nav,.wrap,.phdr,footer{padding-left:20px;padding-right:20px}
  .frow{flex-direction:column}
  .phdr h1{font-size:1.2rem}
  .nav-menu a{padding:6px 10px;font-size:.74rem}
}
</style>
