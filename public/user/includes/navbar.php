<style>
    .dashboard-nav {
        background-color: var(--navy-dark);
        height: 70px;
        display: flex;
        align-items: center;
    }

    .bg-navy-pill {
        background-color: var(--navy-light);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
    }

    .bg-navy-pill .nav-link {
        color: white;
        font-size: 0.8rem;
        font-weight: bold;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .bg-navy-pill .nav-link.active {
        background-color: white;
        color: var(--navy-dark) !important;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark dashboard-nav px-4 position-relative">
    <div class="container-fluid d-flex justify-content-center">
        <a class="navbar-brand fw-bold position-absolute start-0 ms-4" href="#">E-Gallery</a>

        <div class="navbar-nav bg-navy-pill p-1">
            <a class="nav-link active px-4" href="#" onclick="showSection('home', this)">Home</a>
            <a class="nav-link px-4" href="#" onclick="showSection('latin-honor', this)">Latin Honor</a>
            <a class="nav-link px-4" href="#" onclick="showSection('departments', this)">Department</a>
            <a class="nav-link px-4 text-danger fw-bold" href="#" onclick="confirmUserLogout(event)">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>