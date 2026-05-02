<?php
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/config/auth.php';
requireLogin();
$uid=uid();

/* All exercises this user has logged */
$s=$pdo->prepare("SELECT DISTINCT e.exercise,e.muscle_group FROM exercises e
    JOIN workouts w ON w.id=e.workout_id WHERE w.user_id=? ORDER BY e.exercise");
$s->execute([$uid]); $allEx=$s->fetchAll();

$selEx=trim($_GET['exercise']??($allEx[0]['exercise']??''));

/* Max weight per session for selected exercise */
$chartLabels=[]; $chartData=[];
if($selEx){
    $s=$pdo->prepare("
        SELECT w.workout_date, MAX(st.weight_kg) AS mw
        FROM sets st
        JOIN exercises e ON e.id=st.exercise_id
        JOIN workouts w ON w.id=e.workout_id
        WHERE w.user_id=? AND e.exercise=?
        GROUP BY w.workout_date,w.id ORDER BY w.workout_date ASC LIMIT 20
    ");
    $s->execute([$uid,$selEx]);
    foreach($s->fetchAll() as $r){
        $chartLabels[]=date('j M',strtotime($r['workout_date']));
        $chartData[]=(float)$r['mw'];
    }
}

/* Weekly sessions (last 8 weeks) */
$s=$pdo->prepare("
    SELECT DATE_FORMAT(DATE_SUB(workout_date,INTERVAL WEEKDAY(workout_date) DAY),'%d %b') AS wk,
           COUNT(*) AS cnt
    FROM workouts WHERE user_id=?
      AND workout_date>=DATE_SUB(CURDATE(),INTERVAL 8 WEEK)
    GROUP BY wk ORDER BY MIN(workout_date) ASC
");
$s->execute([$uid]); $wkRows=$s->fetchAll();
$wkLabels=array_column($wkRows,'wk');
$wkData=array_map('intval',array_column($wkRows,'cnt'));

/* Personal Records */
$s=$pdo->prepare("
    SELECT e.exercise,e.muscle_group,
           MAX(st.weight_kg) AS maxkg,
           MAX(st.reps) AS maxreps
    FROM sets st
    JOIN exercises e ON e.id=st.exercise_id
    JOIN workouts w ON w.id=e.workout_id
    WHERE w.user_id=? AND st.weight_kg>0
    GROUP BY LOWER(e.exercise),e.muscle_group
    ORDER BY maxkg DESC LIMIT 8
");
$s->execute([$uid]); $prs=$s->fetchAll();

$icons=['Chest'=>'💪','Back'=>'🏋️','Legs'=>'🦵','Shoulders'=>'🔥',
        'Biceps'=>'💪','Triceps'=>'🦾','Core'=>'⚡','Cardio'=>'🏃'];

$pageTitle='Progress';

$js="document.addEventListener('DOMContentLoaded',()=>{
  drawLine('chart-weight',".json_encode($chartLabels).",".json_encode($chartData).",'#6c63ff');
  drawBar('chart-weekly',".json_encode($wkLabels).",".json_encode($wkData).",'#f97316');
});";

require_once __DIR__.'/config/header.php';
?>

<div class="page-title">Progress 📈</div>
<div class="page-sub">Track your strength gains over time</div>

<!-- EXERCISE SELECTOR -->
<?php if(!empty($allEx)): ?>
<div class="card" style="margin-bottom:14px;padding:14px 16px">
  <form method="GET" style="display:flex;gap:10px;align-items:flex-end">
    <div class="fgroup" style="flex:1;margin-bottom:0">
      <label>📊 Exercise — Weight Over Time</label>
      <select name="exercise" onchange="this.form.submit()" style="font-size:15px;font-weight:600">
        <?php foreach($allEx as $ex): ?>
        <option value="<?= htmlspecialchars($ex['exercise']) ?>"
          <?= $selEx===$ex['exercise']?'selected':'' ?>>
          <?= $icons[$ex['muscle_group']]??'🏋️' ?> <?= htmlspecialchars($ex['exercise']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>
  </form>
</div>
<?php endif; ?>

<!-- WEIGHT CHART -->
<div class="chart-card">
  <div class="chart-lbl">
    <?= $selEx ? htmlspecialchars($selEx).' — Max Weight (kg) Per Session' : 'Log workouts to see your chart' ?>
  </div>
  <canvas id="chart-weight" height="200"></canvas>
</div>

<!-- WEEKLY CHART -->
<div class="chart-card">
  <div class="chart-lbl">Weekly Sessions (Last 8 Weeks)</div>
  <?php if(empty($wkData)): ?>
  <div style="text-align:center;padding:30px;color:var(--muted);font-size:13px">Log more workouts to see this chart</div>
  <?php else: ?>
  <canvas id="chart-weekly" height="180"></canvas>
  <?php endif; ?>
</div>

<!-- PERSONAL RECORDS -->
<?php if(!empty($prs)): ?>
<div class="sec-head"><div class="sec-title">🏆 Personal Records</div></div>
<?php foreach($prs as $i=>$pr): ?>
<div class="workout-item" style="margin-bottom:8px">
  <div class="workout-icon" style="font-size:18px;background:rgba(234,179,8,.12)">
    <?php if($i===0) echo '🥇'; elseif($i===1) echo '🥈'; elseif($i===2) echo '🥉'; else echo ($i+1).'th'; ?>
  </div>
  <div class="workout-info">
    <div class="workout-title"><?= htmlspecialchars($pr['exercise']) ?></div>
    <div class="workout-meta">
      <?= $icons[$pr['muscle_group']]??'🏋️' ?> <?= htmlspecialchars($pr['muscle_group']) ?>
      &bull; Max reps: <?= (int)$pr['maxreps'] ?>
    </div>
  </div>
  <div style="text-align:right">
    <div style="font-size:22px;font-weight:800;color:var(--accent)"><?= number_format($pr['maxkg'],1) ?></div>
    <div style="font-size:11px;color:var(--muted);font-weight:600">kg</div>
  </div>
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="empty" style="padding:32px">
  <div class="empty-icon">🏋️</div>
  <div class="empty-title">No records yet</div>
  <div class="empty-txt">Start logging workouts with weights to see your PRs here!</div>
</div>
<?php endif; ?>

<?php require_once __DIR__.'/config/footer.php'; ?>
