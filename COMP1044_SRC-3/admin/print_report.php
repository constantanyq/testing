<?php
session_start();
require_once "../connection.php";
$pageTitle = "Print Report";
include "header.php";

// Filters
$filterLecturer = isset($_GET['assessor']) ? $_GET['assessor'] : '';
$filterGrade = isset($_GET['grade_filter']) ? $_GET['grade_filter'] : '';

// Build query with optional filters
$where = [];
if ($filterLecturer !== '') {
    $fl = mysqli_real_escape_string($conn, $filterLecturer);
    $where[] = "i.lecturer_id = '$fl'";
}

// Grade filter applied after fetching (since grade is derived)
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT i.internship_id, s.student_id, s.student_name, s.programme,
               c.company_name, l.lecturer_name, l.lecturer_id,
               sv.supervisor_name, i.average_marks
        FROM internship i
        LEFT JOIN student    s  ON i.student_id    = s.student_id
        LEFT JOIN company    c  ON i.company_id    = c.company_id
        LEFT JOIN lecturer   l  ON i.lecturer_id   = l.lecturer_id
        LEFT JOIN supervisor sv ON i.supervisor_id = sv.supervisor_id
        $whereSQL
        ORDER BY s.student_id";
$res = mysqli_query($conn, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($res)) {
    // derive grade
    $m = $r['average_marks'];
    if ($m === null) {
        $r['grade'] = 'Pending';
    } elseif ($m >= 80) {
        $r['grade'] = 'Distinction';
    } elseif ($m >= 50) {
        $r['grade'] = 'Pass';
    } else {
        $r['grade'] = 'Fail';
    }
    $rows[] = $r;
}

// Apply grade filter in PHP
if ($filterGrade !== '') {
    $rows = array_filter($rows, fn($r) => strtolower($r['grade']) === strtolower($filterGrade));
    $rows = array_values($rows);
}

// Stats 
$cTotal = count($rows);
$cDistinct = count(array_filter($rows, fn($r) => $r['grade'] === 'Distinction'));
$cPass = count(array_filter($rows, fn($r) => $r['grade'] === 'Pass'));
$cFail = count(array_filter($rows, fn($r) => $r['grade'] === 'Fail'));
$cPending = count(array_filter($rows, fn($r) => $r['grade'] === 'Pending'));
$gradedRows = array_filter($rows, fn($r) => $r['average_marks'] !== null);
$avgMark = count($gradedRows) > 0
    ? array_sum(array_column(iterator_to_array((function () use ($gradedRows) {
        foreach ($gradedRows as $r)
            yield $r; })()), 'average_marks')) / count($gradedRows)
    : null;
// Simpler avg calculation
$markSum = 0;
$markCount = 0;
foreach ($rows as $r) {
    if ($r['average_marks'] !== null) {
        $markSum += $r['average_marks'];
        $markCount++;
    }
}
$avgMark = $markCount > 0 ? $markSum / $markCount : null;

// Lecturer list for filter dropdown 
$lecRes = mysqli_query($conn, "SELECT lecturer_id, lecturer_name FROM lecturer ORDER BY lecturer_name");
$lecturers = [];
while ($l = mysqli_fetch_assoc($lecRes))
    $lecturers[] = $l;
?>

<!-- Topbar with breadcrumb + action buttons -->
<div class="topbar rpt-topbar">
    <span class="breadcrumb">Admin &rsaquo; Print Results</span>
    <div class="rpt-topbar-actions">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Print This Page</button>
        <a href="dashboard.php" class="btn btn-outline">← Back</a>
    </div>
</div>

