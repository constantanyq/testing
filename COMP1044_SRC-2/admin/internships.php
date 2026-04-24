<?php
session_start();
require_once "../connection.php";
$pageTitle = "Internships";
include "header.php";

$msg = "";

// ---- DELETE ----
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM internship WHERE internship_id = $del_id");
    $msg = "Internship record deleted.";
}

// ---- ADD ----
if (isset($_POST['action']) && $_POST['action'] == 'add') {
    $student_id    = mysqli_real_escape_string($conn, $_POST['student_id']);
    $lecturer_id   = mysqli_real_escape_string($conn, $_POST['lecturer_id']);
    $supervisor_id = mysqli_real_escape_string($conn, $_POST['supervisor_id']);
    $company_id    = mysqli_real_escape_string($conn, $_POST['company_id']);
    $duration      = mysqli_real_escape_string($conn, $_POST['duration']);

    // internship_id is AUTO_INCREMENT – no need to provide it
    $chk = mysqli_query($conn, "SELECT internship_id FROM internship WHERE student_id='$student_id'");
    if (mysqli_num_rows($chk) > 0) {
        $msg = "ERROR: This student already has an internship record.";
    } else {
        $sql = "INSERT INTO internship (student_id, lecturer_id, supervisor_id, company_id, duration)
                VALUES ('$student_id','$lecturer_id','$supervisor_id','$company_id','$duration')";
        mysqli_query($conn, $sql);
        $msg = "Internship record added successfully.";
    }
}

// ---- EDIT ----
if (isset($_POST['action']) && $_POST['action'] == 'edit') {
    $iid           = (int)$_POST['internship_id'];
    $lecturer_id   = mysqli_real_escape_string($conn, $_POST['lecturer_id']);
    $supervisor_id = mysqli_real_escape_string($conn, $_POST['supervisor_id']);
    $company_id    = mysqli_real_escape_string($conn, $_POST['company_id']);
    $duration      = mysqli_real_escape_string($conn, $_POST['duration']);

    $sql = "UPDATE internship SET lecturer_id='$lecturer_id', supervisor_id='$supervisor_id',
            company_id='$company_id', duration='$duration'
            WHERE internship_id=$iid";
    mysqli_query($conn, $sql);
    $msg = "Internship record updated.";
}

// ---- ADD COMPANY ----
if (isset($_POST['action']) && $_POST['action'] == 'add_company') {
    $cname = mysqli_real_escape_string($conn, trim($_POST['company_name']));
    if ($cname) {
        mysqli_query($conn, "INSERT INTO company (company_name) VALUES ('$cname')");
        $msg = "Company added.";
    }
}

// ---- Data for dropdowns ----

