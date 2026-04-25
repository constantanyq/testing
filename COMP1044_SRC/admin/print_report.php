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

<div class="topbar rpt-topbar">
    <span class="breadcrumb">Admin &rsaquo; Print Results</span>
    <div class="rpt-topbar-actions">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Print This Page</button>
        <a href="dashboard.php" class="btn btn-outline">← Back</a>
    </div>
</div>

<div class="page-body">

    <div class="card rpt-options-card no-print">
        <div class="rpt-options-header">
            <strong>⚙️ Report Options</strong>
        </div>
        <div class="card-body" style="padding-top: 20px;">
        <form method="GET" action="print_report.php" class="rpt-filter-form">
            <div class="form-row" style="grid-template-columns:1fr 1fr;">
                <div class="form-group">
                    <label>Filter by Assessor</label>
                    <select name="assessor" class="rpt-select" style="width: 100%;">
                        <option value="">All Students</option>
                        <?php foreach ($lecturers as $l): ?>
                        <option value="<?= htmlspecialchars($l['lecturer_id']) ?>"
                            <?= $filterLecturer===$l['lecturer_id']?'selected':'' ?>>
                            <?= htmlspecialchars($l['lecturer_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Grade Filter</label>
                    <select name="grade_filter" class="rpt-select" style="width: 100%;">
                        <option value="">All Grades</option>
                        <option value="Distinction" <?= $filterGrade==='Distinction'?'selected':'' ?>>Distinction</option>
                        <option value="Pass"        <?= $filterGrade==='Pass'       ?'selected':'' ?>>Pass</option>
                        <option value="Fail"        <?= $filterGrade==='Fail'       ?'selected':'' ?>>Fail</option>
                        <option value="Pending"     <?= $filterGrade==='Pending'    ?'selected':'' ?>>Pending</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Apply Filter</button>
        </form>
        </div>
    </div>

    <div class="rpt-print-header">
        <div class="login-icon-wrap" style="width: 52px; height: 52px; font-size: 26px; border-radius: 14px; margin-bottom: 0; box-shadow: none;">
            🎓
        </div>
        <div class="rpt-print-title-block">
            <div class="rpt-print-title">IRMS — Internship Result Report</div>
            <div class="rpt-print-meta">Generated: <?= date('d F Y, h:i A') ?> &nbsp;|&nbsp; Total Records: <?= $cTotal ?></div>
        </div>
    </div>

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

</div><style>
/* ── Screen: print header hidden, show only on print ── */
.rpt-print-header { display:none; }

/* ── Print styles ────────────────────────────────────── */
@media print {
    /* Hide everything that isn't the report */
    .sidebar, .topbar, .no-print, .rpt-options-card { display:none !important; }
    .main-content { margin-left:0 !important; }
    .page-body { padding:20px !important; }

    /* Show the print header */
    .rpt-print-header {
        display:flex !important;
        align-items:center;
        gap:16px;
        margin-bottom:18px;
        padding-bottom:14px;
        border-bottom:3px solid #1e3a5f;
    }
    .rpt-print-title { font-size:20px; font-weight:800; color:#1e3a5f; }
    .rpt-print-meta  { font-size:12px; color:#64748b; margin-top:3px; }

    /* Force colours to print */
    * { -webkit-print-color-adjust:exact !important; print-color-adjust:exact !important; }

    /* Stats row */
    .rpt-stats-row {
        display:flex !important;
        gap:10px;
        margin-bottom:16px;
        flex-wrap:nowrap;
    }
    .rpt-stat-box { flex:1; }

    /* Table header dark */
    .rpt-table thead th {
        background:#1e3a5f !important;
        color:#fff !important;
        font-size:10px;
        padding:8px 10px;
    }
    .rpt-table tbody td { padding:8px 10px; font-size:11px; }

    /* Row colours */
    .rpt-row-distinction { background:#fffbeb !important; }
    .rpt-row-pass        { background:#f0fdf4 !important; }
    .rpt-row-fail        { background:#fff1f2 !important; }

    /* Badges */
    .rpt-badge-distinction { background:#fef3c7 !important; color:#92400e !important; border:1px solid #fcd34d; }
    .rpt-badge-pass        { background:#d1fae5 !important; color:#065f46 !important; border:1px solid #6ee7b7; }
    .rpt-badge-fail        { background:#fee2e2 !important; color:#991b1b !important; border:1px solid #fca5a5; }
    .rpt-badge-pending     { background:#f1f5f9 !important; color:#475569 !important; border:1px solid #cbd5e1; }

    /* Card / table-card */
    .rpt-table-card { box-shadow:none !important; border:1px solid #e2e8f0 !important; }

    /* Stat box colours */
    .rpt-stat-distinction { background:#fffbeb !important; border-color:#fcd34d !important; }
    .rpt-stat-pass        { background:#f0fdf4 !important; border-color:#6ee7b7 !important; }
    .rpt-stat-fail        { background:#fff1f2 !important; border-color:#fca5a5 !important; }
    .rpt-stat-avg         { background:#eff6ff !important; border-color:#bfdbfe !important; }
}

/* ── Screen styles for stat row / table ── */
.rpt-stats-row {
    display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap;
}
.rpt-stat-box {
    flex:1; min-width:80px; text-align:center;
    padding:14px 10px; border-radius:8px;
    border:1.5px solid var(--border); background:var(--card);
    box-shadow:var(--shadow-sm);
}
.rpt-stat-distinction { border-color:#fcd34d; background:#fffbeb; }
.rpt-stat-pass        { border-color:#6ee7b7; background:#f0fdf4; }
.rpt-stat-fail        { border-color:#fca5a5; background:#fff1f2; }
.rpt-stat-pending     { border-color:#cbd5e1; background:#f8fafc; }
.rpt-stat-avg         { border-color:#bfdbfe; background:#eff6ff; }
.rpt-stat-num         { font-size:26px; font-weight:800; color:var(--text); line-height:1; }
.rpt-stat-label       { font-size:11px; color:var(--muted); font-weight:600; text-transform:uppercase; margin-top:4px; }

.rpt-table-card { overflow:hidden; }
.rpt-table-header {
    display:flex; justify-content:space-between; align-items:center;
    padding:14px 20px; background:#f8fafc;
    border-bottom:2px solid var(--border);
    font-size:13.5px;
}
.rpt-record-count { font-size:12px; color:var(--muted); font-weight:500; }

.rpt-badge {
    display:inline-flex; align-items:center;
    padding:3px 10px; border-radius:99px;
    font-size:11.5px; font-weight:600;
}
.rpt-badge-distinction { background:#fef3c7; color:#92400e; border:1px solid #fcd34d; }
.rpt-badge-pass        { background:#d1fae5; color:#065f46; border:1px solid #6ee7b7; }
.rpt-badge-fail        { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
.rpt-badge-pending     { background:#f1f5f9; color:#475569; border:1px solid #cbd5e1; }

.rpt-row-distinction { background:#fffbeb; }
.rpt-row-pass        { background:#f0fdf4; }
.rpt-row-fail        { background:#fff1f2; }
</style>

<?php include "footer.php"; ?>