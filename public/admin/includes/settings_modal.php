<?php
// --- FETCH DYNAMIC DATA FROM DB FOR SETTINGS & UPLOADS ---
$allDepartments = [];
$allPrograms = [];
$sysSettings = [
    'system_name' => 'USTP E-Gallery',
    'default_class_year' => '2029',
    'maintenance_mode' => '0'
];

if (isset($conn)) {
    // Fetch Departments
    $deptRes = $conn->query("SELECT * FROM departments ORDER BY name ASC");
    if ($deptRes && $deptRes->num_rows > 0) {
        while ($row = $deptRes->fetch_assoc()) {
            $allDepartments[] = $row;
        }
    }

    // Fetch Programs 
    $progRes = $conn->query("SELECT p.*, COALESCE(d.abbreviation, 'UNKNOWN') as dept_abbr FROM programs p LEFT JOIN departments d ON p.department_id = d.id ORDER BY p.name ASC");
    if ($progRes && $progRes->num_rows > 0) {
        while ($row = $progRes->fetch_assoc()) {
            $allPrograms[] = $row;
        }
    }

    // NEW: Fetch Class Years
    $yearRes = $conn->query("SELECT * FROM class_years ORDER BY year DESC");
    if ($yearRes && $yearRes->num_rows > 0) {
        while ($row = $yearRes->fetch_assoc()) {
            $allYears[] = $row;
        }
    }

    // NEW: Fetch Global Settings!
    $setRes = $conn->query("SELECT setting_key, setting_value FROM system_settings");
    if ($setRes && $setRes->num_rows > 0) {
        while ($row = $setRes->fetch_assoc()) {
            $sysSettings[$row['setting_key']] = $row['setting_value'];
        }
    }
}
?>