// Students not yet assigned
$availableStudents = [];
$sRes = mysqli_query($conn,
    "SELECT student_id, student_name FROM student
     WHERE student_id NOT IN (SELECT student_id FROM internship)
     ORDER BY student_name");
while ($r = mysqli_fetch_assoc($sRes)) $availableStudents[] = $r;

$allLecturers = [];
$lRes = mysqli_query($conn, "SELECT lecturer_id, lecturer_name FROM lecturer ORDER BY lecturer_name");
while ($r = mysqli_fetch_assoc($lRes)) $allLecturers[] = $r;

// Supervisors – include their company_id so JS can filter
$allSupervisors = [];
$svRes = mysqli_query($conn,
    "SELECT sv.supervisor_id, sv.supervisor_name, sv.company_id, c.company_name
     FROM supervisor sv
     LEFT JOIN company c ON sv.company_id = c.company_id
     ORDER BY sv.supervisor_name");
while ($r = mysqli_fetch_assoc($svRes)) $allSupervisors[] = $r;

$allCompanies = [];
$cRes = mysqli_query($conn, "SELECT company_id, company_name FROM company ORDER BY company_name");
while ($r = mysqli_fetch_assoc($cRes)) $allCompanies[] = $r;

// Edit mode
$editRow = null;
if (isset($_GET['edit'])) {
    $eid    = (int)$_GET['edit'];
    $res    = mysqli_query($conn, "SELECT * FROM internship WHERE internship_id=$eid");
    $editRow = mysqli_fetch_assoc($res);
}

// Helpers for pre-filling edit mode text fields
function getLecturerLabel($lecturers, $id) {
    foreach ($lecturers as $l) {
        if ($l['lecturer_id'] == $id) return $l['lecturer_name'].' ('.$l['lecturer_id'].')';
    }
    return '';
}
function getSupervisorLabel($supervisors, $id) {
    foreach ($supervisors as $s) {
        if ($s['supervisor_id'] == $id) return $s['supervisor_name'].' ('.$s['supervisor_id'].')';
    }
    return '';
}
function getCompanyLabel($companies, $id) {
    foreach ($companies as $c) {
        if ($c['company_id'] == $id) return $c['company_name'];
    }
    return '';
}
?>

<div class="topbar">
    <h1>Manage Internships</h1>
    <span class="breadcrumb">Admin &rsaquo; Internships</span>
</div>

<div class="page-body">

<?php if ($msg): ?>
    <div class="alert <?= strpos($msg,'ERROR') === 0 ? 'alert-danger' : 'alert-success' ?>">
        <?= htmlspecialchars($msg) ?>
    </div>
<?php endif; ?>

<!-- Add Company -->
<div class="card">
    <div class="card-title">Add Company</div>
    <form method="POST" style="display:flex; gap:10px; align-items:flex-end;">
        <input type="hidden" name="action" value="add_company">
        <div class="form-group mb-0" style="flex:1">
            <label>Company Name</label>
            <input type="text" name="company_name" placeholder="e.g. Petronas Berhad" required>
        </div>
        <button type="submit" class="btn btn-success">➕ Add Company</button>
    </form>
</div>

<!-- Add / Edit Internship -->
<div class="card">
    <div class="card-title"><?= $editRow ? 'Edit Internship' : 'Assign Internship' ?></div>

    <form method="POST" id="internshipForm">
        <input type="hidden" name="action" value="<?= $editRow ? 'edit' : 'add' ?>">
        <?php if ($editRow): ?>
            <input type="hidden" name="internship_id" value="<?= $editRow['internship_id'] ?>">
        <?php endif; ?>

        <!-- STUDENT -->
        <?php if (!$editRow): ?>
        <div class="form-group">
            <label>Student</label>
            <div class="search-dropdown" id="sd-student">
                <input type="text" class="sd-input" placeholder="Type student name or ID to search..." autocomplete="off">
                <input type="hidden" name="student_id" class="sd-value">
                <div class="sd-list"></div>
            </div>
            <small class="text-muted">Only students without an internship are shown.</small>
        </div>
        <?php else: ?>
        <div class="form-group">
            <label>Student (cannot change)</label>
            <input type="text" value="<?= htmlspecialchars($editRow['student_id']) ?>" readonly style="background:#f0f4f8;">
            <input type="hidden" name="student_id" value="<?= htmlspecialchars($editRow['student_id']) ?>">
        </div>
        <?php endif; ?>

        <div class="form-row">
            <!-- COMPANY – pick this FIRST, supervisor list will filter -->
            <div class="form-group">
                <label>Company</label>
                <div class="search-dropdown" id="sd-company">
                    <input type="text" class="sd-input"
                           placeholder="Type company name to search..."
                           autocomplete="off"
                           value="<?= $editRow ? htmlspecialchars(getCompanyLabel($allCompanies, $editRow['company_id'])) : '' ?>">
                    <input type="hidden" name="company_id" class="sd-value"
                           value="<?= $editRow ? htmlspecialchars($editRow['company_id']) : '' ?>">
                    <div class="sd-list"></div>
                </div>
            </div>

            <!-- DURATION -->
            <div class="form-group">
                <label>Duration (weeks)</label>
                <input type="number" name="duration" min="1" max="52"
                       value="<?= htmlspecialchars($editRow['duration'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-row">
            <!-- LECTURER -->
            <div class="form-group">
                <label>Lecturer (Assessor)</label>
                <div class="search-dropdown" id="sd-lecturer">
                    <input type="text" class="sd-input"
                           placeholder="Type lecturer name to search..."
                           autocomplete="off"
                           value="<?= $editRow ? htmlspecialchars(getLecturerLabel($allLecturers, $editRow['lecturer_id'])) : '' ?>">
                    <input type="hidden" name="lecturer_id" class="sd-value"
                           value="<?= $editRow ? htmlspecialchars($editRow['lecturer_id']) : '' ?>">
                    <div class="sd-list"></div>
                </div>
            </div>

            <!-- SUPERVISOR – filtered by company -->
            <div class="form-group">
                <label>Supervisor (Assessor)</label>
                <div class="search-dropdown" id="sd-supervisor">
                    <input type="text" class="sd-input"
                           placeholder="Select a company first..."
                           autocomplete="off"
                           value="<?= $editRow ? htmlspecialchars(getSupervisorLabel($allSupervisors, $editRow['supervisor_id'])) : '' ?>">
                    <input type="hidden" name="supervisor_id" class="sd-value"
                           value="<?= $editRow ? htmlspecialchars($editRow['supervisor_id']) : '' ?>">
                    <div class="sd-list"></div>
                </div>
                <small class="text-muted" id="sup-hint">⬅ Pick a company first to filter supervisors</small>
            </div>
        </div>

        <div style="display:flex; gap:10px; margin-top:6px;">
            <button type="submit" class="btn btn-primary">
                <?= $editRow ? '💾 Update' : '➕ Assign Internship' ?>
            </button>
            <?php if ($editRow): ?>
                <a href="internships.php" class="btn btn-outline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Internship List -->
<div class="card">
    <div class="card-title flex-between">
        <span>Internship Records</span>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search...">
        </div>
    </div>
    <div class="table-wrap">
    <table id="mainTable">
        <thead>
            <tr>
                <th>ID</th><th>Student</th><th>Company</th>
                <th>Lecturer</th><th>Supervisor</th>
                <th>Duration</th><th>Avg Marks</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT i.*, s.student_name, c.company_name, l.lecturer_name, sv.supervisor_name
                FROM internship i
                LEFT JOIN student    s  ON i.student_id    = s.student_id
                LEFT JOIN company    c  ON i.company_id    = c.company_id
                LEFT JOIN lecturer   l  ON i.lecturer_id   = l.lecturer_id
                LEFT JOIN supervisor sv ON i.supervisor_id = sv.supervisor_id
                ORDER BY i.internship_id";
        $res = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($res)):
        ?>
        <tr>
            <td><?= $row['internship_id'] ?></td>
            <td><?= htmlspecialchars($row['student_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['company_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['lecturer_name'] ?? '—') ?></td>
            <td><?= htmlspecialchars($row['supervisor_name'] ?? '—') ?></td>
            <td><?= $row['duration'] ? $row['duration'].' wks' : '—' ?></td>
            <td><?= $row['average_marks'] !== null ? number_format($row['average_marks'],2) : '<span class="text-muted">Pending</span>' ?></td>
            <td>
                <a href="internships.php?edit=<?= $row['internship_id'] ?>" class="btn btn-warning btn-sm">✏️</a>
                <a href="internships.php?delete=<?= $row['internship_id'] ?>"
                   class="btn btn-danger btn-sm confirm-delete"
                   data-msg="Delete this internship record?">🗑️</a>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    </div>
</div>

</div><!-- page-body -->

<script>
// ============================================================
// All supervisor data including which company they belong to
// ============================================================
var allSupervisors = <?= json_encode(array_map(function($r) {
    return [
        'id'           => $r['supervisor_id'],
        'label'        => $r['supervisor_name'] . ' (' . $r['supervisor_id'] . ')',
        'company_id'   => (string)($r['company_id'] ?? ''),
        'company_name' => $r['company_name'] ?? ''
    ];
}, $allSupervisors)) ?>;

var allLecturers = <?= json_encode(array_map(function($r) {
    return ['id' => $r['lecturer_id'], 'label' => $r['lecturer_name'].' ('.$r['lecturer_id'].')'];
}, $allLecturers)) ?>;

var allStudents = <?= json_encode(array_map(function($r) {
    return ['id' => $r['student_id'], 'label' => $r['student_id'].' – '.$r['student_name']];
}, $availableStudents)) ?>;

var allCompanies = <?= json_encode(array_map(function($r) {
    return ['id' => (string)$r['company_id'], 'label' => $r['company_name']];
}, $allCompanies)) ?>;

// ============================================================
// Generic searchable dropdown builder
// ============================================================
function buildDropdown(containerId, items, onSelect) {
    var container   = document.getElementById(containerId);
    if (!container) return;
    var textInput   = container.querySelector('.sd-input');
    var hiddenInput = container.querySelector('.sd-value');
    var listBox     = container.querySelector('.sd-list');

    function renderList(query, sourceItems) {
        listBox.innerHTML = '';
        var q        = query.toLowerCase().trim();
        var filtered = sourceItems.filter(function(item) {
            return item.label.toLowerCase().indexOf(q) !== -1;
        });

        if (filtered.length === 0) {
            var empty = document.createElement('div');
            empty.className   = 'sd-item sd-empty';
            empty.textContent = q ? 'No results for "' + query + '"' : 'No options available';
            listBox.appendChild(empty);
        } else {
            filtered.forEach(function(item) {
                var div = document.createElement('div');
                div.className = 'sd-item';
                // Bold matched part
                if (q) {
                    var lbl   = item.label;
                    var lower = lbl.toLowerCase();
                    var idx   = lower.indexOf(q);
                    if (idx !== -1) {
                        lbl = lbl.substring(0, idx)
                            + '<mark>' + lbl.substring(idx, idx + q.length) + '</mark>'
                            + lbl.substring(idx + q.length);
                    }
                    div.innerHTML = lbl;
                } else {
                    div.textContent = item.label;
                }
                div.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    textInput.value   = item.label;
                    hiddenInput.value = item.id;
                    textInput.classList.add('sd-selected');
                    listBox.style.display = 'none';
                    if (onSelect) onSelect(item); // callback when item is picked
                });
                listBox.appendChild(div);
            });
        }
        listBox.style.display = 'block';
    }

    // Expose method so supervisor can be refreshed externally
    container._renderList    = renderList;
    container._getItems      = function() { return items; };
    container._setItems      = function(newItems) { items = newItems; };
    container._textInput     = textInput;
    container._hiddenInput   = hiddenInput;
    container._listBox       = listBox;

    textInput.addEventListener('input', function() {
        hiddenInput.value = '';
        textInput.classList.remove('sd-selected');
        renderList(this.value, items);
    });
    textInput.addEventListener('focus', function() {
        renderList(this.value, items);
    });
    textInput.addEventListener('blur', function() {
        setTimeout(function() {
            listBox.style.display = 'none';
            if (!hiddenInput.value) {
                textInput.value = '';
                textInput.classList.remove('sd-selected');
            }
        }, 200);
    });
}

