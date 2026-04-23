<?php
session_start();
require_once "../connection.php";
$pageTitle = "Results";
include "header.php";

// Search/filter
$searchTerm = isset($_GET['search']) ? mysqli_real_escape_string($conn, trim($_GET['search'])) : '';
$whereClause = '';
if ($searchTerm) {
    $whereClause = "WHERE s.student_id LIKE '%$searchTerm%' OR s.student_name LIKE '%$searchTerm%'";
}

$sql = "SELECT i.internship_id, i.average_marks, i.s_marks_id, i.l_marks_id,
               s.student_id, s.student_name, s.programme,
               c.company_name, l.lecturer_name, sv.supervisor_name
        FROM internship i
        LEFT JOIN student    s  ON i.student_id    = s.student_id
        LEFT JOIN company    c  ON i.company_id    = c.company_id
        LEFT JOIN lecturer   l  ON i.lecturer_id   = l.lecturer_id
        LEFT JOIN supervisor sv ON i.supervisor_id = sv.supervisor_id
        $whereClause
        ORDER BY i.internship_id";
$results = mysqli_query($conn, $sql);
?>

<div class="topbar">
    <h1>All Internship Results</h1>
    
</div>

<div class="page-body">

<!-- Search -->
<div class="card card-form">
    <div class="card-title">Search Results</div>
    <div class="card-body">
    <form method="GET" style="display:flex; gap:10px; align-items:flex-end;">
        <div class="form-group mb-0" style="flex:1">
            <label>Search by Student ID or Name</label>
            <input type="text" name="search" value="<?= htmlspecialchars($searchTerm) ?>" placeholder="Type to search...">
        </div>
        <button type="submit" class="btn btn-primary">🔍 Search</button>
        <?php if ($searchTerm): ?>
            <a href="results.php" class="btn btn-outline">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Legend -->
<div style="display:flex; gap:12px; margin-bottom:16px; flex-wrap:wrap;">
    <span class="badge badge-distinction">🌟 Distinction (≥ 80)</span>
    <span class="badge badge-pass">✅ Pass (50–79)</span>
    <span class="badge badge-fail">❌ Fail (&lt; 50)</span>
    <span class="text-muted" style="font-size:12px; align-self:center;">⚪ Pending = marks not entered yet</span>
</div>

<br>
<br>
<!-- Results Table -->
<div class="card card-table">
    <div class="card-title">Results Overview</div>
    <div class="table-wrap">
    <table id="mainTable">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Programme</th>
                <th>Company</th>
                <th>Lecturer</th>
                <th>Supervisor</th>
                <th>Supervisor Score</th>
                <th>Lecturer Score</th>
                <th>Average Marks</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($results)):
            $avg = $row['average_marks'];

            // Get supervisor total
            $sup_total = null;
            if ($row['s_marks_id']) {
                $smid = (int)$row['s_marks_id'];
                $stRes = mysqli_query($conn, "SELECT SUM(total_marks) as st FROM supervisor_marks WHERE s_marks_id=$smid");
                $stRow = mysqli_fetch_assoc($stRes);
                $sup_total = $stRow['st'];
            }

            // Get lecturer total
            $lec_total = null;
            if ($row['l_marks_id']) {
                $lmid = (int)$row['l_marks_id'];
                $ltRes = mysqli_query($conn, "SELECT SUM(total_marks) as lt FROM lecturer_marks WHERE l_marks_id=$lmid");
                $ltRow = mysqli_fetch_assoc($ltRes);
                $lec_total = $ltRow['lt'];
            }

            // Row class for highlighting
            $rowClass = 'result-row';
            $badge = '<span class="text-muted">Pending</span>';
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
            <td><?= htmlspecialchars($row['lecturer_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['supervisor_name'] ?? '—') ?></td>
            <td><?= $sup_total !== null ? number_format($sup_total,2) : '<span class="text-muted">—</span>' ?></td>
            <td><?= $lec_total !== null ? number_format($lec_total,2) : '<span class="text-muted">—</span>' ?></td>
            <td class="mark-value">
                <?php if ($avg !== null): ?>
                    <strong><?= number_format($avg,2) ?></strong>
                <?php else: ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>
            </td>
            <td><?= $badge ?></td>
            <td>
                <a href="result_detail.php?id=<?= $row['internship_id'] ?>" class="btn btn-outline btn-sm">📋 View</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>

</div>
<?php include "footer.php"; ?>
