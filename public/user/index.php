<?php 
include_once("../../app/middleware/user.php");
// Ensure Database connection is available for the user side
include_once("../../app/config/config.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Gallery | Home</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        /* Global Variables & Layout */
        :root {
            --navy-dark: #1A1851;
            --navy-light: #252266;
            --text-gray: #a0a0c0;
        }
        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main { flex: 1 0 auto; }
        .bg-navy { background-color: #1A1851 !important; }
        
        /* Shared 5-Column Grid */
        @media (min-width: 768px) { .col-md-2-4 { flex: 0 0 auto; width: 20%; } }

        /* Shared Animations */
        .fade-in-up { animation: fadeInUp 0.6s ease-out forwards; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Shared Premium Back Button */
        .btn-back {
            background-color: white; border: 1px solid #dee2e6; color: var(--navy-dark);
            width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .btn-back i { font-size: 1.2rem; line-height: 1; }
        .btn-back:hover { background-color: var(--navy-dark); color: white; border-color: var(--navy-dark); transform: translateX(-3px); }

        /* Shared Student Profile Image */
        .honor-profile img {
            width: 100%; aspect-ratio: 1 / 1; object-fit: cover; object-position: top center; 
            border-radius: 8px; transition: 0.3s ease; border: 1px solid #ddd;
        }
        .honor-profile small { font-size: 0.65rem; line-height: 1.4; color: #000; display: block; margin-top: 8px; }

        /* Shared Pagination */
        .pagination .page-link { border: 1px solid #dee2e6; margin: 0 2px; border-radius: 4px !important; }
        .pagination .page-item.active .page-link { background-color: #fff; border-color: #dee2e6; color: #000; }
        .custom-pagination .page-item { margin: 0 4px; }
        .custom-pagination .page-link {
            width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;
            border-radius: 6px !important; font-size: 0.85rem; font-weight: 600;
            color: var(--navy-dark); border: 1px solid #dee2e6; background-color: white; transition: all 0.2s ease;
        }
        .custom-pagination .page-item.active .page-link {
            background-color: var(--navy-dark) !important; border-color: var(--navy-dark) !important; color: white !important;
        }
        .custom-pagination .prev-next { background-color: #f8f9fa !important; color: var(--navy-dark) !important; border: 1px solid #dee2e6 !important; font-size: 0.9rem; }
        .custom-pagination .prev-next:hover { background-color: var(--navy-dark) !important; color: white !important; border-color: var(--navy-dark) !important; }
        .custom-pagination .page-link i { line-height: 1; display: flex; align-items: center; }

        /* Scroll to Top Button Styling */
        #scrollTopBtn {
            position: fixed; bottom: 40px; right: 40px; z-index: 99; width: 55px; height: 55px; border-radius: 50%;
            background-color: var(--navy-dark); color: white; border: none; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            font-size: 1.5rem; display: flex; align-items: center; justify-content: center;
            cursor: pointer; opacity: 0; visibility: hidden; transition: all 0.3s ease;
        }
        #scrollTopBtn:hover { background-color: #ffb11f; color: var(--navy-dark); transform: translateY(-5px); }
        #scrollTopBtn.show { opacity: 1; visibility: visible; }
    </style>
</head>
<body>

    <?php include('includes/navbar.php'); ?>
    <?php include('includes/hero.php'); ?>

    <main class="container py-5">
        <?php include('includes/home.php'); ?>
        <?php include('includes/latin_honor.php'); ?>
        
        <?php include('includes/departments.php'); ?>
        <?php include('includes/section_view.php'); ?>
        <?php include('includes/student_grid.php'); ?>
    </main>

    <?php include('includes/footer.php'); ?>
    
    <?php include('includes/modals.php'); ?>

    <button id="scrollTopBtn" onclick="scrollToTop()" title="Go to top">
        <i class="bi bi-arrow-up-short"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function showSection(sectionId, navElement) {
            const sections = ['home', 'latin-honor', 'departments', 'section-view', 'student-grid-view'];
            sections.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = 'none';
            });

            const target = document.getElementById(sectionId);
            if (target) target.style.display = 'block';

            document.querySelectorAll('.nav-link').forEach(link => link.classList.remove('active'));
            if (navElement) navElement.classList.add('active');
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function confirmUserLogout(event) {
            event.preventDefault(); 
            Swal.fire({
                title: 'Ready to leave?', text: "You will be logged out of E-Gallery.", icon: 'question',
                showCancelButton: true, confirmButtonColor: '#ff4d4d', cancelButtonColor: '#1A1851', confirmButtonText: 'Yes, log me out!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "../../app/controllers/logoutController.php";
                }
            });
        }

        const scrollTopBtn = document.getElementById("scrollTopBtn");
        window.onscroll = function() {
            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                scrollTopBtn.classList.add("show");
            } else {
                scrollTopBtn.classList.remove("show");
            }
        };

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>