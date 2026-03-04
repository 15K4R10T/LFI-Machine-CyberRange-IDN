<?php
$active = 'basic-2';
$page   = $_GET['page'] ?? '';
$output = ''; $error = ''; $resolved = '';

if ($page !== '') {
    // VULNERABLE: menambahkan .php secara otomatis, tapi bisa di-bypass dengan null byte
    // Pada PHP < 5.3.4 null byte (%00) memutus string
    // Simulasi: kita strip null byte agar tetap bekerja di PHP 8 tapi show the concept
    $page_clean = str_replace("\0", '', $page);
    $resolved   = $page_clean . (str_contains($page, "\0") ? '' : '.php');

    // Jika ada null byte asli, gunakan path sebelum null byte
    if (str_contains($page, "\0") || str_contains($page, '%00')) {
        $resolved = $page_clean;
    }

    // Cek apakah file ada dan baca
    if (file_exists($resolved)) {
        $output = file_get_contents($resolved);
    } else {
        // Coba tanpa ekstensi tambahan jika ada null byte di URL
        $raw = urldecode($page);
        if (str_contains($raw, "\0")) {
            $resolved = explode("\0", $raw)[0];
            if (file_exists($resolved)) {
                $output = file_get_contents($resolved);
            } else {
                $error = "File tidak ditemukan: " . htmlspecialchars($resolved, ENT_QUOTES, 'UTF-8');
            }
        } else {
            $error = "File tidak ditemukan: " . htmlspecialchars($resolved, ENT_QUOTES, 'UTF-8') . " (ingat: ekstensi .php ditambahkan otomatis)";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Basic 2: Null Byte LFI — IDN Lab</title>
<?php include '../includes/shared_css.php'; ?>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc"><a href="/">Dashboard</a><span class="bc-sep">/</span><span>Basic 2: LFI + Null Byte</span></div>
    <h1>LFI + Null Byte <span class="tag o">BASIC 2</span></h1>
    <p class="phdr-desc">Aplikasi menambahkan ekstensi <code class="ic">.php</code> secara otomatis pada setiap input. Teknik null byte injection memutus string sebelum ekstensi ditambahkan, memungkinkan pembacaan file non-PHP.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Objectives</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Pahami mengapa input normal gagal membaca file selain <code class="ic">.php</code></span></li>
      <li><div class="obj-n">2</div><span>Gunakan null byte (<code class="ic">%00</code>) di akhir path untuk memutus penambahan ekstensi</span></li>
      <li><div class="obj-n">3</div><span>Baca file <code class="ic">/etc/passwd</code> yang tidak memiliki ekstensi <code class="ic">.php</code></span></li>
      <li><div class="obj-n">4</div><span>Baca <code class="ic">/var/private/config.env</code> menggunakan teknik null byte</span></li>
    </ul>
  </div>

  <div class="box">
    <div class="box-t">Vulnerability Context</div>
    <p class="prose" style="margin-bottom:12px">Kode yang menyebabkan kerentanan ini:</p>
    <div class="qbox"><div class="ql">Vulnerable PHP Code</div><span class="val">$page</span> = <span class="val">$_GET</span>[<span class="str">'page'</span>];
<span class="cm">// Developer bermaksud membatasi ke file .php saja</span>
<span class="val">$file</span> = <span class="val">$page</span> . <span class="str">".php"</span>;
<span class="at">include</span>(<span class="val">$file</span>);</div>
    <p class="prose" style="margin-bottom:12px">Dengan null byte: <code class="ic">/etc/passwd%00</code> menjadi <code class="ic">/etc/passwd</code> sebelum string terminator, sehingga ekstensi <code class="ic">.php</code> diabaikan. Teknik ini bekerja pada PHP &lt; 5.3.4.</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
      <div style="background:var(--rbg);border:1px solid var(--rbdr);border-radius:var(--r);padding:12px 14px">
        <div style="font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--red);font-family:var(--mono);margin-bottom:6px">Tanpa Null Byte</div>
        <code style="font-size:.78rem;font-family:var(--mono);color:var(--t2)">/etc/passwd + ".php" = /etc/passwd.php</code><br>
        <span style="font-size:.75rem;color:var(--red)">File tidak ditemukan</span>
      </div>
      <div style="background:var(--gbg);border:1px solid var(--gbdr);border-radius:var(--r);padding:12px 14px">
        <div style="font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--green);font-family:var(--mono);margin-bottom:6px">Dengan Null Byte</div>
        <code style="font-size:.78rem;font-family:var(--mono);color:var(--t2)">/etc/passwd%00 + ".php" = /etc/passwd</code><br>
        <span style="font-size:.75rem;color:var(--green)">File berhasil dibaca</span>
      </div>
    </div>
  </div>

  <div class="box">
    <div class="box-t">File Loader &mdash; Extension .php ditambahkan otomatis</div>
    <div class="frow" style="align-items:flex-end">
      <div class="fg" style="flex:1;margin-bottom:0">
        <label class="fl">Parameter ?page= <span style="color:var(--red);font-size:.65rem">(ekstensi .php ditambahkan otomatis)</span></label>
        <input class="fi" type="text" id="page-input"
          value="<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>"
          placeholder="Contoh: about &rarr; dimuat sebagai about.php">
      </div>
      <button class="btn btn-r" onclick="goToPage()">Load</button>
      <?php if($page): ?><a href="/basic-2/" class="btn btn-g">Reset</a><?php endif; ?>
    </div>
    <p style="margin-top:10px;font-size:.75rem;color:var(--t3);font-family:var(--mono)">
      Path yang akan dimuat: <span style="color:var(--orange)"><?= $page ? htmlspecialchars($resolved, ENT_QUOTES, 'UTF-8') : '[input]' . '.php' ?></span>
    </p>
  </div>

  <?php if ($page !== ''): ?>
  <div class="box">
    <div class="box-t">Resolved Path</div>
    <div class="qbox" style="margin-bottom:0"><?= htmlspecialchars($resolved, ENT_QUOTES, 'UTF-8') ?></div>
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
      <summary>Hint 1 &mdash; Mengapa file biasa gagal?</summary>
      <div class="hint-body">Coba masukkan <code class="ic">/etc/passwd</code> — akan dicari file <code class="ic">/etc/passwd.php</code> yang tidak ada. Perhatikan pesan error yang menyebut ekstensi <code class="ic">.php</code>.</div>
    </details>
    <details class="hint">
      <summary>Hint 2 &mdash; Null byte di URL</summary>
      <div class="hint-body">Null byte direpresentasikan sebagai <code class="ic">%00</code> dalam URL encoding.<br>Input: <code class="ic">/etc/passwd%00</code><br>Server memproses: <code class="ic">/etc/passwd</code> (string berhenti di null byte)</div>
    </details>
    <details class="hint">
      <summary>Hint 3 &mdash; Payload lengkap</summary>
      <div class="hint-body">Di URL bar browser:<br><code class="ic">/basic-2/?page=/etc/passwd%00</code><br>Atau di input field, ketik: <code class="ic">/etc/passwd</code> diikuti karakter null (Ctrl+Shift+U lalu 0000 di Linux).</div>
    </details>
    <details class="hint">
      <summary>Hint 4 &mdash; Versi PHP modern</summary>
      <div class="hint-body">Null byte fix diterapkan sejak PHP 5.3.4. Pada versi modern, teknik ini tidak lagi bekerja secara native. Alternatif yang masih relevan: path truncation (<code class="ic">./././.</code> berulang hingga path terpotong).</div>
    </details>
  </div>

</div>

<script>
function goToPage() {
    var v = document.getElementById('page-input').value;
    if (v) window.location.href = '/basic-2/?page=' + encodeURIComponent(v);
}
document.getElementById('page-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') goToPage();
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
