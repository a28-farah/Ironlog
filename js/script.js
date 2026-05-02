/* IronLog — script.js */
'use strict';

/* ══════════════════════════════════
   EXERCISE LIBRARY
   ══════════════════════════════════ */
const EXERCISES = {
  'Chest':     ['Bench Press','Incline Bench Press','Decline Bench Press','Dumbbell Fly',
                'Cable Fly','Chest Dip','Push Up','Incline Dumbbell Press',
                'Machine Chest Press','Pec Deck'],
  'Back':      ['Deadlift','Barbell Row','Dumbbell Row','Lat Pulldown','Pull Up',
                'Chin Up','Seated Cable Row','T-Bar Row','Single Arm Row',
                'Face Pull','Rack Pull'],
  'Shoulders': ['Overhead Press','Dumbbell Shoulder Press','Arnold Press','Lateral Raise',
                'Front Raise','Rear Delt Fly','Upright Row','Cable Lateral Raise',
                'Machine Shoulder Press','Shrugs'],
  'Biceps':    ['Bicep Curl','Hammer Curl','Preacher Curl','Incline Curl','Cable Curl',
                'Concentration Curl','EZ Bar Curl','Machine Curl','Reverse Curl'],
  'Triceps':   ['Tricep Pushdown','Skull Crusher','Close Grip Bench Press',
                'Overhead Tricep Extension','Tricep Dip','Cable Overhead Extension',
                'Kickback','Machine Tricep Press','Diamond Push Up'],
  'Legs':      ['Barbell Squat','Leg Press','Hack Squat','Front Squat',
                'Bulgarian Split Squat','Leg Extension','Leg Curl',
                'Romanian Deadlift','Stiff Leg Deadlift','Calf Raise',
                'Seated Calf Raise','Walking Lunge','Step Up','Hip Thrust'],
  'Core':      ['Plank','Crunches','Leg Raise','Russian Twist','Cable Crunch',
                'Ab Wheel Rollout','Hanging Knee Raise','Side Plank','Decline Sit Up'],
  'Cardio':    ['Running','Cycling','Rowing Machine','Jump Rope','Stair Climber',
                'Elliptical','HIIT Sprints','Battle Ropes','Swimming','Walking']
};

const MUSCLE_ICONS = {
  'Chest':'💪','Back':'🏋️','Shoulders':'🔥','Biceps':'💪',
  'Triceps':'🦾','Legs':'🦵','Core':'⚡','Cardio':'🏃'
};

/* Cardio exercises — show Distance (km) instead of Weight (kg) */
const CARDIO_MUSCLE = 'Cardio';

/* ══════════════════════════════════
   EXERCISE BLOCK BUILDER
   ══════════════════════════════════ */
let exCount = 0;

function addExerciseBlock(container, preselectedMuscle) {
  preselectedMuscle = preselectedMuscle || null;
  exCount++;
  var idx = exCount;
  var muscles = Object.keys(EXERCISES);

  var div = document.createElement('div');
  div.className = 'ex-block';
  div.id = 'ex-' + idx;

  var chipsHtml = '';
  for (var i = 0; i < muscles.length; i++) {
    var m = muscles[i];
    chipsHtml += '<button type="button" class="muscle-chip' +
      (m === preselectedMuscle ? ' selected' : '') + '"' +
      ' data-muscle="' + m + '"' +
      ' onclick="selectMuscle(' + idx + ',\'' + m + '\')">' +
      (MUSCLE_ICONS[m] || '🏋️') + ' ' + m +
      '</button>';
  }

  div.innerHTML =
    '<button type="button" class="del-ex" onclick="removeEx(' + idx + ')" title="Remove">×</button>' +

    '<div class="fgroup" style="margin-bottom:10px">' +
      '<label>Muscle Group</label>' +
      '<div class="muscle-tabs" id="chips-' + idx + '">' + chipsHtml + '</div>' +
    '</div>' +

    '<div class="fgroup" style="margin-bottom:14px">' +
      '<label>Exercise</label>' +
      '<select name="ex[' + idx + '][exercise]" id="exsel-' + idx + '" required' +
        ' style="font-size:15px;font-weight:600">' +
        '<option value="">— tap a muscle group above —</option>' +
      '</select>' +
      '<input type="hidden" name="ex[' + idx + '][muscle]" id="exmuscle-' + idx + '"' +
        ' value="' + (preselectedMuscle || '') + '">' +
    '</div>' +

    /* set column headers — updated dynamically */
    '<div class="set-header" id="set-header-' + idx + '">' +
      '<span></span>' +
      '<span id="col-reps-' + idx + '">Reps</span>' +
      '<span id="col-val-' + idx + '">Weight (kg)</span>' +
      '<span></span>' +
    '</div>' +

    '<div id="sets-' + idx + '"></div>' +

    '<button type="button" class="btn btn-ghost btn-sm" style="margin-top:6px"' +
      ' onclick="addSet(' + idx + ')">+ Add Set</button>';

  container.appendChild(div);

  if (preselectedMuscle) {
    populateExerciseDropdown(idx, preselectedMuscle);
  }

  addSet(idx); // auto-add first set
}

