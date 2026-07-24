<nav class="navbar navbar-expand-lg shadow-sm" id="top-navbar" style="z-index: 1050;">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
            <i class="bi bi-mortarboard-fill me-2"></i>Student Dashboard
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">

                {{-- Theme Toggle --}}
                <li class="nav-item me-3 d-flex align-items-center">
                    <button class="theme-toggle-btn shadow-sm" id="theme-toggle" title="Toggle Dark/Light Mode">
                        <i class="bi bi-moon-fill" id="theme-icon"></i>
                    </button>
                </li>

                {{-- Notifications Dropdown --}}
                @auth
                @php
                    $unreadNotifications = auth()->user()->unreadNotifications;
                @endphp
                <li class="nav-item dropdown me-3 d-flex align-items-center">
                    <a class="nav-link text-white position-relative theme-toggle-btn shadow-sm" href="#" id="notificationsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="text-decoration: none;">
                        <i class="bi bi-bell-fill"></i>
                        @if($unreadNotifications->count() > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow border border-2 border-white notification-pulse" id="notification-badge" style="font-size: 0.8rem; padding: 0.35em 0.6em; transform: translate(-30%, -30%) !important;">
                                {{ $unreadNotifications->count() > 99 ? '99+' : $unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-0" aria-labelledby="notificationsDropdown" style="width: 380px; max-height: 450px; border-radius: 12px; overflow: hidden;">
                        <li class="p-3 bg-primary text-white d-flex justify-content-between align-items-center" style="border-radius: 12px 12px 0 0;">
                            <h6 class="mb-0 fw-bold">Notifications</h6>
                            @if($unreadNotifications->count() > 0)
                                <button class="btn btn-sm btn-light py-0 px-2 shadow-sm text-primary fw-bold" id="mark-all-read-btn" style="font-size: 0.75rem;">Mark all as read</button>
                            @endif
                        </li>
                        <div class="overflow-auto" style="max-height: 380px;">
                            @forelse(auth()->user()->notifications->take(15) as $notification)
                                <li class="border-bottom p-3 dropdown-item text-wrap position-relative notification-item {{ $notification->read_at ? 'bg-light' : 'bg-white' }}" data-id="{{ $notification->id }}" style="white-space: normal; {{ !$notification->read_at ? 'cursor: pointer;' : '' }}">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="rounded p-2 {{ $notification->data['icon_bg'] ?? 'bg-secondary-subtle text-secondary' }}">
                                            <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }} fs-5"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">{{ $notification->data['title'] ?? 'Notification' }}</h6>
                                            <p class="mb-1 text-muted" style="font-size: 0.8rem; line-height: 1.3;">{{ $notification->data['description'] ?? '' }}</p>
                                            <small class="text-secondary" style="font-size: 0.7rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if(!$notification->read_at)
                                            <span class="position-absolute top-50 end-0 translate-middle-y me-3 p-1 bg-primary border border-light rounded-circle">
                                                <span class="visually-hidden">New alert</span>
                                            </span>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="p-5 text-center text-muted">
                                    <i class="bi bi-bell-slash fs-1 d-block mb-3 opacity-50"></i>
                                    No notifications yet.
                                </li>
                            @endforelse
                        </div>
                    </ul>
                </li>
                @endauth

                {{-- Logged-in user dropdown --}}
                @auth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white d-flex align-items-center gap-2"
                       href="#" id="userDropdown" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="bg-white text-primary rounded-circle d-inline-flex align-items-center justify-content-center fw-bold"
                              style="width:32px;height:32px;font-size:0.85rem;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1" aria-labelledby="userDropdown"
                        style="min-width:200px; border-radius:12px;">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <div class="fw-semibold text-body-emphasis" style="font-size:0.9rem;">{{ auth()->user()->name }}</div>
                                <div class="text-body-secondary" style="font-size:0.78rem;">{{ auth()->user()->email }}</div>
                            </div>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @endauth

            </ul>
        </div>
    </div>
</nav>