<style>
    #settingsModal .modal-content {
        border-radius: 12px;
        height: 650px;
        min-height: 650px;
        overflow: hidden;
    }

    #settingsModal .settings-sidebar {
        width: 250px;
        flex-shrink: 0;
        background-color: var(--bs-body-bg);
        border-right: 2px solid var(--bs-border-color);
        padding: 30px 20px;
    }

    #settingsModal .settings-main {
        background-color: var(--bs-body-bg);
        padding: 40px;
        position: relative;
        overflow-y: auto;
    }

    #settingsModal .settings-nav .nav-link {
        color: var(--bs-body-color);
        font-weight: 800;
        font-size: 0.85rem;
        border-radius: 8px;
        margin-bottom: 5px;
        padding: 12px 15px;
        text-align: left;
        transition: all 0.2s;
    }

    #settingsModal .settings-nav .nav-link.active {
        background-color: rgba(26, 24, 81, 0.1);
        color: #1A1851;
    }

    #settingsModal .settings-nav .nav-link:hover:not(.active) {
        background-color: var(--bs-secondary-bg);
        color: inherit;
    }

    #settingsModal .settings-nav .nav-link i {
        font-size: 1.1rem;
        width: 24px;
        display: inline-block;
    }

    #settingsModal .btn-navy {
        background-color: #1A1851 !important;
        color: white !important;
        font-weight: 800;
        border: none;
        border-radius: 6px;
        padding: 8px 24px;
        transition: 0.2s;
    }

    #settingsModal .btn-navy:hover {
        background-color: #2a2775 !important;
        color: white !important;
        opacity: 1 !important;
    }

    #settingsModal .form-switch.settings-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        cursor: pointer;
    }

    #settingsModal .settings-switch .form-check-label {
        padding-top: 4px;
        font-weight: 700;
        cursor: pointer;
    }

    #settingsModal .theme-list-group .list-group-item {
        cursor: pointer;
        font-weight: 600;
        border: none;
        border-radius: 8px !important;
        margin-bottom: 5px;
        padding: 12px 20px;
        transition: 0.2s;
    }

    #settingsModal .theme-list-group .list-group-item.active-theme {
        background-color: rgba(26, 24, 81, 0.1);
        color: #1A1851;
        font-weight: 800;
    }

    #settingsModal .theme-apply-container {
        background: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 12px;
        padding: 20px;
        margin-top: 30px;
    }

    /* Custom Input Modal Styling */
    .custom-input-modal .modal-content {
        border-radius: 10px !important;
        padding: 35px;
        border: 1px solid var(--bs-border-color);
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
    }

    .custom-input-modal h4 {
        font-weight: 900;
        margin-bottom: 25px;
        font-size: 1.5rem;
    }

    .custom-input-modal .form-label {
        font-weight: 800;
        font-size: 0.95rem;
        color: var(--bs-body-color);
    }

    .custom-input-modal .form-control,
    .custom-input-modal .form-select {
        border-radius: 4px;
        background-color: var(--bs-body-bg);
        color: var(--bs-body-color);
        border: 2px solid #e2e8f0;
        padding: 12px 15px;
    }

    .custom-input-modal .form-control:focus,
    .custom-input-modal .form-select:focus {
        border-color: #1A1851;
        box-shadow: none;
    }

    .btn-outline-navy {
        border: 2px solid #1A1851;
        color: #1A1851;
        background: transparent;
        border-radius: 4px;
        font-weight: 800;
        transition: 0.2s;
    }

    .btn-outline-navy:hover {
        background-color: rgba(26, 24, 81, 0.05);
        color: #1A1851;
    }

    .custom-input-modal .btn-navy {
        border-radius: 4px;
    }

    /* --- DARK MODE --- */
    [data-bs-theme="dark"] #settingsModal .modal-content,
    [data-bs-theme="dark"] #settingsModal .settings-sidebar,
    [data-bs-theme="dark"] #settingsModal .settings-main {
        background-color: #1e1e1e !important;
        border-color: #333 !important;
        color: #e0e0e0 !important;
    }

    [data-bs-theme="dark"] #settingsModal .settings-nav .nav-link.active {
        background-color: rgba(130, 170, 255, 0.1);
        color: #82aaff;
    }

    [data-bs-theme="dark"] #settingsModal .settings-nav .nav-link:hover:not(.active) {
        color: #fff !important;
    }

    [data-bs-theme="dark"] #settingsModal .btn-navy {
        background-color: #82aaff !important;
        color: #121212 !important;
    }

    [data-bs-theme="dark"] #settingsModal .btn-navy:hover {
        background-color: #a0c2ff !important;
        color: #121212 !important;
        opacity: 1 !important;
    }

    [data-bs-theme="dark"] #settingsModal .theme-list-group .list-group-item.active-theme {
        background-color: rgba(130, 170, 255, 0.1);
        color: #82aaff;
    }

    [data-bs-theme="dark"] .custom-input-modal .modal-content {
        background-color: #1e1e1e !important;
        border-color: #333 !important;
    }

    [data-bs-theme="dark"] .custom-input-modal .form-control,
    [data-bs-theme="dark"] .custom-input-modal .form-select {
        background-color: #2c2c2c !important;
        color: white !important;
        border-color: #555 !important;
    }

    [data-bs-theme="dark"] .custom-input-modal .form-control:focus,
    [data-bs-theme="dark"] .custom-input-modal .form-select:focus {
        border-color: #82aaff !important;
    }

    [data-bs-theme="dark"] .btn-outline-navy {
        border-color: #82aaff;
        color: #82aaff;
    }

    [data-bs-theme="dark"] .btn-outline-navy:hover {
        background-color: rgba(130, 170, 255, 0.1);
        color: #82aaff;
    }
</style>

