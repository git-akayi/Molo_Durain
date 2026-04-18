<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="sidebar d-flex flex-column justify-content-between" id="sidebar">
    <div>
        <div class="sidebar-header">
            <button class="menu-toggle-btn" onclick="toggleSidebar()">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="sidebar-logo text-nowrap">E-Gallery</div>
        </div>

        <small class="text-secondary px-4 mt-2 d-block fw-bold menu-label" style="font-size: 10px;">Main menu</small>
        
        <ul class="nav flex-column mt-2">
            <li class="nav-item">
                <a href="index.php" class="nav-link <?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                    <i class="bi bi-grid-fill me-3"></i> <span class="menu-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="upload.php" class="nav-link <?php echo ($currentPage == 'upload.php') ? 'active' : ''; ?>">
                    <i class="bi bi-box-arrow-up me-3"></i> <span class="menu-text">Uploads</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#userModal">
                    <i class="bi bi-person-fill-add me-3"></i> <span class="menu-text">Add Users</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#sectionModal">
                    <i class="bi bi-folder-plus me-3"></i> <span class="menu-text">Add Section</span>
                </a>
            </li>
        </ul>
    </div>
    
    <ul class="nav flex-column mb-4">
        <li class="nav-item">
            <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#settingsModal">
                <i class="bi bi-gear me-3"></i> <span class="menu-text">Settings</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="../../app/controllers/logoutController.php" class="nav-link">
                <i class="bi bi-box-arrow-right me-3"></i> <span class="menu-text">Logout</span>
            </a>
        </li>
    </ul>
</nav>