<?php
session_start();
require_once "../connection.php";

// Only students can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$sid = $_SESSION['user_id'];

// Fetch student internship with related info
$sql = "SELECT i.*, s.student_name, s.student_id, s.programme, s.student_email,
               c.company_name, l.lecturer_name, sv.supervisor_name
        FROM internship i
        JOIN student    s  ON i.student_id    = s.student_id
        LEFT JOIN company    c  ON i.company_id    = c.company_id
        LEFT JOIN lecturer   l  ON i.lecturer_id   = l.lecturer_id
        LEFT JOIN supervisor sv ON i.supervisor_id = sv.supervisor_id
        WHERE i.student_id = '$sid'";
$res = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);

// Fetch supervisor marks
$supMarks = [];
if ($data && $data['s_marks_id']) {
    $smid = (int) $data['s_marks_id'];
    $smRes = mysqli_query(
        $conn,
        "SELECT sm.*, comp.component_name, comp.component_weightage
         FROM supervisor_marks sm
         JOIN component comp ON sm.component_id = comp.component_id
         WHERE sm.s_marks_id = $smid ORDER BY comp.component_id"
    );
    while ($r = mysqli_fetch_assoc($smRes))
        $supMarks[] = $r;
}

// Fetch lecturer marks
$lecMarks = [];
if ($data && $data['l_marks_id']) {
    $lmid = (int) $data['l_marks_id'];
    $lmRes = mysqli_query(
        $conn,
        "SELECT lm.*, comp.component_name, comp.component_weightage
         FROM lecturer_marks lm
         JOIN component comp ON lm.component_id = comp.component_id
         WHERE lm.l_marks_id = $lmid ORDER BY comp.component_id"
    );
    while ($r = mysqli_fetch_assoc($lmRes))
        $lecMarks[] = $r;
}

// Determine final grade label and colour
$avg = $data['average_marks'] ?? null;
$finalClass = 'pass';
$finalLabel = 'Pass';
$finalColour = '#155724';

if ($avg !== null) {
    if ($avg >= 80) {
        $finalClass = 'distinction';
        $finalLabel = '🌟 Distinction';
        $finalColour = '#856404';
    } elseif ($avg >= 50) {
        $finalClass = 'pass';
        $finalLabel = '✅ Pass';
        $finalColour = '#155724';
    } else {
        $finalClass = 'fail';
        $finalLabel = '❌ Fail';
        $finalColour = '#721c24';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard – IRMS</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .student-info-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.07);
            padding: 28px 28px 20px;
            margin-bottom: 24px;
        }

        .student-info-card .card-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 22px;
        }

        .student-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b5bdb, #74c0fc);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #fff;
            font-weight: 700;
            flex-shrink: 0;
        }

        .student-full-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
        }

        .student-id-badge {
            display: inline-block;
            margin-top: 4px;
            font-size: 12px;
            font-weight: 600;
            color: var(--accent);
            background: #eff3ff;
            padding: 2px 10px;
            border-radius: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 14px;
        }

        .info-tile {
            background: #f8f9fc;
            border: 1px solid #e8ecf4;
            border-left: 3px solid var(--accent);
            border-radius: 10px;
            padding: 12px 14px;
        }

        .info-tile .tile-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 5px;
        }

        .info-tile .tile-value {
            font-size: 15px;
            font-weight: 600;
            color: var(--dark);
            line-height: 1.3;
        }
    </style>
</head>

