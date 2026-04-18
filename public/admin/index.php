<?php
// 1. Security Check
include_once("../../app/middleware/admin.php");

$adminName = 'Admin';
$db_path = "../../app/config/config.php";

if (file_exists($db_path)) {
    include_once($db_path);

    if (isset($conn)) {
        $admin_id = $_SESSION['user_id'];

        $query = "SELECT username FROM `user` WHERE id = ?";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("i", $admin_id); 
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $adminName = $row['username'];
            }
            $stmt->close();
        }

        // FETCH RECENT USERS DIRECTLY FROM DB 
        $recentUsersQuery = "SELECT `username`, `role`, `dateCreated` FROM `user` ORDER BY `dateCreated` DESC LIMIT 5";
        $recentUsersResult = $conn->query($recentUsersQuery);
        
        $allUsersQuery = "SELECT `username`, `role`, `dateCreated` FROM `user` ORDER BY `dateCreated` DESC LIMIT 50";
        $allUsersResult = $conn->query($allUsersQuery);

        // --- NEW: FETCH RECENT UPLOADED PHOTOS ---
        $recentPhotosQuery = "
            SELECT sp.full_name, d.name as dept_name, p.name as prog_name, s.name as sec_name, sp.latin_honor, sp.class_year, sp.photo_path, sp.uploaded_at
            FROM student_profiles sp
            LEFT JOIN departments d ON sp.department_id = d.id
            LEFT JOIN programs p ON sp.program_id = p.id
            LEFT JOIN sections s ON sp.section_id = s.id
            ORDER BY sp.uploaded_at DESC LIMIT 5
        ";
        $recentPhotosResult = $conn->query($recentPhotosQuery);

        // --- NEW: FETCH LIVE STUDENT COUNTS ---
        // 1. Get total students in the gallery
        $totalStudentsQuery = "SELECT COUNT(id) as total FROM student_profiles";
        $totalResult = $conn->query($totalStudentsQuery);
        $totalStudents = $totalResult->fetch_assoc()['total'] ?? 0;

        // 2. Get students for the default E-Gallery class year (2029)
        $defaultYear = 2029;
        $yearQuery = "SELECT COUNT(id) as total_year FROM student_profiles WHERE class_year = ?";
        if ($stmt = $conn->prepare($yearQuery)) {
            $stmt->bind_param("s", $defaultYear);
            $stmt->execute();
            $yearResult = $stmt->get_result();
            $yearStudents = $yearResult->fetch_assoc()['total_year'] ?? 0;
            $stmt->close();
        }

        // 3. Fetch all available class years to populate the dropdown dynamically
        $yearsQuery = "SELECT DISTINCT class_year FROM student_profiles ORDER BY class_year DESC";
        $yearsResult = $conn->query($yearsQuery);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* --- BASE STYLES & LAYOUT --- */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; overflow-x: hidden; transition: background-color 0.3s, color 0.3s; }
        .content-area { margin-left: 240px; width: calc(100% - 240px); min-height: 100vh; background-color: var(--bs-body-bg); }

        /* --- SIDEBAR --- */
        .sidebar { width: 240px; height: 100vh; background-color: #1A1851; color: white; position: fixed; z-index: 1000; transition: width 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), transform 0.3s ease; overflow-x: hidden; }
        .sidebar-header { display: flex; align-items: center; padding: 15px 15px; height: 70px; }
        .menu-toggle-btn { background: transparent; border: none; color: #a0a0c0; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: 0.2s; cursor: pointer; flex-shrink: 0; }
        .menu-toggle-btn:hover { background-color: rgba(255, 255, 255, 0.1); color: white; }
        .sidebar-logo { font-size: 1.3rem; font-weight: bold; margin-left: 10px; }
        .sidebar .nav-link { color: #a0a0c0; padding: 12px 20px; transition: 0.3s; text-decoration: none; display: flex; align-items: center; white-space: nowrap; }
        .sidebar .nav-link i { font-size: 1.2rem; width: 25px; text-align: center; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255, 255, 255, 0.1); }

        /* --- CARDS & CONTAINERS --- */
        .dashboard-card, .photo-upload-card, .user-table-wrapper { background-color: var(--bs-body-bg); border: 1px solid var(--bs-border-color); border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); padding: 25px; }

        /* --- THE BUTTONS --- */
        .btn-navy { background-color: #1A1851 !important; color: white !important; font-weight: 800; border: none; border-radius: 6px; padding: 8px 24px; transition: 0.3s; }
        .btn-navy:hover { background-color: #2a2775 !important; color: white !important; opacity: 1 !important;}
        .btn-tab { border: 1px solid #1A1851 !important; color: #1A1851 !important; background-color: transparent !important; border-radius: 4px; padding: 6px 16px; font-weight: bold; font-size: 0.85rem; }
        .active-toggle { background-color: #1A1851 !important; color: white !important; border-color: #1A1851 !important; }
        .btn-upload-left { background-color: #1A1851 !important; color: white !important; font-weight: bold; border: none; border-radius: 4px; padding: 6px 16px; font-size: 0.85rem; margin-top: 10px; display: inline-flex; align-items: center; justify-content: center; }

        /* --- TYPOGRAPHY & DASHBOARD --- */
        .welcome-text { color: #6c757d; font-size: 1.1rem; margin-bottom: 30px; }
        .welcome-name { color: #1A1851; font-weight: 800; }
        .dashboard-table-head { border-bottom: 2px solid var(--bs-heading-color); }
        .dashboard-table-head th { border: none !important; padding-bottom: 12px; }

        /* --- THE FLAWLESS NATIVE DARK MODE OVERRIDES --- */
        [data-bs-theme="dark"] body, [data-bs-theme="dark"] .content-area { background-color: #121212 !important; color: #e0e0e0 !important; }
        [data-bs-theme="dark"] .bg-light, [data-bs-theme="dark"] .bg-body-secondary, [data-bs-theme="dark"] .bg-body-tertiary { background-color: #2c2c2c !important; color: #e0e0e0 !important; border-color: #444 !important; }
        [data-bs-theme="dark"] .dashboard-card, [data-bs-theme="dark"] .photo-upload-card, [data-bs-theme="dark"] .user-table-wrapper, [data-bs-theme="dark"] .modal-content, [data-bs-theme="dark"] .settings-sidebar, [data-bs-theme="dark"] .settings-main { background-color: #1e1e1e !important; border-color: #333 !important; color: #e0e0e0 !important; box-shadow: none !important; }
        [data-bs-theme="dark"] .form-control, [data-bs-theme="dark"] .form-select, [data-bs-theme="dark"] textarea { background-color: #2c2c2c !important; color: white !important; border-color: #444 !important; }
        [data-bs-theme="dark"] .btn-navy, [data-bs-theme="dark"] .btn-upload-left { background-color: #82aaff !important; color: #121212 !important; }
        [data-bs-theme="dark"] .btn-navy:hover { background-color: #a0c2ff !important; color: #121212 !important; opacity: 1 !important;}
        [data-bs-theme="dark"] .btn-tab { border-color: #82aaff !important; color: #82aaff !important; background-color: transparent !important; }
        [data-bs-theme="dark"] .active-toggle { background-color: #82aaff !important; color: #121212 !important; border-color: #82aaff !important; }
        [data-bs-theme="dark"] .text-dark { color: #e0e0e0 !important; }
        [data-bs-theme="dark"] h1, [data-bs-theme="dark"] h2, [data-bs-theme="dark"] h3, [data-bs-theme="dark"] h4, [data-bs-theme="dark"] h5, [data-bs-theme="dark"] h6, [data-bs-theme="dark"] .form-label { color: #ffffff !important; }
        [data-bs-theme="dark"] .welcome-name { color: #82aaff !important; }
        [data-bs-theme="dark"] .dashboard-table-head { border-bottom: 2px solid #555 !important; }
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
            
            // If the chart exists, update its colors instantly when switching themes!
            if (typeof window.visitsChart !== 'undefined') {
                window.visitsChart.data.datasets[0].backgroundColor = isDark ? '#82aaff' : '#1A1851';
                window.visitsChart.options.scales.x.ticks.color = isDark ? '#a0a0c0' : '#6c757d';
                window.visitsChart.options.scales.y.ticks.color = isDark ? '#a0a0c0' : '#6c757d';
                window.visitsChart.update();
            }
        }
        const savedTheme = localStorage.getItem('themeMode') || 'light';
        applyGlobalTheme(savedTheme);

        if (localStorage.getItem('sidebarState') === 'collapsed') {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>

    <div class="d-flex">
        <?php include('includes/sidebar.php'); ?>
        <?php include('includes/upload_user.php'); ?>
        <?php include('includes/upload_section.php'); ?>

        <main class="content-area p-4 p-lg-5" id="content-area">

            <div class="d-flex align-items-center mb-4 d-md-none">
                <button class="mobile-toggle-btn shadow-sm me-3" onclick="toggleSidebar()"><i class="bi bi-list fs-4"></i></button>
                <h3 class="fw-bold m-0">Dashboard</h3>
            </div>

            <h2 class="fw-bold mb-1 d-none d-md-block text-dark">Dashboard</h2>

            <div class="welcome-text d-none d-md-block mb-4">
                Welcome back, <span class="welcome-name"><?php echo htmlspecialchars($adminName); ?></span>
            </div>

            <div class="row g-4 align-items-stretch">

                <div class="col-lg-5">
                    <div class="dashboard-card h-100 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <h6 class="fw-bold m-0 text-dark">User Visit<br><small class="text-muted fw-normal" id="chartFilterLabel" style="font-size: 11px;">Monthly</small></h6>

                            <div class="dropdown">
                                <button class="btn btn-sm border fw-bold dropdown-toggle text-dark" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-funnel"></i> Filter
                                </button>
                                <ul class="dropdown-menu shadow">
                                    <li><a class="dropdown-item small chart-filter-btn" href="#" onclick="updateChart('daily', this)">Daily</a></li>
                                    <li><a class="dropdown-item small chart-filter-btn" href="#" onclick="updateChart('weekly', this)">Weekly</a></li>
                                    <li><a class="dropdown-item small chart-filter-btn active" href="#" onclick="updateChart('monthly', this)">Monthly</a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex-grow-1 position-relative" style="min-height: 200px;">
                            <canvas id="visitsChart"></canvas>
                        </div>

                    </div>
                </div>

                <div class="col-lg-3 d-flex flex-column gap-4">
                    <div class="dashboard-card flex-fill text-center py-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold m-0 text-dark" style="font-size: 0.8rem;">Students Every Class Year</h6>
                            <select class="form-select form-select-sm w-auto fw-bold py-1" style="font-size: 0.75rem;" onchange="updateYearCount(this.value)">
                                <?php 
                                if (isset($yearsResult) && $yearsResult->num_rows > 0) {
                                    while ($yearRow = $yearsResult->fetch_assoc()) {
                                        $y = $yearRow['class_year'];
                                        $selected = ($y == $defaultYear) ? 'selected' : '';
                                        echo "<option value='" . htmlspecialchars($y) . "' $selected>" . htmlspecialchars($y) . "</option>";
                                    }
                                } else {
                                    echo "<option value='2029' selected>2029</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <h2 class="fw-bold m-0 text-dark" id="liveClassYearCount"><?php echo number_format($yearStudents); ?></h2>
                        <small class="text-muted" style="font-size: 0.75rem;">Total</small>
                    </div>

                    <div class="dashboard-card flex-fill text-center py-4">
                        <h6 class="fw-bold text-start mb-3 text-dark" style="font-size: 0.8rem;">Students</h6>
                        <h2 class="fw-bold m-0 text-dark" id="liveTotalCount"><?php echo number_format($totalStudents); ?></h2>
                        <small class="text-muted" style="font-size: 0.75rem;">Total</small>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="dashboard-card h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold m-0 text-dark">New user added</h6>
                            <a href="#" class="fw-bold text-decoration-none text-body" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#viewAllUsersModal">View all <i class="bi bi-chevron-double-right"></i></a>
                        </div>
                        <table class="table table-borderless table-sm m-0">
                            <thead class="dashboard-table-head">
                                <tr>
                                    <th class="fw-bold pb-2 text-dark" style="font-size: 0.8rem;">Username</th>
                                    <th class="fw-bold pb-2 text-dark" style="font-size: 0.8rem;">Role</th>
                                    <th class="fw-bold text-end pb-2 text-dark" style="font-size: 0.8rem;">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($recentUsersResult) && $recentUsersResult->num_rows > 0) {
                                    while ($userRow = $recentUsersResult->fetch_assoc()) {
                                        $dateAdded = date("n-j-Y", strtotime($userRow['dateCreated']));
                                        $roleBadge = $userRow['role'] === 'admin' 
                                            ? '<span class="badge bg-danger">Admin</span>' 
                                            : '<span class="badge bg-primary">Student</span>';
                                            
                                        echo "<tr>";
                                        echo "<td class='fw-bold pt-3 text-dark' style='font-size: 0.85rem;'>" . htmlspecialchars($userRow['username']) . "</td>";
                                        echo "<td class='pt-3'>" . $roleBadge . "</td>";
                                        echo "<td class='text-end pt-3 fw-bold text-dark' style='font-size: 0.85rem;'>" . $dateAdded . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center text-muted pt-3 small'>No new users found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="dashboard-card mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold m-0 fs-5 text-dark">New add photo in gallery</h6>
                    <button class="btn btn-outline-secondary btn-sm fw-bold px-3"><i class="bi bi-funnel"></i> Filter</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-borderless align-middle m-0" id="dashboardStudentTable">
                        <thead class="dashboard-table-head">
                            <tr>
                                <th class="fw-bold text-dark">Name</th>
                                <th class="fw-bold text-dark">Department</th>
                                <th class="fw-bold text-dark">Program</th>
                                <th class="fw-bold text-dark">Section</th>
                                <th class="fw-bold text-dark">Latin Honor</th>
                                <th class="fw-bold text-dark">Class year</th>
                                <th class="fw-bold text-dark">Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (isset($recentPhotosResult) && $recentPhotosResult->num_rows > 0) {
                                while ($photoRow = $recentPhotosResult->fetch_assoc()) {
                                    $dateUploaded = date("m-d-Y", strtotime($photoRow['uploaded_at']));
                                    // Make sure path is correct relative to the dashboard
                                    $imgSrc = $photoRow['photo_path'];
                                    
                                    // Handle empty Latin Honors
                                    $latin = !empty($photoRow['latin_honor']) && $photoRow['latin_honor'] !== 'None' 
                                             ? $photoRow['latin_honor'] 
                                             : '<span class="text-muted">None</span>';

                                    echo "<tr>";
                                    echo "<td class='py-3'>";
                                    echo "    <div class='d-flex align-items-center fw-bold text-dark' style='font-size: 0.85rem;'>";
                                    echo "        <img src='" . htmlspecialchars($imgSrc) . "' class='rounded-circle me-3 border' style='width: 35px; height: 35px; object-fit: cover;' alt='Profile'>";
                                    echo "        " . htmlspecialchars($photoRow['full_name']);
                                    echo "    </div>";
                                    echo "</td>";
                                    echo "<td class='fw-bold text-dark' style='font-size: 0.8rem;'>" . htmlspecialchars($photoRow['dept_name']) . "</td>";
                                    echo "<td class='fw-bold text-dark' style='font-size: 0.8rem;'>" . htmlspecialchars($photoRow['prog_name']) . "</td>";
                                    echo "<td class='fw-bold text-dark' style='font-size: 0.8rem;'>" . htmlspecialchars($photoRow['sec_name']) . "</td>";
                                    echo "<td class='fw-bold text-dark' style='font-size: 0.8rem;'>" . $latin . "</td>";
                                    echo "<td class='fw-bold text-dark' style='font-size: 0.8rem;'>" . htmlspecialchars($photoRow['class_year']) . "</td>";
                                    echo "<td class='fw-bold text-dark' style='font-size: 0.8rem;'>" . $dateUploaded . "</td>";
                                    echo "<td class='text-end'><button class='btn btn-sm fs-5 text-body'><i class='bi bi-three-dots'></i></button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center py-4 text-muted'>No photos uploaded yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <div class="modal fade" id="viewAllUsersModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" style="border-radius: 12px; background-color: var(--bs-body-bg);">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="fw-bold m-0 text-dark">All Recent Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover align-middle">
                            <thead class="sticky-top" style="background-color: var(--bs-body-bg);">
                                <tr>
                                    <th class="fw-bold text-dark border-bottom border-dark">Username</th>
                                    <th class="fw-bold text-dark border-bottom border-dark">Role</th>
                                    <th class="fw-bold text-end text-dark border-bottom border-dark">Date Added</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($allUsersResult) && $allUsersResult->num_rows > 0) {
                                    while ($allUserRow = $allUsersResult->fetch_assoc()) {
                                        $dateAddedAll = date("M j, Y, g:i A", strtotime($allUserRow['dateCreated']));
                                        $roleBadge = $allUserRow['role'] === 'admin' 
                                            ? '<span class="badge bg-danger">Admin</span>' 
                                            : '<span class="badge bg-primary">Student</span>';
                                        
                                        echo "<tr>";
                                        echo "<td class='fw-bold text-dark'>" . htmlspecialchars($allUserRow['username']) . "</td>";
                                        echo "<td>" . $roleBadge . "</td>";
                                        echo "<td class='text-end text-muted small fw-bold'>" . $dateAddedAll . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center text-muted py-4'>No users found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

        // --- LIVE CHART.JS LOGIC ---
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('visitsChart').getContext('2d');
            const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
            
            // Initialize Chart
            window.visitsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Visits',
                        data: [],
                        backgroundColor: isDark ? '#82aaff' : '#1A1851',
                        borderRadius: 4,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { display: false },
                            ticks: { color: isDark ? '#a0a0c0' : '#6c757d' }
                        },
                        x: { 
                            grid: { display: false },
                            ticks: { color: isDark ? '#a0a0c0' : '#6c757d' }
                        }
                    }
                }
            });

            // Load default (Monthly)
            updateChart('monthly', document.querySelector('.chart-filter-btn.active'));
        });

        // Function to fetch data and update chart
        function updateChart(filterType, element) {
            // Update UI Active State
            if (element) {
                document.querySelectorAll('.chart-filter-btn').forEach(btn => btn.classList.remove('active'));
                element.classList.add('active');
                
                // Capitalize first letter for the label
                document.getElementById('chartFilterLabel').innerText = filterType.charAt(0).toUpperCase() + filterType.slice(1);
            }

            // Fetch the live data
            fetch(`../../app/controllers/getVisitsData.php?filter=${filterType}`)
                .then(response => response.json())
                .then(data => {
                    window.visitsChart.data.labels = data.labels;
                    window.visitsChart.data.datasets[0].data = data.data;
                    window.visitsChart.update();
                })
                .catch(error => console.error("Error fetching chart data:", error));
        }

        // Function to fetch live student count when dropdown changes
        function updateYearCount(year) {
            fetch(`../../app/controllers/getStudentYearCount.php?year=${year}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Formats the number with commas and updates the HTML
                        document.getElementById('liveClassYearCount').innerText = parseInt(data.count).toLocaleString();
                    }
                })
                .catch(error => console.error("Error fetching year count:", error));
        }
    </script>
</body>

</html>