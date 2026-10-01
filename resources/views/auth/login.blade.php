<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - CarePlus Doctor CRM</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: radial-gradient(circle at 10% 20%, #0f172a 0%, #1e293b 90%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Ambient Glow Circles */
        body::before {
            content: '';
            position: absolute;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(13, 148, 136, 0.25) 0%, rgba(0,0,0,0) 70%);
            top: -100px;
            left: -100px;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.2) 0%, rgba(0,0,0,0) 70%);
            bottom: -150px;
            right: -150px;
            z-index: 0;
        }

        .login-card {
            background: #ffffff;
            border-radius: 1.25rem;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), 0 0 30px rgba(13, 148, 136, 0.15);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }

        .login-header {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            padding: 2.5rem 2rem;
            text-align: center;
            color: #ffffff;
            position: relative;
        }

        .login-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-top: 10px solid #0f766e;
        }

        .login-body {
            padding: 2.25rem 2rem;
        }

        .form-control {
            border-radius: 0.6rem;
            padding: 0.65rem 0.85rem;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 0.25rem rgba(13, 148, 136, 0.2);
        }

        .btn-teal {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 0.6rem;
            border: none;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3);
            transition: all 0.2s ease;
        }

        .btn-teal:hover {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(13, 148, 136, 0.4);
            transform: translateY(-1px);
        }

        .demo-card {
            background: #f8fafc;
            border-radius: 0.75rem;
            padding: 1rem;
            border: 1px solid #e2e8f0;
        }

        .demo-btn {
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.45rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        .demo-btn:hover {
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3 shadow" style="width: 68px; height: 68px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px);">
            <i class="bi bi-hospital-fill fs-1 text-white"></i>
        </div>
        <h4 class="fw-bold mb-1 tracking-tight">CarePlus Doctor CRM</h4>
        <p class="text-white-50 mb-0 small">Clinic & Patient Management System</p>
    </div>

    <div class="login-body">
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show small rounded-3" role="alert">
                <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="loginForm">
            @csrf
            
            <div class="mb-3">
                <label for="email" class="form-label fw-medium text-secondary small text-uppercase">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" class="form-control border-start-0" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="doctor@clinic.com">
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-medium text-secondary small text-uppercase">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" class="form-control border-start-0" id="password" name="password" required placeholder="••••••••">
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small text-muted" for="remember">
                        Keep me signed in
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-teal w-100 mb-4">
                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to Account
            </button>
        </form>

        <!-- Quick Demo Accounts Switcher -->
        <div class="demo-card">
            <div class="text-uppercase text-muted fw-bold mb-2 d-flex align-items-center justify-content-between" style="font-size: 0.68rem; letter-spacing: 0.05em;">
                <span><i class="bi bi-lightning-charge-fill text-warning me-1"></i> 1-Click Demo Login</span>
                <span class="badge bg-secondary bg-opacity-10 text-secondary">Click Role</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-danger demo-btn flex-fill" onclick="setDemo('admin@clinic.com', 'password')">
                    <i class="bi bi-shield-lock-fill me-1"></i> Admin
                </button>
                <button type="button" class="btn btn-outline-primary demo-btn flex-fill" onclick="setDemo('doctor@clinic.com', 'password')">
                    <i class="bi bi-person-badge-fill me-1"></i> Doctor
                </button>
                <button type="button" class="btn btn-outline-warning demo-btn flex-fill text-dark" onclick="setDemo('receptionist@clinic.com', 'password')">
                    <i class="bi bi-headset me-1"></i> Receptionist
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function setDemo(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
        document.getElementById('loginForm').submit();
    }
</script>

</body>
</html>
