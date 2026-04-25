<?php
// TEMPLATE DOWNLOAD 
if (isset($_GET['template']) && $_GET['template'] === 'student') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="student_template.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['student_id', 'student_name', 'student_password', 'student_email', 'programme']);
    fputcsv($out, ['STU006', 'Ahmad Bin Ali', 'password123', 'ahmad@student.edu', 'BSc Computer Science']);
    fputcsv($out, ['STU007', 'Siti Binti Hassan', 'password123', 'siti@student.edu', 'BSc Information Technology']);
    fclose($out);
    exit();
}

session_start();
require_once "../connection.php";
$pageTitle = "Students";
include "header.php";

$msg     = "";
$csvLog  = []; // rows from CSV import

// CSV UPLOAD
if (isset($_POST['action']) && $_POST['action'] === 'csv_upload') {
    if (!empty($_FILES['csv_file']['tmp_name'])) {
        $handle = fopen($_FILES['csv_file']['tmp_name'], 'r');

        // Strip UTF-8 BOM from first field (Excel adds it)
        $rawHeader    = fgetcsv($handle);
        $rawHeader[0] = ltrim($rawHeader[0], "\xEF\xBB\xBF");
        $header       = array_map(fn($h) => strtolower(trim($h)), $rawHeader);

        $required = ['student_id','student_name','student_password','student_email','programme'];
        $missing  = array_diff($required, $header);

        if ($missing) {
            $msg = "ERROR: CSV is missing required columns: " . implode(', ', $missing) . ". Please use the provided template.";
        } else {
            $added = $updated = $errors = 0;
            $rowNum = 1;

            while (($row = fgetcsv($handle)) !== false) {
                $rowNum++;
                // Skip completely empty lines
                if (count(array_filter($row, fn($v) => trim($v) !== '')) === 0) continue;
                if (count($row) < count($header)) continue;

                $data   = array_combine($header, $row);
                $sid    = mysqli_real_escape_string($conn, trim($data['student_id']       ?? ''));
                $sname  = mysqli_real_escape_string($conn, trim($data['student_name']     ?? ''));
                $spass  = mysqli_real_escape_string($conn, trim($data['student_password'] ?? ''));
                $semail = mysqli_real_escape_string($conn, trim($data['student_email']    ?? ''));
                $sprog  = mysqli_real_escape_string($conn, trim($data['programme']        ?? ''));

                if (!$sid || !$sname || !$spass || !$semail || !$sprog) {
                    $csvLog[] = ['row' => $rowNum, 'id' => $sid ?: '(empty)', 'status' => 'error', 'msg' => 'Missing required field'];
                    $errors++;
                    continue;
                }

                // Check if student already exists
                $chk = mysqli_query($conn, "SELECT student_id FROM student WHERE student_id='$sid'");
                $exists = mysqli_num_rows($chk) > 0;

                $sql = "INSERT INTO student (student_id, student_name, student_password, student_email, programme)
                        VALUES ('$sid','$sname','$spass','$semail','$sprog')
                        ON DUPLICATE KEY UPDATE
                            student_name='$sname', student_password='$spass',
                            student_email='$semail', programme='$sprog'";

                if (mysqli_query($conn, $sql)) {
                    $affected = mysqli_affected_rows($conn);
                    if (!$exists) {
                        $statusLabel = "Added: $sname";
                        $csvLog[] = ['row' => $rowNum, 'id' => $sid, 'status' => 'ok', 'msg' => $statusLabel];
                        $added++;
                    } else {
                        $statusLabel = "Updated: $sname";
                        $csvLog[] = ['row' => $rowNum, 'id' => $sid, 'status' => 'updated', 'msg' => $statusLabel];
                        $updated++;
                    }
                } else {
                    $csvLog[] = ['row' => $rowNum, 'id' => $sid, 'status' => 'error', 'msg' => mysqli_error($conn)];
                    $errors++;
                }
            }
            fclose($handle);
            $parts = [];
            if ($added)   $parts[] = "{$added} added";
            if ($updated) $parts[] = "{$updated} updated";
            if ($errors)  $parts[] = "{$errors} error(s)";
            $msg = "CSV Import: " . implode(', ', $parts) . ".";
        }
    } else {
        $msg = "ERROR: No file uploaded or file is empty.";
    }
}

