<?php
session_start();
require_once "../connection.php";
$pageTitle = "Dashboard";
include "header.php";

$totalStudents    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM student"))[0];
$totalInternships = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM internship"))[0];
$totalLecturers   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM lecturer"))[0];
$totalSupervisors = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM supervisor"))[0];
$pendingMarks     = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM internship WHERE average_marks IS NULL"))[0];
?>

<div class="topbar">
    <div>
        <div class="topbar-greeting">Good <?= (date('H') < 12) ? 'morning' : ((date('H') < 18) ? 'afternoon' : 'evening') ?>, <?= htmlspecialchars(explode(' ', $_SESSION['name'])[0]) ?> 👋</div>
        <div class="breadcrumb">Admin Panel &rsaquo; Dashboard</div>
    </div>
    <span class="topbar-date"><?= date('l, d F Y') ?></span>
</div>

<div class="page-body">

<!-- ── Welcome banner ── -->
<div class="welcome-banner">
    <div class="welcome-banner-text">
        <h2>What would you like to do today?</h2>
        <p>You have full administrative access. Select an action below or use the sidebar to navigate.</p>
    </div>
    <?php if ($pendingMarks > 0): ?>
    <div class="banner-alert">
        <span class="banner-alert-icon">⚠️</span>
        <span><strong><?= $pendingMarks ?></strong> internship<?= $pendingMarks > 1 ? 's' : '' ?> still awaiting marks</span>
    </div>
    <?php endif; ?>
</div>

<!-- ── Action cards ── -->
<div class="action-grid">
    <a href="students.php" class="action-card action-blue">
        <div class="action-icon">👨‍🎓</div>
        <div class="action-content">
            <div class="action-title">Manage Students</div>
            <div class="action-desc">Add, edit or remove student profiles and programme details</div>
        </div>
        <div class="action-count"><?= $totalStudents ?></div>
        <div class="action-arrow">→</div>
    </a>

    <a href="internships.php" class="action-card action-green">
        <div class="action-icon">🏢</div>
        <div class="action-content">
            <div class="action-title">Manage Internships</div>
            <div class="action-desc">Assign students to companies and assessors</div>
        </div>
        <div class="action-count"><?= $totalInternships ?></div>
        <div class="action-arrow">→</div>
    </a>

    <a href="users.php" class="action-card action-amber">
        <div class="action-icon">👥</div>
        <div class="action-content">
            <div class="action-title">Manage Users</div>
            <div class="action-desc">Create and manage lecturer and supervisor accounts</div>
        </div>
        <div class="action-count"><?= $totalLecturers + $totalSupervisors ?></div>
        <div class="action-arrow">→</div>
    </a>

    <a href="results.php" class="action-card action-indigo">
        <div class="action-icon">📊</div>
        <div class="action-content">
            <div class="action-title">View All Results</div>
            <div class="action-desc">Browse and filter internship marks across all students</div>
        </div>
        <div class="action-count"><?= $totalInternships - $pendingMarks ?> done</div>
        <div class="action-arrow">→</div>
    </a>
</div>

<!-- ── Stats row ── -->
<div class="stats-grid">
    <div class="stat-box">
        <div class="stat-num"><?= $totalStudents ?></div>
        <div class="stat-label">Students</div>
    </div>
    <div class="stat-box">
        <div class="stat-num"><?= $totalInternships ?></div>
        <div class="stat-label">Internships</div>
    </div>
    <div class="stat-box">
        <div class="stat-num"><?= $totalLecturers ?></div>
        <div class="stat-label">Lecturers</div>
    </div>
    <div class="stat-box">
        <div class="stat-num"><?= $totalSupervisors ?></div>
        <div class="stat-label">Supervisors</div>
    </div>
</div>

<!-- ── Recent internships ── -->
<div class="card card-table">
    <div class="card-title">📋 Recent Internship Activity</div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th><th>Student</th><th>Company</th>
                <th>Lecturer</th><th>Supervisor</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT i.internship_id, s.student_name, c.company_name,
                       l.lecturer_name, sv.supervisor_name, i.average_marks
                FROM internship i
                LEFT JOIN student    s  ON i.student_id    = s.student_id
                LEFT JOIN company    c  ON i.company_id    = c.company_id
                LEFT JOIN lecturer   l  ON i.lecturer_id   = l.lecturer_id
                LEFT JOIN supervisor sv ON i.supervisor_id = sv.supervisor_id
                ORDER BY i.internship_id DESC LIMIT 8";
        $res = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($res)):
            $mark = $row['average_marks'];
            $cls  = '';
            if ($mark !== null) {
                if ($mark >= 80)     $cls = 'row-distinction';
                elseif ($mark >= 50) $cls = 'row-pass';
                else                 $cls = 'row-fail';
            }
        ?>
        <tr class="<?= $cls ?>">
            <td><?= $row['internship_id'] ?></td>
            <td><strong><?= htmlspecialchars($row['student_name']) ?></strong></td>
            <td><?= htmlspecialchars($row['company_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['lecturer_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['supervisor_name'] ?? '—') ?></td>
            <td>
                <?php if ($mark !== null): ?>
                    <span class="badge <?= $mark >= 80 ? 'badge-distinction' : ($mark >= 50 ? 'badge-pass' : 'badge-fail') ?>">
                        <?= number_format($mark, 1) ?>%
                    </span>
                <?php else: ?>
                    <span class="badge" style="background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0">Pending</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>

</div><!-- page-body -->
<?php include "footer.php"; ?>
