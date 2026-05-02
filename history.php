<?php
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/config/auth.php';
requireLogin();
$uid=uid();

/* Delete */
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['del'])){
    $pdo->prepare("DELETE FROM workouts WHERE id=? AND user_id=?")->execute([(int)$_POST['del'],$uid]);
    flash('success','Workout deleted.');
    header('Location:/ironlog/history.php'); exit;
}

/* All workouts */
$s=$pdo->prepare("
    SELECT w.id,w.title,w.workout_date,w.duration_min,
           COUNT(DISTINCT e.id) AS excount,
           COUNT(st.id) AS setcount,
           MAX(st.weight_kg) AS maxkg,
           GROUP_CONCAT(DISTINCT e.muscle_group ORDER BY e.sort_order SEPARATOR ',') AS muscles
    FROM workouts w
    LEFT JOIN exercises e ON e.workout_id=w.id
    LEFT JOIN sets st ON st.exercise_id=e.id
    WHERE w.user_id=?
    GROUP BY w.id ORDER BY w.workout_date DESC,w.id DESC
");
$s->execute([$uid]); $workouts=$s->fetchAll();

/* Detail view */
$detail=null; $detailEx=[];
if(isset($_GET['view'])){
    $s=$pdo->prepare("SELECT * FROM workouts WHERE id=? AND user_id=?");
    $s->execute([(int)$_GET['view'],$uid]);
    $detail=$s->fetch();
    if($detail){
        $s=$pdo->prepare("SELECT * FROM exercises WHERE workout_id=? ORDER BY sort_order");
        $s->execute([$detail['id']]); $exList=$s->fetchAll();
        foreach($exList as $ex){
            $s2=$pdo->prepare("SELECT * FROM sets WHERE exercise_id=? ORDER BY set_no");
            $s2->execute([$ex['id']]); $ex['sets']=$s2->fetchAll();
            $detailEx[]=$ex;
        }
    }
}

$icons=['Chest'=>'💪','Back'=>'🏋️','Legs'=>'🦵','Shoulders'=>'🔥',
        'Biceps'=>'💪','Triceps'=>'🦾','Core'=>'⚡','Cardio'=>'🏃'];

$pageTitle='History';
require_once __DIR__.'/config/header.php';
?>

<div class="page-title">History 📋</div>
<div class="page-sub"><?= count($workouts) ?> session<?= count($workouts)!=1?'s':'' ?> logged</div>

<?php if(empty($workouts)): ?>
<div class="empty">
  <div class="empty-icon">📋</div>
  <div class="empty-title">No workouts yet</div>
  <div class="empty-txt"><a href="/ironlog/log.php">Log your first workout</a> to get started!</div>
</div>
<?php else: ?>

<?php
// Group by month
$grouped=[];
foreach($workouts as $w){
    $key=date('F Y',strtotime($w['workout_date']));
    $grouped[$key][]=$w;
}
foreach($grouped as $month=>$list):
?>
<div class="sec-head"><div class="sec-title"><?= $month ?></div></div>

<?php foreach($list as $w):
  $muscleList=$w['muscles']?explode(',',$w['muscles']):[];
  $icon=$icons[$muscleList[0]??'']??'🏋️';
  $dateStr=date('D j',strtotime($w['workout_date']));
?>
<div class="workout-item">
  <div class="workout-icon"><?= $icon ?></div>
  <div class="workout-info">
    <div class="workout-title"><?= htmlspecialchars($w['title']) ?></div>
    <div class="workout-meta">
      <?= implode(' · ', array_unique($muscleList)) ?>
      &bull; <?= $w['setcount'] ?> sets
      <?= $w['maxkg']>0 ? " &bull; top: {$w['maxkg']} kg" : '' ?>
      <?= $w['duration_min'] ? " &bull; {$w['duration_min']}min" : '' ?>
    </div>
  </div>
  <div class="workout-right">
    <div class="workout-date"><?= $dateStr ?></div>
    <div style="display:flex;gap:6px">
      <a href="/ironlog/history.php?view=<?= $w['id'] ?>" class="btn btn-ghost btn-xs">View</a>
      <form method="POST" style="display:inline">
        <input type="hidden" name="del" value="<?= $w['id'] ?>">
        <button class="btn btn-red btn-xs" data-confirm="Delete this workout?">Del</button>
      </form>
    </div>
  </div>
</div>
<?php endforeach; ?>
<?php endforeach; ?>
<?php endif; ?>

<!-- DETAIL MODAL -->
<?php if($detail): ?>
<div class="modal-bg open" id="detail-modal">
  <div class="modal">
    <div class="modal-title"><?= htmlspecialchars($detail['title']) ?></div>
    <div style="color:var(--muted);font-size:13px;margin-bottom:16px">
      <?= date('l, j F Y',strtotime($detail['workout_date'])) ?>
      <?= $detail['duration_min'] ? " &bull; {$detail['duration_min']} min" : '' ?>
    </div>

    <?php if($detail['notes']): ?>
    <div style="background:var(--card2);border-radius:8px;padding:10px 14px;
      font-size:13px;color:var(--muted);margin-bottom:16px;font-style:italic">
      "<?= htmlspecialchars($detail['notes']) ?>"
    </div>
    <?php endif; ?>

    <?php foreach($detailEx as $ex): ?>
    <div class="ex-detail">
      <div class="ex-name">
        <?= $icons[$ex['muscle_group']]??'🏋️' ?>
        <?= htmlspecialchars($ex['exercise']) ?>
        <span class="muscle-badge"><?= htmlspecialchars($ex['muscle_group']) ?></span>
      </div>
      <div class="sets-mini">
        <?php foreach($ex['sets'] as $i=>$st): ?>
        <div class="set-pill">
          Set <?= $st['set_no'] ?>:
          <b><?= $st['reps'] ?> reps</b>
          <?php if($st['weight_kg']>0): ?>
          <span>@ <?= number_format($st['weight_kg'],1) ?> kg</span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <div class="modal-foot">
      <form method="POST" style="display:inline">
        <input type="hidden" name="del" value="<?= $detail['id'] ?>">
        <button class="btn btn-red btn-sm" data-confirm="Delete this workout?">🗑 Delete</button>
      </form>
      <a href="/ironlog/history.php" class="btn btn-ghost btn-sm">Close</a>
    </div>
  </div>
</div>
<?php endif; ?>

<?php require_once __DIR__.'/config/footer.php'; ?>
