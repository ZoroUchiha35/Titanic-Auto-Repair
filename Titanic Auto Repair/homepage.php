<?php
require_once 'config.php';
requireLogin();

// Get user info
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];

// You can fetch additional user data here if needed
$stmt = $pdo->prepare("SELECT phone, vehicle_info FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch();

// The index.html content (your homepage) with added logout button
?>
<?php
// Read the index.html file
$homepage_content = file_get_contents('index.html');

// Add logout button to the navigation
$logout_button = '
<li>
    <a href="#" class="nav-link" id="logoutBtn" style="color: #ff6b6b;">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</li>';

// Insert logout button before closing </ul> in navigation
$homepage_content = str_replace(
    '</ul>',
    $logout_button . '</ul>',
    $homepage_content
);

// Add logout functionality to the JavaScript section
$logout_script = '
// Logout functionality
const logoutBtn = document.getElementById("logoutBtn");
if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
        e.preventDefault();
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "logout.php";
        }
    });
}';

// Insert logout script before closing </script> tag
$homepage_content = str_replace(
    '</script>',
    $logout_script . '</script>',
    $homepage_content
);

// Add welcome message to hero section
$welcome_message = '<p style="font-size: 1.2rem; margin-bottom: 10px; color: #4fc3f7;">Welcome back, ' . htmlspecialchars($full_name) . '!</p>';
$homepage_content = str_replace(
    '<p>Your trusted partner for all automotive repairs since 2018</p>',
    $welcome_message . '<p>Your trusted partner for all automotive repairs since 2018</p>',
    $homepage_content
);

// Output the modified homepage
echo $homepage_content;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <!-- Security Meta Tags -->
<meta name="rating" content="general">
<meta name="robots" content="index, follow">
<meta name="googlebot" content="index, follow">
<meta name="theme-color" content="#c62828">
</body>
</html>