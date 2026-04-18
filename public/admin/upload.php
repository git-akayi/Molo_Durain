<?php
include_once("../../app/middleware/admin.php");
include_once("../../app/config/config.php");

// Fetch ALL Sections so JavaScript can use them
$allSections = [];
if (isset($conn)) {
    $secRes = $conn->query("SELECT * FROM sections ORDER BY name ASC");
    if ($secRes && $secRes->num_rows > 0) {
        while($row = $secRes->fetch_assoc()) {
            $allSections[] = $row;
        }
    }
}

$allYears = [];
$yearRes = $conn->query("SELECT * FROM class_years ORDER BY year DESC");
if ($yearRes && $yearRes->num_rows > 0) {
    while($row = $yearRes->fetch_assoc()) {
        $allYears[] = $row;
    }
}

// Fetch the default year from settings
$defaultYear = '2029'; // Fallback
$setRes = $conn->query("SELECT setting_value FROM system_settings WHERE setting_key = 'default_class_year'");
if ($setRes && $setRes->num_rows > 0) {
    $defaultYear = $setRes->fetch_assoc()['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Upload - E-Gallery</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* --- BASE STYLES & LAYOUT --- */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; overflow-x: hidden; transition: background-color 0.3s, color 0.3s; }
        .content-area { margin-left: 240px; width: calc(100% - 240px); min-height: 100vh; background-color: var(--bs-body-bg); }

        .sidebar { width: 240px; height: 100vh; background-color: #1A1851; color: white; position: fixed; z-index: 1000; transition: width 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), transform 0.3s ease; overflow-x: hidden; }
        .sidebar-header { display: flex; align-items: center; padding: 15px 15px; height: 70px; }
        .menu-toggle-btn { background: transparent; border: none; color: #a0a0c0; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.2s; cursor: pointer; flex-shrink: 0; }
        .menu-toggle-btn:hover { background-color: rgba(255, 255, 255, 0.1); color: white; }
        .sidebar-logo { font-size: 1.3rem; font-weight: bold; margin-left: 10px; }
        .sidebar .nav-link { color: #a0a0c0; padding: 12px 20px; transition: 0.3s; text-decoration: none; display: flex; align-items: center; white-space: nowrap; }
        .sidebar .nav-link i { font-size: 1.2rem; width: 25px; text-align: center; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255, 255, 255, 0.1); }

        /* Cards & Containers */
        .dashboard-card, .photo-upload-card, .user-table-wrapper { 
            background-color: var(--bs-body-bg); 
            border: 1px solid var(--bs-border-color); 
            border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.03); 
            padding: 25px; 
        }

        /* --- THE BUTTONS --- */
        .btn-navy { background-color: #1A1851 !important; color: white !important; font-weight: 800; border: none; border-radius: 6px; padding: 8px 24px; transition: 0.3s; }
        .btn-navy:hover { background-color: #2a2775 !important; color: white !important; opacity: 1 !important; }

        .btn-upload-left { 
            background-color: #1A1851 !important; 
            color: white !important; 
            font-weight: bold; 
            border: none; 
            border-radius: 4px; 
            padding: 8px 24px; 
            font-size: 0.85rem; 
            margin-top: 10px; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            transition: 0.3s;
        }
        .btn-upload-left:hover { background-color: #2a2775 !important; color: white !important; opacity: 1 !important; }

        /* Typography & Components */
        .welcome-text { color: #6c757d; font-size: 1.1rem; margin-bottom: 30px; }
        .welcome-name { color: #1A1851; font-weight: 800; }
        .dashboard-table-head { border-bottom: 2px solid var(--bs-heading-color); }
        .dashboard-table-head th { border: none !important; padding-bottom: 12px; }
        .chart-bar { width: 30px; background-color: var(--bs-secondary-bg); border-radius: 4px 4px 0 0; transition: 0.3s; }
        .chart-bar:hover { background-color: #1A1851; }

        /* --- THE FLAWLESS NATIVE DARK MODE OVERRIDES --- */
        [data-bs-theme="dark"] body, [data-bs-theme="dark"] .content-area { background-color: #121212 !important; color: #e0e0e0 !important; }
        [data-bs-theme="dark"] .bg-light, [data-bs-theme="dark"] .bg-body-secondary, [data-bs-theme="dark"] .bg-body-tertiary { background-color: #2c2c2c !important; color: #e0e0e0 !important; border-color: #444 !important; }
        [data-bs-theme="dark"] .dashboard-card, [data-bs-theme="dark"] .photo-upload-card, [data-bs-theme="dark"] .user-table-wrapper, [data-bs-theme="dark"] .modal-content { background-color: #1e1e1e !important; border-color: #333 !important; color: #e0e0e0 !important; }
        [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select, [data-bs-theme="dark"] textarea { background-color: #2c2c2c !important; color: white !important; border-color: #444 !important; }
        [data-bs-theme="dark"] .btn-navy, [data-bs-theme="dark"] .btn-upload-left { background-color: #82aaff !important; color: #121212 !important; }
        [data-bs-theme="dark"] .btn-navy:hover, [data-bs-theme="dark"] .btn-upload-left:hover { background-color: #6a8ccc !important; color: #121212 !important; opacity: 1 !important; }
        [data-bs-theme="dark"] .text-dark { color: #e0e0e0 !important; }
        [data-bs-theme="dark"] h1, [data-bs-theme="dark"] h2, [data-bs-theme="dark"] h3, [data-bs-theme="dark"] h4, [data-bs-theme="dark"] h5, [data-bs-theme="dark"] h6, [data-bs-theme="dark"] .form-label { color: #ffffff !important; }
        [data-bs-theme="dark"] .welcome-name { color: #82aaff !important; }
        [data-bs-theme="dark"] .dashboard-table-head { border-bottom: 2px solid #555 !important; }
        [data-bs-theme="dark"] .chart-bar { background-color: #3a3f58; }
        [data-bs-theme="dark"] .chart-bar:hover { background-color: #82aaff; }
        [data-bs-theme="dark"] .upload-icon-wrapper { background-color: #2c2c2c !important; }
        [data-bs-theme="dark"] .upload-icon-wrapper i { color: #82aaff !important; }
        [data-bs-theme="dark"] .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

        @media (min-width: 769px) {
            body.sidebar-collapsed .sidebar { width: 75px; }
            body.sidebar-collapsed .content-area { margin-left: 75px; width: calc(100% - 75px); }
            body.sidebar-collapsed .sidebar .menu-text, body.sidebar-collapsed .sidebar .sidebar-logo { display: none; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.mobile-open { transform: translateX(0); }
            .content-area { margin-left: 0; width: 100%; padding: 20px !important; }
        }
        .mobile-toggle-btn { background-color: var(--bs-body-bg); border: 1px solid var(--bs-border-color); color: var(--bs-body-color); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    </style>
</head>

<body>
    <script>
        function applyGlobalTheme(mode) {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = mode === 'dark' || (mode === 'system' && prefersDark);
            document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
        }
        const savedTheme = localStorage.getItem('themeMode') || 'light';
        applyGlobalTheme(savedTheme);

        if (localStorage.getItem('sidebarState') === 'collapsed') {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>

    <div class="d-flex">
        <?php include('includes/sidebar.php'); ?>

        <main class="content-area p-5" id="content-area">
            
            <div class="d-flex align-items-center mb-4 d-md-none">
                <button class="mobile-toggle-btn shadow-sm me-3" onclick="toggleSidebar()">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h3 class="fw-bold m-0 text-dark">Upload Management</h3>
            </div>

            <?php include('includes/upload_photo.php'); ?>

        </main>
    </div> 

    <?php include('includes/upload_user.php'); ?>
    <?php include('includes/upload_section.php'); ?>
    <?php include('includes/settings_modal.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function toggleSidebar() {
            if (window.innerWidth > 768) {
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarState', document.body.classList.contains('sidebar-collapsed') ? 'collapsed' : 'expanded');
            } else {
                document.getElementById('sidebar').classList.toggle('mobile-open');
            }
        }

        // ========================================================
        // --- DYNAMIC JAVASCRIPT DIRECT FROM DATABASE ---
        // ========================================================
        const dbDepartments = <?php echo json_encode($allDepartments ?? []); ?>;
        const dbPrograms = <?php echo json_encode($allPrograms ?? []); ?>;
        const dbSections = <?php echo json_encode($allSections ?? []); ?>; // NEW: Fetch Sections from DB

        const programsData = {};
        const programAbbreviations = {};

        dbPrograms.forEach(p => {
            if (!programsData[p.department_id]) {
                programsData[p.department_id] = [];
            }
            programsData[p.department_id].push({id: p.id, name: p.name});
            programAbbreviations[p.id] = p.abbreviation;
        });

        document.addEventListener('DOMContentLoaded', () => {
            const deptSelects = [
                document.getElementById('photoDeptSelect'), 
                document.getElementById('modalDeptSelect')
            ];
            
            deptSelects.forEach(select => {
                if (select) {
                    select.innerHTML = '<option value="" selected>Select Department</option>';
                    dbDepartments.forEach(d => {
                        let opt = document.createElement('option');
                        opt.value = d.id; 
                        opt.textContent = d.name;
                        select.appendChild(opt);
                    });
                }
            });
        });

        function updatePrograms(deptSelectId, programSelectId) {
            const deptSelect = document.getElementById(deptSelectId);
            const programSelect = document.getElementById(programSelectId);
            const selectedDeptId = deptSelect.value;
            
            programSelect.innerHTML = '<option value="" selected>Select Program</option>';
            
            if (selectedDeptId && programsData[selectedDeptId]) {
                programsData[selectedDeptId].forEach(prog => {
                    let option = document.createElement("option");
                    option.text = prog.name; 
                    option.value = prog.id; // Passing ID instead of text
                    programSelect.add(option);
                });
            }
        }

        function updatePhotoSections() {
            const programSelect = document.getElementById("photoProgramSelect");
            const sectionSelect = document.getElementById("photoSectionSelect");
            const selectedProgramId = programSelect.value;
            
            sectionSelect.innerHTML = '<option value="" selected>Select Section</option>';
            
            if (selectedProgramId) {
                // Filter sections based on selected program ID
                const filteredSections = dbSections.filter(s => s.program_id == selectedProgramId);
                
                if (filteredSections.length > 0) {
                    filteredSections.forEach(sec => {
                        let option = document.createElement("option");
                        option.text = sec.name; 
                        option.value = sec.id; // Pass ID instead of text
                        sectionSelect.add(option);
                    });
                } else {
                    sectionSelect.innerHTML = '<option value="" selected>No sections added yet</option>';
                }
            } else {
                sectionSelect.innerHTML = '<option value="" selected>Select Program first</option>';
            }
        }

        // ========================================================
        // --- ADD SECTION TO DB LOGIC ---
        // ========================================================
        function addNewSection() {
            const dept = document.getElementById("modalDeptSelect").value;
            const progId = document.getElementById("modalProgramSelect").value;
            const secInput = document.getElementById("modalSectionInput").value.trim();

            if(!dept || !progId || !secInput) {
                Swal.fire({ icon: 'warning', title: 'Missing Info', text: 'Please select a Department, Program, and type a Section name.' });
                return;
            }

            let abbr = programAbbreviations[progId] ? programAbbreviations[progId] : "SEC";
            let finalSectionName = abbr + " - " + secInput;

            let formData = new FormData();
            formData.append('program_id', progId);
            formData.append('name', finalSectionName);

            fetch('../../app/controllers/addSectionController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Section Added!', text: finalSectionName + ' is now available.', showConfirmButton: false, timer: 1500 })
                    .then(() => {
                        location.reload(); // Reload to refresh arrays
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Network Error', 'error');
            });
        }
    </script>
</body>
</html>