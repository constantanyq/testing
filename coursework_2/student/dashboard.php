<?php
session_start();
require_once "../connection.php";

// Only students can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$sid = $_SESSION['user_id'];

// Get this student's internship
$sql = "SELECT i.*, s.student_name, s.student_id, s.programme, s.student_email,
               c.company_name, l.lecturer_name, sv.supervisor_name
        FROM internship i
        JOIN student    s  ON i.student_id    = s.student_id
        LEFT JOIN company    c  ON i.company_id    = c.company_id
        LEFT JOIN lecturer   l  ON i.lecturer_id   = l.lecturer_id
        LEFT JOIN supervisor sv ON i.supervisor_id = sv.supervisor_id
        WHERE i.student_id = '$sid'";
$res  = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);

// Get supervisor marks
$supMarks = [];
if ($data && $data['s_marks_id']) {
    $smid  = (int)$data['s_marks_id'];
    $smRes = mysqli_query($conn,
        "SELECT sm.*, comp.component_name, comp.component_weightage
         FROM supervisor_marks sm
         JOIN component comp ON sm.component_id = comp.component_id
         WHERE sm.s_marks_id = $smid ORDER BY comp.component_id");
    while ($r = mysqli_fetch_assoc($smRes)) $supMarks[] = $r;
}

// Get lecturer marks
$lecMarks = [];
if ($data && $data['l_marks_id']) {
    $lmid  = (int)$data['l_marks_id'];
    $lmRes = mysqli_query($conn,
        "SELECT lm.*, comp.component_name, comp.component_weightage
         FROM lecturer_marks lm
         JOIN component comp ON lm.component_id = comp.component_id
         WHERE lm.l_marks_id = $lmid ORDER BY comp.component_id");
    while ($r = mysqli_fetch_assoc($lmRes)) $lecMarks[] = $r;
}

$avg        = $data['average_marks'] ?? null;

// ---- RANKING (from old petandingan logic) ----
$rankPos   = 0;
$rankTotal = 0;
$rankRes = mysqli_query($conn,
    "SELECT s.student_id, i.average_marks
     FROM internship i
     JOIN student s ON i.student_id = s.student_id
     WHERE i.average_marks IS NOT NULL
     ORDER BY i.average_marks DESC");
while ($rr = mysqli_fetch_assoc($rankRes)) {
    $rankTotal++;
    if ($rr['student_id'] === $sid) $rankPos = $rankTotal;
}
$finalClass = 'pass';
$finalLabel = 'Pass';
$finalColour= '#155724';

if ($avg !== null) {
    if ($avg >= 80) {
        $finalClass  = 'distinction';
        $finalLabel  = '🌟 Distinction';
        $finalColour = '#856404';
    } elseif ($avg >= 50) {
        $finalClass  = 'pass';
        $finalLabel  = '✅ Pass';
        $finalColour = '#155724';
    } else {
        $finalClass  = 'fail';
        $finalLabel  = '❌ Fail';
        $finalColour = '#721c24';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Result – InternTrack</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="sidebar-logo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="24" height="24">
              <defs><linearGradient id="sl" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:#1e3a5f"/><stop offset="100%" style="stop-color:#2563eb"/></linearGradient></defs>
              <circle cx="100" cy="100" r="96" fill="url(#sl)"/>
              <ellipse cx="100" cy="118" rx="28" ry="20" fill="#f0f9ff" opacity="0.9"/>
              <circle cx="100" cy="88" r="16" fill="#f0f9ff" opacity="0.9"/>
              <rect x="76" y="69" width="48" height="7" rx="2" fill="#1e3a5f"/>
              <polygon points="100,58 130,69 100,80 70,69" fill="#1e3a5f"/>
              <line x1="116" y1="69" x2="122" y2="82" stroke="#fbbf24" stroke-width="2.5"/>
              <circle cx="122" cy="84" r="2.5" fill="#fbbf24"/>
              <path d="M66,132 Q83,128 100,132 L100,155 Q83,151 66,155 Z" fill="#bfdbfe"/>
              <path d="M100,132 Q117,128 134,132 L134,155 Q117,151 100,155 Z" fill="#bfdbfe" opacity="0.8"/>
              <rect x="98" y="132" width="4" height="23" fill="#93c5fd"/>
            </svg>
        </div>
        <div class="sidebar-brand-text">
            <h2>InternTrack</h2>
            <p>Student Panel</p>
        </div>
    </div>
    <div class="sidebar-user">
        <span>Student</span>
        <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
    </div>
    <nav>
        <a href="dashboard.php" class="active">
            <span class="nav-icon">📊</span> My Result
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="../logout.php">🚪 Logout</a>
    </div>
</aside>

<div class="main-content">
<div class="topbar">
    <h1>My Internship Result</h1>
    <span class="breadcrumb">Student &rsaquo; My Result</span>
</div>

<div class="page-body">

<?php if (!$data): ?>
    <div class="alert alert-info">
        ℹ️ You have not been assigned to an internship yet. Please contact your admin.
    </div>

<?php else: ?>

    <!-- Final Mark Display -->
    <?php if ($avg !== null): ?>
    <div class="final-mark-box <?= $finalClass ?>">
        <div class="big-mark" style="color:<?= $finalColour ?>"><?= number_format($avg, 2) ?></div>
        <div class="mark-label" style="color:<?= $finalColour ?>"><?= $finalLabel ?></div>
        <div style="font-size:12px; color:var(--muted); margin-top:6px;">Final Internship Score</div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        ⏳ Your marks have not been entered yet. Please check back later.
    </div>
    <?php endif; ?>

    <!-- Student Info -->
    <div class="result-summary">
        <div class="result-summary-item">
            <div class="label">Student ID</div>
            <div class="value"><?= htmlspecialchars($data['student_id']) ?></div>
        </div>
        <div class="result-summary-item">
            <div class="label">Name</div>
            <div class="value"><?= htmlspecialchars($data['student_name']) ?></div>
        </div>
        <div class="result-summary-item">
            <div class="label">Programme</div>
            <div class="value"><?= htmlspecialchars($data['programme']) ?></div>
        </div>
        <div class="result-summary-item">
            <div class="label">Company</div>
            <div class="value"><?= htmlspecialchars($data['company_name'] ?? '—') ?></div>
        </div>
        <div class="result-summary-item">
            <div class="label">Duration</div>
            <div class="value"><?= $data['duration'] ? $data['duration'].' weeks' : '—' ?></div>
        </div>
        <div class="result-summary-item">
            <div class="label">Lecturer</div>
            <div class="value"><?= htmlspecialchars($data['lecturer_name'] ?? '—') ?></div>
        </div>
        <div class="result-summary-item">
            <div class="label">Supervisor</div>
            <div class="value"><?= htmlspecialchars($data['supervisor_name'] ?? '—') ?></div>
        </div>
    </div>

    <!-- Supervisor Marks Breakdown -->
    <div class="card">
        <div class="card-title">
            Supervisor Assessment
            <?php if ($supMarks): ?>
                <?php
                    $supTotal = array_sum(array_column($supMarks, 'total_marks'));
                    echo "– <span style='color:var(--accent)'>" . number_format($supTotal,2) . " / 100</span>";
                ?>
            <?php endif; ?>
        </div>

        <?php if ($supMarks): ?>
        <div class="table-wrap">
        <table class="marks-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Component</th>
                    <th>Weight</th>
                    <th>Your Mark</th>
                    <th>Weighted Score</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($supMarks as $i => $m):
                $mark      = (float)$m['component_mark'];
                $barColour = $mark >= 70 ? 'green' : ($mark >= 50 ? 'orange' : 'red');
            ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><?= htmlspecialchars($m['component_name']) ?></td>
                <td class="weight-cell"><?= $m['component_weightage'] ?>%</td>
                <td>
                    <div class="mark-bar-wrap">
                        <span style="min-width:36px;"><?= number_format($mark,1) ?></span>
                        <div class="mark-bar">
                            <div class="mark-bar-fill <?= $barColour ?>" style="width:<?= min($mark,100) ?>%"></div>
                        </div>
                    </div>
                </td>
                <td class="total-cell"><?= number_format($m['total_marks'],2) ?></td>
                <td class="text-muted" style="font-style:italic; font-size:12px;">
                    <?= htmlspecialchars($m['comments'] ?? '—') ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr style="background:#f0f4f8; font-weight:700;">
                <td colspan="4" style="text-align:right;">Supervisor Total</td>
                <td colspan="2"><?= number_format(array_sum(array_column($supMarks,'total_marks')),2) ?> / 100</td>
            </tr>
            </tbody>
        </table>
        </div>
        <?php else: ?>
            <p class="text-muted">Supervisor marks have not been entered yet.</p>
        <?php endif; ?>
    </div>

    <!-- Lecturer Marks Breakdown -->
    <div class="card">
        <div class="card-title">
            Lecturer Assessment
            <?php if ($lecMarks): ?>
                <?php
                    $lecTotal = array_sum(array_column($lecMarks, 'total_marks'));
                    echo "– <span style='color:var(--accent)'>" . number_format($lecTotal,2) . " / 100</span>";
                ?>
            <?php endif; ?>
        </div>

        <?php if ($lecMarks): ?>
        <div class="table-wrap">
        <table class="marks-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Component</th>
                    <th>Weight</th>
                    <th>Your Mark</th>
                    <th>Weighted Score</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($lecMarks as $i => $m):
                $mark      = (float)$m['component_mark'];
                $barColour = $mark >= 70 ? 'green' : ($mark >= 50 ? 'orange' : 'red');
            ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><?= htmlspecialchars($m['component_name']) ?></td>
                <td class="weight-cell"><?= $m['component_weightage'] ?>%</td>
                <td>
                    <div class="mark-bar-wrap">
                        <span style="min-width:36px;"><?= number_format($mark,1) ?></span>
                        <div class="mark-bar">
                            <div class="mark-bar-fill <?= $barColour ?>" style="width:<?= min($mark,100) ?>%"></div>
                        </div>
                    </div>
                </td>
                <td class="total-cell"><?= number_format($m['total_marks'],2) ?></td>
                <td class="text-muted" style="font-style:italic; font-size:12px;">
                    <?= htmlspecialchars($m['comments'] ?? '—') ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr style="background:#f0f4f8; font-weight:700;">
                <td colspan="4" style="text-align:right;">Lecturer Total</td>
                <td colspan="2"><?= number_format(array_sum(array_column($lecMarks,'total_marks')),2) ?> / 100</td>
            </tr>
            </tbody>
        </table>
        </div>
        <?php else: ?>
            <p class="text-muted">Lecturer marks have not been entered yet.</p>
        <?php endif; ?>
    </div>

<?php endif; // end if data ?>

</div><!-- page-body -->
</div><!-- main-content -->
</div><!-- layout -->
<script src="../script.js"></script>
</body>
</html>
