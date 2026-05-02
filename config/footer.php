<?php $page = basename($_SERVER['PHP_SELF'],'.php'); ?>
</div><!-- /page-wrap -->

<!-- BOTTOM NAV -->
<nav class="bottom-nav">
  <a href="/ironlog/dashboard.php" class="nav-item <?= $page==='dashboard'?'active':'' ?>">
    <span class="icon">🏠</span>Home
  </a>
  <a href="/ironlog/log.php" class="nav-item <?= $page==='log'?'active':'' ?>">
    <span class="icon">➕</span>Log
  </a>
  <a href="/ironlog/history.php" class="nav-item <?= $page==='history'?'active':'' ?>">
    <span class="icon">📋</span>History
  </a>
  <a href="/ironlog/goals.php" class="nav-item <?= $page==='goals'?'active':'' ?>">
    <span class="icon">🎯</span>Goals
  </a>
  <a href="/ironlog/progress.php" class="nav-item <?= $page==='progress'?'active':'' ?>">
    <span class="icon">📈</span>Progress
  </a>
</nav>

<div id="toast"></div>
<script src="/ironlog/js/script.js"></script>
<?php if(!empty($js)) echo "<script>$js</script>"; ?>
</body>
</html>