/* ── Select muscle chip ── */
function selectMuscle(idx, muscle) {
  var chips = document.querySelectorAll('#chips-' + idx + ' .muscle-chip');
  for (var i = 0; i < chips.length; i++) {
    chips[i].classList.toggle('selected', chips[i].dataset.muscle === muscle);
  }
  document.getElementById('exmuscle-' + idx).value = muscle;
  populateExerciseDropdown(idx, muscle);
  updateSetHeaders(idx, muscle);
  updateSetLabels(idx, muscle);
}

/* ── Populate exercise dropdown based on muscle ── */
function populateExerciseDropdown(idx, muscle) {
  var sel  = document.getElementById('exsel-' + idx);
  var list = EXERCISES[muscle] || [];
  var html = '<option value="">— choose exercise —</option>';
  for (var i = 0; i < list.length; i++) {
    html += '<option value="' + list[i] + '">' + list[i] + '</option>';
  }
  sel.innerHTML = html;
  sel.focus();
}

/* ── Update column header labels (Reps / Weight vs Distance) ── */
function updateSetHeaders(idx, muscle) {
  var isCardio = (muscle === CARDIO_MUSCLE);
  var colReps  = document.getElementById('col-reps-' + idx);
  var colVal   = document.getElementById('col-val-'  + idx);
  if (colReps) colReps.textContent = isCardio ? 'Sets'     : 'Reps';
  if (colVal)  colVal.textContent  = isCardio ? 'Distance (km)' : 'Weight (kg)';
}

/* ── Update placeholder text in existing set rows ── */
function updateSetLabels(idx, muscle) {
  var isCardio = (muscle === CARDIO_MUSCLE);
  var rows = document.querySelectorAll('#sets-' + idx + ' .set-row');
  for (var i = 0; i < rows.length; i++) {
    var repsInput = rows[i].querySelector('.input-reps');
    var valInput  = rows[i].querySelector('.input-val');
    if (repsInput) {
      repsInput.placeholder = isCardio ? '1'    : '10';
      repsInput.title       = isCardio ? 'Number of sets / intervals' : 'Repetitions';
    }
    if (valInput) {
      valInput.placeholder  = isCardio ? '5.0'  : '0';
      valInput.step         = isCardio ? '0.1'  : '0.5';
      valInput.title        = isCardio ? 'Distance in kilometres' : 'Weight in kilograms';
    }
  }
}

function removeEx(idx) {
  var el = document.getElementById('ex-' + idx);
  if (el) el.remove();
}

/* ══════════════════════════════════
   SETS
   ══════════════════════════════════ */
var setCounts = {};

function addSet(exIdx) {
  if (!setCounts[exIdx]) setCounts[exIdx] = 0;
  setCounts[exIdx]++;
  var n = setCounts[exIdx];

  /* Work out whether this exercise block is cardio */
  var muscleInput = document.getElementById('exmuscle-' + exIdx);
  var muscle      = muscleInput ? muscleInput.value : '';
  var isCardio    = (muscle === CARDIO_MUSCLE);

  var container = document.getElementById('sets-' + exIdx);
  var row = document.createElement('div');
  row.className = 'set-row';
  row.id = 'set-' + exIdx + '-' + n;

  row.innerHTML =
    '<div class="set-num">' + n + '</div>' +

    /* Reps / Sets column */
    '<input type="number" class="input-reps"' +
      ' name="ex[' + exIdx + '][sets][' + n + '][reps]"' +
      ' placeholder="' + (isCardio ? '1' : '10') + '"' +
      ' min="1" max="999" required inputmode="numeric"' +
      ' title="' + (isCardio ? 'Number of sets / intervals' : 'Repetitions') + '"' +
      ' style="font-size:16px;font-weight:700;text-align:center">' +

    /* Weight (kg) OR Distance (km) column */
    '<input type="number" class="input-val"' +
      ' name="ex[' + exIdx + '][sets][' + n + '][kg]"' +
      ' placeholder="' + (isCardio ? '5.0' : '0') + '"' +
      ' min="0" max="9999"' +
      ' step="' + (isCardio ? '0.1' : '0.5') + '"' +
      ' inputmode="decimal"' +
      ' title="' + (isCardio ? 'Distance in kilometres' : 'Weight in kilograms') + '"' +
      ' style="font-size:16px;font-weight:700;text-align:center">' +

    '<button type="button" class="del-set" onclick="removeSet(' + exIdx + ',' + n + ')">×</button>';

  container.appendChild(row);
}

