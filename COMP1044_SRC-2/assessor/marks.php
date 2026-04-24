<?php
session_start();
require_once "../connection.php";
$pageTitle = "Enter Marks";
include "header.php";

$uid  = $_SESSION['user_id'];
$role = $_SESSION['role']; // 'lecturer' or 'supervisor'
$msg  = "";

// ---- SAVE MARKS ----
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_marks') {
    $iid        = (int)$_POST['internship_id'];
    $marks      = $_POST['marks'];      // array: component_id => mark value
    $comments   = $_POST['comments'];   // array: component_id => comment

    // Validate all marks 0-100
    $valid = true;
    foreach ($marks as $m) {
        if ($m === '' || $m < 0 || $m > 100) { $valid = false; break; }
    }

    if (!$valid) {
        $msg = "ERROR: All marks must be between 0 and 100.";
    } else {
        // Get weightages from component table
        $compRes = mysqli_query($conn, "SELECT component_id, component_weightage FROM component");
        $weights = [];
        while ($c = mysqli_fetch_assoc($compRes)) {
            $weights[$c['component_id']] = $c['component_weightage'];
        }

        // Determine which marks table to use
        if ($role == 'supervisor') {
            $marksTable  = 'supervisor_marks';
            $marksIdCol  = 's_marks_id';
            $internshipCol = 's_marks_id'; // column in internship table
        } else {
            $marksTable  = 'lecturer_marks';
            $marksIdCol  = 'l_marks_id';
            $internshipCol = 'l_marks_id';
        }

        // Check if marks already exist for this internship
        $intRes = mysqli_query($conn, "SELECT $internshipCol FROM internship WHERE internship_id=$iid");
        $intRow = mysqli_fetch_assoc($intRes);
        $existingMarksId = $intRow[$internshipCol];

        if ($existingMarksId) {
            // UPDATE existing rows
            $smid = (int)$existingMarksId;
            foreach ($marks as $cid => $mark) {
                $cid     = (int)$cid;
                $mark    = (float)$mark;
                $w       = isset($weights[$cid]) ? $weights[$cid] : 0;
                $total   = ($mark * $w) / 100;
                $comment = mysqli_real_escape_string($conn, trim($comments[$cid] ?? ''));

                mysqli_query($conn,
                    "UPDATE $marksTable
                     SET component_mark=$mark, total_marks=$total, comments='$comment'
                     WHERE $marksIdCol=$smid AND component_id=$cid");
            }
            $newMarksId = $smid;
        } else {
            // INSERT new rows — use internship_id as the marks group id
            $newMarksId = $iid;
            foreach ($marks as $cid => $mark) {
                $cid     = (int)$cid;
                $mark    = (float)$mark;
                $w       = isset($weights[$cid]) ? $weights[$cid] : 0;
                $total   = ($mark * $w) / 100;
                $comment = mysqli_real_escape_string($conn, trim($comments[$cid] ?? ''));

                mysqli_query($conn,
                    "INSERT INTO $marksTable ($marksIdCol, component_id, component_mark, comments, total_marks)
                     VALUES ($newMarksId, $cid, $mark, '$comment', $total)");
            }
            // Link marks id back to internship
            mysqli_query($conn,
                "UPDATE internship SET $internshipCol=$newMarksId WHERE internship_id=$iid");
        }

        // Recalculate average_marks (average of supervisor total + lecturer total)
        $intRes2  = mysqli_query($conn, "SELECT s_marks_id, l_marks_id FROM internship WHERE internship_id=$iid");
        $intRow2  = mysqli_fetch_assoc($intRes2);
        $smid2    = $intRow2['s_marks_id'];
        $lmid2    = $intRow2['l_marks_id'];

        $sTotal = null;
        $lTotal = null;

        if ($smid2) {
            $st = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_marks) FROM supervisor_marks WHERE s_marks_id=$smid2"));
            $sTotal = (float)$st[0];
        }
        if ($lmid2) {
            $lt = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_marks) FROM lecturer_marks WHERE l_marks_id=$lmid2"));
            $lTotal = (float)$lt[0];
        }

        // Calculate average based on what's available
        if ($sTotal !== null && $lTotal !== null) {
            $avg = ($sTotal + $lTotal) / 2;
        } elseif ($sTotal !== null) {
            $avg = $sTotal;
        } elseif ($lTotal !== null) {
            $avg = $lTotal;
        } else {
            $avg = null;
        }

        if ($avg !== null) {
            mysqli_query($conn, "UPDATE internship SET average_marks=$avg WHERE internship_id=$iid");
        }

        $msg = "Marks saved successfully!";
    }
}

// Get students assigned to this assessor
if ($role == 'supervisor') {
    $colFilter = "supervisor_id";
} else {
    $colFilter = "lecturer_id";
}

