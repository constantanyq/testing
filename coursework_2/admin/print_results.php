<?php
session_start();
require_once "../connection.php";
$pageTitle = "Print Results";
include "header.php";

// Filter options
$filter      = $_GET['filter']   ?? 'all';
$assessor_id = $_GET['assessor'] ?? '';
$grade_filter = $_GET['grade']   ?? 'all';

// Build WHERE clause
$where = "1=1";
if ($filter === 'lecturer' && $assessor_id) {
    $assessor_id = mysqli_real_escape_string($conn, $assessor_id);
    $where .= " AND i.lecturer_id = '$assessor_id'";
} elseif ($filter === 'supervisor' && $assessor_id) {
    $assessor_id = mysqli_real_escape_string($conn, $assessor_id);
    $where .= " AND i.supervisor_id = '$assessor_id'";
}
if ($grade_filter === 'distinction') $where .= " AND i.average_marks >= 80";
elseif ($grade_filter === 'pass')    $where .= " AND i.average_marks >= 50 AND i.average_marks < 80";
elseif ($grade_filter === 'fail')    $where .= " AND i.average_marks < 50 AND i.average_marks IS NOT NULL";
elseif ($grade_filter === 'pending') $where .= " AND i.average_marks IS NULL";

$sql = "SELECT s.student_id, s.student_name, s.programme,
               c.company_name,
               l.lecturer_name, sv.supervisor_name,
               i.average_marks
        FROM internship i
        LEFT JOIN student    s  ON i.student_id    = s.student_id
        LEFT JOIN company    c  ON i.company_id    = c.company_id
        LEFT JOIN lecturer   l  ON i.lecturer_id   = l.lecturer_id
        LEFT JOIN supervisor sv ON i.supervisor_id = sv.supervisor_id
        WHERE $where
        ORDER BY i.average_marks DESC";
$res = mysqli_query($conn, $sql);
$rows = [];
while ($r = mysqli_fetch_assoc($res)) $rows[] = $r;

// Totals for summary
$total   = count($rows);
$graded  = count(array_filter($rows, fn($r) => $r['average_marks'] !== null));
$dist    = count(array_filter($rows, fn($r) => $r['average_marks'] >= 80));
$pass    = count(array_filter($rows, fn($r) => $r['average_marks'] >= 50 && $r['average_marks'] < 80));
$fail    = count(array_filter($rows, fn($r) => $r['average_marks'] !== null && $r['average_marks'] < 50));
$pending = $total - $graded;
$avg_all = $graded > 0 ? array_sum(array_column(array_filter($rows, fn($r) => $r['average_marks'] !== null), 'average_marks')) / $graded : 0;

// Fetch assessor lists for dropdowns
$lecturers   = mysqli_query($conn, "SELECT lecturer_id, lecturer_name FROM lecturer ORDER BY lecturer_name");
$supervisors = mysqli_query($conn, "SELECT supervisor_id, supervisor_name FROM supervisor ORDER BY supervisor_name");
?>

<div class="topbar">
    <div>
        <div class="topbar-greeting">Print / Export Results</div>
        <div class="breadcrumb">Admin &rsaquo; Print Results</div>
    </div>
    <div style="display:flex;gap:10px;">
        <button onclick="window.print()" class="btn btn-primary">🖨️ Print This Page</button>
        <a href="results.php" class="btn btn-outline">← Back</a>
    </div>
</div>

<div class="page-body">

