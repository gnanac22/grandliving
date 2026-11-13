<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (isLoggedIn()) {
    redirect(ADMIN_URL . '/index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($token)) {
        $error = 'Invalid security token. Please try again.';
    } elseif (rateLimitCheck('admin_login', LOGIN_RATE_LIMIT, LOGIN_RATE_WINDOW)) {
        $error = 'Too many login attempts. Please wait 15 minutes before trying again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password.';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM admins WHERE (username = ? OR email = ?) AND is_active = 1 LIMIT 1');
            $stmt->execute([$username, $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password_hash'])) {
                if (session_status() !== PHP_SESSION_ACTIVE) {
                    session_start();
                }
                session_regenerate_id(true);

                $_SESSION['admin_id']        = $admin['id'];
                $_SESSION['admin_username']  = $admin['username'];
                $_SESSION['admin_email']     = $admin['email'];
                $_SESSION['admin_role']      = $admin['role'];
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['last_regen']      = time();

                // Update last login
                $up = $pdo->prepare('UPDATE admins SET last_login = NOW() WHERE id = ?');
                $up->execute([$admin['id']]);

                logActivity($pdo, 'Login', 'Admin logged in successfully', $admin['id']);
                rateLimitReset('admin_login');

                redirect(ADMIN_URL . '/index.php');
            } else {
                $error = 'Invalid credentials. Please verify your username and password.';
                logActivity($pdo, 'Failed Login Attempt', "Username attempted: {$username}");
            }
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Authentication — Grand Furniture Vizag</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Manrope:wght@400;500;600&display=swap">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    background: #171614;
    color: #F5F0E8;
    font-family: 'Manrope', sans-serif;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
  }
  .login-card {
    width: 100%;
    max-width: 440px;
    background: #1E1A17;
    border: 1px solid rgba(213,190,154,0.25);
    padding: 44px 36px;
    border-radius: 4px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.6);
  }
  .brand-header {
    text-align: center;
    margin-bottom: 32px;
  }
  .brand-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 2.2rem;
    font-weight: 300;
    color: #F5F0E8;
    letter-spacing: 0.12em;
    text-transform: uppercase;
  }
  .brand-sub {
    font-size: 0.72rem;
    letter-spacing: 0.25em;
    text-transform: uppercase;
    color: #D5BE9A;
    margin-top: 4px;
  }
  .form-group {
    margin-bottom: 20px;
  }
  .form-group label {
    display: block;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: #D5BE9A;
    margin-bottom: 8px;
    font-weight: 600;
  }
  .form-group input {
    width: 100%;
    background: #171614;
    border: 1px solid rgba(213,190,154,0.3);
    color: #F5F0E8;
    padding: 12px 16px;
    font-size: 0.95rem;
    border-radius: 2px;
    outline: none;
    transition: border-color 0.3s;
  }
  .form-group input:focus {
    border-color: #D5BE9A;
  }
  .btn-submit {
    width: 100%;
    background: #D5BE9A;
    color: #171614;
    border: none;
    padding: 14px;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.2em;
    text-transform: uppercase;
    cursor: pointer;
    border-radius: 2px;
    margin-top: 10px;
    transition: background 0.3s, transform 0.2s;
  }
  .btn-submit:hover {
    background: #C3AB85;
    transform: translateY(-1px);
  }
  .error-box {
    background: rgba(163, 59, 42, 0.2);
    border-left: 3px solid #A33B2A;
    color: #FFAAA0;
    padding: 12px 16px;
    font-size: 0.85rem;
    margin-bottom: 24px;
  }
  .demo-hint {
    margin-top: 28px;
    padding-top: 20px;
    border-top: 1px solid rgba(213,190,154,0.12);
    font-size: 0.75rem;
    color: #B7AA98;
    text-align: center;
    line-height: 1.6;
  }
  .demo-hint strong { color: #D5BE9A; }
</style>
</head>
<body>

<div class="login-card">
  <div class="brand-header">
    <h1 class="brand-title">Grand</h1>
    <div class="brand-sub">Furniture Vizag &bull; Admin Portal</div>
  </div>

  <?php if ($error): ?>
    <div class="error-box"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
    
    <div class="form-group">
      <label for="username">Username or Email</label>
      <input type="text" id="username" name="username" required autocomplete="username" autofocus>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" required autocomplete="current-password">
    </div>

    <button type="submit" class="btn-submit">Sign In to Dashboard</button>
  </form>

  <div class="demo-hint">
    Default credentials for initial setup:<br>
    Username: <strong>admin</strong> &bull; Password: <strong>Admin@2024</strong>
  </div>
</div>

</body>
</html>
