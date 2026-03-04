<?php
$active = 'basic-1';
$page   = $_GET['page'] ?? '';
$output = ''; $error = ''; $filepath = '';

if ($page !== '') {
    // VULNERABLE: input langsung digunakan di include() tanpa validasi
    $filepath = $page;
    if (file_exists($filepath)) {
        ob_start();
        // Gunakan file_get_contents agar tidak mengeksekusi PHP
        $output = file_get_contents($filepath);
        ob_end_clean();
    } else {
        $error = "File tidak ditemukan: " . htmlspecialchars($filepath, ENT_QUOTES, 'UTF-8');
    }
}

$pages = ['home' => 'Beranda', 'about' => 'Tentang', 'contact' => 'Kontak'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Basic 1: Basic LFI — IDN Lab</title>
<?php include '../includes/shared_css.php'; ?>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc"><a href="/">Dashboard</a><span class="bc-sep">/</span><span>Basic 1: Basic LFI</span></div>
    <h1>Basic LFI <span class="tag g">BASIC 1</span></h1>
    <p class="phdr-desc">Aplikasi ini menggunakan parameter <code class="ic">?page=</code> untuk menentukan file yang akan dimuat. Input diteruskan langsung ke fungsi <code class="ic">file_get_contents()</code> tanpa validasi apapun.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Objectives</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Konfirmasi kerentanan dengan memuat file di luar direktori yang diharapkan</span></li>
      <li><div class="obj-n">2</div><span>Baca isi file <code class="ic">/etc/passwd</code> untuk mengidentifikasi user pada sistem</span></li>
      <li><div class="obj-n">3</div><span>Baca file <code class="ic">/var/private/config.env</code> yang berisi kredensial database</span></li>
      <li><div class="obj-n">4</div><span>Temukan dan baca <code class="ic">/var/private/flag.txt</code> untuk mendapatkan flag</span></li>
    </ul>
  </div>

  <div class="box">
    <div class="box-t">Vulnerability Context</div>
    <p class="prose" style="margin-bottom:12px">Kode PHP yang rentan pada aplikasi ini:</p>
    <div class="qbox"><div class="ql">Vulnerable PHP Code</div><span class="val">$page</span> = <span class="val">$_GET</span>[<span class="str">'page'</span>];            <span class="cm">// tidak ada validasi</span>
<span class="kw">$output</span> = <span class="at">file_get_contents</span>(<span class="val">$page</span>); <span class="cm">// langsung digunakan</span>
<span class="kw">echo</span> <span class="val">$output</span>;</div>
    <p class="prose">Karena tidak ada whitelist, blacklist, maupun pembatasan direktori, attacker dapat menentukan path file apapun yang dapat dibaca oleh proses web server.</p>
  </div>

  <!-- Navigation simulasi aplikasi normal -->
  <div class="box">
    <div class="box-t">Aplikasi Navigasi &mdash; Normal Usage</div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
      <?php foreach ($pages as $k => $v): ?>
      <a href="/basic-1/?page=pages/<?= $k ?>.html"
         style="padding:7px 16px;border-radius:var(--r);font-size:.8rem;font-weight:600;border:1px solid var(--bd);color:var(--t2);background:var(--el);text-decoration:none;transition:all .15s"
         onmouseover="this.style.borderColor='var(--bd2)';this.style.color='var(--t1)'"
         onmouseout="this.style.borderColor='var(--bd)';this.style.color='var(--t2)'">
        <?= $v ?>
      </a>
      <?php endforeach; ?>
    </div>
    <div class="frow" style="align-items:flex-end">
      <div class="fg" style="flex:1;margin-bottom:0">
        <label class="fl">Parameter ?page= <span style="color:var(--red);font-size:.65rem">(VULNERABLE)</span></label>
        <input class="fi" type="text" id="page-input"
          value="<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>"
          placeholder="pages/home.html atau path absolut seperti /etc/passwd">
      </div>
      <button class="btn btn-r" onclick="goToPage()">Load File</button>
      <?php if($page): ?>
      <a href="/basic-1/" class="btn btn-g">Reset</a>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($page !== ''): ?>

  <div class="box">
    <div class="box-t">Request Detail</div>
    <div class="qbox" style="margin-bottom:0">
      <div class="ql">Resolved Path</div><?= htmlspecialchars($filepath, ENT_QUOTES, 'UTF-8') ?>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert a-err"><?= $error ?></div>
  <?php elseif ($output !== ''): ?>
    <div class="box">
      <div class="box-t">File Output &mdash; <?= strlen($output) ?> bytes</div>
      <div class="file-out"><?= htmlspecialchars($output, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <?php if (str_contains($output, 'FLAG{')): ?>
    <div class="alert a-ok" style="font-family:var(--mono)"><strong>FLAG ditemukan dalam file tersebut.</strong></div>
    <?php endif; ?>
  <?php endif; ?>

  <?php endif; ?>

  <div class="box">
    <div class="box-t">Hints</div>
    <details class="hint">
      <summary>Hint 1 &mdash; Konfirmasi kerentanan</summary>
      <div class="hint-body">Coba masukkan path absolut: <code class="ic">/etc/hostname</code><br>Jika nama host container muncul, parameter ini rentan terhadap LFI.</div>
    </details>
    <details class="hint">
      <summary>Hint 2 &mdash; File sistem yang umum dibaca</summary>
      <div class="hint-body">
        <code class="ic">/etc/passwd</code> &mdash; daftar user sistem<br>
        <code class="ic">/etc/hosts</code> &mdash; konfigurasi jaringan<br>
        <code class="ic">/proc/version</code> &mdash; versi kernel Linux
      </div>
    </details>
    <details class="hint">
      <summary>Hint 3 &mdash; Temukan file aplikasi</summary>
      <div class="hint-body">Source code aplikasi ini ada di <code class="ic">/var/www/html/basic-1/index.php</code>.<br>Membaca source code dapat mengungkap path file sensitif lainnya yang disimpan di server.</div>
    </details>
    <details class="hint">
      <summary>Hint 4 &mdash; Lokasi flag</summary>
      <div class="hint-body">Flag tersimpan di <code class="ic">/var/private/flag.txt</code>.<br>File konfigurasi dengan kredensial ada di <code class="ic">/var/private/config.env</code>.</div>
    </details>
  </div>

</div>

<script>
function goToPage() {
    var v = document.getElementById('page-input').value;
    if (v) window.location.href = '/basic-1/?page=' + encodeURIComponent(v);
}
document.getElementById('page-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') goToPage();
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
