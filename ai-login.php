<?php
// Session handling with error prevention
if (session_status() === PHP_SESSION_NONE) session_start();

include 'ai-db.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, username, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_regenerate_id(true);
                }
                $_SESSION['admin'] = ['logged_in' => true];
                header("Location: ai-dashboard.php");
                exit();
            } else {
                $error = "🔐 Invalid credentials";
            }
        } else {
            $error = "👤 User not found";
        }
        $stmt->close();
    } else {
        $error = "📝 Both fields are required";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎓 Attendance System Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: 
                linear-gradient(rgba(0, 0, 0, 0.5), 
                rgba(0, 0, 0, 0.5)),
                url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.85);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }
        .header-gradient {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="header-gradient py-5 px-6 text-center">
            <h1 class="text-2xl font-bold text-white">
                📚 School Attendance System
            </h1>
            <p class="text-white/80 mt-1">Track student presence efficiently</p>
        </div>
        
        <div class="p-8">
            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded flex items-start">
                    <span class="text-xl mr-2">⚠️</span>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        👤 Username
                    </label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        🔒 Password
                    </label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
                
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                    <span class="mr-2">🚪</span> Login to Dashboard
                </button>
            </form>
            
            <div class="mt-6 text-center text-sm text-gray-600">
                <p>📅 Need help? Contact admin</p>
            </div>
        </div>
    </div>
</body>
</html>