<?php
session_start();
require_once "../connection.php";
$pageTitle = "Result Detail";
include "header.php";

if (!isset($_GET['id'])) {
    header("Location: results.php");
    exit();
}

$iid = (int)$_GET['id'];

// Get internship + student info
$sql = "SELECT i.*, s.student_id, s.student_name, s.programme, s.student_email,
               c.company_name, l.lecturer_name, sv.supervisor_name
        FROM internship i
        LEFT JOIN student    s  ON i.student_id    = s.student_id
        LEFT JOIN company    c  ON i.company_id    = c.company_id
        LEFT JOIN lecturer   l  ON i.lecturer_id   = l.lecturer_id
        LEFT JOIN supervisor sv ON i.supervisor_id = sv.supervisor_id
        WHERE i.internship_id = $iid";
$res = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);

if (!$data) {
    echo "<div class='page-body'><div class='alert alert-danger'>Record not found.</div></div>";
    include "footer.php";
    exit();
}

// Get supervisor component marks
$supMarks = [];
if ($data['s_marks_id']) {
    $smid = (int)$data['s_marks_id'];
    $smRes = mysqli_query($conn,
        "SELECT sm.*, c.component_name, c.component_weightage
         FROM supervisor_marks sm
         JOIN component c ON sm.component_id = c.component_id
         WHERE sm.s_marks_id = $smid
         ORDER BY c.component_id");
    while ($r = mysqli_fetch_assoc($smRes)) $supMarks[] = $r;
}

// Get lecturer component marks
$lecMarks = [];
if ($data['l_marks_id']) {
    $lmid = (int)$data['l_marks_id'];
    $lmRes = mysqli_query($conn,
        "SELECT lm.*, c.component_name, c.component_weightage
         FROM lecturer_marks lm
         JOIN component c ON lm.component_id = c.component_id
         WHERE lm.l_marks_id = $lmid
         ORDER BY c.component_id");
    while ($r = mysqli_fetch_assoc($lmRes)) $lecMarks[] = $r;
}

$avg = $data['average_marks'];
$finalClass = 'pass';
$finalLabel = 'Pass';
if ($avg !== null) {
    if ($avg >= 80) { $finalClass = 'distinction'; $finalLabel = '🌟 Distinction'; }
    elseif ($avg >= 50) { $finalClass = 'pass'; $finalLabel = '✅ Pass'; }
    else { $finalClass = 'fail'; $finalLabel = '❌ Fail'; }
}
?>

<div class="topbar">
    <h1>Result Detail</h1>
    
</div>

<div class="page-body">

    <div style="margin-bottom:12px;">
        <a href="results.php" class="btn btn-outline btn-sm">← Back to Results</a>
    </div>

    <!-- Final Mark Box -->
    <?php if ($avg !== null): ?>
    <div class="final-mark-box <?= $finalClass ?>">
        <div class="big-mark"><?= number_format($avg,2) ?></div>
        <div class="mark-label"><?= $finalLabel ?></div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">Marks have not been entered yet for this student.</div>
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
    </div>

    <!-- Supervisor Marks -->
    <div class="card">
        <div class="card-title">Supervisor Assessment – <?= htmlspecialchars($data['supervisor_name'] ?? '—') ?></div>
        <?php if ($supMarks): ?>
        <div class="table-wrap">
        <table class="marks-table">
            <thead>
                <tr>
                    <th>Component</th>
                    <th>Weight</th>
                    <th>Mark (/100)</th>
                    <th>Weighted</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
            <?php $supTotal = 0; foreach ($supMarks as $m): $supTotal += $m['total_marks']; ?>
            <tr>
                <td><?= htmlspecialchars($m['component_name']) ?></td>
                <td class="weight-cell"><?= $m['component_weightage'] ?>%</td>
                <td><?= number_format($m['component_mark'],1) ?></td>
                <td class="total-cell"><?= number_format($m['total_marks'],2) ?></td>
                <td class="text-muted"><?= htmlspecialchars($m['comments'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="background:#f0f4f8; font-weight:700;">
                <td colspan="3">Supervisor Total</td>
                <td colspan="2"><?= number_format($supTotal,2) ?> / 100</td>
            </tr>
            </tbody>
        </table>
        </div>
        <?php else: ?>
            <p class="text-muted">No supervisor marks entered yet.</p>
        <?php endif; ?>
    </div>

    <!-- Lecturer Marks -->
    <div class="card">
        <div class="card-title">Lecturer Assessment – <?= htmlspecialchars($data['lecturer_name'] ?? '—') ?></div>
        <?php if ($lecMarks): ?>
        <div class="table-wrap">
        <table class="marks-table">
            <thead>
                <tr>
                    <th>Component</th>
                    <th>Weight</th>
                    <th>Mark (/100)</th>
                    <th>Weighted</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
            <?php $lecTotal = 0; foreach ($lecMarks as $m): $lecTotal += $m['total_marks']; ?>
            <tr>
                <td><?= htmlspecialchars($m['component_name']) ?></td>
                <td class="weight-cell"><?= $m['component_weightage'] ?>%</td>
                <td><?= number_format($m['component_mark'],1) ?></td>
                <td class="total-cell"><?= number_format($m['total_marks'],2) ?></td>
                <td class="text-muted"><?= htmlspecialchars($m['comments'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr style="background:#f0f4f8; font-weight:700;">
                <td colspan="3">Lecturer Total</td>
                <td colspan="2"><?= number_format($lecTotal,2) ?> / 100</td>
            </tr>
            </tbody>
        </table>
        </div>
        <?php else: ?>
            <p class="text-muted">No lecturer marks entered yet.</p>
        <?php endif; ?>
    </div>

</div>
<?php include "footer.php"; ?>