<body>
    <div class="layout">

        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <h2>🎓 IRMS</h2>
                <p>Student Portal</p>
            </div>
            <div class="sidebar-user">
                <span>Student</span>
                <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
            </div>
            <nav>
                <a href="dashboard.php" class="active">
                    <span class="nav-icon">🏠</span> Dashboard
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="../logout.php">🚪 Logout</a>
            </div>
        </aside>

        <div class="main-content">
            <div class="topbar">
                <h1>My Dashboard</h1>
                <span class="breadcrumb">Student &rsaquo; Dashboard</span>
            </div>

            <div class="page-body">

                <?php if (!$data): ?>
                    <div class="alert alert-info">
                        ℹ️ You have not been assigned to an internship yet. Please contact your admin.
                    </div>

                <?php else: ?>

                    <!-- Student information card -->
                    <div class="student-info-card">
                        <div class="card-header">
                            <div class="student-avatar">
                                <?= strtoupper(substr($data['student_name'], 0, 1)) ?>
                            </div>
                            <div>
                                <div class="student-full-name"><?= htmlspecialchars($data['student_name']) ?></div>
                                <span class="student-id-badge"><?= htmlspecialchars($data['student_id']) ?></span>
                            </div>
                        </div>
                        <div class="info-grid">
                            <div class="info-tile">
                                <div class="tile-label">Programme</div>
                                <div class="tile-value"><?= htmlspecialchars($data['programme'] ?? '—') ?></div>
                            </div>
                            <div class="info-tile">
                                <div class="tile-label">Email</div>
                                <div class="tile-value" style="font-size:13px;">
                                    <?= htmlspecialchars($data['student_email'] ?? '—') ?></div>
                            </div>
                            <div class="info-tile">
                                <div class="tile-label">Company</div>
                                <div class="tile-value"><?= htmlspecialchars($data['company_name'] ?? '—') ?></div>
                            </div>
                            <div class="info-tile">
                                <div class="tile-label">Duration</div>
                                <div class="tile-value"><?= $data['duration'] ? $data['duration'] . ' weeks' : '—' ?></div>
                            </div>
                            <div class="info-tile">
                                <div class="tile-label">Lecturer</div>
                                <div class="tile-value"><?= htmlspecialchars($data['lecturer_name'] ?? '—') ?></div>
                            </div>
                            <div class="info-tile">
                                <div class="tile-label">Supervisor</div>
                                <div class="tile-value"><?= htmlspecialchars($data['supervisor_name'] ?? '—') ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Final mark summary -->
                    <?php if ($avg !== null): ?>
                        <div class="final-mark-box <?= $finalClass ?>">
                            <div class="big-mark" style="color:<?= $finalColour ?>"><?= number_format($avg, 2) ?></div>
                            <div class="mark-label" style="color:<?= $finalColour ?>"><?= $finalLabel ?></div>
                            <div style="font-size:12px; color:var(--muted); margin-top:6px;">Final Internship Score</div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            ⏳ Your marks have not been entered yet. Please check back later.
                        </div>
                    <?php endif; ?>

                    <!-- Supervisor marks breakdown -->
                    <div class="card">
                        <div class="card-title">
                            Supervisor Assessment
                            <?php if ($supMarks): ?>
                                <?php
                                $supTotal = array_sum(array_column($supMarks, 'total_marks'));
                                echo "– <span style='color:var(--accent)'>" . number_format($supTotal, 2) . " / 100</span>";
                                ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($supMarks): ?>
                            <div class="table-wrap">
                                <table class="marks-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Component</th>
                                            <th>Weight</th>
                                            <th>Your Mark</th>
                                            <th>Weighted Score</th>
                                            <th>Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($supMarks as $i => $m):
                                            $mark = (float) $m['component_mark'];
                                            $barColour = $mark >= 70 ? 'green' : ($mark >= 50 ? 'orange' : 'red');
                                            ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= htmlspecialchars($m['component_name']) ?></td>
                                                <td class="weight-cell"><?= $m['component_weightage'] ?>%</td>
                                                <td>
                                                    <div class="mark-bar-wrap">
                                                        <span style="min-width:36px;"><?= number_format($mark, 1) ?></span>
                                                        <div class="mark-bar">
                                                            <div class="mark-bar-fill <?= $barColour ?>"
                                                                style="width:<?= min($mark, 100) ?>%"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="total-cell"><?= number_format($m['total_marks'], 2) ?></td>
                                                <td class="text-muted" style="font-style:italic; font-size:12px;">
                                                    <?= htmlspecialchars($m['comments'] ?? '—') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr style="background:#f0f4f8; font-weight:700;">
                                            <td colspan="4" style="text-align:right;">Supervisor Total</td>
                                            <td colspan="2">
                                                <?= number_format(array_sum(array_column($supMarks, 'total_marks')), 2) ?> / 100
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Supervisor marks have not been entered yet.</p>
                        <?php endif; ?>
                    </div>

                    <!-- Lecturer marks breakdown -->
                    <div class="card">
                        <div class="card-title">
                            Lecturer Assessment
                            <?php if ($lecMarks): ?>
                                <?php
                                $lecTotal = array_sum(array_column($lecMarks, 'total_marks'));
                                echo "– <span style='color:var(--accent)'>" . number_format($lecTotal, 2) . " / 100</span>";
                                ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($lecMarks): ?>
                            <div class="table-wrap">
                                <table class="marks-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Component</th>
                                            <th>Weight</th>
                                            <th>Your Mark</th>
                                            <th>Weighted Score</th>
                                            <th>Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lecMarks as $i => $m):
                                            $mark = (float) $m['component_mark'];
                                            $barColour = $mark >= 70 ? 'green' : ($mark >= 50 ? 'orange' : 'red');
                                            ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= htmlspecialchars($m['component_name']) ?></td>
                                                <td class="weight-cell"><?= $m['component_weightage'] ?>%</td>
                                                <td>
                                                    <div class="mark-bar-wrap">
                                                        <span style="min-width:36px;"><?= number_format($mark, 1) ?></span>
                                                        <div class="mark-bar">
                                                            <div class="mark-bar-fill <?= $barColour ?>"
                                                                style="width:<?= min($mark, 100) ?>%"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="total-cell"><?= number_format($m['total_marks'], 2) ?></td>
                                                <td class="text-muted" style="font-style:italic; font-size:12px;">
                                                    <?= htmlspecialchars($m['comments'] ?? '—') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr style="background:#f0f4f8; font-weight:700;">
                                            <td colspan="4" style="text-align:right;">Lecturer Total</td>
                                            <td colspan="2">
                                                <?= number_format(array_sum(array_column($lecMarks, 'total_marks')), 2) ?> / 100
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Lecturer marks have not been entered yet.</p>
                        <?php endif; ?>
                    </div>

                <?php endif; ?>

            </div><!-- page-body -->
        </div><!-- main-content -->
    </div><!-- layout -->
    <script src="../script.js"></script>
</body>

</html>