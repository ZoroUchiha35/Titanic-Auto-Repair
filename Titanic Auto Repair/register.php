<?php
require_once 'config.php';
requireGuest();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $vehicle_info = trim($_POST['vehicle_info'] ?? '');
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($full_name)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'Username or email already exists';
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, address, vehicle_info) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $address, $vehicle_info]);
                
                $success = 'Registration successful! You can now login.';
                
                // Clear form
                $_POST = [];
            }
        } catch(PDOException $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Titanic Auto Repair</title>
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
            
            /* Background Image */
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('https://i.pinimg.com/1200x/f1/ca/c8/f1cac83c89750699cd8b5eee7ab042fb.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }
        
        .container {
            width: 100%;
            max-width: 500px;
            background-color: rgba(45, 45, 45, 0.9);
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            border-top: 4px solid var(--accent-red);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
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
        }
        
        .logo h1 {
            font-size: 2.2rem;
            margin-bottom: 5px;
            background: linear-gradient(to right, var(--accent-red), var(--accent-blue));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .logo p {
            color: var(--text-light);
            font-style: italic;
            font-size: 1rem;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .alert-error {
            background-color: rgba(198, 40, 40, 0.2);
            border: 1px solid var(--accent-red);
            color: #ff8a8a;
        }
        
        .alert-success {
            background-color: rgba(21, 101, 192, 0.2);
            border: 1px solid var(--accent-blue);
            color: #90caf9;
        }
        
        .form-group {
            margin-bottom: 20px;
            animation: slideIn 0.5s ease-out;
            animation-fill-mode: both;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }
        .form-group:nth-child(4) { animation-delay: 0.4s; }
        .form-group:nth-child(5) { animation-delay: 0.5s; }
        .form-group:nth-child(6) { animation-delay: 0.6s; }
        .form-group:nth-child(7) { animation-delay: 0.7s; }
        .form-group:nth-child(8) { animation-delay: 0.8s; }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-light);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
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
            box-shadow: 0 0 0 2px rgba(21, 101, 192, 0.3);
            transform: translateY(-2px);
        }
        
        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            width: 100%;
            padding: 15px;
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
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2f63d3);
            color: var(--text-light);
            box-shadow: 0 4px 15px rgba(40, 90, 198, 0.4);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #035cf7, #2f63d3);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(40, 90, 198, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, var(--accent-blue), #1976d2);
            color: var(--text-light);
            margin-top: 15px;
            box-shadow: 0 4px 15px rgba(21, 101, 192, 0.3);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #0d47a1, var(--accent-blue));
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(21, 101, 192, 0.4);
        }
        
        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            animation: fadeIn 0.8s ease-out;
        }
        
        .login-link a {
            color: var(--accent-blue);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
            position: relative;
        }
        
        .login-link a:hover {
            color: #64b5f6;
        }
        
        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent-blue);
            transition: width 0.3s;
        }
        
        .login-link a:hover::after {
            width: 100%;
        }
        
        .required {
            color: var(--accent-red);
        }
        
        .form-note {
            font-size: 0.85rem;
            color: var(--text-grey);
            margin-top: 5px;
            font-style: italic;
        }
        
        .floating-tools {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 2rem;
            color: var(--accent-metal);
            opacity: 0.3;
            z-index: -1;
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
            
            .floating-tools {
                display: none;
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
    <!-- Floating decorative elements -->
    <div class="floating-tools">
        <i class="fas fa-tools"></i>
        <i class="fas fa-wrench" style="margin-left: 20px;"></i>
        <i class="fas fa-cog" style="margin-left: 20px;"></i>
    </div>
    
    <div class="container">
        <div class="logo">
            <div class="logo-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h1>Titanic Auto Repair</h1>
            <p>Join Our Garage Family</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username <span class="required">*</span></label>
                <input type="text" id="username" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                       required autofocus placeholder="Enter your username">
            </div>
            
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                       required placeholder="your.email@example.com">
            </div>
            
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control" 
                       required placeholder="••••••••">
                <div class="form-note">Minimum 6 characters</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                       required placeholder="••••••••">
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name <span class="required">*</span></label>
                <input type="text" id="full_name" name="full_name" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                       required placeholder="John Doe">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-control" 
                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                       placeholder="+1 (234) 567-8900">
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="form-control" placeholder="Your address..."><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="vehicle_info">Vehicle Information</label>
                <textarea id="vehicle_info" name="vehicle_info" class="form-control" placeholder="Make, Model, Year (e.g., Toyota Camry 2020)"><?php echo htmlspecialchars($_POST['vehicle_info'] ?? ''); ?></textarea>
                <div class="form-note">Optional: Helps us serve you better</div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>
        
        <div class="login-link">
            Already part of our garage family? <a href="login.php">Sign In here</a>
        </div>
    </div>
</body>
</html>