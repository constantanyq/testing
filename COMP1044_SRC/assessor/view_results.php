<?php
session_start();
require_once "../connection.php";
$pageTitle = "View Results";
include "header.php";

$uid  = $_SESSION['user_id'];
$role = $_SESSION['role'];

$colFilter = ($role == 'supervisor') ? 'supervisor_id' : 'lecturer_id';

// Search
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$extra  = $search ? "AND (s.student_id LIKE '%$search%' OR s.student_name LIKE '%$search%')" : '';

$sql = "SELECT i.internship_id, i.average_marks, i.s_marks_id, i.l_marks_id,
               s.student_id, s.student_name, s.programme,
               c.company_name
        FROM internship i
        JOIN student s    ON i.student_id   = s.student_id
        LEFT JOIN company c ON i.company_id = c.company_id
        WHERE i.$colFilter = '$uid' $extra
        ORDER BY s.student_name";
$results = mysqli_query($conn, $sql);
?>

<div class="topbar">
    <h1>My Students' Results</h1>
    <span class="breadcrumb">Assessor &rsaquo; Results</span>
</div>

<div class="page-body">

<!-- Search -->
<div class="card">
    <form method="GET" style="display:flex; gap:10px; align-items:flex-end;">
        <div class="form-group mb-0" style="flex:1">
            <label>Search by Student ID or Name</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Type to search...">
        </div>
        <button type="submit" class="btn btn-primary">🔍 Search</button>
        <?php if ($search): ?>
            <a href="view_results.php" class="btn btn-outline">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Highlight Legend -->
<div style="display:flex; gap:12px; margin-bottom:14px; flex-wrap:wrap;">
    <span class="badge badge-distinction">🌟 Distinction (≥ 80)</span>
    <span class="badge badge-pass">✅ Pass (50–79)</span>
    <span class="badge badge-fail">❌ Fail (&lt; 50)</span>
</div>

<!-- Results Table -->
<div class="card">
    <div class="card-title">Results</div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Programme</th>
                <th>Company</th>
                <th>My Score</th>
                <th>Average Marks</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($results)):
            $avg = $row['average_marks'];

            // My own score
            $myScore = null;
            if ($role == 'supervisor' && $row['s_marks_id']) {
                $smid   = (int)$row['s_marks_id'];
                $stRes  = mysqli_query($conn, "SELECT SUM(total_marks) as t FROM supervisor_marks WHERE s_marks_id=$smid");
                $myScore = (float)mysqli_fetch_assoc($stRes)['t'];
            } elseif ($role == 'lecturer' && $row['l_marks_id']) {
                $lmid   = (int)$row['l_marks_id'];
                $ltRes  = mysqli_query($conn, "SELECT SUM(total_marks) as t FROM lecturer_marks WHERE l_marks_id=$lmid");
                $myScore = (float)mysqli_fetch_assoc($ltRes)['t'];
            }

            // Row colour
            $rowClass = 'result-row';
            $badge    = '<span class="text-muted">Pending</span>';
            if ($avg !== null) {
                if ($avg >= 80) {
                    $rowClass .= ' row-distinction';
                    $badge = '<span class="badge badge-distinction">🌟 Distinction</span>';
                } elseif ($avg >= 50) {
                    $rowClass .= ' row-pass';
                    $badge = '<span class="badge badge-pass">✅ Pass</span>';
                } else {
                    $rowClass .= ' row-fail';
                    $badge = '<span class="badge badge-fail">❌ Fail</span>';
                }
            }
        ?>
        <tr class="<?= $rowClass ?>">
            <td><?= htmlspecialchars($row['student_id']) ?></td>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['programme']) ?></td>
            <td><?= htmlspecialchars($row['company_name'] ?? '—') ?></td>
            <td>
                <?= $myScore !== null ? number_format($myScore, 2) : '<span class="text-muted">Not entered</span>' ?>
            </td>
            <td class="mark-value">
                <?= $avg !== null ? '<strong>'.number_format($avg,2).'</strong>' : '<span class="text-muted">—</span>' ?>
            </td>
            <td><?= $badge ?></td>
            <td>
                <a href="result_detail.php?iid=<?= $row['internship_id'] ?>" class="btn btn-outline btn-sm">📋 View</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>

</div>
<?php include "footer.php"; ?>
