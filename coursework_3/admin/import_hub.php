<?php
session_start();
require_once "../connection.php";
$pageTitle = "Bulk Import Hub";
include "header.php";
?>

<div class="topbar">
    <h1>Bulk Import Center</h1>
</div>

<div class="page-body">
    <div class="alert alert-info">
        💡 <strong>Pro-tip:</strong> Use the CSV templates provided in each section to ensure your data matches the database structure exactly.
    </div>

    <div class="dash-list">
        <div class="card dash-list-item border-blue">
            <div class="dash-item-left">
                <div class="dash-item-icon">🎓</div>
                <div class="dash-item-text">
                    <h3>Student Import</h3>
                    <p>Format: <code>student_id, student_name, email, programme, password</code></p>
                </div>
            </div>
            <div class="dash-item-action">
                <a href="import_students.php" class="btn btn-primary">Start Import ➔</a>
            </div>
        </div>

        <div class="card dash-list-item border-indigo">
            <div class="dash-item-left">
                <div class="dash-item-icon">👨‍🏫</div>
                <div class="dash-item-text">
                    <h3>Lecturer Import</h3>
                    <p>Format: <code>lecturer_id, lecturer_name, email, password</code></p>
                </div>
            </div>
            <div class="dash-item-action">
                <a href="import_lecturers.php" class="btn btn-primary">Start Import ➔</a>
            </div>
        </div>

        <div class="card dash-list-item border-teal">
            <div class="dash-item-left">
                <div class="dash-item-icon">🏢</div>
                <div class="dash-item-text">
                    <h3>Supervisor Import</h3>
                    <p>Format: <code>supervisor_id, supervisor_name, email, password, company_id</code></p>
                </div>
            </div>
            <div class="dash-item-action">
                <a href="import_supervisors.php" class="btn btn-primary">Start Import ➔</a>
            </div>
        </div>

        <div class="card dash-list-item border-purple">
            <div class="dash-item-left">
                <div class="dash-item-icon">💼</div>
                <div class="dash-item-text">
                    <h3>Internship Assignment Import</h3>
                    <p>Format: <code>student_id, company_id, lecturer_id, supervisor_id, duration</code></p>
                </div>
            </div>
            <div class="dash-item-action">
                <a href="import_internships.php" class="btn btn-primary">Start Import ➔</a>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>