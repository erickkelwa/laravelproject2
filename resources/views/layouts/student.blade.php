<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Portal')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .navbar-brand { font-weight: bold; color: #0d6efd !important; }
        .card { border: none; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 0.5rem; }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('student.dashboard') }}">
                <i class="bi bi-mortarboard-fill me-2"></i>Student Portal
            </a>
            <div class="d-flex align-items-center">
                <span class="me-3 text-muted">Welcome, {{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-5 flex-grow-1">
        <!-- Toast Notifications -->
        <div class="toast-container position-fixed top-0 end-0 p-3 mt-3" style="z-index: 1055;">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-lg border-0" role="alert" style="border-left: 5px solid #198754 !important; animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; min-width: 320px;">
                    <div class="d-flex align-items-center pe-3">
                        <i class="bi bi-check-circle-fill fs-3 me-3 text-success"></i>
                        <div>
                            <h6 class="alert-heading mb-1 fw-bold">Success!</h6>
                            <div class="small">{{ session('success') }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-lg border-0" role="alert" style="border-left: 5px solid #dc3545 !important; animation: slideInRight 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; min-width: 320px;">
                    <div class="d-flex align-items-center pe-3">
                        <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-danger"></i>
                        <div>
                            <h6 class="alert-heading mb-1 fw-bold">Error!</h6>
                            <div class="small">{{ session('error') }}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <footer class="bg-white text-center py-4 border-top mt-auto">
        <div class="container text-muted small">
            &copy; {{ date('Y') }} Student Portal. All rights reserved.
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Auto-dismiss Toasts Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var alerts = document.querySelectorAll('.toast-container .alert');
                alerts.forEach(function(alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 6000);
        });
    </script>
</body>
</html>
