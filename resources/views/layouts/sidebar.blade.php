<div class="d-flex flex-column flex-shrink-0 p-3 text-white" style="width: 250px; height: calc(100vh - 56px); background-color: #212529; overflow-y: auto;">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item mb-2">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : 'text-white' }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.index', 'students.show') ? 'active' : 'text-white' }}">
                <i class="bi bi-people me-2"></i> Students
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('students.create') }}" class="nav-link {{ request()->routeIs('students.create') ? 'active' : 'text-white' }}">
                <i class="bi bi-person-plus me-2"></i> Add Student
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('fees.index') }}" class="nav-link {{ request()->routeIs('fees.index', 'fees.show') ? 'active' : 'text-white' }}">
                <i class="bi bi-cash-stack me-2"></i> Payments
            </a>
        </li>
        <li class="nav-item mb-2">
            <a href="{{ route('fees.create') }}" class="nav-link {{ request()->routeIs('fees.create', 'fees.edit') ? 'active' : 'text-white' }}">
                <i class="bi bi-cash me-2"></i> Add Payment
            </a>
        </li>
        <!-- Reports Dropdown -->
        <li class="nav-item mb-2">
            <a href="#reportsMenu" data-bs-toggle="collapse" class="nav-link text-white d-flex align-items-center {{ request()->routeIs('reports.*') ? 'active' : '' }}" aria-expanded="{{ request()->routeIs('reports.*') ? 'true' : 'false' }}">
                <i class="bi bi-file-earmark-bar-graph me-2"></i> Financial Reports 
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
            </a>
            <div class="collapse {{ request()->routeIs('reports.*') ? 'show' : '' }}" id="reportsMenu">
                <ul class="nav flex-column ms-3 mt-1 pb-1">
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link text-white opacity-75 {{ request()->routeIs('reports.index') ? 'active opacity-100' : '' }}">
                            <i class="bi bi-grid me-2"></i> Overview Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('reports.term-wise-record') }}" class="nav-link text-white opacity-75 {{ request()->routeIs('reports.term-wise-record') ? 'active opacity-100' : '' }}">
                            <i class="bi bi-grid-3x3-gap-fill me-2"></i> Term-wise Fee Record
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        
        <!-- Academics Dropdown -->
        <li class="nav-item mb-2">
            <a href="#academicsMenu" data-bs-toggle="collapse" class="nav-link text-white d-flex align-items-center" aria-expanded="false">
                <i class="bi bi-mortarboard me-2"></i> Academics 
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
            </a>
            <div class="collapse {{ request()->is('attendance*') || request()->is('exams*') ? 'show' : '' }}" id="academicsMenu">
                <ul class="nav flex-column ms-3 mt-1 pb-1">
                    <li class="nav-item">
                        <a href="{{ route('attendance.index') }}" class="nav-link text-white opacity-75 {{ request()->routeIs('attendance.*') ? 'active opacity-100' : '' }}">
                            <i class="bi bi-calendar-check me-2"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('exams.index') }}" class="nav-link text-white opacity-75 {{ request()->routeIs('exams.*') ? 'active opacity-100' : '' }}">
                            <i class="bi bi-journal-text me-2"></i> Exam Results
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        <!-- Staff Dropdown -->
        <li class="nav-item mb-2">
            <a href="#staffMenu" data-bs-toggle="collapse" class="nav-link text-white d-flex align-items-center" aria-expanded="false">
                <i class="bi bi-person-workspace me-2"></i> Staff Portal 
                <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
            </a>
            <div class="collapse {{ request()->is('staff*') ? 'show' : '' }}" id="staffMenu">
                <ul class="nav flex-column ms-3 mt-1 pb-1">
                    <li class="nav-item">
                        <a href="{{ route('staff.index') }}" class="nav-link text-white opacity-75 {{ request()->routeIs('staff.*') ? 'active opacity-100' : '' }}">
                            <i class="bi bi-people me-2"></i> Manage Staff
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('staff.create') }}" class="nav-link text-white opacity-75 {{ request()->routeIs('staff.create') ? 'active opacity-100' : '' }}">
                            <i class="bi bi-person-plus me-2"></i> Add Staff
                        </a>
                    </li>
                </ul>
            </div>
        </li>
    </ul>
    <hr>
    <div class="small text-center text-secondary">
        &copy; {{ date('Y') }} Student System
    </div>
</div>
