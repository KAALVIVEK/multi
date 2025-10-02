<?php 
// Ensure this file is only included by a main PHP page (like dashboard.php)
if (basename($_SERVER['PHP_SELF']) == 'header.php') {
    die('Access denied.');
}

// Fetch current user data for display in the profile dropdown and sidebar
$user = get_current_user();
$is_admin = is_admin();
// Defensive defaults for display-only usage
$displayUsername = htmlspecialchars($user['username'] ?? 'User');
$displayRole = htmlspecialchars(isset($user['role']) ? ucfirst($user['role']) : 'User');
$displayWallet = is_numeric($user['wallet_balance'] ?? null) ? number_format($user['wallet_balance'], 2) : number_format(0, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AuthPanel | Dashboard</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <style>
        /* Custom Styles for the premium AuthPanel look */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb; /* Light background for contrast */
        }
        .sidebar {
            background-color: #1f2937; /* Dark Slate Sidebar */
            color: #d1d5db;
        }
        .header-fixed {
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s;
        }
        .card-shadow:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }
        .glowing-button {
            background-image: linear-gradient(to right, #4c51bf, #667eea);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .glowing-button:hover {
            background-image: linear-gradient(to right, #667eea, #4c51bf);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.5);
        }
        .sidebar-link-active {
            background-color: #4338ca; /* Indigo-700 */
            color: #ffffff;
            font-weight: 600;
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Fixed Sidebar (Left Navigation) -->
        <aside id="sidebar" class="sidebar w-64 p-4 flex-shrink-0 hidden md:block z-20">
            <div class="mb-8 pt-2">
                <h1 class="text-xl font-bold text-white tracking-wide">AuthPanel</h1>
            </div>
            
            <nav class="space-y-2">
                <p class="text-xs text-gray-400 uppercase tracking-wider mt-4">MENU</p>

                <!-- MyDash Statics -->
                <a href="dashboard.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-700 transition <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'sidebar-link-active' : ''; ?>">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    <span>MyDash Statics</span>
                </a>

                <!-- Multi-Panel (Records) -->
                <p class="text-xs text-gray-400 uppercase tracking-wider pt-4">RECORDS</p>
                <a href="keygen.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-700 transition <?php echo (basename($_SERVER['PHP_SELF']) == 'keygen.php') ? 'sidebar-link-active' : ''; ?>">
                    <i data-lucide="key" class="w-5 h-5"></i>
                    <span>Generate Keys</span>
                </a>
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-700 transition">
                    <i data-lucide="history" class="w-5 h-5"></i>
                    <span>Order History (TBD)</span>
                </a>
                <a href="transactions.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-700 transition <?php echo (basename($_SERVER['PHP_SELF']) == 'transactions.php') ? 'sidebar-link-active' : ''; ?>">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                    <span>Wallet Transaction</span>
                </a>

                <?php if ($is_admin): ?>
                <p class="text-xs text-gray-400 uppercase tracking-wider pt-4">ADMIN TOOLS</p>
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-700 transition">
                    <i data-lucide="shield" class="w-5 h-5"></i>
                    <span>User & Balance (TBD)</span>
                </a>
                <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-700 transition">
                    <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                    <span>Product Pricing (TBD)</span>
                </a>
                <?php endif; ?>
            </nav>

            <div class="mt-8 pt-4 border-t border-gray-700">
                <p class="text-xs text-gray-400">Wallet Balance:</p>
                <p class="text-xl font-bold text-green-400">₹<?php echo $displayWallet; ?></p>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            
            <!-- Fixed Top Navigation Bar -->
            <header id="top-header" class="header-fixed fixed w-full md:w-[calc(100%-16rem)] top-0 left-0 md:left-64 h-16 flex items-center justify-between px-6 z-10">
                <button id="mobileMenuButton" class="md:hidden text-gray-600 hover:text-indigo-600">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
                
                <!-- Search Bar -->
                <div class="relative hidden md:block w-96 ml-4">
                    <input type="text" placeholder="Press / to search" class="w-full bg-gray-100 border border-gray-200 rounded-full py-2 pl-10 pr-4 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>

                <!-- Right Icons and Profile Dropdown -->
                <div class="flex items-center space-x-4">
                    <i data-lucide="sun" class="w-5 h-5 text-gray-500 cursor-pointer hover:text-indigo-600 transition"></i>
                    <i data-lucide="bell" class="w-5 h-5 text-gray-500 cursor-pointer hover:text-indigo-600 transition"></i>
                    
                    <!-- User Profile Dropdown -->
                    <div class="relative group">
                        <img src="https://placehold.co/32x32/indigo/white?text=U" alt="User Avatar" class="rounded-full cursor-pointer border-2 border-indigo-400">
                        
                        <!-- Dropdown Content (Simulated) -->
                        <div class="absolute right-0 mt-3 w-48 bg-white border border-gray-200 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="p-4 border-b">
                                <p class="font-semibold"><?php echo $displayUsername; ?></p>
                                <p class="text-xs text-gray-500">Role: <?php echo $displayRole; ?></p>
                            </div>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="settings" class="w-4 h-4 mr-2"></i> Account settings
                            </a>
                            <a href="logout.php" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100 border-t">
                                <i data-lucide="log-out" class="w-4 h-4 mr-2"></i> Sign out
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Container -->
            <main class="flex-1 overflow-y-auto pt-16" id="main-content-area">
                <!-- Content will be injected here by dashboard.php, keygen.php, etc. -->

<!-- The rest of the page (content and footer) will be included in the main handler file -->