// ============================================================
// Build all dropdowns
// ============================================================

// Company dropdown – when a company is picked, filter supervisors
buildDropdown('sd-company', allCompanies, function(selectedCompany) {
    // Filter supervisors to only those who belong to this company
    var filtered = allSupervisors.filter(function(sv) {
        return sv.company_id === selectedCompany.id;
    });

    var supContainer = document.getElementById('sd-supervisor');
    if (!supContainer) return;

    // Reset supervisor field
    supContainer._hiddenInput.value = '';
    supContainer._textInput.value   = '';
    supContainer._textInput.classList.remove('sd-selected');
    supContainer._setItems(filtered);

    // Update placeholder hint
    var hint = document.getElementById('sup-hint');
    if (filtered.length === 0) {
        supContainer._textInput.placeholder = 'No supervisors found for this company';
        if (hint) hint.textContent = '⚠️ No supervisors linked to this company yet. Add one in Manage Users.';
    } else {
        supContainer._textInput.placeholder = 'Type to search supervisors from ' + selectedCompany.label + '...';
        if (hint) hint.textContent = filtered.length + ' supervisor(s) available for this company';
    }
});

buildDropdown('sd-lecturer',   allLecturers,  null);
buildDropdown('sd-supervisor', allSupervisors, null); // starts with all; gets filtered when company picked