<!-- ── Filters (hidden when printing) ── -->
<div class="card card-form no-print">
    <div class="card-title">🔍 Report Options</div>
    <div class="card-body">
    <form method="GET" action="">
        <div class="form-row" style="grid-template-columns:1fr 1fr 1fr;">
            <div class="form-group">
                <label>Filter by Assessor</label>
                <select name="filter" id="filterType" onchange="toggleAssessor()">
                    <option value="all"        <?= $filter=='all'        ? 'selected' : '' ?>>All Students</option>
                    <option value="lecturer"   <?= $filter=='lecturer'   ? 'selected' : '' ?>>By Lecturer</option>
                    <option value="supervisor" <?= $filter=='supervisor' ? 'selected' : '' ?>>By Supervisor</option>
                </select>
            </div>
            <div class="form-group" id="assessorGroup" style="<?= $filter=='all'?'display:none':'' ?>">
                <label>Select Assessor</label>
                <?php if ($filter === 'supervisor'): ?>
                <select name="assessor">
                    <option value="">-- All --</option>
                    <?php while ($r = mysqli_fetch_assoc($supervisors)): ?>
                    <option value="<?= htmlspecialchars($r['supervisor_id']) ?>"
                        <?= $assessor_id==$r['supervisor_id']?'selected':'' ?>>
                        <?= htmlspecialchars($r['supervisor_name']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                <?php else: ?>
                <select name="assessor">
                    <option value="">-- All --</option>
                    <?php while ($r = mysqli_fetch_assoc($lecturers)): ?>
                    <option value="<?= htmlspecialchars($r['lecturer_id']) ?>"
                        <?= $assessor_id==$r['lecturer_id']?'selected':'' ?>>
                        <?= htmlspecialchars($r['lecturer_name']) ?>
                    </option>
                    <?php endwhile; ?>
                </select>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Grade Filter</label>
                <select name="grade">
                    <option value="all"        <?= $grade_filter=='all'        ?'selected':'' ?>>All Grades</option>
                    <option value="distinction"<?= $grade_filter=='distinction'?'selected':'' ?>>Distinction (≥80)</option>
                    <option value="pass"       <?= $grade_filter=='pass'       ?'selected':'' ?>>Pass (50–79)</option>
                    <option value="fail"       <?= $grade_filter=='fail'       ?'selected':'' ?>>Fail (&lt;50)</option>
                    <option value="pending"    <?= $grade_filter=='pending'    ?'selected':'' ?>>Pending</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Apply Filter</button>
    </form>
    </div>
</div>

<!-- ── Print Header (only shows when printing) ── -->
<div class="print-header">
    <div class="print-logo-row">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="48" height="48">
          <circle cx="100" cy="100" r="96" fill="#1e3a5f"/>
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
        <div>
            <div class="print-title">InternTrack — Internship Result Report</div>
            <div class="print-meta">Generated: <?= date('d F Y, h:i A') ?> &nbsp;|&nbsp; Total Records: <?= $total ?></div>
        </div>
    </div>
</div>

<!-- ── Summary strip ── -->
<div class="print-summary-row">
    <div class="print-sum-box"><div class="psb-num"><?= $total ?></div><div class="psb-lbl">Total</div></div>
    <div class="print-sum-box psb-dist"><div class="psb-num"><?= $dist ?></div><div class="psb-lbl">Distinction</div></div>
    <div class="print-sum-box psb-pass"><div class="psb-num"><?= $pass ?></div><div class="psb-lbl">Pass</div></div>
    <div class="print-sum-box psb-fail"><div class="psb-num"><?= $fail ?></div><div class="psb-lbl">Fail</div></div>
    <div class="print-sum-box psb-pend"><div class="psb-num"><?= $pending ?></div><div class="psb-lbl">Pending</div></div>
    <div class="print-sum-box psb-avg"><div class="psb-num"><?= $graded > 0 ? number_format($avg_all,1).'%' : '—' ?></div><div class="psb-lbl">Avg. Mark</div></div>
</div>

<!-- ── Results table ── -->
<div class="card card-table">
    <div class="card-title">📋 Internship Results
        <span style="font-size:11px;font-weight:500;color:var(--muted);margin-left:8px;"><?= $total ?> records</span>
    </div>
    <div class="table-wrap">
    <table id="printTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Student ID</th>
                <th>Name</th>
                <th>Programme</th>
                <th>Company</th>
                <th>Lecturer</th>
                <th>Supervisor</th>
                <th>Mark</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $i => $row):
            $m = $row['average_marks'];
            $rowCls = '';
            $badgeCls = '';
            $grade = '—';
            if ($m !== null) {
                if ($m >= 80)      { $rowCls='row-distinction'; $badgeCls='badge-distinction'; $grade='Distinction'; }
                elseif ($m >= 50)  { $rowCls='row-pass';        $badgeCls='badge-pass';        $grade='Pass'; }
                else               { $rowCls='row-fail';        $badgeCls='badge-fail';        $grade='Fail'; }
            }
        ?>
        <tr class="<?= $rowCls ?>">
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($row['student_id']) ?></td>
            <td><strong><?= htmlspecialchars($row['student_name']) ?></strong></td>
            <td><?= htmlspecialchars($row['programme'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['company_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['lecturer_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['supervisor_name'] ?? '—') ?></td>
            <td><?= $m !== null ? number_format($m, 2).'%' : '<span class="text-muted">Pending</span>' ?></td>
            <td><?php if ($m !== null): ?>
                <span class="badge <?= $badgeCls ?>"><?= $grade ?></span>
                <?php else: ?>
                <span class="badge" style="background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0">Pending</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

</div><!-- page-body -->

<script>
function toggleAssessor() {
    var v = document.getElementById('filterType').value;
    document.getElementById('assessorGroup').style.display = (v === 'all') ? 'none' : 'block';
}
</script>

<style>
@media print {
    .sidebar, .topbar, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .page-body { padding: 0 !important; }
    .print-header { display: block !important; margin-bottom: 20px; }
    .card { box-shadow: none !important; border: 1px solid #ccc !important; }
    .card-title { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    thead th { background: #1e3a5f !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    tr.row-distinction { background: #fffbeb !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    tr.row-pass        { background: #f0fdf4 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    tr.row-fail        { background: #fff1f2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .print-summary-row { display: flex !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
.print-header { display: none; }
.print-logo-row { display: flex; align-items: center; gap: 14px; margin-bottom: 8px; }
.print-title  { font-size: 18px; font-weight: 700; color: #1e3a5f; }
.print-meta   { font-size: 12px; color: #64748b; }
.print-summary-row {
    display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;
}
.print-sum-box {
    flex: 1; min-width: 80px; text-align: center; padding: 14px 10px;
    border-radius: 8px; border: 1.5px solid var(--border); background: var(--card);
    box-shadow: var(--shadow-sm);
}
.psb-dist { border-color: #fcd34d; background: #fffbeb; }
.psb-pass { border-color: #6ee7b7; background: #f0fdf4; }
.psb-fail { border-color: #fca5a5; background: #fff1f2; }
.psb-pend { border-color: #cbd5e1; background: #f8fafc; }
.psb-avg  { border-color: #bfdbfe; background: #eff6ff; }
.psb-num  { font-size: 26px; font-weight: 800; color: var(--text); line-height: 1; }
.psb-lbl  { font-size: 11px; color: var(--muted); font-weight: 600; text-transform: uppercase; margin-top: 4px; }
</style>

<?php include "footer.php"; ?>
