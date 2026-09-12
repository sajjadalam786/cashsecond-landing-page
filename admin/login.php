<?php
require_once __DIR__ . '/auth.php';

$error = '';
$redirect = $_GET['redirect'] ?? 'index.php';

if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    
    if ($password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_user'] = 'CashSecond Admin';
        header('Location: ' . (filter_var($redirect, FILTER_SANITIZE_URL) ?: 'index.php'));
        exit;
    } else {
        $error = 'Incorrect password. Please enter the valid admin key.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Admin Login — CashSecond Pricing Desk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        body {
            background: #0B0D10;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            color: #FFFFFF;
        }
        .login-card {
            background: #15181E;
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            padding: 36px 28px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
            position: relative;
        }
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #0071E3, #25D366);
            border-radius: 20px 20px 0 0;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 113, 227, 0.15);
            color: #2997FF;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.06em;
            padding: 6px 14px;
            border-radius: 20px;
            margin-bottom: 18px;
            text-transform: uppercase;
            border: 1px solid rgba(0, 113, 227, 0.3);
        }
        .login-title {
            font-size: 22px;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 6px;
        }
        .login-subtitle {
            font-size: 13.5px;
            color: #8E8E93;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .form-group {
            text-align: left;
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: #E5E5EA;
            margin-bottom: 8px;
        }
        .form-input {
            width: 100%;
            padding: 14px 16px;
            font-size: 16px; /* 16px prevents mobile iOS auto-zoom */
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            background: #0B0D10;
            color: #FFFFFF;
            transition: all 0.2s ease;
            outline: none;
        }
        .form-input:focus {
            border-color: #0071E3;
            background: #12151B;
            box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.25);
        }
        .error-banner {
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.35);
            color: #F87171;
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 18px;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-submit {
            width: 100%;
            padding: 14px 20px;
            background: #0071E3;
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 16px rgba(0, 113, 227, 0.4);
            min-height: 48px; /* Touch target */
        }
        .btn-submit:hover {
            background: #0077ED;
            transform: translateY(-1px);
        }
        .btn-submit:active {
            transform: translateY(0);
        }
        .login-footer {
            margin-top: 22px;
            font-size: 12px;
            color: #636366;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand-badge">📱 CASHSECOND</div>
        <h1 class="login-title">Pricing &amp; Deductions</h1>
        <p class="login-subtitle">Enter password to view and edit iPhone base prices and deductions in real time.</p>

        <?php if (!empty($error)): ?>
            <div class="error-banner">
                <span>⚠️</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php<?= !empty($redirect) ? '?redirect=' . urlencode($redirect) : '' ?>">
            <div class="form-group">
                <label class="form-label" for="password">Admin Key</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Enter password" autofocus required autocomplete="current-password">
            </div>

            <button type="submit" class="btn-submit">
                Unlock Dashboard →
            </button>
        </form>

        <div class="login-footer">
            🔒 CashSecond Real-Time Management
        </div>
    </div>
</body>
</html>