// DELETE — redirect after so page reloads clean
if (isset($_GET['delete'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM student WHERE student_id = '$del_id'")) {
        header("Location: students.php?deleted=1");
    } else {
        header("Location: students.php?err=fk");
    }
    exit();
}

// Handle redirect messages
if (isset($_GET['deleted'])) $msg = "Student deleted successfully.";
if (isset($_GET['err']) && $_GET['err'] === 'fk') $msg = "ERROR: Cannot delete student. They are assigned to an internship.";


// ADD
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $sid   = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $sname = mysqli_real_escape_string($conn, trim($_POST['student_name']));
    $spass = mysqli_real_escape_string($conn, trim($_POST['student_password']));
    $semail= mysqli_real_escape_string($conn, trim($_POST['student_email']));
    $sprog = mysqli_real_escape_string($conn, trim($_POST['programme']));

    $check = mysqli_query($conn, "SELECT student_id FROM student WHERE student_id='$sid'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "ERROR: Student ID already exists.";
    } else {
        mysqli_query($conn, "INSERT INTO student (student_id,student_name,student_password,student_email,programme)
                VALUES ('$sid','$sname','$spass','$semail','$sprog')");
        $msg = "Student added successfully.";
    }
}

// EDIT
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $sid   = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $sname = mysqli_real_escape_string($conn, trim($_POST['student_name']));
    $spass = mysqli_real_escape_string($conn, trim($_POST['student_password']));
    $semail= mysqli_real_escape_string($conn, trim($_POST['student_email']));
    $sprog = mysqli_real_escape_string($conn, trim($_POST['programme']));

    mysqli_query($conn, "UPDATE student SET student_name='$sname',student_password='$spass',
            student_email='$semail',programme='$sprog' WHERE student_id='$sid'");
    $msg = "Student updated successfully.";
}

// Fetch student for editing
$editStudent = null;
if (isset($_GET['edit'])) {
    $eid = mysqli_real_escape_string($conn, $_GET['edit']);
    $res = mysqli_query($conn, "SELECT * FROM student WHERE student_id='$eid'");
    $editStudent = mysqli_fetch_assoc($res);
}

// Suggest next ID
$nextStuId = 'STU001';
$sRes = mysqli_query($conn, "SELECT student_id FROM student ORDER BY student_id DESC LIMIT 1");
if ($sRow = mysqli_fetch_assoc($sRes)) {
    preg_match('/(\D+)(\d+)$/', $sRow['student_id'], $sm);
    $nextStuId = $sm ? $sm[1] . str_pad((int)$sm[2] + 1, strlen($sm[2]), '0', STR_PAD_LEFT) : $sRow['student_id'];
}
?>

<div class="topbar">
    <h1>Manage Students</h1>
    <span class="breadcrumb">Admin &rsaquo; Students</span>
</div>

<div class="page-body">

<?php if ($msg): ?>
    <div class="alert <?= strpos($msg,'ERROR') === 0 ? 'alert-danger' : 'alert-success' ?>">
        <?= strpos($msg,'ERROR') === 0 ? '⚠️' : '✅' ?> <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<?php if ($csvLog): ?>
<!-- CSV Import Report -->
<div class="card">
    <div class="card-title flex-between">
        <span>📋 CSV Import Report</span>
        <span class="text-muted text-sm"><?= count($csvLog) ?> row(s) processed</span>
    </div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr><th>Row</th><th>ID</th><th>Name / Note</th><th>Result</th></tr>
        </thead>
        <tbody>
        <?php foreach ($csvLog as $log): ?>
        <tr>
            <td class="text-muted"><?= $log['row'] ?></td>
            <td><strong><?= htmlspecialchars($log['id']) ?></strong></td>
            <td><?= htmlspecialchars($log['msg']) ?></td>
            <td>
                <?php if ($log['status'] === 'ok'): ?>
                    <span class="badge badge-pass">✅ Added</span>
                <?php elseif ($log['status'] === 'updated'): ?>
                    <span class="badge badge-pending">🔄 Updated</span>
                <?php elseif ($log['status'] === 'skipped'): ?>
                    <span class="badge badge-pending">⏭ Skipped</span>
                <?php else: ?>
                    <span class="badge badge-fail">❌ Error</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php endif; ?>

<!-- CSV BULK UPLOAD-->
<div class="card">
    <div class="card-title flex-between">
        <span>📤 Bulk Upload via CSV</span>
        <a href="students.php?template=student" class="btn btn-outline btn-sm">
            ⬇️ Download Template
        </a>
    </div>

    <div class="csv-info-box">
        <div class="csv-info-icon">💡</div>
        <div>
            <strong>How it works:</strong> Download the template, fill in student data (one row per student), then upload the file.
            <div class="csv-columns">
                Required columns:
                <span class="csv-col">student_id</span>
                <span class="csv-col">student_name</span>
                <span class="csv-col">student_password</span>
                <span class="csv-col">student_email</span>
                <span class="csv-col">programme</span>
            </div>
        </div>
    </div>

    <!-- CSV Preview Table (shown after file is selected, before upload) -->
    <div id="csvPreviewSection" style="display:none; margin-top:16px;">
        <div class="card-title" style="font-size:0.9rem;">👁️ CSV Preview — Please verify before uploading</div>
        <div class="table-wrap" style="max-height:260px; overflow-y:auto;">
            <table id="csvPreviewTable">
                <thead id="csvPreviewHead"></thead>
                <tbody id="csvPreviewBody"></tbody>
            </table>
        </div>
        <p id="csvPreviewNote" style="font-size:0.8rem; color:var(--text-muted); margin-top:6px;"></p>
    </div>

    <form method="POST" enctype="multipart/form-data" style="margin-top:16px;">
        <input type="hidden" name="action" value="csv_upload">
        <div class="csv-upload-area" id="csvDropZone">
            <input type="file" name="csv_file" id="csvFile" accept=".csv" required style="display:none;">
            <div class="csv-upload-icon">📁</div>
            <p class="csv-upload-label">Drag & drop your CSV file here, or <span class="csv-browse-link" onclick="document.getElementById('csvFile').click()">browse</span></p>
            <p class="csv-upload-hint" id="csvFileName">Accepted format: .csv only</p>
        </div>
        <div style="margin-top:12px;">
            <button type="submit" class="btn btn-success">📤 Upload & Import</button>
        </div>
    </form>
</div>

<!-- MANUAL ADD / EDIT FORM -->
<div class="card">
    <div class="card-title"><?= $editStudent ? '✏️ Edit Student' : '➕ Add Single Student' ?></div>
    <form method="POST" action="">
        <input type="hidden" name="action" value="<?= $editStudent ? 'edit' : 'add' ?>">

        <div class="form-row">
            <div class="form-group">
                <label>Student ID</label>
                <input type="text" name="student_id"
                       value="<?= htmlspecialchars($editStudent['student_id'] ?? $nextStuId) ?>"
                       <?= $editStudent ? 'readonly' : '' ?> required>
            </div>
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="student_name"
                       value="<?= htmlspecialchars($editStudent['student_name'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="student_email"
                       value="<?= htmlspecialchars($editStudent['student_email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label>Programme</label>
                <input type="text" name="programme"
                       value="<?= htmlspecialchars($editStudent['programme'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="text" name="student_password"
                   value="<?= htmlspecialchars($editStudent['student_password'] ?? '') ?>" required>
        </div>

        <div class="flex-gap">
            <button type="submit" class="btn btn-primary">
                <?= $editStudent ? '💾 Update Student' : '➕ Add Student' ?>
            </button>
            <?php if ($editStudent): ?>
                <a href="students.php" class="btn btn-outline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- STUDENT LIST -->
<div class="card">
    <div class="card-title flex-between">
        <span>Student List</span>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by name or ID...">
        </div>
    </div>
    <div class="table-wrap">
    <table id="mainTable">
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Programme</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $res = mysqli_query($conn, "SELECT * FROM student ORDER BY student_id");
        while ($row = mysqli_fetch_assoc($res)):
        ?>
        <tr>
            <td><strong><?= htmlspecialchars($row['student_id']) ?></strong></td>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><span class="text-muted"><?= htmlspecialchars($row['student_email']) ?></span></td>
            <td><?= htmlspecialchars($row['programme']) ?></td>
            <td>
                <a href="students.php?edit=<?= urlencode($row['student_id']) ?>"
                   class="btn btn-warning btn-sm">✏️ Edit</a>
                <a href="students.php?delete=<?= urlencode($row['student_id']) ?>"
                   class="btn btn-danger btn-sm confirm-delete"
                   data-msg="Delete student <?= htmlspecialchars($row['student_name']) ?>? This cannot be undone.">
                   🗑️ Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>

</div><!-- page-body -->

<script>
// ── CSV PREVIEW ──────────────────────────────────────────────────────────────
const requiredCols = ['student_id','student_name','student_password','student_email','programme'];

function clearCsvPreview() {
    document.getElementById('csvFile').value = '';
    document.getElementById('csvFileName').textContent = 'Accepted format: .csv only';
    document.getElementById('csvPreviewSection').style.display = 'none';
    document.getElementById('clearCsvBtn').style.display = 'none';
    document.getElementById('uploadBtn').disabled = false;
}

function parseAndPreviewCSV(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const text = e.target.result.replace(/^\uFEFF/, ''); // strip BOM
        const lines = text.split(/\r?\n/).filter(l => l.trim() !== '');
        if (lines.length < 1) return;

        // Parse CSV rows (handles quoted fields)
        function parseCSVLine(line) {
            const result = [];
            let cur = '', inQ = false;
            for (let i = 0; i < line.length; i++) {
                const ch = line[i];
                if (ch === '"') { inQ = !inQ; }
                else if (ch === ',' && !inQ) { result.push(cur.trim()); cur = ''; }
                else { cur += ch; }
            }
            result.push(cur.trim());
            return result;
        }

        const headers = parseCSVLine(lines[0]).map(h => h.toLowerCase().replace(/"/g,'').trim());
        const missing = requiredCols.filter(c => !headers.includes(c));

        const previewHead = document.getElementById('csvPreviewHead');
        const previewBody = document.getElementById('csvPreviewBody');
        const previewNote = document.getElementById('csvPreviewNote');

        // Build header row, highlight wrong/missing columns
        let headHtml = '<tr>';
        headers.forEach(h => {
            const ok = requiredCols.includes(h);
            headHtml += `<th style="${ok ? '' : 'color:var(--danger);'}">${h}${ok ? '' : ' ⚠️'}</th>`;
        });
        headHtml += '</tr>';
        previewHead.innerHTML = headHtml;

        // Build data rows (max 10 preview)
        const dataRows = lines.slice(1, 11);
        let bodyHtml = '';
        dataRows.forEach((line, i) => {
            const cols = parseCSVLine(line);
            bodyHtml += '<tr>';
            cols.forEach(c => { bodyHtml += `<td>${c.replace(/</g,'&lt;')}</td>`; });
            bodyHtml += '</tr>';
        });
        previewBody.innerHTML = bodyHtml;

        // Status note
        const totalData = lines.length - 1;
        if (missing.length > 0) {
            previewNote.innerHTML = `<span style="color:var(--danger);">⚠️ Missing required columns: <strong>${missing.join(', ')}</strong>. Please fix your CSV before uploading.</span>`;
            document.getElementById('uploadBtn').disabled = true;
        } else {
            previewNote.innerHTML = `✅ Columns look correct. Showing ${Math.min(10, totalData)} of ${totalData} row(s).`;
            document.getElementById('uploadBtn').disabled = false;
        }

        document.getElementById('csvPreviewSection').style.display = 'block';
        document.getElementById('clearCsvBtn').style.display = 'inline-block';
    };
    reader.readAsText(file);
}

document.getElementById('csvFile').addEventListener('change', function() {
    if (this.files.length > 0) {
        document.getElementById('csvFileName').textContent = this.files[0].name;
        parseAndPreviewCSV(this.files[0]);
    }
});

// Drag & drop
const dropZone = document.getElementById('csvDropZone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.style.borderColor = 'var(--primary)'; });
dropZone.addEventListener('dragleave', () => { dropZone.style.borderColor = ''; });
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.style.borderColor = '';
    const f = e.dataTransfer.files[0];
    if (f && f.name.endsWith('.csv')) {
        const dt = new DataTransfer();
        dt.items.add(f);
        document.getElementById('csvFile').files = dt.files;
        document.getElementById('csvFileName').textContent = f.name;
        parseAndPreviewCSV(f);
    }
});
</script>

<?php include "footer.php"; ?>
