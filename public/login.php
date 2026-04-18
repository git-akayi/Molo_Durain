<?php
session_start();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Gallery Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <style>
        body {
            background-color: #fcfcfc !important; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            text-align: center;
            padding: 20px;
        }

        .logo-img {
            width: 300px; 
            max-width: 100%; 
            object-fit: contain;
            margin-bottom: 25px;
        }

        .welcome-text {
            font-weight: 800;
            color: #000;
            font-size: 1.4rem;
            margin-bottom: 8px;
        }

        .sub-text {
            color: #999;
            font-size: 0.85rem;
            margin-bottom: 30px;
        }

        .login-card {
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 12px;
            border: 1px solid #f0f0f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            text-align: left; /* Keep the inputs left-aligned inside the card */
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 6px;
        }

        .form-control {
            height: 48px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.9rem;
            color: #555;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: #ffb11f;
            box-shadow: 0 0 0 0.25rem rgba(255, 177, 31, 0.25);
        }

        .form-control::placeholder {
            color: #bbb;
        }

        /* Custom Checkbox */
        .form-check-input {
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: #ffb11f;
            border-color: #ffb11f;
        }
        .form-check-label {
            font-size: 0.85rem;
            color: #777;
            cursor: pointer;
            margin-top: 2px;
        }

        .btn-login {
            background-color: #ffb11f !important;
            color: white !important;
            border-radius: 8px;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            height: 50px;
            transition: background-color 0.2s;
        }

        .btn-login:hover {
            background-color: #e69d12 !important;
        }
    </style>
</head>

<body>

    <div class="login-container">
        
        <img src="../public/user/assets/Img/Logo/USTP-Web-Logo.webp" alt="USTP Logo" class="logo-img">
        <h2 class="welcome-text">Welcome back to E-Gallery</h2>
        <p class="sub-text">Enter your username and password to continue.</p>

        <div class="login-card">
            <form id="loginForm" action="../app/controllers/loginController.php" method="POST" autocomplete="off" novalidate>
                
                <div class="mb-4">
                    <label class="form-label" for="username">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
                </div>

                <div class="mb-4">
                    <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                </div>

                <div class="mb-4 form-check d-flex align-items-center">
                    <input type="checkbox" class="form-check-input me-2" id="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>

                <button type="submit" class="btn btn-login w-100 mt-2" name="login">Sign in</button>
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Form Validation Script
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (username === '' || password === '') {
                event.preventDefault(); 
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Information',
                    text: 'Please enter both your Username and Password.',
                    confirmButtonColor: '#ffb11f',
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'px-4 py-2 fw-bold rounded-3'
                    }
                });
            }
        });
    </script>

    <?php if (isset($_SESSION['error'])): ?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: '<?php echo addslashes($_SESSION['error']); ?>',
                confirmButtonColor: '#ffb11f',
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'px-4 py-2 fw-bold rounded-3'
                }
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

</body>
</html>