$studentsRes = mysqli_query($conn,
    "SELECT i.internship_id, i.s_marks_id, i.l_marks_id,
            s.student_id, s.student_name, s.programme
     FROM internship i
     JOIN student s ON i.student_id = s.student_id
     WHERE i.$colFilter = '$uid'
     ORDER BY s.student_name");
$students = [];
while ($r = mysqli_fetch_assoc($studentsRes)) $students[] = $r;

// Which student is selected for marks entry
$selectedStudent = null;
$existingMarks   = [];
$selectedIid     = isset($_GET['iid']) ? (int)$_GET['iid'] : 0;

if ($selectedIid) {
    foreach ($students as $st) {
        if ($st['internship_id'] == $selectedIid) {
            $selectedStudent = $st;
            break;
        }
    }

    // Load existing marks if any
    if ($selectedStudent) {
        $marksIdCol = ($role == 'supervisor') ? 's_marks_id' : 'l_marks_id';
        $marksTable = ($role == 'supervisor') ? 'supervisor_marks' : 'lecturer_marks';
        $midVal     = $selectedStudent[$marksIdCol];

        if ($midVal) {
            $emRes = mysqli_query($conn,
                "SELECT * FROM $marksTable WHERE $marksIdCol = $midVal");
            while ($r = mysqli_fetch_assoc($emRes)) {
                $existingMarks[$r['component_id']] = $r;
            }
        }
    }
}

// Get all components
$compRes = mysqli_query($conn, "SELECT * FROM component ORDER BY component_id");
$components = [];
while ($c = mysqli_fetch_assoc($compRes)) $components[] = $c;
?>

<div class="topbar">
    <h1>Enter Marks</h1>
    <span class="breadcrumb">Assessor &rsaquo; Enter Marks</span>
</div>

<div class="page-body">

<?php if ($msg): ?>
    <div class="alert <?= strpos($msg,'ERROR') === 0 ? 'alert-danger' : 'alert-success' ?>">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Step 1: Select Student -->
<div class="card">
    <div class="card-title">Step 1 – Select Student</div>
    <?php if (empty($students)): ?>
        <p class="text-muted">No students are assigned to you yet.</p>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Programme</th>
                <th>Marks Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $st):
            $marksIdCol = ($role == 'supervisor') ? 's_marks_id' : 'l_marks_id';
            $hasMarks   = !empty($st[$marksIdCol]);
        ?>
        <tr class="<?= ($selectedIid == $st['internship_id']) ? 'row-pass' : '' ?>">
            <td><?= htmlspecialchars($st['student_id']) ?></td>
            <td><?= htmlspecialchars($st['student_name']) ?></td>
            <td><?= htmlspecialchars($st['programme']) ?></td>
            <td>
                <?php if ($hasMarks): ?>
                    <span class="badge badge-pass">✅ Marked</span>
                <?php else: ?>
                    <span class="badge badge-fail">⏳ Pending</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="marks.php?iid=<?= $st['internship_id'] ?>" class="btn btn-primary btn-sm">
                    <?= $hasMarks ? '✏️ Edit Marks' : '➕ Enter Marks' ?>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<!-- Step 2: Marks Entry Form -->
<?php if ($selectedStudent): ?>
<div class="card">
    <div class="card-title">
        Step 2 – Marks for: <strong><?= htmlspecialchars($selectedStudent['student_name']) ?></strong>
        (<?= htmlspecialchars($selectedStudent['student_id']) ?>)
    </div>

    <div class="alert alert-info" style="font-size:12px;">
        💡 Enter a mark out of <strong>100</strong> for each component. The weighted score is calculated automatically.
        All marks must be between <strong>0 and 100</strong>.
    </div>

    <form method="POST" onsubmit="return validateMarksForm()">
        <input type="hidden" name="action"        value="save_marks">
        <input type="hidden" name="internship_id" value="<?= $selectedStudent['internship_id'] ?>">

        <div class="table-wrap">
        <table class="marks-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Assessment Component</th>
                    <th>Weightage</th>
                    <th>Mark (/100)</th>
                    <th>Weighted Score</th>
                    <th>Comment (optional)</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($components as $i => $comp):
                $cid      = $comp['component_id'];
                $prevMark = $existingMarks[$cid]['component_mark'] ?? '';
                $prevCmt  = $existingMarks[$cid]['comments'] ?? '';
                $prevWt   = $existingMarks[$cid]['total_marks'] ?? '0.00';
            ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><?= htmlspecialchars($comp['component_name']) ?></td>
                <td class="weight-cell"><?= $comp['component_weightage'] ?>%</td>
                <td>
                    <input type="number"
                           class="mark-input"
                           name="marks[<?= $cid ?>]"
                           value="<?= htmlspecialchars($prevMark) ?>"
                           min="0" max="100" step="0.5"
                           data-weight="<?= $comp['component_weightage'] ?>"
                           required>
                </td>
                <td class="total-cell"><?= $prevMark !== '' ? number_format((float)$prevMark * $comp['component_weightage'] / 100, 2) : '0.00' ?></td>
                <td>
                    <input type="text"
                           name="comments[<?= $cid ?>]"
                           value="<?= htmlspecialchars($prevCmt) ?>"
                           placeholder="Optional comment..."
                           style="width:100%; padding:5px 8px; border:1px solid #dce3ec; border-radius:4px; font-size:12px;">
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background:#1a3a5c; color:#fff;">
                    <td colspan="4" style="padding:12px 14px; font-weight:700; text-align:right;">
                        Total Weighted Score:
                    </td>
                    <td colspan="2" style="padding:12px 14px; font-size:18px; font-weight:800;">
                        <span id="grand-total">
                            <?php
                            if ($existingMarks) {
                                $gt = array_sum(array_column($existingMarks, 'total_marks'));
                                echo number_format($gt, 2);
                            } else {
                                echo '0.00';
                            }
                            ?>
                        </span>
                        / 100
                    </td>
                </tr>
            </tfoot>
        </table>
        </div>

        <div style="margin-top:16px; display:flex; gap:10px;">
            <button type="submit" class="btn btn-success">💾 Save Marks</button>
            <a href="marks.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

</div>
<?php include "footer.php"; ?>
