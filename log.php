<?php
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/config/auth.php';
require_once __DIR__.'/config/exercises.php';
requireLogin();
$uid=uid();
$err='';

if($_SERVER['REQUEST_METHOD']==='POST'){
    $title  = trim($_POST['title']??'');
    $date   = trim($_POST['date']??'');
    $dur    = (int)($_POST['duration']??0)?:null;
    $notes  = trim($_POST['notes']??'');
    $exData = $_POST['ex']??[];

    if(!$title||!$date){ $err='Please enter a workout name and date.'; }
    elseif(empty($exData)){ $err='Add at least one exercise.'; }
    else{
        try{
            $pdo->beginTransaction();
            $pdo->prepare("INSERT INTO workouts(user_id,title,workout_date,duration_min,notes) VALUES(?,?,?,?,?)")
                ->execute([$uid,$title,$date,$dur,$notes?:null]);
            $wid=(int)$pdo->lastInsertId();

            $ord=0;
            foreach($exData as $ex){
                $eName  = trim($ex['exercise']??'');
                $muscle = trim($ex['muscle']??'');
                $sets   = $ex['sets']??[];
                if(!$eName||!$muscle) continue;

                $pdo->prepare("INSERT INTO exercises(workout_id,muscle_group,exercise,sort_order) VALUES(?,?,?,?)")
                    ->execute([$wid,$muscle,$eName,++$ord]);
                $eid=(int)$pdo->lastInsertId();

                $sno=0;
                foreach($sets as $s){
                    $reps=(int)($s['reps']??0);
                    $kg  =(float)($s['kg']??0);
                    if($reps<1) continue;
                    $pdo->prepare("INSERT INTO sets(exercise_id,set_no,reps,weight_kg) VALUES(?,?,?,?)")
                        ->execute([$eid,++$sno,$reps,$kg]);
                }
            }
            $pdo->commit();
            flash('success',"Workout saved! Great work 💪");
            header('Location:/ironlog/dashboard.php'); exit;
        } catch(Exception $e){
            $pdo->rollBack();
            $err='Something went wrong. Please try again.';
        }
    }
}

$pageTitle='Log Workout';
require_once __DIR__.'/config/header.php';
?>

<div class="page-title">Log Workout 🏋️</div>
<div class="page-sub">Track every set and see your gains over time</div>

<?php if($err): ?>
<div class="alert alert-red"><?= htmlspecialchars($err) ?></div>
<?php endif; ?>

<form method="POST" id="log-form">

  <!-- SESSION INFO CARD -->
  <div class="card" style="margin-bottom:16px">
    <div class="fgroup">
      <label>📝 Workout Name</label>
      <input type="text" name="title" placeholder="e.g. Push Day, Leg Day, Full Body…"
        value="<?= htmlspecialchars($_POST['title']??'') ?>" required
        style="font-size:16px;font-weight:600">
    </div>
    <div class="frow">
      <div class="fgroup">
        <label>📅 Date</label>
        <input type="date" id="wkt-date" name="date"
          value="<?= htmlspecialchars($_POST['date']??date('Y-m-d')) ?>" required>
      </div>
      <div class="fgroup">
        <label>⏱ Duration (mins)</label>
        <input type="number" name="duration" placeholder="e.g. 60"
          min="1" max="300" inputmode="numeric"
          value="<?= htmlspecialchars($_POST['duration']??'') ?>">
      </div>
    </div>
    <div class="fgroup" style="margin-bottom:0">
      <label>💬 Notes (optional)</label>
      <input type="text" name="notes" placeholder="How did it feel?"
        value="<?= htmlspecialchars($_POST['notes']??'') ?>">
    </div>
  </div>

  <!-- EXERCISES -->
  <div class="sec-head" style="margin-top:20px">
    <div class="sec-title">Exercises</div>
    <button type="button" class="btn btn-orange btn-sm" onclick="addExerciseBlock(document.getElementById('ex-container'))">
      + Add Exercise
    </button>
  </div>

  <div id="ex-container"></div>

  <!-- SUBMIT -->
  <button type="submit" class="btn btn-primary btn-block" style="margin-top:16px;font-size:16px;padding:15px">
    ✅ Save Workout
  </button>
  <a href="/ironlog/dashboard.php" class="btn btn-ghost btn-block" style="margin-top:8px">Cancel</a>

</form>

<script>
// Auto-add first exercise block on load
document.addEventListener('DOMContentLoaded', () => {
  addExerciseBlock(document.getElementById('ex-container'));
});
</script>

<?php require_once __DIR__.'/config/footer.php'; ?>
