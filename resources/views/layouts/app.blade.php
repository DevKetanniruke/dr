<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - {{ $setting->clinic_name ?? 'CarePlus Clinic' }}</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-teal: #0d9488;
            --brand-teal-dark: #0f766e;
            --brand-teal-light: #f0fdf4;
            --brand-cyan: #06b6d4;
            --brand-indigo: #4f46e5;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
            --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 2px 6px -1px rgba(15, 23, 42, 0.03);
            --card-shadow-hover: 0 12px 28px -4px rgba(15, 23, 42, 0.09), 0 4px 10px -2px rgba(15, 23, 42, 0.04);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--body-bg);
            color: #334155;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 270px;
            background: var(--sidebar-bg);
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
        }

        .sidebar .brand-header {
            padding: 1.25rem 1.5rem;
            background: linear-gradient(180deg, #020617 0%, #0f172a 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 0;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 4px;
        }

        .sidebar .nav-section-title {
            padding: 0.75rem 1.5rem 0.35rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.7rem 1.25rem;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 0.6rem;
            margin: 0.15rem 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .sidebar .nav-link:hover {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.06);
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            color: #ffffff;
            background: linear-gradient(135deg, rgba(13, 148, 136, 0.25) 0%, rgba(6, 182, 212, 0.2) 100%);
            border-left: 3.5px solid var(--brand-teal);
            font-weight: 600;
        }

        .sidebar .nav-link i {
            font-size: 1.15rem;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .sidebar .nav-link:hover i, .sidebar .nav-link.active i {
            color: var(--brand-cyan);
        }

        /* Sidebar Backdrop Overlay for Mobile */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(4px);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .sidebar-backdrop.show {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile Drawer State */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0 !important;
            }
        }

        /* Main Wrapper */
        .main-wrapper {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* Top Header Navigation */
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 0.85rem 2rem;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .content-body {
            padding: 2rem;
            flex: 1;
        }

        /* Premium Card Utilities */
        .card-custom {
            border: 1px solid var(--card-border);
            border-radius: 0.85rem;
            box-shadow: var(--card-shadow);
            background: #ffffff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: var(--card-shadow-hover);
        }

        .stat-card {
            border-radius: 1rem;
            padding: 1.5rem;
            background: #ffffff;
            border: 1px solid var(--card-border);
            box-shadow: var(--card-shadow);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
        }

        .stat-card .stat-icon-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Brand Colors & Gradient Accent Buttons */
        .btn-brand {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            color: #ffffff;
            border: none;
            font-weight: 600;
            border-radius: 0.5rem;
            padding: 0.55rem 1.2rem;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.25);
            transition: all 0.2s ease;
        }

        .btn-brand:hover {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            color: #ffffff;
            box-shadow: 0 6px 16px rgba(13, 148, 136, 0.35);
            transform: translateY(-1px);
        }

        .badge-role {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.3rem 0.65rem;
            border-radius: 2rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-role-admin { background-color: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-role-doctor { background-color: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; }
        .badge-role-receptionist { background-color: #fffbeb; color: #d97706; border: 1px solid #fde68a; }

        /* Custom Scrollbar for Main Content */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Mobile Backdrop Overlay -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar Navigation Drawer -->
    <aside class="sidebar" id="sidebar">
        <!-- Brand Header -->
        <div class="brand-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 bg-gradient bg-teal d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%); width: 42px; height: 42px; shadow: 0 4px 10px rgba(13,148,136,0.3);">
                    <i class="bi bi-hospital-fill text-white fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 text-white fw-bold" style="letter-spacing: -0.01em;">{{ Str::limit($setting->clinic_name ?? 'CarePlus Clinic', 18) }}</h6>
                    <small class="text-info fw-medium" style="font-size: 0.72rem; letter-spacing: 0.04em;">DOCTOR CRM SYSTEM</small>
                </div>
            </div>
            <!-- Mobile Sidebar Close Button -->
            <button class="btn btn-sm text-white-50 d-lg-none p-1 border-0" id="closeSidebar" aria-label="Close sidebar">
                <i class="bi bi-x-lg fs-5"></i>
            </button>
        </div>

        <!-- Scrollable Navigation Items -->
        <div class="sidebar-scroll">
            <div class="nav-section-title">Core Management</div>
            <div class="nav flex-column">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard Overview</span>
                </a>

                <a href="{{ route('patients.index') }}" class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Patients Directory</span>
                </a>

                <a href="{{ route('appointments.index') }}" class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar2-week-fill"></i>
                    <span>Appointments & Queue</span>
                </a>
            </div>

            <div class="nav-section-title mt-3">Clinical Operations</div>
            <div class="nav flex-column">
                <a href="{{ route('visits.index') }}" class="nav-link {{ request()->routeIs('visits.*') ? 'active' : '' }}">
                    <i class="bi bi-clipboard2-pulse-fill"></i>
                    <span>Consultations & Vitals</span>
                </a>

                <a href="{{ route('prescriptions.index') }}" class="nav-link {{ request()->routeIs('prescriptions.*') ? 'active' : '' }}">
                    <i class="bi bi-capsule-pill"></i>
                    <span>Digital Prescriptions</span>
                </a>

                <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff"></i>
                    <span>Billing & Payments</span>
                </a>
            </div>

            @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                <div class="nav-section-title mt-3">Analytics & Intelligence</div>
                <div class="nav flex-column">
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('reports.revenue') }}" class="nav-link {{ request()->routeIs('reports.revenue') ? 'active' : '' }}">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Revenue Reports</span>
                        </a>
                    @endif

                    <a href="{{ route('reports.visits') }}" class="nav-link {{ request()->routeIs('reports.visits') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart-steps"></i>
                        <span>Visit Volume Report</span>
                    </a>
                </div>
            @endif

            @if(Auth::user()->isAdmin())
                <div class="nav-section-title mt-3">Clinic Settings</div>
                <div class="nav flex-column">
                    <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i>
                        <span>Staff User Accounts</span>
                    </a>

                    <a href="{{ route('settings.edit') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="bi bi-sliders2"></i>
                        <span>Clinic Configuration</span>
                    </a>
                </div>
            @endif
        </div>

        <!-- Footer Clinic Info -->
        <div class="p-3 border-top border-secondary border-opacity-25 bg-slate-950 text-white-50 small d-flex align-items-center justify-content-between">
            <div>
                <span class="d-block fw-semibold text-white" style="font-size: 0.75rem;">System Active</span>
                <span style="font-size: 0.68rem;"><i class="bi bi-circle-fill text-success me-1" style="font-size: 0.5rem;"></i> Laravel v{{ Illuminate\Foundation\Application::VERSION }}</span>
            </div>
            <span class="badge bg-secondary bg-opacity-20 text-light border border-secondary border-opacity-25" style="font-size: 0.65rem;">v1.0</span>
        </div>
    </aside>

    <!-- Main Wrapper Content Area -->
    <div class="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <!-- Hamburger Menu Button for Mobile -->
                <button class="btn btn-light border d-lg-none" id="toggleSidebar" aria-label="Toggle Navigation">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="mb-0 fw-bold text-dark tracking-tight">@yield('title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Quick Date Badge -->
                <div class="d-none d-md-flex align-items-center gap-2 bg-light px-3 py-1.5 rounded-pill border text-muted small">
                    <i class="bi bi-calendar3 text-teal"></i>
                    <span>{{ date('D, M j, Y') }}</span>
                </div>

                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 rounded-pill px-3 py-1.5 border shadow-sm" type="button" data-bs-toggle="dropdown">
                        <div class="rounded-circle bg-teal text-white fw-bold d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem; background: linear-gradient(135deg, #0d9488 0%, #0284c7 100%);">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="fw-semibold text-dark small">{{ Auth::user()->name }}</span>
                        <span class="badge badge-role badge-role-{{ Auth::user()->role }}">{{ Auth::user()->role }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2" style="min-width: 220px; border-radius: 0.75rem;">
                        <li class="px-3 py-2 border-bottom">
                            <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                            <div class="text-muted small">{{ Auth::user()->email }}</div>
                            @if(Auth::user()->specialization)
                                <div class="badge bg-light text-secondary border mt-1 font-monospace" style="font-size: 0.7rem;">{{ Auth::user()->specialization }}</div>
                            @endif
                        </li>
                        <li class="mt-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger rounded d-flex align-items-center gap-2 py-2 fw-medium">
                                    <i class="bi bi-box-arrow-right"></i> Sign Out Portal
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            <!-- Flash Alert Notifications -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                    <div>{{ session('success') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                    <div>{{ session('info') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                        <strong class="text-danger">Action required — Please review the input errors below:</strong>
                    </div>
                    <ul class="mb-0 mt-1 ps-4 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Mobile Navigation Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');
            const toggleBtn = document.getElementById('toggleSidebar');
            const closeBtn = document.getElementById('closeSidebar');

            function openSidebar() {
                sidebar?.classList.add('show');
                sidebarBackdrop?.classList.add('show');
                document.body.style.overflow = 'hidden';
            }

            function closeSidebar() {
                sidebar?.classList.remove('show');
                sidebarBackdrop?.classList.remove('show');
                document.body.style.overflow = '';
            }

            toggleBtn?.addEventListener('click', openSidebar);
            closeBtn?.addEventListener('click', closeSidebar);
            sidebarBackdrop?.addEventListener('click', closeSidebar);

            // Automatically close mobile sidebar menu when clicking any nav link
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        closeSidebar();
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    closeSidebar();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
