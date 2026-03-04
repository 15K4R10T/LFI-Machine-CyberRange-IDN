<?php $active = 'home'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LFI Injection Lab — ID-Networkers</title>
<?php include 'includes/shared_css.php'; ?>
<style>
.hero{background:var(--surface);border-bottom:1px solid var(--bd)}
.hero-in{max-width:1160px;margin:0 auto;padding:60px 40px 52px;display:grid;grid-template-columns:1fr 210px;gap:56px;align-items:center;position:relative}
.hero-in::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 50% 100% at 0 50%,rgba(230,57,70,.05),transparent 65%);pointer-events:none}
.hero-eye{display:inline-flex;align-items:center;gap:8px;font-size:.66rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--red);background:var(--rbg);border:1px solid var(--rbdr);padding:4px 12px;border-radius:20px;margin-bottom:20px}
.hero-eye i{width:6px;height:6px;border-radius:50%;background:var(--red);animation:blink 2s infinite;flex-shrink:0}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
.hero h1{font-size:2.5rem;font-weight:800;line-height:1.15;letter-spacing:-.025em;color:var(--t1);margin-bottom:16px}
.hero h1 b{color:var(--red)}
.hero-sub{font-size:.9rem;color:var(--t2);max-width:520px;line-height:1.8;margin-bottom:24px}
.hero-note{display:inline-flex;align-items:center;gap:10px;font-size:.72rem;font-family:var(--mono);color:var(--t3);border:1px solid var(--bd);border-radius:var(--r);padding:8px 16px;background:var(--bg)}
.dot-r{width:7px;height:7px;border-radius:50%;background:var(--red);flex-shrink:0;animation:blink 2s infinite}
.hero-stats{display:flex;flex-direction:column;gap:10px}
.stat{background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);padding:16px 20px;text-align:center;transition:border-color .15s}
.stat:hover{border-color:var(--red)}
.stat-n{font-size:2rem;font-weight:800;color:var(--red);font-family:var(--mono);line-height:1}
.stat-l{font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);margin-top:5px}
.main{max-width:1160px;margin:0 auto;padding:44px 40px 72px}
.sec{margin-bottom:44px}
.sec-head{display:flex;align-items:center;gap:12px;margin-bottom:20px}
.sec-head::before{content:'';width:3px;height:16px;background:var(--red);border-radius:2px;flex-shrink:0}
.sec-head h2{font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--t2)}
.about{background:var(--card);border:1px solid var(--bd);border-left:3px solid var(--red);border-radius:var(--r2);padding:22px 26px}
.about p{font-size:.88rem;color:var(--t2);line-height:1.85}
/* ATTACK FLOW */
.flow{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:0}
.flow-step{background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);padding:16px;position:relative;text-align:center}
.flow-step:not(:last-child)::after{content:'→';position:absolute;right:-14px;top:50%;transform:translateY(-50%);color:var(--t3);font-size:1rem;z-index:1}
.flow-num{font-size:.62rem;font-weight:700;letter-spacing:.1em;font-family:var(--mono);color:var(--red);margin-bottom:6px}
.flow-title{font-size:.84rem;font-weight:700;color:var(--t1);margin-bottom:4px}
.flow-desc{font-size:.73rem;color:var(--t2);line-height:1.5}
/* MODULE GRID */
.mod-section{margin-bottom:26px}
.mod-label{font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--t3);margin-bottom:12px;font-family:var(--mono)}
.mods{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.mod{display:block;color:inherit;background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);overflow:hidden;transition:transform .15s,border-color .15s,box-shadow .15s}
.mod:hover{transform:translateY(-3px);border-color:var(--bd2);box-shadow:0 14px 40px rgba(0,0,0,.45)}
.mod-line{height:3px}
.mod-line.g{background:var(--green)}.mod-line.o{background:var(--orange)}.mod-line.r{background:var(--red)}.mod-line.p{background:var(--purple)}
.mod-body{padding:20px}
.mod-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.mod-ico{width:36px;height:36px;border-radius:var(--r);display:flex;align-items:center;justify-content:center}
.mod-ico.g{background:var(--gbg);color:var(--green)}
.mod-ico.o{background:var(--obg);color:var(--orange)}
.mod-ico.r{background:var(--rbg);color:var(--red)}
.mod-ico.p{background:var(--pbg);color:var(--purple)}
.mod-ico svg{width:17px;height:17px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
.mod h3{font-size:.92rem;font-weight:700;color:var(--t1);margin-bottom:7px}
.mod-desc{font-size:.81rem;color:var(--t2);line-height:1.65;margin-bottom:12px}
.mod-list{list-style:none;display:flex;flex-direction:column;gap:4px;margin-bottom:18px}
.mod-list li{font-size:.75rem;color:var(--t3);font-family:var(--mono);padding-left:13px;position:relative}
.mod-list li::before{content:'›';position:absolute;left:0;color:var(--red)}
.mod-foot{display:flex;align-items:center;justify-content:space-between;padding-top:13px;border-top:1px solid var(--bd);font-size:.77rem;font-weight:600;color:var(--t3);transition:color .15s}
.mod:hover .mod-foot{color:var(--red)}
.mod-foot svg{width:13px;height:13px;fill:none;stroke:currentColor;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round}
/* FILE TARGETS */
.targets{display:grid;grid-template-columns:repeat(3,1fr);gap:12px}
.target{background:var(--card);border:1px solid var(--bd);border-radius:var(--r2);padding:16px}
.target-path{font-family:var(--mono);font-size:.76rem;color:var(--orange);margin-bottom:6px;word-break:break-all}
.target-desc{font-size:.77rem;color:var(--t2);line-height:1.55}
@media(max-width:900px){
  .hero-in,.mods,.flow,.targets{grid-template-columns:1fr}
  .hero-stats{flex-direction:row}.stat{flex:1}
  .nav,.main,footer{padding-left:20px;padding-right:20px}
  .hero-in{padding:40px 20px}
  .flow-step:not(:last-child)::after{display:none}
}
</style>
</head>
<body>
<?php include 'includes/nav.php'; ?>

<div class="hero">
  <div class="hero-in">
    <div>
      <div class="hero-eye"><i></i>Vulnerability Research</div>
      <h1>LFI Injection<br><b>Lab Environment</b></h1>
      <p class="hero-sub">Lingkungan praktik Local File Inclusion (LFI) terstruktur untuk keperluan edukasi keamanan siber. Enam modul mencakup teknik dari basic file read, path traversal, null byte, filter bypass, log poisoning, hingga LFI-to-RCE.</p>
      <div class="hero-note">
        <span class="dot-r"></span>
        FOR EDUCATIONAL USE ONLY &mdash; Gunakan hanya di environment lab terisolasi
      </div>
    </div>
    <div class="hero-stats">
      <div class="stat"><div class="stat-n">6</div><div class="stat-l">Modules</div></div>
      <div class="stat"><div class="stat-n">6</div><div class="stat-l">Challenges</div></div>
      <div class="stat"><div class="stat-n">6</div><div class="stat-l">Flags</div></div>
    </div>
  </div>
</div>

<div class="main">

  <div class="sec">
    <div class="sec-head"><h2>Tentang Lab</h2></div>
    <div class="about">
      <p>Lab ini menyimulasikan kerentanan Local File Inclusion yang terjadi ketika aplikasi web menggunakan input pengguna untuk menentukan file yang akan di-include atau dibaca, tanpa validasi yang memadai. Setiap modul merepresentasikan skenario nyata — dari pembacaan file konfigurasi sensitif, traversal direktori, bypass berbagai mekanisme filter, manipulasi log server, hingga eskalasi menjadi Remote Code Execution (RCE).</p>
    </div>
  </div>

  <div class="sec">
    <div class="sec-head"><h2>Attack Flow</h2></div>
    <div class="flow">
      <div class="flow-step"><div class="flow-num">01</div><div class="flow-title">Identify</div><div class="flow-desc">Temukan parameter yang memuat file: <code class="ic">?page=</code>, <code class="ic">?file=</code>, <code class="ic">?lang=</code></div></div>
      <div class="flow-step"><div class="flow-num">02</div><div class="flow-title">Probe</div><div class="flow-desc">Uji dengan path file yang diketahui, perhatikan error message dan respons server</div></div>
      <div class="flow-step"><div class="flow-num">03</div><div class="flow-title">Traverse</div><div class="flow-desc">Gunakan <code class="ic">../</code> untuk keluar dari direktori aplikasi dan menjangkau file sistem</div></div>
      <div class="flow-step"><div class="flow-num">04</div><div class="flow-title">Extract</div><div class="flow-desc">Baca file sensitif: konfigurasi, credential, source code, atau escalate ke RCE</div></div>
    </div>
  </div>

  <div class="sec">
    <div class="sec-head"><h2>Lab Modules</h2></div>

    <div class="mod-section">
      <div class="mod-label">Basic Series</div>
      <div class="mods">
        <a href="/basic-1/" class="mod">
          <div class="mod-line g"></div>
          <div class="mod-body">
            <div class="mod-top">
              <div class="mod-ico g"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
              <span class="tag g">BASIC 1</span>
            </div>
            <h3>Basic LFI</h3>
            <p class="mod-desc">Parameter <code class="ic">?page=</code> langsung digunakan sebagai argumen <code class="ic">include()</code> tanpa validasi apapun.</p>
            <ul class="mod-list"><li>include() tanpa filter</li><li>Baca file konfigurasi sensitif</li><li>Enumerasi struktur direktori</li></ul>
            <div class="mod-foot"><span>Mulai Modul</span><svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
          </div>
        </a>
        <a href="/basic-2/" class="mod">
          <div class="mod-line o"></div>
          <div class="mod-body">
            <div class="mod-top">
              <div class="mod-ico o"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div>
              <span class="tag o">BASIC 2</span>
            </div>
            <h3>LFI + Null Byte</h3>
            <p class="mod-desc">Aplikasi menambahkan ekstensi <code class="ic">.php</code> secara otomatis. Gunakan null byte untuk memutus string dan bypass penambahan ekstensi.</p>
            <ul class="mod-list"><li>Extension appending bypass</li><li>Null byte injection (%00)</li><li>Baca file non-PHP</li></ul>
            <div class="mod-foot"><span>Mulai Modul</span><svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
          </div>
        </a>
        <a href="/basic-3/" class="mod">
          <div class="mod-line g"></div>
          <div class="mod-body">
            <div class="mod-top">
              <div class="mod-ico g"><svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></div>
              <span class="tag g">BASIC 3</span>
            </div>
            <h3>LFI + Path Traversal</h3>
            <p class="mod-desc">Aplikasi membatasi file ke direktori tertentu. Gunakan sequence <code class="ic">../</code> untuk keluar dari direktori dan menjangkau file di luar batas.</p>
            <ul class="mod-list"><li>Directory restriction bypass</li><li>Relative path traversal (../)</li><li>Baca /etc/passwd dan file sistem</li></ul>
            <div class="mod-foot"><span>Mulai Modul</span><svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
          </div>
        </a>
      </div>
    </div>

    <div class="mod-section">
      <div class="mod-label">Advanced Series</div>
      <div class="mods">
        <a href="/advanced-1/" class="mod">
          <div class="mod-line o"></div>
          <div class="mod-body">
            <div class="mod-top">
              <div class="mod-ico o"><svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
              <span class="tag o">ADV 1</span>
            </div>
            <h3>LFI + Filter Bypass</h3>
            <p class="mod-desc">Server memblokir sequence <code class="ic">../</code>. Pelajari teknik encoding dan variasi path untuk melewati filter berbasis string replacement.</p>
            <ul class="mod-list"><li>Double encoding bypass</li><li>URL encoding (%2e%2e%2f)</li><li>Filter evasion techniques</li></ul>
            <div class="mod-foot"><span>Mulai Modul</span><svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
          </div>
        </a>
        <a href="/advanced-2/" class="mod">
          <div class="mod-line r"></div>
          <div class="mod-body">
            <div class="mod-top">
              <div class="mod-ico r"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></div>
              <span class="tag r">ADV 2</span>
            </div>
            <h3>LFI + Log Poisoning</h3>
            <p class="mod-desc">Injeksi PHP code ke dalam Apache access log melalui User-Agent header, kemudian include log tersebut untuk mengeksekusi kode injeksi.</p>
            <ul class="mod-list"><li>User-Agent header injection</li><li>Apache log file inclusion</li><li>PHP code execution via log</li></ul>
            <div class="mod-foot"><span>Mulai Modul</span><svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
          </div>
        </a>
        <a href="/advanced-3/" class="mod">
          <div class="mod-line p"></div>
          <div class="mod-body">
            <div class="mod-top">
              <div class="mod-ico p"><svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
              <span class="tag p">ADV 3</span>
            </div>
            <h3>LFI to RCE</h3>
            <p class="mod-desc">Eskalasi LFI menjadi Remote Code Execution menggunakan PHP wrapper <code class="ic">php://input</code> dan <code class="ic">data://</code> untuk mengeksekusi arbitrary PHP code.</p>
            <ul class="mod-list"><li>php://input wrapper</li><li>data:// URI scheme</li><li>Arbitrary code execution</li></ul>
            <div class="mod-foot"><span>Mulai Modul</span><svg viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <div class="sec">
    <div class="sec-head"><h2>Target Files</h2></div>
    <div class="targets">
      <?php
      $targets = [
        ['/etc/passwd',              'Daftar user sistem Linux'],
        ['/etc/hosts',               'Mapping hostname ke IP address'],
        ['/var/private/flag.txt',    'File flag challenge utama'],
        ['/var/private/config.env',  'Konfigurasi aplikasi + kredensial'],
        ['/home/labuser/.secret',    'File tersembunyi di home directory'],
        ['/var/log/apache2/access.log', 'Apache access log (target log poisoning)'],
      ];
      foreach ($targets as $t): ?>
      <div class="target">
        <div class="target-path"><?= $t[0] ?></div>
        <div class="target-desc"><?= $t[1] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
