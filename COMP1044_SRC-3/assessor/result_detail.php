<?php
session_start();
require_once "../connection.php";
$pageTitle = "Result Detail";
include "header.php";

$uid  = $_SESSION['user_id'];
$role = $_SESSION['role'];

if (!isset($_GET['iid'])) {
    header("Location: view_results.php");
    exit();
}

$iid       = (int)$_GET['iid'];
$colFilter = ($role == 'supervisor') ? 'supervisor_id' : 'lecturer_id';

// Verify this student is assigned to this assessor
$sql = "SELECT i.*, s.student_id, s.student_name, s.programme, s.student_email,
               c.company_name, l.lecturer_name, sv.supervisor_name
        FROM internship i
        LEFT JOIN student    s  ON i.student_id    = s.student_id
        LEFT JOIN company    c  ON i.company_id    = c.company_id
        LEFT JOIN lecturer   l  ON i.lecturer_id   = l.lecturer_id
        LEFT JOIN supervisor sv ON i.supervisor_id = sv.supervisor_id
        WHERE i.internship_id = $iid AND i.$colFilter = '$uid'";
$res  = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);

if (!$data) {
    echo "<div class='page-body'><div class='alert alert-danger'>Record not found or access denied.</div></div>";
    include "footer.php";
    exit();
}

// Get supervisor component marks
$supMarks = [];
if ($data['s_marks_id']) {
    $smid  = (int)$data['s_marks_id'];
    $smRes = mysqli_query($conn,
        "SELECT sm.*, comp.component_name, comp.component_weightage
         FROM supervisor_marks sm
         JOIN component comp ON sm.component_id = comp.component_id
         WHERE sm.s_marks_id = $smid ORDER BY comp.component_id");
    while ($r = mysqli_fetch_assoc($smRes)) $supMarks[] = $r;
}

// Get lecturer component marks
$lecMarks = [];
if ($data['l_marks_id']) {
    $lmid  = (int)$data['l_marks_id'];
    $lmRes = mysqli_query($conn,
        "SELECT lm.*, comp.component_name, comp.component_weightage
         FROM lecturer_marks lm
         JOIN component comp ON lm.component_id = comp.component_id
         WHERE lm.l_marks_id = $lmid ORDER BY comp.component_id");
    while ($r = mysqli_fetch_assoc($lmRes)) $lecMarks[] = $r;
}

$avg        = $data['average_marks'];
$finalClass = 'pass';
$finalLabel = 'Pass';
if ($avg !== null) {
    if ($avg >= 80)     { $finalClass = 'distinction'; $finalLabel = '🌟 Distinction'; }
    elseif ($avg >= 50) { $finalClass = 'pass';        $finalLabel = '✅ Pass'; }
    else                { $finalClass = 'fail';         $finalLabel = '❌ Fail'; }
}
?>

<div class="topbar">
    <h1>Result Detail</h1>
    <span class="breadcrumb">Assessor &rsaquo; Results &rsaquo; Detail</span>
</div>

<div class="page-body">

    <div style="margin-bottom:12px; display:flex; gap:10px;">
        <a href="view_results.php" class="btn btn-outline btn-sm">← Back</a>
        <a href="marks.php?iid=<?= $iid ?>" class="btn btn-warning btn-sm">✏️ Edit My Marks</a>
    </div>

    <?php if ($avg !== null): ?>
    <div class="final-mark-box <?= $finalClass ?>">
        <div class="big-mark"><?= number_format($avg, 2) ?></div>
        <div class="mark-label"><?= $finalLabel ?></div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">Marks have not been fully entered yet.</div>
    <?php endif; ?>

    <!-- Student Summary -->
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
    </div>

    <!-- Marks breakdown (shared helper) -->
    <?php
    function renderMarksTable($marksArray, $title) {
        echo "<div class='card'>";
        echo "<div class='card-title'>$title</div>";
        if (empty($marksArray)) {
            echo "<p class='text-muted'>No marks entered yet.</p>";
        } else {
            echo "<div class='table-wrap'><table class='marks-table'>";
            echo "<thead><tr><th>Component</th><th>Weight</th><th>Mark /100</th><th>Weighted</th><th>Comment</th></tr></thead>";
            echo "<tbody>";
            $total = 0;
            foreach ($marksArray as $m) {
                $total += $m['total_marks'];
                $mark  = (float)$m['component_mark'];
                // Mini progress bar colour
                $barColour = $mark >= 70 ? 'green' : ($mark >= 50 ? 'orange' : 'red');
                echo "<tr>";
                echo "<td>".htmlspecialchars($m['component_name'])."</td>";
                echo "<td class='weight-cell'>".$m['component_weightage']."%</td>";
                echo "<td>
                        <div class='mark-bar-wrap'>
                            <span>".number_format($mark,1)."</span>
                            <div class='mark-bar'>
                                <div class='mark-bar-fill $barColour' style='width:".min($mark,100)."%'></div>
                            </div>
                        </div>
                      </td>";
                echo "<td class='total-cell'>".number_format($m['total_marks'],2)."</td>";
                echo "<td class='text-muted'>".htmlspecialchars($m['comments'] ?? '')."</td>";
                echo "</tr>";
            }
            echo "<tr style='background:#f0f4f8; font-weight:700;'>
                    <td colspan='3'>Total</td>
                    <td colspan='2'>".number_format($total,2)." / 100</td>
                  </tr>";
            echo "</tbody></table></div>";
        }
        echo "</div>";
    }

    renderMarksTable($supMarks, "Supervisor Assessment – " . htmlspecialchars($data['supervisor_name'] ?? '—'));
    renderMarksTable($lecMarks, "Lecturer Assessment – "   . htmlspecialchars($data['lecturer_name']   ?? '—'));
    ?>

</div>
<?php include "footer.php"; ?>
