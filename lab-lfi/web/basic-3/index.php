<?php
$active = 'basic-3';
$page   = $_GET['page'] ?? '';
$output = ''; $error = ''; $resolved = '';
$base   = '/var/www/html/basic-3/pages/';

if ($page !== '') {
    // VULNERABLE: hanya prepend base dir, tapi tidak cegah ../
    $resolved = $base . $page;

    // Normalisasi path untuk tampilan
    $real = realpath($resolved);
    if ($real && file_exists($real)) {
        $output = file_get_contents($real);
        $resolved = $real;
    } else {
        $error = "File tidak ditemukan: " . htmlspecialchars($resolved, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Basic 3: Path Traversal LFI — IDN Lab</title>
<?php include '../includes/shared_css.php'; ?>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc"><a href="/">Dashboard</a><span class="bc-sep">/</span><span>Basic 3: LFI + Path Traversal</span></div>
    <h1>LFI + Path Traversal <span class="tag g">BASIC 3</span></h1>
    <p class="phdr-desc">Aplikasi membatasi file ke direktori <code class="ic">/var/www/html/basic-3/pages/</code>, namun tidak mencegah penggunaan sequence <code class="ic">../</code> untuk keluar dari batasan tersebut.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Objectives</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Pahami cara kerja pembatasan base directory dan mengapa <code class="ic">../</code> dapat menembusnya</span></li>
      <li><div class="obj-n">2</div><span>Hitung jumlah level direktori yang perlu dilalui untuk mencapai root filesystem</span></li>
      <li><div class="obj-n">3</div><span>Baca <code class="ic">/etc/passwd</code> menggunakan teknik path traversal</span></li>
      <li><div class="obj-n">4</div><span>Baca file flag di <code class="ic">/var/private/flag.txt</code> menggunakan traversal dari base directory</span></li>
    </ul>
  </div>

  <div class="box">
    <div class="box-t">Vulnerability Context</div>
    <p class="prose" style="margin-bottom:12px">Developer bermaksud membatasi akses ke direktori <code class="ic">pages/</code>:</p>
    <div class="qbox"><div class="ql">Vulnerable PHP Code</div><span class="val">$base</span> = <span class="str">'/var/www/html/basic-3/pages/'</span>;
<span class="val">$page</span> = <span class="val">$_GET</span>[<span class="str">'page'</span>];
<span class="cm">// Developer mengira file pasti berada di $base</span>
<span class="val">$file</span> = <span class="val">$base</span> . <span class="val">$page</span>;
<span class="kw">echo</span> <span class="at">file_get_contents</span>(<span class="val">$file</span>);</div>
    <p class="prose" style="margin-bottom:12px">Dengan menyisipkan <code class="ic">../</code>, attacker dapat naik satu level direktori per sequence. Perbaikan yang benar adalah menggunakan <code class="ic">realpath()</code> dan memvalidasi bahwa hasil path masih di dalam base directory.</p>

    <div style="background:var(--el);border:1px solid var(--bd);border-radius:var(--r);padding:14px 16px;font-family:var(--mono);font-size:.78rem;color:var(--t2);line-height:1.85">
      <div style="color:var(--t3);font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px">Directory Tree</div>
      / (root)<br>
      &nbsp;&nbsp;├── etc/<br>
      &nbsp;&nbsp;│&nbsp;&nbsp;&nbsp;└── passwd &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:var(--orange)">&lt;-- target</span><br>
      &nbsp;&nbsp;└── var/<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;├── private/<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;│&nbsp;&nbsp;&nbsp;└── flag.txt &nbsp;<span style="color:var(--orange)">&lt;-- target</span><br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── www/html/basic-3/<br>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└── pages/ &nbsp;&nbsp;&nbsp;&nbsp;<span style="color:var(--green)">&lt;-- base dir (kamu ada di sini)</span>
    </div>
  </div>

  <div class="box">
    <div class="box-t">File Loader &mdash; Base: /var/www/html/basic-3/pages/</div>
    <div class="frow" style="align-items:flex-end">
      <div class="fg" style="flex:1;margin-bottom:0">
        <label class="fl">Parameter ?page= <span style="color:var(--red);font-size:.65rem">(tidak ada validasi ../)</span></label>
        <input class="fi" type="text" id="page-input"
          value="<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>"
          placeholder="welcome.txt atau ../../../etc/passwd">
      </div>
      <button class="btn btn-r" onclick="goToPage()">Load</button>
      <?php if($page): ?><a href="/basic-3/" class="btn btn-g">Reset</a><?php endif; ?>
    </div>
    <p style="margin-top:10px;font-size:.75rem;color:var(--t3);font-family:var(--mono)">
      Full path: <span style="color:var(--orange)"><?= htmlspecialchars($base, ENT_QUOTES,'UTF-8') ?><span style="color:var(--red)"><?= htmlspecialchars($page, ENT_QUOTES,'UTF-8') ?></span></span>
    </p>
  </div>

  <?php if ($page !== ''): ?>
  <div class="box">
    <div class="box-t">Resolved Path</div>
    <div class="qbox" style="margin-bottom:0"><?= htmlspecialchars($resolved ?: $base.$page, ENT_QUOTES, 'UTF-8') ?></div>
  </div>

  <?php if ($error): ?>
    <div class="alert a-err"><?= $error ?></div>
  <?php elseif ($output !== ''): ?>
    <div class="box">
      <div class="box-t">File Output &mdash; <?= strlen($output) ?> bytes</div>
      <div class="file-out"><?= htmlspecialchars($output, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <?php if (str_contains($output, 'FLAG{')): ?>
    <div class="alert a-ok" style="font-family:var(--mono)"><strong>FLAG ditemukan.</strong></div>
    <?php endif; ?>
  <?php endif; ?>
  <?php endif; ?>

  <div class="box">
    <div class="box-t">Hints</div>
    <details class="hint">
      <summary>Hint 1 &mdash; Cara kerja ../</summary>
      <div class="hint-body"><code class="ic">../</code> berarti "naik satu level direktori".<br>Dari <code class="ic">/var/www/html/basic-3/pages/</code>:<br>
      <code class="ic">../</code> = <code class="ic">/var/www/html/basic-3/</code><br>
      <code class="ic">../../</code> = <code class="ic">/var/www/html/</code><br>
      <code class="ic">../../../../../</code> = <code class="ic">/</code> (root)</div>
    </details>
    <details class="hint">
      <summary>Hint 2 &mdash; Hitung level direktori</summary>
      <div class="hint-body">Base directory: <code class="ic">/var/www/html/basic-3/pages/</code><br>Untuk mencapai root (<code class="ic">/</code>) diperlukan <strong>6 level</strong> traversal:<br><code class="ic">../../../../../.. </code> = root<br>Kemudian tambahkan path target: <code class="ic">../../../../../etc/passwd</code></div>
    </details>
    <details class="hint">
      <summary>Hint 3 &mdash; Payload siap pakai</summary>
      <div class="hint-body">
        Baca /etc/passwd: <code class="ic">../../../../../etc/passwd</code><br>
        Baca flag: <code class="ic">../../../../private/flag.txt</code><br>
        Baca source code modul ini: <code class="ic">../index.php</code>
      </div>
    </details>
    <details class="hint">
      <summary>Hint 4 &mdash; Mitigasi yang benar</summary>
      <div class="hint-body">Gunakan <code class="ic">realpath()</code> untuk mendapatkan path absolut yang sudah dinormalisasi, lalu validasi bahwa hasilnya diawali dengan base directory:<br><code class="ic">if (!str_starts_with(realpath($file), $base)) { die('Access denied'); }</code></div>
    </details>
  </div>

</div>

<script>
function goToPage() {
    var v = document.getElementById('page-input').value;
    if (v) window.location.href = '/basic-3/?page=' + encodeURIComponent(v);
}
document.getElementById('page-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') goToPage();
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
