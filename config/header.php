<?php
$flash = getFlash();
$page  = basename($_SERVER['PHP_SELF'], '.php');
$init  = strtoupper(mb_substr(ufull(), 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
<title><?= htmlspecialchars($pageTitle??'IronLog') ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/ironlog/css/style.css">
</head>
<body>

<!-- TOP BAR -->
<header class="topbar">
  <div class="logo">Iron<span>Log</span></div>
  <div class="user-chip">
    <div class="avatar"><?= $init ?></div>
    <a href="/ironlog/logout.php" class="btn btn-ghost btn-sm">Out</a>
  </div>
</header>

<!-- MAIN -->
<div class="page-wrap">

<?php if($flash): ?>
<div class="alert alert-<?= $flash[0]==='success'?'green':'red' ?>">
  <?= htmlspecialchars($flash[1]) ?>
</div>
<?php endif; ?>