function removeSet(exIdx, n) {
  var cont = document.getElementById('sets-' + exIdx);
  if (!cont || cont.children.length <= 1) return;
  var row = document.getElementById('set-' + exIdx + '-' + n);
  if (row) row.remove();
  renumberSets(exIdx);
}

function renumberSets(exIdx) {
  var nums = document.querySelectorAll('#sets-' + exIdx + ' .set-num');
  for (var i = 0; i < nums.length; i++) {
    nums[i].textContent = i + 1;
  }
}

/* ══════════════════════════════════
   TOAST
   ══════════════════════════════════ */
function toast(msg, type) {
  type = type || 'ok';
  var t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.className = 'show ' + type;
  clearTimeout(t._t);
  t._t = setTimeout(function() { t.className = ''; }, 3000);
}

/* ══════════════════════════════════
   MODAL
   ══════════════════════════════════ */
function openModal(id)  { var el = document.getElementById(id); if (el) el.classList.add('open'); }
function closeModal(id) { var el = document.getElementById(id); if (el) el.classList.remove('open'); }

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('modal-bg')) e.target.classList.remove('open');
});

/* ══════════════════════════════════
   AUTH TAB SWITCH
   ══════════════════════════════════ */
function switchTab(tab) {
  var tabs = ['login', 'register'];
  for (var i = 0; i < tabs.length; i++) {
    var t = tabs[i];
    var panel = document.getElementById(t + '-panel');
    var btn   = document.querySelector('[data-tab="' + t + '"]');
    if (panel) panel.style.display = (t === tab) ? 'block' : 'none';
    if (btn)   btn.classList.toggle('active', t === tab);
  }
}

/* ══════════════════════════════════
   GOAL TYPE SWITCHER (goals.php)
   ══════════════════════════════════ */
function setGoalType(type) {
  var chips = document.querySelectorAll('#gtype-row .muscle-chip');
  for (var i = 0; i < chips.length; i++) {
    chips[i].classList.toggle('selected', chips[i].dataset.gtype === type);
  }
  var val = document.getElementById('gtype-val');
  if (val) val.value = type;
  var fields = ['kg', 'reps', 'days'];
  for (var j = 0; j < fields.length; j++) {
    var f = document.getElementById('field-' + fields[j]);
    if (f) f.style.display = (fields[j] === type) ? 'block' : 'none';
  }
}

/* ══════════════════════════════════
   CANVAS CHARTS
   ══════════════════════════════════ */
function drawLine(id, labels, data, color) {
  color = color || '#6c63ff';
  var canvas = document.getElementById(id);
  if (!canvas) return;
  var ctx = canvas.getContext('2d');
  var W = (canvas.parentElement.clientWidth - 36) || 500;
  canvas.width = W; canvas.height = 200;
  var H = 200;
  ctx.clearRect(0, 0, W, H);

  var pad = { t: 20, r: 16, b: 38, l: 48 };
  var cw = W - pad.l - pad.r;
  var ch = H - pad.t - pad.b;
  var max = Math.max.apply(null, data.concat([1]));
  var min = Math.min.apply(null, data.concat([0]));
  var span = (max - min) || 1;

  /* Grid lines */
  for (var g = 0; g <= 4; g++) {
    var gy = pad.t + ch - (g / 4) * ch;
    ctx.strokeStyle = '#2e2e3a'; ctx.lineWidth = 1;
    ctx.beginPath(); ctx.moveTo(pad.l, gy); ctx.lineTo(pad.l + cw, gy); ctx.stroke();
    ctx.fillStyle = '#8888a0'; ctx.font = '11px Inter,sans-serif'; ctx.textAlign = 'right';
    ctx.fillText(Math.round(min + (g / 4) * span), pad.l - 6, gy + 4);
  }

  if (data.length < 2) {
    ctx.fillStyle = '#8888a0'; ctx.font = '13px Inter,sans-serif'; ctx.textAlign = 'center';
    ctx.fillText('Log more sessions to see your chart!', W / 2, H / 2);
    return;
  }

  var xp = function(i) { return pad.l + (i / (data.length - 1)) * cw; };
  var yp = function(v) { return pad.t + ch - ((v - min) / span) * ch; };

  /* Gradient fill */
  var grad = ctx.createLinearGradient(0, pad.t, 0, pad.t + ch);
  grad.addColorStop(0, color + '44'); grad.addColorStop(1, color + '00');
  ctx.beginPath();
  for (var i = 0; i < data.length; i++) {
    if (i === 0) ctx.moveTo(xp(i), yp(data[i]));
    else ctx.lineTo(xp(i), yp(data[i]));
  }
  ctx.lineTo(xp(data.length - 1), pad.t + ch);
  ctx.lineTo(xp(0), pad.t + ch);
  ctx.closePath(); ctx.fillStyle = grad; ctx.fill();

  /* Line */
  ctx.beginPath(); ctx.strokeStyle = color; ctx.lineWidth = 2.5; ctx.lineJoin = 'round';
  for (var i = 0; i < data.length; i++) {
    if (i === 0) ctx.moveTo(xp(i), yp(data[i]));
    else ctx.lineTo(xp(i), yp(data[i]));
  }
  ctx.stroke();

  /* Dots + value labels */
  for (var i = 0; i < data.length; i++) {
    ctx.beginPath(); ctx.arc(xp(i), yp(data[i]), 5, 0, Math.PI * 2);
    ctx.fillStyle = color; ctx.strokeStyle = '#0f0f13'; ctx.lineWidth = 2;
    ctx.fill(); ctx.stroke();
    if (data[i] > 0) {
      ctx.fillStyle = color; ctx.font = 'bold 11px Inter'; ctx.textAlign = 'center';
      ctx.fillText(data[i] + 'kg', xp(i), yp(data[i]) - 10);
    }
    ctx.fillStyle = '#8888a0'; ctx.font = '11px Inter,sans-serif'; ctx.textAlign = 'center';
    ctx.fillText(labels[i], xp(i), H - 6);
  }
}

