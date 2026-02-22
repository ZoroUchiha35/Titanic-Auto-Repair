<?php
require_once 'config.php';
requireGuest();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        try {
            // Check user credentials
            $stmt = $pdo->prepare("SELECT id, username, email, password, full_name FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['logged_in'] = true;
                
                // Redirect to homepage
                header("Location: homepage.php");
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } catch(PDOException $e) {
            $error = 'Login failed: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Titanic Auto Repair</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #1a1a1a;
            --secondary-dark: #2d2d2d;
            --accent-red: #c62828;
            --accent-blue: #1565c0;
            --accent-grey: #424242;
            --accent-metal: #607d8b;
            --text-light: #f5f5f5;
            --text-grey: #bdbdbd;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            
            /* Background Image - Different from register for variety */
            background: linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.75)), 
                        url('https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2072&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        
        .container {
            width: 100%;
            max-width: 450px;
            background-color: rgba(45, 45, 45, 0.9);
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border-top: 4px solid var(--accent-blue);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            animation: slideUp 0.5s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        h1, h2, h3 {
            font-family: 'Oswald', sans-serif;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-icon {
            color: var(--accent-red);
            font-size: 3rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        
        .logo h1 {
            font-size: 2.2rem;
            margin-bottom: 5px;
            background: linear-gradient(to right, var(--accent-blue), var(--accent-red));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .logo p {
            color: var(--text-light);
            font-style: italic;
            font-size: 1.1rem;
            margin-top: 5px;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            background-color: rgba(198, 40, 40, 0.2);
            border: 1px solid var(--accent-red);
            color: #ff8a8a;
            animation: shake 0.5s ease-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .form-group {
            margin-bottom: 25px;
            animation: slideIn 0.5s ease-out;
            animation-fill-mode: both;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-light);
            font-size: 1.05rem;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 15px;
            background-color: rgba(26, 26, 26, 0.8);
            border: 1px solid var(--accent-grey);
            border-radius: 4px;
            color: var(--text-light);
            font-size: 1rem;
            font-family: 'Roboto', sans-serif;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.2);
            transform: translateY(-2px);
        }
        
        .btn {
            display: inline-block;
            width: 100%;
            padding: 16px;
            font-weight: 600;
            text-transform: uppercase;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1.1rem;
            border: none;
            text-decoration: none;
            text-align: center;
            font-family: 'Oswald', sans-serif;
            letter-spacing: 1.5px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent-blue), #1976d2);
            color: var(--text-light);
            box-shadow: 0 4px 15px rgba(21, 101, 192, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0d47a1, var(--accent-blue));
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(21, 101, 192, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, var(--accent-red), #d32f2f);
            color: var(--text-light);
            margin-top: 15px;
            box-shadow: 0 4px 15px rgba(198, 40, 40, 0.3);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #b71c1c, var(--accent-red));
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(198, 40, 40, 0.4);
        }
        
        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            animation: fadeIn 0.8s ease-out;
        }
        
        .register-link a {
            color: var(--accent-red);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
            position: relative;
        }
        
        .register-link a:hover {
            color: #ef5350;
        }
        
        .register-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent-red);
            transition: width 0.3s;
        }
        
        .register-link a:hover::after {
            width: 100%;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 8px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        
        .remember-me label {
            margin-bottom: 0;
            cursor: pointer;
            font-size: 0.95rem;
            color: var(--text-grey);
        }
        
        .forgot-password a {
            color: var(--accent-blue);
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
        }
        
        .forgot-password a:hover {
            color: #64b5f6;
            text-decoration: underline;
        }
        
        .feature-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .feature-icon {
            width: 40px;
            height: 40px;
            background: rgba(21, 101, 192, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-blue);
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        
        .feature-icon:hover {
            background: rgba(198, 40, 40, 0.2);
            color: var(--accent-red);
            transform: translateY(-3px);
        }
        
        .security-note {
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: var(--text-grey);
            opacity: 0.8;
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .logo h1 {
                font-size: 1.8rem;
            }
            
            body {
                padding: 10px;
                background-attachment: scroll;
            }
            
            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .feature-icons {
                gap: 15px;
            }
        }
        
        @media (max-width: 768px) {
            body {
                background-attachment: scroll;
            }
        }
    </style>

<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%232d2d2d'
rx='20'/%3E%3Cpath d='M30 25 L45 40 L35 55 L25 45 L30 25' fill='%23c62828'/%3E%3Cpath d='M65 35 L80 50 L70 65 L55 50 L65 35' fill='%231565c0'/%3E%3Ccircle cx='45' cy='55' r='8' fill='%23c62828'/%3E%3Ccircle cx='55' cy='45' r='8' fill='%231565c0'/%3E%3C/svg%3E">

</head>
<body>
    <div class="container">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h1>Titanic Auto Repair</h1>
            <p>Welcome Back to the Garage</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       required autofocus placeholder="Enter username or email">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" 
                       required placeholder="••••••••">
            </div>
            
            <div class="remember-forgot">
                <div class="forgot-password">
                    <a href="#">Forgot password?</a>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="register-link">
            New to Titanic Auto Repair? <a href="register.php">Create an account</a>
        </div>
        
        <div class="security-note">
            <i class="fas fa-lock"></i> Your information is securely encrypted
        </div>
    </div>
    
    <script>
        // Add subtle typing effect to the username field
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            if (usernameField && !usernameField.value) {
                usernameField.focus();
            }
            
            // Add floating label effect
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                control.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>