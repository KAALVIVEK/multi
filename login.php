<?php
include_once 'auth.php';

$error = '';

// Check if the user is already logged in
if (is_logged_in()) {
    redirect('dashboard.php');
}

// Handle POST request from the login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } elseif ($user = login_user($username, $password)) {
        // Login successful! Redirect to dashboard.
        redirect('dashboard.php');
    } else {
        $error = "Invalid username or password. Access denied.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuthPanel - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1f2937 0%, #111827 50%, #1f2937 100%);
            min-height: 100vh;
        }
        .glowing-button {
            position: relative;
            overflow: hidden;
            background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
            transition: all 0.3s ease;
        }
        .glowing-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(100, 100, 255, 0.5);
        }
        .card-shadow {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5), 0 0 50px rgba(100, 100, 255, 0.2);
            background: rgba(255, 255, 255, 0.05); /* Semi-transparent background */
            backdrop-filter: blur(10px);
        }
        .input-focus:focus {
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.5);
        }
    </style>
</head>
<body class="flex items-center justify-center">
    <div class="p-8 w-full max-w-md">
        <div class="card-shadow p-8 md:p-10 rounded-2xl border border-indigo-500/30">
            <div class="text-center mb-8">
                <i data-lucide="shield" class="w-12 h-12 text-indigo-400 mx-auto mb-3"></i>
                <h1 class="text-3xl font-extrabold text-white mb-2">AuthPanel Login</h1>
                <p class="text-indigo-300">Access the Reseller Control Center</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-900/40 text-red-300 border border-red-500/30 p-3 rounded-lg mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-indigo-300 mb-2">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        required 
                        class="input-focus w-full bg-gray-700/50 border border-gray-600/50 text-white p-3 rounded-xl transition duration-200"
                        placeholder="Enter your username"
                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                    >
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-medium text-indigo-300 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        class="input-focus w-full bg-gray-700/50 border border-gray-600/50 text-white p-3 rounded-xl transition duration-200"
                        placeholder="••••••••"
                    >
                </div>

                <button type="submit" class="glowing-button w-full text-white font-bold py-3 rounded-xl text-lg shadow-lg">
                    <i data-lucide="log-in" class="inline w-5 h-5 mr-2"></i>
                    SIGN IN
                </button>
            </form>
            
            <p class="mt-8 text-center text-sm text-indigo-400">
                System is closed. Contact owner for account access.
            </p>
        </div>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