<div class="page-body">

    <!-- Report Options card -->
    <div class="card rpt-options-card">
        <div class="rpt-options-header">
            <span>⚙️ 🔍 <strong>Report Options</strong></span>
        </div>
        <form method="GET" action="print_report.php" class="rpt-filter-form">
            <div class="rpt-filter-row">
                <div class="rpt-filter-group">
                    <label class="rpt-filter-label">FILTER BY ASSESSOR</label>
                    <select name="assessor" class="rpt-select">
                        <option value="">All Students</option>
                        <?php foreach ($lecturers as $l): ?>
                            <option value="<?= htmlspecialchars($l['lecturer_id']) ?>"
                                <?= $filterLecturer === $l['lecturer_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($l['lecturer_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="rpt-filter-group">
                    <label class="rpt-filter-label">GRADE FILTER</label>
                    <select name="grade_filter" class="rpt-select">
                        <option value="">All Grades</option>
                        <option value="Distinction" <?= $filterGrade === 'Distinction' ? 'selected' : '' ?>>Distinction
                        </option>
                        <option value="Pass" <?= $filterGrade === 'Pass' ? 'selected' : '' ?>>Pass</option>
                        <option value="Fail" <?= $filterGrade === 'Fail' ? 'selected' : '' ?>>Fail</option>
                        <option value="Pending" <?= $filterGrade === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:16px;">Apply Filter</button>
        </form>
    </div>

    <!-- Stat boxes -->
    <div class="rpt-stats-row">
        <div class="rpt-stat-box">
            <div class="rpt-stat-num"><?= $cTotal ?></div>
            <div class="rpt-stat-label">TOTAL</div>
        </div>
        <div class="rpt-stat-box rpt-stat-distinction">
            <div class="rpt-stat-num"><?= $cDistinct ?></div>
            <div class="rpt-stat-label">DISTINCTION</div>
        </div>
        <div class="rpt-stat-box rpt-stat-pass">
            <div class="rpt-stat-num"><?= $cPass ?></div>
            <div class="rpt-stat-label">PASS</div>
        </div>
        <div class="rpt-stat-box rpt-stat-fail">
            <div class="rpt-stat-num"><?= $cFail ?></div>
            <div class="rpt-stat-label">FAIL</div>
        </div>
        <div class="rpt-stat-box rpt-stat-pending">
            <div class="rpt-stat-num"><?= $cPending ?></div>
            <div class="rpt-stat-label">PENDING</div>
        </div>
        <div class="rpt-stat-box rpt-stat-avg">
            <div class="rpt-stat-num"><?= $avgMark !== null ? number_format($avgMark, 1) . '%' : '—' ?></div>
            <div class="rpt-stat-label">AVG. MARK</div>
        </div>
    </div>

    <!-- Results table -->
    <div class="card rpt-table-card">
        <div class="rpt-table-header">
            <span>≡ 📋 <strong>Internship Results</strong></span>
            <span class="rpt-record-count"><?= $cTotal ?> records</span>
        </div>
        <div class="table-wrap">
            <table class="rpt-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>STUDENT ID</th>
                        <th>NAME</th>
                        <th>PROGRAMME</th>
                        <th>COMPANY</th>
                        <th>LECTURER</th>
                        <th>SUPERVISOR</th>
                        <th>MARK</th>
                        <th>GRADE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $i => $row):
                        $grade = $row['grade'];
                        $mark = $row['average_marks'];
                        $rowClass = '';
                        if ($grade === 'Distinction')
                            $rowClass = 'rpt-row-distinction';
                        elseif ($grade === 'Pass')
                            $rowClass = 'rpt-row-pass';
                        elseif ($grade === 'Fail')
                            $rowClass = 'rpt-row-fail';
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($row['student_id']) ?></td>
                            <td><strong><?= htmlspecialchars($row['student_name']) ?></strong></td>
                            <td><?= htmlspecialchars($row['programme'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['company_name'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['lecturer_name'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($row['supervisor_name'] ?? '—') ?></td>
                            <td class="<?= $mark !== null ? '' : 'text-muted' ?>">
                                <?= $mark !== null ? number_format($mark, 2) . '%' : 'Pending' ?>
                            </td>
                            <td>
                                <?php if ($grade === 'Distinction'): ?>
                                    <span class="rpt-badge rpt-badge-distinction">Distinction</span>
                                <?php elseif ($grade === 'Pass'): ?>
                                    <span class="rpt-badge rpt-badge-pass">Pass</span>
                                <?php elseif ($grade === 'Fail'): ?>
                                    <span class="rpt-badge rpt-badge-fail">Fail</span>
                                <?php else: ?>
                                    <span class="rpt-badge rpt-badge-pending">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;color:var(--muted);padding:24px;">No records match the
                                selected filters.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div><!-- page-body -->

<?php include "footer.php"; ?>