// Student dropdown (only on add form)
buildDropdown('sd-student', allStudents, null);

// On edit mode, pre-filter the supervisor list to the already-selected company
var preCompanyId = document.querySelector('#sd-company .sd-value') ?
                   document.querySelector('#sd-company .sd-value').value : '';
if (preCompanyId) {
    var preFiltered = allSupervisors.filter(function(sv) {
        return sv.company_id === preCompanyId;
    });
    var supCont = document.getElementById('sd-supervisor');
    if (supCont) supCont._setItems(preFiltered);
}

// ============================================================
// Form validation before submit
// ============================================================
document.getElementById('internshipForm').addEventListener('submit', function(e) {
    var errors = [];
    var stuHidden = document.querySelector('#sd-student .sd-value');
    if (stuHidden && !stuHidden.value)                                  errors.push('Student');
    if (!document.querySelector('#sd-company .sd-value').value)         errors.push('Company');
    if (!document.querySelector('#sd-lecturer .sd-value').value)        errors.push('Lecturer');
    if (!document.querySelector('#sd-supervisor .sd-value').value)      errors.push('Supervisor');

    if (errors.length > 0) {
        e.preventDefault();
        alert('Please select from the dropdown list for:\n• ' + errors.join('\n• '));
    }
});
</script>

<?php include "footer.php"; ?>
