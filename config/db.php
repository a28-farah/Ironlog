<?php
// config/db.php — Database connection for XAMPP

define('DB_NAME', 'ironlog');
define('DB_USER', 'root');
define('DB_PASS', '');      // XAMPP default = empty password
define('DB_PORT', 3306);    // Try 3307 if this fails

$dsn = "mysql:host=127.0.0.1;port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    $msg = $e->getMessage();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Database Error — IronLog</title>
    <style>
      *{box-sizing:border-box;margin:0;padding:0}
      body{background:#0f0f13;color:#f1f1f5;font-family:'Segoe UI',sans-serif;
           display:flex;align-items:center;justify-content:center;min-height:100vh;padding:20px}
      .box{background:#1a1a22;border:1px solid #2e2e3a;border-radius:16px;padding:36px;max-width:620px;width:100%}
      h1{color:#ef4444;font-size:20px;margin-bottom:8px}
      p{color:#8888a0;font-size:14px;line-height:1.6}
      .err{background:#0f0f13;border:1px solid #3a2020;border-radius:8px;padding:12px 16px;
           font-family:monospace;font-size:12px;color:#f97316;margin:16px 0;word-break:break-all}
      h2{font-size:14px;font-weight:700;color:#6c63ff;margin:22px 0 10px;
         text-transform:uppercase;letter-spacing:.5px}
      .step{background:#22222d;border-radius:10px;padding:16px 18px;margin-bottom:10px}
      .step-title{color:#f1f1f5;font-weight:700;font-size:14px;margin-bottom:8px}
      ol{padding-left:18px}
      li{font-size:13px;color:#c0c0cc;line-height:2}
      b,code{color:#f1f1f5}
      code{background:#0f0f13;padding:1px 6px;border-radius:4px;font-family:monospace}
      a{color:#6c63ff}
      .green{color:#22c55e;font-weight:700}
      .footer{text-align:center;margin-top:24px;font-size:13px;color:#8888a0}
    </style>
    </head>
    <body>
    <div class="box">
      <h1>&#9888; Database Connection Failed</h1>
      <p>IronLog cannot connect to MySQL. This is easy to fix &mdash; follow the steps below.</p>
      <div class="err"><?= htmlspecialchars($msg) ?></div>

      <h2>Fix it now</h2>

      <div class="step">
        <div class="step-title">&#9312; Start MySQL in XAMPP Control Panel</div>
        <ol>
          <li>Open the <b>XAMPP Control Panel</b> on your computer</li>
          <li>Find the row labelled <b>MySQL</b></li>
          <li>Click the <b>Start</b> button next to it</li>
          <li>Wait until it turns <span class="green">green</span> and shows a port number</li>
          <li>Also make sure <b>Apache</b> is started (green)</li>
        </ol>
      </div>

      <div class="step">
        <div class="step-title">&#9313; Import the database (first time only)</div>
        <ol>
          <li>Go to <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a></li>
          <li>Click <b>New</b> in the left sidebar</li>
          <li>Database name: type <b>ironlog</b> &rarr; click <b>Create</b></li>
          <li>Click the <b>ironlog</b> database in the sidebar</li>
          <li>Click the <b>Import</b> tab at the top</li>
          <li>Click <b>Choose File</b> &rarr; select <b>database.sql</b> from your ironlog folder</li>
          <li>Scroll down and click <b>Import</b></li>
          <li>You should see a <span class="green">success message</span></li>
        </ol>
      </div>

      <div class="step">
        <div class="step-title">&#9314; Still failing? Try port 3307</div>
        <ol>
          <li>Open <b>config/db.php</b> in Notepad or any text editor</li>
          <li>Find the line: <code>define('DB_PORT', 3306);</code></li>
          <li>Change <code>3306</code> to <code>3307</code></li>
          <li>Save the file and <a href="/ironlog/index.php">reload this page</a></li>
        </ol>
      </div>

      <div class="step">
        <div class="step-title">&#9315; If you set a MySQL root password</div>
        <ol>
          <li>Open <b>config/db.php</b></li>
          <li>Find: <code>define('DB_PASS', '');</code></li>
          <li>Add your password between the quotes, e.g. <code>define('DB_PASS', 'mypassword');</code></li>
          <li>Save and reload</li>
        </ol>
      </div>

      <div class="footer">
        After fixing MySQL &rarr; <a href="/ironlog/index.php">Click here to reload IronLog</a>
      </div>
    </div>
    </body>
    </html>
    <?php
    exit;
}
