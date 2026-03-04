<?php $active = $active ?? 'home'; ?>
<nav class="nav">
  <a class="nav-logo" href="/"><img src="/pict/LOGO-IDN-SOSMED-200x63.png" alt="ID-Networkers"></a>
  <div class="nav-menu">
    <a href="/"             class="<?= $active==='home'      ?'on':'' ?>">Dashboard</a>
    <div class="nav-sep"></div>
    <a href="/basic-1/"     class="<?= $active==='basic-1'   ?'on':'' ?>">Basic 1</a>
    <a href="/basic-2/"     class="<?= $active==='basic-2'   ?'on':'' ?>">Basic 2</a>
    <a href="/basic-3/"     class="<?= $active==='basic-3'   ?'on':'' ?>">Basic 3</a>
    <div class="nav-sep"></div>
    <a href="/advanced-1/"  class="<?= $active==='advanced-1'?'on':'' ?>">Advanced 1</a>
    <a href="/advanced-2/"  class="<?= $active==='advanced-2'?'on':'' ?>">Advanced 2</a>
    <a href="/advanced-3/"  class="<?= $active==='advanced-3'?'on':'' ?>">Advanced 3</a>
  </div>
  <div class="nav-pill">Security Lab</div>
</nav>
