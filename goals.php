<?php
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/config/auth.php';
requireLogin();
$uid=uid(); $err='';

/* Add goal */
if($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='add'){
    $desc    = trim($_POST['description']??'');
    $type    = $_POST['gtype']??'kg';
    $tkg     = $type==='kg'  ? (float)($_POST['target_kg']??0)   : null;
    $treps   = $type==='reps'? (int)($_POST['target_reps']??0)   : null;
    $tdays   = $type==='days'? (int)($_POST['target_days']??0)   : null;
    $deadline= trim($_POST['deadline']??'')?:null;

    if(!$desc){ $err='Please enter a goal description.'; }
    elseif(!$tkg && !$treps && !$tdays){ $err='Please enter a target value.'; }
    else{
        $pdo->prepare("INSERT INTO goals(user_id,description,target_kg,target_reps,target_days,deadline) VALUES(?,?,?,?,?,?)")
            ->execute([$uid,$desc,$tkg,$treps,$tdays,$deadline]);
        flash('success','Goal added! Keep pushing 🎯');
        header('Location:/ironlog/goals.php'); exit;
    }
}

/* Delete */
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['del'])){
    $pdo->prepare("DELETE FROM goals WHERE id=? AND user_id=?")->execute([(int)$_POST['del'],$uid]);
    flash('success','Goal removed.');
    header('Location:/ironlog/goals.php'); exit;
}

/* Mark done */
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['done_id'])){
    $pdo->prepare("UPDATE goals SET done=1 WHERE id=? AND user_id=?")->execute([(int)$_POST['done_id'],$uid]);
    flash('success','Goal achieved! 🏆 Amazing work!');
    header('Location:/ironlog/goals.php'); exit;
}

/* Fetch goals */
$s=$pdo->prepare("SELECT * FROM goals WHERE user_id=? ORDER BY done ASC,created_at DESC");
$s->execute([$uid]); $goals=$s->fetchAll();