<div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="d-flex h-100 w-100">

                <div class="settings-sidebar">
                    <h4 class="fw-bold mb-4 ms-2">Settings</h4>

                    <div class="nav flex-column settings-nav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-profile" type="button"><i class="bi bi-person-fill"></i> Profile</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-theme" type="button"><i class="bi bi-circle-half"></i> Theme</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-activity" type="button"><i class="bi bi-clock-history"></i> Activity logs</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-general" type="button"><i class="bi bi-gear-fill"></i> General Settings</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-data" type="button" id="nav-btn-data"><i class="bi bi-database-fill"></i> Data Management</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-storage" type="button"><i class="bi bi-hdd-fill"></i> Storage & Backups</button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-export" type="button"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Export Data</button>
                    </div>
                </div>

                <div class="settings-main flex-grow-1">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-4" data-bs-dismiss="modal"></button>

                    <div class="tab-content mt-5 pt-2" id="v-pills-tabContent">

                        <div class="tab-pane fade show active" id="tab-profile">
                            <h4 class="fw-bold mb-4">Profile Security</h4>
                            <div class="row g-4 max-w-75">
                                <div class="col-md-6">
                                    <label class="form-label">Admin Username</label>
                                    <input type="text" class="form-control" value="admin1">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Recovery Email</label>
                                    <input type="email" class="form-control" placeholder="admin@ustp.edu.ph">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" placeholder="••••••••">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" placeholder="••••••••">
                                </div>
                                <div class="col-12 mt-4">
                                    <div class="form-check form-switch settings-switch d-flex align-items-center gap-3">
                                        <input class="form-check-input m-0" type="checkbox" id="twoFactorSwitch">
                                        <label class="form-check-label m-0" for="twoFactorSwitch">Enable Two-Factor Authentication (2FA)</label>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button class="btn btn-navy fw-bold">Save Profile</button>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-theme">
                            <h4 class="fw-bold mb-4">Appearance & Branding</h4>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label mb-3">System Mode</label>
                                    <div class="list-group theme-list-group shadow-sm border rounded-3 p-1">
                                        <div class="list-group-item d-flex justify-content-between align-items-center" onclick="selectThemeMode(this, 'system')">
                                            System Default <i class="bi bi-check-circle-fill text-navy theme-check d-none"></i>
                                        </div>
                                        <div class="list-group-item active-theme d-flex justify-content-between align-items-center" onclick="selectThemeMode(this, 'light')">
                                            Light Mode <i class="bi bi-check-circle-fill text-navy theme-check"></i>
                                        </div>
                                        <div class="list-group-item d-flex justify-content-between align-items-center" onclick="selectThemeMode(this, 'dark')">
                                            Dark Mode <i class="bi bi-check-circle-fill text-navy theme-check d-none"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label mb-3">School Logo</label>
                                    <div class="border rounded-3 p-3 text-center bg-body-secondary" style="border-style: dashed !important;">
                                        <i class="bi bi-image fs-1 text-muted"></i>
                                        <p class="text-muted small mt-2 mb-2">Upload a new logo to replace the E-Gallery text.</p>
                                        <button class="btn btn-outline-secondary btn-sm fw-bold px-4" onclick="document.getElementById('logoUpload').click()">Browse Files</button>
                                        <input type="file" id="logoUpload" hidden accept="image/png, image/jpeg">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="theme-apply-container d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="fw-bold m-0">Ready to update?</h6>
                                            <small class="text-muted">Changes will apply across the entire dashboard.</small>
                                        </div>
                                        <button class="btn btn-navy fw-bold" onclick="applySelectedTheme()">Apply Theme</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-activity">
                            <div class="d-flex justify-content-between align-items-center mb-4 pe-2">
                                <h4 class="fw-bold m-0">Activity Logs</h4>
                                <button class="btn btn-outline-danger btn-sm fw-bold">Clear Logs</button>
                            </div>
                            <div class="table-responsive border rounded-3" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-hover align-middle m-0">
                                    <thead class="table-group-divider sticky-top" style="background-color: var(--bs-body-bg);">
                                        <tr>
                                            <th>Admin</th>
                                            <th>Action</th>
                                            <th>Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($conn)) {
                                            $logQuery = "SELECT u.username, a.action, a.created_at 
                                                         FROM activity_logs a 
                                                         JOIN user u ON a.admin_id = u.id 
                                                         ORDER BY a.created_at DESC LIMIT 15";
                                            $logResult = $conn->query($logQuery);

                                            if ($logResult && $logResult->num_rows > 0) {
                                                while ($log = $logResult->fetch_assoc()) {
                                                    $time = date("M j, Y, g:i A", strtotime($log['created_at']));
                                                    $actionClass = strpos(strtolower($log['action']), 'delete') !== false || strpos(strtolower($log['action']), 'deleted') !== false ? 'text-danger' : 'text-body';

                                                    echo "<tr>";
                                                    echo "<td class='fw-bold'>" . htmlspecialchars($log['username']) . "</td>";
                                                    echo "<td class='$actionClass'>" . htmlspecialchars($log['action']) . "</td>";
                                                    echo "<td class='text-muted small'>" . $time . "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='3' class='text-center text-muted small py-4'>No recent activity found.</td></tr>";
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-general">
                            <h4 class="fw-bold mb-4">General Settings</h4>
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label">System Name</label>
                                    <input type="text" class="form-control" id="systemNameInput" value="<?php echo htmlspecialchars($sysSettings['system_name']); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Default Class Year</label>
                                    <select class="form-select" name="class_year" id="classYearSelect">
                                        <?php foreach ($allYears as $y): ?>
                                            <option value="<?php echo htmlspecialchars($y['year']); ?>" <?php echo ($defaultYear == $y['year']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($y['year']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Auto-fills the upload form to save time.</small>
                                </div>

                                <div class="col-12 mt-5 p-4 border rounded-3 bg-body-secondary">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="fw-bold m-0 text-danger">Maintenance Mode</h6>
                                            <small class="text-muted">Takes the user-facing site offline for updates.</small>
                                        </div>
                                        <div class="form-check form-switch settings-switch m-0">
                                            <input class="form-check-input" type="checkbox" id="maintenanceSwitch" <?php echo ($sysSettings['maintenance_mode'] == '1') ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button class="btn btn-navy fw-bold" onclick="saveGeneralSettings()">Save Changes</button>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-data">
                            <h4 class="fw-bold mb-4">Data Management</h4>
                            <p class="text-muted mb-4">Manage the standard lists used in the upload forms.</p>
                            
                            <div class="row g-4 align-items-start">
                                <div class="col-md-6 d-flex flex-column gap-4">
                                    
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3 d-flex justify-content-between">Departments <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="openStackedModal('addDeptModal')">+</button></h6>
                                            <ul class="list-group list-group-flush border-top" style="max-height: 250px; overflow-y: auto;">
                                                <?php if (!empty($allDepartments)): ?>
                                                    <?php foreach ($allDepartments as $dept): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 small">
                                                            <?php echo htmlspecialchars($dept['abbreviation'] . ' - ' . $dept['name']); ?>
                                                            <i class="bi bi-x-circle text-danger cursor-pointer" style="cursor: pointer;" onclick="deleteItem('department', <?php echo $dept['id']; ?>, '<?php echo addslashes($dept['name']); ?>')"></i>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <li class="list-group-item px-0 py-2 small text-muted">No departments found.</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3 d-flex justify-content-between">Class Years <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="openStackedModal('addClassYearModal')">+</button></h6>
                                            <ul class="list-group list-group-flush border-top" style="max-height: 250px; overflow-y: auto;">
                                                <?php if (!empty($allYears)): ?>
                                                    <?php foreach ($allYears as $y): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 small">
                                                            <?php echo htmlspecialchars($y['year']); ?>
                                                            <i class="bi bi-x-circle text-danger cursor-pointer" style="cursor: pointer;" onclick="deleteItem('class_year', <?php echo $y['id']; ?>, '<?php echo addslashes($y['year']); ?>')"></i>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <li class="list-group-item px-0 py-2 small text-muted">No class years found.</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-body">
                                            <h6 class="fw-bold mb-3 d-flex justify-content-between">Programs <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="openStackedModal('addProgModal')">+</button></h6>
                                            <ul class="list-group list-group-flush border-top" style="max-height: 250px; overflow-y: auto;">
                                                <?php if (!empty($allPrograms)): ?>
                                                    <?php foreach ($allPrograms as $prog): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 small">
                                                            <div>
                                                                <?php echo htmlspecialchars($prog['name']); ?><br>
                                                                <small class="text-muted" style="font-size: 0.7rem;"><?php echo htmlspecialchars($prog['dept_abbr']); ?></small>
                                                            </div>
                                                            <i class="bi bi-x-circle text-danger cursor-pointer" style="cursor: pointer;" onclick="deleteItem('program', <?php echo $prog['id']; ?>, '<?php echo addslashes($prog['name']); ?>')"></i>
                                                        </li>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <li class="list-group-item px-0 py-2 small text-muted">No programs found.</li>
                                                <?php endif; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-storage">
                            <h4 class="fw-bold mb-4">Storage & Backups</h4>
                            <div class="mb-5">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="fw-bold fs-6">Server Storage</span>
                                    <span class="fw-bold text-muted fs-6">45GB / 100GB</span>
                                </div>
                                <div class="progress" style="height: 12px; border-radius: 10px;">
                                    <div class="progress-bar" role="progressbar" style="width: 45%; background-color: #1A1851;"></div>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-4 text-center h-100 bg-body-tertiary">
                                        <i class="bi bi-database-down fs-1 text-primary mb-2 d-block"></i>
                                        <h6 class="fw-bold">Database Backup</h6>
                                        <p class="text-muted small mb-3">Create a safe copy of the MySQL database.</p>
                                        <button class="btn btn-outline-primary fw-bold px-4">Generate SQL</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-export">
                            <h4 class="fw-bold mb-4">Export Data</h4>
                            <p class="text-muted mb-4">Download your system data for offline use or reporting.</p>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="border rounded-3 p-4 text-center h-100 bg-body-tertiary d-flex flex-column align-items-center justify-content-center">
                                        <i class="bi bi-file-earmark-spreadsheet fs-1 text-success mb-2 d-block"></i>
                                        <h6 class="fw-bold">Export Students CSV</h6>
                                        <p class="text-muted small mb-3">Select a specific class year or download all records.</p>

                                        <div class="w-100 mb-4 text-start" style="max-width: 250px;">
                                            <label class="form-label small fw-bold">Select Class Year</label>
                                            <select class="form-select form-select-sm shadow-sm" id="exportClassYear">
                                                <option value="all" selected>All Years</option>
                                                <option value="2028">2028</option>
                                                <option value="2029">2029</option>
                                                <option value="2030">2030</option>
                                            </select>
                                        </div>

                                        <button class="btn btn-outline-success fw-bold px-4" onclick="exportStudentData()">Download CSV</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade custom-input-modal" id="addDeptModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content p-4 p-md-5">
            <h4 class="m-0 mb-4 fw-bold">Departments</h4>
            <div class="mb-3">
                <label class="form-label">Department <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="ui-dept-name" placeholder="Full Department Name (e.g. Engineer)">
            </div>
            <div class="mb-4">
                <label class="form-label">Abbreviation <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="ui-dept-abbr" placeholder="Abbreviation (e.g. CEA)">
            </div>
            <div class="d-flex gap-3 mt-2">
                <button type="button" class="btn btn-navy flex-grow-1 py-2 fw-bold" onclick="submitDepartment()">Add Department</button>
                <button type="button" class="btn btn-outline-navy flex-grow-1 fw-bold py-2" onclick="closeStackedModal('addDeptModal')">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade custom-input-modal" id="addProgModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
        <div class="modal-content p-4 p-md-5">
            <h4 class="m-0 mb-4 fw-bold">Programs</h4>
            <div class="mb-3">
                <label class="form-label">Department <span class="text-danger">*</span></label>
                <select class="form-select" id="ui-prog-dept">
                    <option value="" disabled selected>Select Department</option>
                    <?php
                    if (!empty($allDepartments)) {
                        foreach ($allDepartments as $dept) {
                            echo '<option value="' . $dept['id'] . '">' . htmlspecialchars($dept['abbreviation'] . ' - ' . $dept['name']) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Program <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="ui-prog-name" placeholder="Full Program Name (e.g. Information Technology)">
            </div>
            <div class="mb-4">
                <label class="form-label">Abbreviation <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="ui-prog-abbr" placeholder="Abbreviation (e.g. IT)">
            </div>
            <div class="d-flex gap-3 mt-2">
                <button type="button" class="btn btn-navy flex-grow-1 py-2 fw-bold" onclick="submitProgram()">Add Program</button>
                <button type="button" class="btn btn-outline-navy flex-grow-1 fw-bold py-2" onclick="closeStackedModal('addProgModal')">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade custom-input-modal" id="addClassYearModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content p-4 p-md-5">
            <h4 class="m-0 mb-4 fw-bold">Class Year</h4>
            <div class="mb-4">
                <label class="form-label">Year <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="ui-year-name" placeholder="e.g. 2032" min="2000" max="2100">
            </div>
            <div class="d-flex gap-3 mt-2">
                <button type="button" class="btn btn-navy flex-grow-1 py-2 fw-bold" onclick="submitClassYear()">Add Year</button>
                <button type="button" class="btn btn-outline-navy flex-grow-1 fw-bold py-2" onclick="closeStackedModal('addClassYearModal')">Cancel</button>
            </div>
        </div>
    </div>
</div>


<script>
    // --- SETTINGS REOPEN LOGIC ---
    document.addEventListener("DOMContentLoaded", function() {
        if (sessionStorage.getItem('reopenSettings')) {
            let tabToOpen = sessionStorage.getItem('reopenSettings');
            sessionStorage.removeItem('reopenSettings');

            let modalEl = document.getElementById('settingsModal');
            if (modalEl) {
                let modal = new bootstrap.Modal(modalEl);
                modal.show();
                setTimeout(() => {
                    let tabBtn = document.getElementById('nav-btn-' + tabToOpen);
                    if (tabBtn) tabBtn.click();
                }, 300);
            }
        }
    });

    // --- NEW: DATABASE SETTINGS AJAX LOGIC ---
    function saveGeneralSettings() {
        const sysName = document.getElementById('systemNameInput').value.trim();
        const defaultYear = document.getElementById('defaultClassYearSelect').value;
        const maintenance = document.getElementById('maintenanceSwitch').checked ? '1' : '0';

        let formData = new FormData();
        formData.append('system_name', sysName);
        formData.append('default_class_year', defaultYear);
        formData.append('maintenance_mode', maintenance);

        fetch('../../app/controllers/updateSettingsController.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                            icon: 'success',
                            title: 'Settings Saved!',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        .then(() => {
                            sessionStorage.setItem('reopenSettings', 'general'); // Reopen to General Tab
                            location.reload();
                        });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Failed to save settings. Check connection.', 'error');
            });
    }

    let pendingTheme = localStorage.getItem('themeMode') || 'light';

    function selectThemeMode(element, mode) {
        pendingTheme = mode;
        const allItems = document.querySelectorAll('.theme-list-group .list-group-item');
        const allChecks = document.querySelectorAll('.theme-list-group .theme-check');

        allItems.forEach(item => item.classList.remove('active-theme'));
        allChecks.forEach(check => check.classList.add('d-none'));

        element.classList.add('active-theme');
        element.querySelector('.theme-check').classList.remove('d-none');
    }

    function applySelectedTheme() {
        localStorage.setItem('themeMode', pendingTheme);
        if (typeof applyGlobalTheme === 'function') {
            applyGlobalTheme(pendingTheme);
        }
        Swal.fire({
            icon: 'success',
            title: 'Theme Applied!',
            showConfirmButton: false,
            timer: 1500
        });
    }

    function exportStudentData() {
        const selectedYear = document.getElementById('exportClassYear').value;
        const exportMessage = selectedYear === 'all' ?
            'Downloading all student records...' :
            `Downloading records for Class of ${selectedYear}...`;

        Swal.fire({
            icon: 'info',
            title: 'Exporting Data',
            text: exportMessage,
            showConfirmButton: false,
            timer: 2000
        });
    }

    // --- CUSTOM STACKED MODAL CONTROLLERS ---
    function openStackedModal(modalId) {
        let modalEl = document.getElementById(modalId);
        if (modalEl) {
            let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    function closeStackedModal(modalId) {
        let modalEl = document.getElementById(modalId);
        if (modalEl) {
            let modalObj = bootstrap.Modal.getInstance(modalEl);
            if (modalObj) {
                modalObj.hide();
            }
        }
    }

    // --- DATA MANAGEMENT AJAX LOGIC ---
    function submitDepartment() {
        const name = document.getElementById('ui-dept-name').value.trim();
        const abbr = document.getElementById('ui-dept-abbr').value.trim();

        if (!name || !abbr) {
            Swal.fire('Error', 'Both fields are required', 'error');
            return;
        }

        let formData = new FormData();
        formData.append('type', 'department');
        formData.append('name', name);
        formData.append('abbreviation', abbr);

        fetch('../../app/controllers/addDataController.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(response => {
                if (response.status === 'success') {
                    closeStackedModal('addDeptModal');
                    Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        .then(() => {
                            sessionStorage.setItem('reopenSettings', 'data'); // Reopen to Data Tab
                            location.reload();
                        });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            });
    }

    function submitProgram() {
        const dept_id = document.getElementById('ui-prog-dept').value;
        const name = document.getElementById('ui-prog-name').value.trim();
        const abbr = document.getElementById('ui-prog-abbr').value.trim();

        if (!dept_id || !name || !abbr) {
            Swal.fire('Error', 'All fields are required', 'error');
            return;
        }

        let formData = new FormData();
        formData.append('type', 'program');
        formData.append('department_id', dept_id);
        formData.append('name', name);
        formData.append('abbreviation', abbr);

        fetch('../../app/controllers/addDataController.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(response => {
                if (response.status === 'success') {
                    closeStackedModal('addProgModal');
                    Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        .then(() => {
                            sessionStorage.setItem('reopenSettings', 'data'); // Reopen to Data Tab
                            location.reload();
                        });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            });
    }

    function submitClassYear() {
        const year = document.getElementById('ui-year-name').value.trim();

        if (!year) {
            Swal.fire('Error', 'Year is required', 'error');
            return;
        }

        let formData = new FormData();
        formData.append('type', 'class_year');
        formData.append('year', year);

        fetch('../../app/controllers/addDataController.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(response => {
                if (response.status === 'success') {
                    closeStackedModal('addClassYearModal');
                    Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 1500
                        })
                        .then(() => {
                            sessionStorage.setItem('reopenSettings', 'data');
                            location.reload();
                        });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            });
    }

    function deleteItem(type, id, itemName) {
        Swal.fire({
            title: `Delete ${itemName}?`,
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                let formData = new FormData();
                formData.append('type', type);
                formData.append('id', id);
                formData.append('name', itemName);

                fetch('../../app/controllers/deleteDataController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(r => r.json())
                    .then(response => {
                        if (response.status === 'success') {
                            sessionStorage.setItem('reopenSettings', 'data'); // Reopen to Data Tab
                            location.reload();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    });
            }
        });
    }
</script>