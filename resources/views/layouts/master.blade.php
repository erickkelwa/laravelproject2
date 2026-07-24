<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Dashboard') | SMS</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            background-color: var(--bs-body-bg); /* Use Bootstrap variable for theme support */
            font-family: 'Outfit', 'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.01em;
        }
        
        .main-content {
            height: calc(100vh - 56px); /* Subtract header height */
            overflow-y: auto;
        }
        
        /* Modern Card Styling */
        .card {
            border: 1px solid rgba(0,0,0,0.03);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            border-radius: 1rem;
            transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(13, 110, 253, 0.08) !important;
            z-index: 10;
        }
        
        [data-bs-theme="dark"] .card {
            border-color: rgba(255,255,255,0.05);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        [data-bs-theme="dark"] .card:hover {
            box-shadow: 0 15px 35px rgba(255, 255, 255, 0.08) !important;
        }

        /* Elite Button Styling */
        .btn {
            border-radius: 0.6rem;
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        
        /* Animations */
        .fade-in {
            animation: fadeIn 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        }
        
        /* Navbar Theme */
        #top-navbar {
            background-color: #0d6efd; /* Primary blue for light mode */
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        
        #top-navbar .navbar-brand,
        #top-navbar .nav-link,
        #top-navbar .navbar-toggler-icon,
        #top-navbar .navbar-brand i {
            color: #ffffff !important;
        }

        [data-bs-theme="dark"] #top-navbar {
            background-color: #1a1d20; /* Darker header for dark mode */
            border-bottom: 1px solid #2b3035;
        }

        /* Theme Toggle Button */
        .theme-toggle-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            color: #ffffff;
            overflow: hidden;
            position: relative;
        }

        .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .theme-toggle-btn i {
            font-size: 1.1rem;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
        }

        [data-bs-theme="dark"] .theme-toggle-btn {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: #ffd43b; /* Warm sun color */
        }
        
        [data-bs-theme="dark"] .theme-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 12px rgba(255, 212, 59, 0.1);
        }

        /* Animation class for the icon */
        .theme-icon-animate {
            animation: popSpin 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes popSpin {
            0% { transform: scale(0.5) rotate(-90deg); opacity: 0; }
            50% { transform: scale(1.2) rotate(10deg); }
            100% { transform: scale(1) rotate(0); opacity: 1; }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .notification-pulse {
            animation: pulse-red 2s infinite;
        }

        @keyframes pulse-red {
            0% { transform: translate(-30%, -30%) scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { transform: translate(-30%, -30%) scale(1.05); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { transform: translate(-30%, -30%) scale(0.95); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
        
        .animation-ding {
            display: inline-block;
            animation: ding 1s ease 1;
            transform-origin: top center;
        }
        
        @keyframes ding {
            0% { transform: rotate(0); }
            20% { transform: rotate(15deg); }
            40% { transform: rotate(-10deg); }
            60% { transform: rotate(5deg); }
            80% { transform: rotate(-3deg); }
            100% { transform: rotate(0); }
        }
    </style>
    @stack('styles')
    
    <!-- Theme Initialization (prevents FOUC) -->
    <script>
        const getStoredTheme = () => localStorage.getItem('theme');
        const getPreferredTheme = () => {
            const storedTheme = getStoredTheme();
            if (storedTheme) {
                return storedTheme;
            }
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };
        const setTheme = theme => {
            if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-bs-theme', theme);
            }
        };
        setTheme(getPreferredTheme());
    </script>
</head>
<body class="d-flex flex-column vh-100">

    <!-- Header -->
    @include('layouts.header')

    <div class="d-flex flex-grow-1">
        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content -->
        <main class="flex-grow-1 p-4 main-content fade-in d-flex flex-column">
            
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

            <!-- Content Area -->
            <div class="flex-grow-1">
                @yield('content')
            </div>

            <!-- Footer -->
            @include('layouts.footer')
        </main>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Delete Confirmation Script -->
    <script>
        function confirmDelete(event) {
            if (!confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
                event.preventDefault();
                return false;
            }
            return true;
        }
    </script>

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

    <!-- Mark Notifications as Read Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const markReadBtn = document.getElementById('mark-all-read-btn');
            if(markReadBtn) {
                markReadBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    fetch('{{ route("notifications.mark-as-read") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(res => res.json()).then(data => {
                        if(data.success) {
                            // Hide badge
                            const badge = document.getElementById('notification-badge');
                            if(badge) badge.remove();
                            // Hide mark all read button
                            markReadBtn.remove();
                            // Remove blue dots
                            document.querySelectorAll('.dropdown-menu .rounded-circle.bg-primary.border-light').forEach(dot => dot.remove());
                            // Change background of unread items
                            document.querySelectorAll('.dropdown-menu .bg-white').forEach(item => {
                                item.classList.remove('bg-white');
                                item.classList.add('bg-light');
                                item.style.cursor = 'default';
                            });
                        }
                    });
                });
            }

            // Single notification click
            document.querySelectorAll('.notification-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    if (this.classList.contains('bg-white')) { // If unread
                        e.preventDefault();
                        e.stopPropagation();
                        const id = this.getAttribute('data-id');
                        fetch(`/notifications/${id}/mark-as-read`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        }).then(res => res.json()).then(data => {
                            if(data.success) {
                                // Mark this item as read visually
                                this.classList.remove('bg-white');
                                this.classList.add('bg-light');
                                this.style.cursor = 'default';
                                const dot = this.querySelector('.rounded-circle.bg-primary.border-light');
                                if (dot) dot.remove();

                                // Update badge count
                                const badge = document.getElementById('notification-badge');
                                if(badge) {
                                    let currentCount = parseInt(badge.innerText);
                                    if (!isNaN(currentCount) && currentCount > 1) {
                                        badge.innerText = currentCount - 1;
                                    } else {
                                        badge.remove();
                                        if (markReadBtn) markReadBtn.remove();
                                    }
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>

    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');

            if (themeToggleBtn && themeIcon) {
                const updateIcon = (theme, animate = false) => {
                    if (animate) {
                        themeIcon.classList.remove('theme-icon-animate');
                        // Trigger reflow to restart animation
                        void themeIcon.offsetWidth;
                        themeIcon.classList.add('theme-icon-animate');
                    }

                    if (theme === 'dark') {
                        themeIcon.classList.remove('bi-moon-fill');
                        themeIcon.classList.add('bi-sun-fill');
                    } else {
                        themeIcon.classList.remove('bi-sun-fill');
                        themeIcon.classList.add('bi-moon-fill');
                    }
                };

                const storedTheme = getStoredTheme() || getPreferredTheme();
                updateIcon(storedTheme, false);

                themeToggleBtn.addEventListener('click', () => {
                    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                    
                    setTheme(newTheme);
                    localStorage.setItem('theme', newTheme);
                    updateIcon(newTheme, true);
                });
            }
        });
    </script>
    <!-- Unread Notifications Toast -->
    @auth
        @php
            $unreadCount = auth()->user()->unreadNotifications->count();
        @endphp
        @if($unreadCount > 0)
            <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
                <div id="unreadToast" class="toast align-items-center text-bg-primary border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body fw-bold">
                            <i class="bi bi-bell-fill me-2 fs-5 animation-ding"></i>
                            You have {{ $unreadCount }} new notification{{ $unreadCount > 1 ? 's' : '' }}!
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const unreadToastEl = document.getElementById('unreadToast');
                    if (unreadToastEl) {
                        const toast = new bootstrap.Toast(unreadToastEl, { delay: 6000 });
                        toast.show();
                    }
                });
            </script>
        @endif
    @endauth
    
    @stack('scripts')
</body>
</html>