/* Calculate progress */
function goalProgress(PDO $pdo,int $uid,array $g):int{
    if($g['target_days']){
        $s=$pdo->prepare("SELECT COUNT(*) FROM workouts WHERE user_id=?");
        $s->execute([$uid]); $cur=(int)$s->fetchColumn();
        return min(100,(int)(($cur/$g['target_days'])*100));
    }
    if($g['target_kg']){
        // Try to extract exercise name from description (first 3 words)
        $words=explode(' ',$g['description']);
        $exName=implode(' ',array_slice($words,0,3));
        $s=$pdo->prepare("SELECT COALESCE(MAX(st.weight_kg),0) FROM sets st
            JOIN exercises e ON e.id=st.exercise_id
            JOIN workouts w ON w.id=e.workout_id
            WHERE w.user_id=? AND LOWER(e.exercise) LIKE ?");
        $s->execute([$uid,'%'.strtolower($words[0]).'%']);
        $cur=(float)$s->fetchColumn();
        return min(100,(int)(($cur/$g['target_kg'])*100));
    }
    if($g['target_reps']){
        $s=$pdo->prepare("SELECT COALESCE(MAX(st.reps),0) FROM sets st
            JOIN exercises e ON e.id=st.exercise_id
            JOIN workouts w ON w.id=e.workout_id WHERE w.user_id=?");
        $s->execute([$uid]); $cur=(int)$s->fetchColumn();
        return min(100,(int)(($cur/$g['target_reps'])*100));
    }
    return 0;
}

$pageTitle='Goals'; require_once __DIR__.'/config/header.php';
?>

<div class="page-title">Goals 🎯</div>
<div class="page-sub">Set targets and watch yourself grow</div>

<?php if($err): ?><div class="alert alert-red"><?= htmlspecialchars($err) ?></div><?php endif; ?>

<!-- ADD GOAL -->
<div class="card" style="margin-bottom:20px">
  <div class="sec-title" style="margin-bottom:14px">➕ New Goal</div>
  <form method="POST">
    <input type="hidden" name="action" value="add">
    <div class="fgroup">
      <label>Goal Description</label>
      <input type="text" name="description" placeholder="e.g. Bench Press 100 kg, Run 5 days/week"
        required style="font-size:15px;font-weight:600">
    </div>

    <!-- Goal type selector -->
    <div class="fgroup">
      <label>Goal Type</label>
      <div style="display:flex;gap:8px;flex-wrap:wrap" id="gtype-row">
        <button type="button" class="muscle-chip selected" data-gtype="kg" onclick="setGoalType('kg')">⚖️ Weight (kg)</button>
        <button type="button" class="muscle-chip" data-gtype="reps" onclick="setGoalType('reps')">🔢 Max Reps</button>
        <button type="button" class="muscle-chip" data-gtype="days" onclick="setGoalType('days')">📅 Total Sessions</button>
      </div>
      <input type="hidden" name="gtype" id="gtype-val" value="kg">
    </div>

    <div class="frow">
      <div class="fgroup" id="field-kg">
        <label>Target Weight (kg)</label>
        <input type="number" name="target_kg" placeholder="e.g. 100" min="1" step="0.5" inputmode="decimal">
      </div>
      <div class="fgroup" id="field-reps" style="display:none">
        <label>Target Reps</label>
        <input type="number" name="target_reps" placeholder="e.g. 20" min="1" inputmode="numeric">
      </div>
      <div class="fgroup" id="field-days" style="display:none">
        <label>Target Sessions</label>
        <input type="number" name="target_days" placeholder="e.g. 50" min="1" inputmode="numeric">
      </div>
      <div class="fgroup">
        <label>Deadline (optional)</label>
        <input type="date" name="deadline">
      </div>
    </div>

    <button class="btn btn-primary btn-sm">Add Goal</button>
  </form>
</div>

<!-- GOALS LIST -->
<?php
$active  =array_filter($goals,fn($g)=>!$g['done']);
$achieved=array_filter($goals,fn($g)=> $g['done']);
?>

<?php if(empty($goals)): ?>
<div class="empty">
  <div class="empty-icon">🎯</div>
  <div class="empty-title">No goals yet</div>
  <div class="empty-txt">Add your first goal above and start working towards it!</div>
</div>
<?php endif; ?>

<?php if(!empty($active)): ?>
<div class="sec-head"><div class="sec-title">Active Goals (<?= count($active) ?>)</div></div>
<?php foreach($active as $g):
  $pct=goalProgress($pdo,$uid,$g);
  $unit=$g['target_kg']?number_format($g['target_kg'],1).' kg':($g['target_reps']?$g['target_reps'].' reps':$g['target_days'].' sessions');
  $dl=$g['deadline']?date('j M Y',strtotime($g['deadline'])):'No deadline';
  $daysLeft=$g['deadline']?max(0,(int)((strtotime($g['deadline'])-time())/86400)):null;
?>
<div class="goal-item">
  <div class="goal-title"><?= htmlspecialchars($g['description']) ?></div>
  <div style="color:var(--muted);font-size:12px;margin-bottom:10px">
    Target: <b style="color:var(--text)"><?= $unit ?></b>
    &bull; <?= $dl ?>
    <?php if($daysLeft!==null): ?>
    &bull; <span style="color:<?= $daysLeft<=7?'var(--orange)':'var(--muted)' ?>"><?= $daysLeft ?> days left</span>
    <?php endif; ?>
  </div>
  <div class="prog-bg"><div class="prog-fill <?= $pct>=100?'done':'' ?>" style="width:<?= $pct ?>%"></div></div>
  <div class="prog-txt" style="margin-bottom:12px">
    <?= $pct ?>% complete <?= $pct>=100?'🎉 Target reached!':'' ?>
  </div>
  <div style="display:flex;gap:8px">
    <?php if($pct>=100): ?>
    <form method="POST" style="display:inline">
      <input type="hidden" name="done_id" value="<?= $g['id'] ?>">
      <button class="btn btn-green btn-xs">✓ Mark Done</button>
    </form>
    <?php endif; ?>
    <form method="POST" style="display:inline">
      <input type="hidden" name="del" value="<?= $g['id'] ?>">
      <button class="btn btn-red btn-xs" data-confirm="Delete this goal?">Delete</button>
    </form>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<?php if(!empty($achieved)): ?>
<div class="sec-head" style="margin-top:10px"><div class="sec-title">🏆 Achieved (<?= count($achieved) ?>)</div></div>
<?php foreach($achieved as $g): ?>
<div class="goal-item" style="opacity:.55">
  <div class="goal-title" style="text-decoration:line-through"><?= htmlspecialchars($g['description']) ?></div>
  <div class="prog-bg"><div class="prog-fill done" style="width:100%"></div></div>
  <div class="prog-txt">Completed ✅</div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<script>
function setGoalType(type){
  document.querySelectorAll('#gtype-row .muscle-chip').forEach(c=>c.classList.toggle('selected',c.dataset.gtype===type));
  document.getElementById('gtype-val').value=type;
  ['kg','reps','days'].forEach(t=>{
    document.getElementById('field-'+t).style.display=t===type?'block':'none';
  });
}
</script>

<?php require_once __DIR__.'/config/footer.php'; ?>