function drawBar(id, labels, data, color) {
  color = color || '#f97316';
  var canvas = document.getElementById(id);
  if (!canvas) return;
  var ctx = canvas.getContext('2d');
  var W = (canvas.parentElement.clientWidth - 36) || 500;
  canvas.width = W; canvas.height = 180;
  var H = 180;
  ctx.clearRect(0, 0, W, H);

  var pad = { t: 20, r: 16, b: 38, l: 36 };
  var cw = W - pad.l - pad.r;
  var ch = H - pad.t - pad.b;
  var max  = Math.max.apply(null, data.concat([1]));
  var n    = data.length;
  var gap  = 8;
  var bw   = Math.max(10, cw / n - gap);

  for (var g = 0; g <= 4; g++) {
    var gy = pad.t + ch - (g / 4) * ch;
    ctx.strokeStyle = '#2e2e3a'; ctx.lineWidth = 1;
    ctx.beginPath(); ctx.moveTo(pad.l, gy); ctx.lineTo(pad.l + cw, gy); ctx.stroke();
  }

  for (var i = 0; i < data.length; i++) {
    var x  = pad.l + (i / n) * cw + gap / 2;
    var bh = (data[i] / max) * ch;
    var y  = pad.t + ch - bh;
    var r  = 5;
    ctx.fillStyle = color + 'cc';
    ctx.beginPath();
    ctx.moveTo(x + r, y); ctx.lineTo(x + bw - r, y);
    ctx.quadraticCurveTo(x + bw, y, x + bw, y + r);
    ctx.lineTo(x + bw, y + bh); ctx.lineTo(x, y + bh); ctx.lineTo(x, y + r);
    ctx.quadraticCurveTo(x, y, x + r, y);
    ctx.closePath(); ctx.fill();

    if (data[i] > 0) {
      ctx.fillStyle = color; ctx.font = 'bold 11px Inter'; ctx.textAlign = 'center';
      ctx.fillText(data[i], x + bw / 2, y - 5);
    }
    ctx.fillStyle = '#8888a0'; ctx.font = '11px Inter'; ctx.textAlign = 'center';
    ctx.fillText(labels[i], x + bw / 2, H - 6);
  }
}

/* ══════════════════════════════════
   DOM READY
   ══════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function() {

  /* Set today's date */
  var df = document.getElementById('wkt-date');
  if (df && !df.value) df.value = new Date().toISOString().split('T')[0];

  /* Auto-add first exercise block on log page */
  var exContainer = document.getElementById('ex-container');
  if (exContainer && exCount === 0) {
    addExerciseBlock(exContainer);
  }

  /* Flash auto-dismiss */
  var fl = document.querySelector('.alert');
  if (fl) {
    setTimeout(function() {
      fl.style.transition = 'opacity .5s';
      fl.style.opacity = '0';
      setTimeout(function() { fl.remove(); }, 500);
    }, 4000);
  }

  /* Confirm delete buttons */
  var confirmBtns = document.querySelectorAll('[data-confirm]');
  for (var i = 0; i < confirmBtns.length; i++) {
    confirmBtns[i].addEventListener('click', function(e) {
      if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
  }

});