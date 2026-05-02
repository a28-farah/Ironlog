<?php
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/config/auth.php';
if(session_status()===PHP_SESSION_NONE) session_start();
if(!empty($_SESSION['uid'])){ header('Location:/ironlog/dashboard.php'); exit; }

$err=''; $tab='login';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['action']??'';

    /* ── LOGIN ── */
    if($action==='login'){
        $tab='login';
        $u=trim($_POST['username']??'');
        $p=$_POST['password']??'';
        if(!$u||!$p){ $err='Please fill in both fields.'; }
        else{
            $s=$pdo->prepare("SELECT id,name,username,password FROM users WHERE username=?");
            $s->execute([$u]);
            $row=$s->fetch();
            if($row && password_verify($p,$row['password'])){
                $_SESSION['uid']=$row['id'];
                $_SESSION['uname']=$row['username'];
                $_SESSION['ufull']=$row['name'];
                header('Location:/ironlog/dashboard.php'); exit;
            } else { $err='Wrong username or password.'; }
        }
    }

    /* ── REGISTER ── */
    if($action==='register'){
        $tab='register';
        $name=trim($_POST['name']??'');
        $u=strtolower(trim($_POST['username']??''));
        $p=$_POST['password']??'';
        $p2=$_POST['password2']??'';
        if(!$name||!$u||!$p){ $err='All fields are required.'; }
        elseif(strlen($p)<6){ $err='Password must be at least 6 characters.'; }
        elseif($p!==$p2){ $err='Passwords do not match.'; }
        else{
            $s=$pdo->prepare("SELECT id FROM users WHERE username=?");
            $s->execute([$u]);
            if($s->fetch()){ $err='Username already taken — choose another.'; }
            else{
                $pdo->prepare("INSERT INTO users(name,username,password) VALUES(?,?,?)")
                    ->execute([$name,$u,password_hash($p,PASSWORD_BCRYPT)]);
                $id=(int)$pdo->lastInsertId();
                $_SESSION['uid']=$id; $_SESSION['uname']=$u; $_SESSION['ufull']=$name;
                header('Location:/ironlog/dashboard.php'); exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
<title>IronLog — Sign In</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/ironlog/css/style.css">
</head>
<body>
<div class="auth-wrap">
  <div class="auth-box">

    <div class="auth-logo">Iron<span>Log</span> 🏋️</div>
    <div class="auth-sub">Your personal gym companion</div>

    <div class="tab-row">
      <button class="tab-btn <?= $tab==='login'?'active':'' ?>" data-tab="login" onclick="switchTab('login')">Sign In</button>
      <button class="tab-btn <?= $tab==='register'?'active':'' ?>" data-tab="register" onclick="switchTab('register')">Register</button>
    </div>

    <?php if($err): ?>
    <div class="alert alert-red"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <!-- LOGIN -->
    <div id="login-panel" style="display:<?= $tab==='login'?'block':'none' ?>">
      <form method="POST">
        <input type="hidden" name="action" value="login">
        <div class="fgroup">
          <label>Username</label>
          <input type="text" name="username" placeholder="Enter your username" autocomplete="username" required>
        </div>
        <div class="fgroup">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter your password" required>
        </div>
        <button class="btn btn-primary btn-block" style="margin-top:6px">Sign In →</button>
      </form>
      <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--muted)">
        No account? <a href="#" onclick="switchTab('register')">Register free</a>
      </p>
      <p style="text-align:center;margin-top:8px;font-size:12px;color:var(--muted)">
        Demo: username <b>demo</b> / password <b>demo123</b>
      </p>
    </div>

    <!-- REGISTER -->
    <div id="register-panel" style="display:<?= $tab==='register'?'block':'none' ?>">
      <form method="POST">
        <input type="hidden" name="action" value="register">
        <div class="fgroup">
          <label>Your Name</label>
          <input type="text" name="name" placeholder="e.g. Alex" required>
        </div>
        <div class="fgroup">
          <label>Username</label>
          <input type="text" name="username" placeholder="Choose a username" required>
        </div>
        <div class="frow">
          <div class="fgroup">
            <label>Password</label>
            <input type="password" name="password" placeholder="Min 6 chars" required>
          </div>
          <div class="fgroup">
            <label>Confirm</label>
            <input type="password" name="password2" placeholder="Repeat" required>
          </div>
        </div>
        <button class="btn btn-primary btn-block" style="margin-top:6px">Create Account →</button>
      </form>
    </div>

  </div>
</div>
<script src="/ironlog/js/script.js"></script>
</body>
</html>
