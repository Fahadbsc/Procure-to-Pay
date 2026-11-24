
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Acme P2P - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 35px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 10px 35px rgba(0,0,0,.08);
        }

        .brand {
            color: #2563eb;
            font-size: 28px;
            font-weight: bold;
        }

        .form-control {
            padding: 12px;
        }

        .login-btn {
            padding: 12px;
            background: #2563eb;
            color: white;
            border: none;
        }

        .login-btn:hover {
            background: #1d4ed8;
            color: white;
        }
    </style>
</head>

<body>

<div class="login-card">

    <div class="text-center mb-4">
        <div class="brand">ACME P2P</div>
        <p class="text-muted">Procure-to-Pay Management System</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">

        @csrf

        <div class="mb-3">
            <label class="form-label">Email Address</label>

            <input type="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email') }}"
                   placeholder="Enter your email"
                   required>
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>

            <input type="password"
                   name="password"
                   class="form-control"
                   placeholder="Enter your password"
                   required>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox"
                   name="remember"
                   value="1"
                   class="form-check-input">

            <label class="form-check-label">
                Remember me
            </label>
        </div>

        <button type="submit"
                class="btn login-btn w-100">
            Login
        </button>

    </form>

    <div class="text-center mt-4">
        <small class="text-muted">
            Acme Industries | Internal ERP
        </small>
    </div>

</div>

</body>
</html>
