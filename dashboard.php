<?php
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/config/auth.php';
requireLogin();
$uid = uid();

/* Stats */
$s = $pdo->prepare("SELECT COUNT(*) FROM workouts WHERE user_id=?");
$s->execute([$uid]);
$total = (int)$s->fetchColumn();

$s = $pdo->prepare("SELECT COUNT(*) FROM workouts WHERE user_id=? AND YEARWEEK(workout_date,1)=YEARWEEK(CURDATE(),1)");
$s->execute([$uid]);
$thisWeek = (int)$s->fetchColumn();

$s = $pdo->prepare("SELECT COUNT(st.id) FROM sets st
    JOIN exercises e ON e.id=st.exercise_id
    JOIN workouts  w ON w.id=e.workout_id
    WHERE w.user_id=?");
$s->execute([$uid]);
$totalSets = (int)$s->fetchColumn();

/* Streak */
$s = $pdo->prepare("SELECT DISTINCT workout_date FROM workouts WHERE user_id=? ORDER BY workout_date DESC");
$s->execute([$uid]);
$dates  = $s->fetchAll(PDO::FETCH_COLUMN);
$streak = 0;
$check  = new DateTime('today');
foreach ($dates as $d) {
    $wd   = new DateTime($d);
    $diff = (int)$check->diff($wd)->days;
    if ($diff <= 1) { $streak++; $check = $wd; } else break;
}

/* Recent 4 workouts */
$s = $pdo->prepare("
    SELECT w.id, w.title, w.workout_date, w.duration_min,
           COUNT(DISTINCT e.id) AS excount,
           COUNT(st.id)         AS setcount
    FROM workouts w
    LEFT JOIN exercises e  ON e.workout_id   = w.id
    LEFT JOIN sets      st ON st.exercise_id = e.id
    WHERE w.user_id = ?
    GROUP BY w.id
    ORDER BY w.workout_date DESC, w.id DESC
    LIMIT 4
");
$s->execute([$uid]);
$recent = $s->fetchAll();

/* Greeting - using if/elseif/else block (no ternary) */
$hour = (int)date('G');
$greet = 'Hello';
if ($hour >= 0 && $hour < 12) {
    $greet = 'Good morning';
}
if ($hour >= 12 && $hour < 17) {
    $greet = 'Good afternoon';
}
if ($hour >= 17) {
    $greet = 'Good evening';
}

$nameParts = explode(' ', ufull());
$firstName = $nameParts[0];

$icons = array(
    'Chest'     => '&#128170;',
    'Back'      => '&#127947;',
    'Legs'      => '&#129459;',
    'Shoulders' => '&#128293;',
    'Biceps'    => '&#128170;',
    'Triceps'   => '&#129470;',
    'Core'      => '&#9889;',
    'Cardio'    => '&#127939;',
);

$pageTitle = 'Dashboard';
require_once __DIR__.'/config/header.php';
?>

<div style="margin-bottom:20px">
  <div class="page-title"><?= $greet ?>, <?= htmlspecialchars($firstName) ?>!</div>
  <div class="page-sub"><?= date('l, j F Y') ?></div>
</div>

<div class="stats-row">
  <div class="stat-box">
    <div class="stat-num"><?= $total ?></div>
    <div class="stat-lbl">Workouts</div>
  </div>
  <div class="stat-box">
    <div class="stat-num"><?= $thisWeek ?></div>
    <div class="stat-lbl">This Week</div>
  </div>
  <div class="stat-box">
    <div class="stat-num"><?= $streak ?></div>
    <div class="stat-lbl">Streak</div>
  </div>
</div>

<a href="/ironlog/log.php" class="btn btn-primary btn-block"
   style="margin-bottom:24px;font-size:16px;padding:15px">
  + Log Today's Workout
</a>

<div class="sec-head">
  <div class="sec-title">Recent Workouts</div>
  <a href="/ironlog/history.php" class="btn btn-ghost btn-xs">See all</a>
</div>

<?php if (empty($recent)): ?>
<div class="empty">
  <div class="empty-icon">&#127947;</div>
  <div class="empty-title">No workouts yet</div>
  <div class="empty-txt">Tap the button above to log your first session!</div>
</div>
<?php else: ?>

<?php foreach ($recent as $w): ?>
<?php
    $ms = $pdo->prepare("SELECT DISTINCT muscle_group FROM exercises WHERE workout_id=? LIMIT 3");
    $ms->execute(array($w['id']));
    $muscles = $ms->fetchAll(PDO::FETCH_COLUMN);
    $firstMuscle = isset($muscles[0]) ? $muscles[0] : '';
    $icon = isset($icons[$firstMuscle]) ? $icons[$firstMuscle] : '&#127947;';
    $ago  = date('j M', strtotime($w['workout_date']));
?>
<div class="workout-item">
  <div class="workout-icon"><?= $icon ?></div>
  <div class="workout-info">
    <div class="workout-title"><?= htmlspecialchars($w['title']) ?></div>
    <div class="workout-meta">
      <?= (int)$w['excount'] ?> exercises &bull; <?= (int)$w['setcount'] ?> sets
      <?php if ($w['duration_min'] > 0): ?>
        &bull; <?= (int)$w['duration_min'] ?> min
      <?php endif; ?>
    </div>
  </div>
  <div class="workout-right">
    <div class="workout-date"><?= $ago ?></div>
    <a href="/ironlog/history.php?view=<?= (int)$w['id'] ?>" class="btn btn-ghost btn-xs">View</a>
  </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php require_once __DIR__.'/config/footer.php'; ?>
