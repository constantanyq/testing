<?php
session_start();
require_once "../connection.php";
$pageTitle = "Dashboard";
include "header.php";

// Count records for stat boxes
$totalStudents    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM student"))[0];
$totalInternships = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM internship"))[0];
$totalLecturers   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM lecturer"))[0];
$totalSupervisors = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM supervisor"))[0];
?>

<div class="topbar">
    <h1>Dashboard</h1>
    <span class="breadcrumb">Admin &rsaquo; Dashboard</span>
</div>

<div class="page-body">

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-num"><?= $totalStudents ?></div>
            <div class="stat-label">Total Students</div>
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

    <!-- Quick Links -->
    <div class="card">
        <div class="card-title">Quick Actions</div>
        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <a href="students.php"    class="btn btn-primary">👨‍🎓 Manage Students</a>
            <a href="internships.php" class="btn btn-success">🏢 Manage Internships</a>
            <a href="users.php"       class="btn btn-warning">👥 Manage Users</a>
            <a href="results.php"     class="btn btn-outline">📊 View All Results</a>
        </div>
    </div>

    <!-- Recent Internships -->
    <div class="card">
        <div class="card-title">Recent Internships</div>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Company</th>
                    <th>Lecturer</th>
                    <th>Supervisor</th>
                    <th>Avg. Marks</th>
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
                <td><?= htmlspecialchars($row['student_name']) ?></td>
                <td><?= htmlspecialchars($row['company_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['lecturer_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['supervisor_name'] ?? '—') ?></td>
                <td class="mark-value">
                    <?php if ($mark !== null): ?>
                        <span class="mark-value"><?= number_format($mark, 2) ?></span>
                    <?php else: ?>
                        <span class="text-muted">Pending</span>
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
