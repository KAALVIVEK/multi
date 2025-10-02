<?php
include_once 'auth.php';
// This function ensures the user is logged in before viewing the dashboard.
secure_page();

// Get current user data (including wallet balance and role)
$current_user = get_current_user();
$user_id = $current_user['id'];
$user_role = $current_user['role'];

// --- Data Fetching Placeholder ---
// In a real application, we would call the API handler here to fetch all dashboard data
// $dashboard_data = fetch_dashboard_data($user_id);
// For now, we use placeholders until the JavaScript fetches the data.

// Include the Header (which contains the fixed navigation and sidebar structure)
include 'includes/header.php';
?>

<div id="content-dashboard" class="p-6 md:p-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Overview Console</h2>
    
    <!-- Stat Cards (Images 6, 8) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Key Count Card -->
        <div class="bg-white p-6 rounded-xl card-shadow flex justify-between items-center border-l-4 border-indigo-500 hover:shadow-lg transition duration-200">
            <div>
                <p class="text-sm font-medium text-gray-500">Active Keys</p>
                <p id="activeKeyCount" class="text-3xl font-bold text-gray-900 mt-1">0</p>
            </div>
            <i data-lucide="key" class="w-8 h-8 text-indigo-400"></i>
        </div>
        
        <!-- Total Orders Card (This typically changes based on Admin/User view) -->
        <div class="bg-white p-6 rounded-xl card-shadow flex justify-between items-center border-l-4 border-green-500 hover:shadow-lg transition duration-200">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Orders</p>
                <p id="orderCount" class="text-3xl font-bold text-gray-900 mt-1">0</p>
            </div>
            <i data-lucide="box" class="w-8 h-8 text-green-400"></i>
        </div>

        <!-- Key Rate Card -->
        <div class="bg-white p-6 rounded-xl card-shadow flex justify-between items-center border-l-4 border-yellow-500 hover:shadow-lg transition duration-200">
            <div>
                <p class="text-sm font-medium text-gray-500">Current Rate</p>
                <p id="keyRate" class="text-3xl font-bold text-gray-900 mt-1">0/min</p>
            </div>
            <i data-lucide="zap" class="w-8 h-8 text-yellow-400"></i>
        </div>
        
        <!-- Top Seller Card -->
        <div class="bg-white p-6 rounded-xl card-shadow flex justify-between items-center border-l-4 border-red-500 hover:shadow-lg transition duration-200">
            <div>
                <p class="text-sm font-medium text-gray-500">Top Seller</p>
                <p class="text-xl font-bold text-gray-900 mt-1">WinStar PRO</p>
            </div>
            <i data-lucide="trophy" class="w-8 h-8 text-red-400"></i>
        </div>
    </div>

    <!-- Sales Statistics and Target (Mimicking Images 8, 10) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Sales Statistics Chart Placeholder -->
        <div class="bg-white p-6 rounded-xl card-shadow">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Sales Statistics</h3>
            <!-- Monthly/Daily Tabs -->
            <div class="flex border-b border-gray-200 mb-4">
                <button onclick="toggleChart('monthly')" class="py-2 px-4 text-sm font-medium text-indigo-600 border-b-2 border-indigo-600 transition-colors">Monthly</button>
                <button onclick="toggleChart('daily')" class="py-2 px-4 text-sm font-medium text-gray-500 hover:text-indigo-600 transition-colors">Daily</button>
            </div>
            <div id="chartContainer" class="h-64 bg-gray-50 rounded-lg flex items-center justify-center text-gray-400">
                [ Placeholder for Chart.js Graph ]
            </div>
        </div>

        <!-- Monthly Target & Wallet Info -->
        <div class="bg-white p-6 rounded-xl card-shadow flex flex-col justify-between">
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Monthly Target</h3>
                <div class="text-center mb-6">
                    <p class="text-5xl font-extrabold text-green-500">0%</p>
                    <p class="text-sm text-red-500 mt-1">-100.0%</p>
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4 border-t pt-4">
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500">Avg Month Sale</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">₹350</p>
                </div>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500">Sales</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">₹0</p>
                </div>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-500">Today</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">₹0</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Recent Activity Table -->
    <div class="bg-white p-6 rounded-xl card-shadow">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Recent Activity Log</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody id="recentActivityBody" class="bg-white divide-y divide-gray-200">
                    <!-- Data loaded via JavaScript API calls -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // --- Frontend Dashboard Logic (MOCK API calls) ---
    const API_ENDPOINT = 'api.php';
    const CURRENT_USER_ID = "<?php echo $user_id; ?>";
    const CURRENT_USER_ROLE = "<?php echo $user_role; ?>";

    // Helper to simulate fetching data (You must replace this with the real fetchAPI function)
    async function fetchAPI(action, data = {}) {
        // This simulates a successful response from api.php
        return { 
            active_keys: 15, 
            key_rate: 1.2, 
            recent_activity: [
                {id: 1001, type: 'purchase', amount: -50.00, date: '2025-10-02 10:00'},
                {id: 1002, type: 'deposit', amount: 200.00, date: '2025-10-01 15:30'},
                {id: 1003, type: 'purchase', amount: -25.00, date: '2025-09-30 08:00'}
            ]
        }; 
    }

    async function loadDashboardStats() {
        // You will integrate the actual fetchAPI implementation here to talk to api.php
        const data = await fetchAPI('load_dashboard_stats', { user_id: CURRENT_USER_ID });

        if (data) {
            document.getElementById('activeKeyCount').textContent = data.active_keys;
            document.getElementById('orderCount').textContent = data.active_keys + 5; // Example calculation
            document.getElementById('keyRate').textContent = data.key_rate + '/min';
            
            const logBody = document.getElementById('recentActivityBody');
            logBody.innerHTML = data.recent_activity.map(item => {
                const amountClass = item.amount > 0 ? 'text-green-500' : 'text-red-500';
                const signedAmount = item.amount > 0 ? `+₹${item.amount.toFixed(2)}` : `-₹${Math.abs(item.amount).toFixed(2)}`;
                
                return `
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-2 px-4 text-sm text-gray-500">${new Date(item.date).toLocaleDateString()}</td>
                        <td class="py-2 px-4 text-sm text-indigo-600 font-mono">#${item.id}</td>
                        <td class="py-2 px-4 text-sm text-gray-700">${item.type.charAt(0).toUpperCase() + item.type.slice(1)}</td>
                        <td class="py-2 px-4 text-sm font-semibold ${amountClass}">${signedAmount}</td>
                    </tr>
                `;
            }).join('');
        }
    }

    function toggleChart(period) {
        const monthlyBtn = document.querySelector('[onclick="toggleChart(\'monthly\')"]');
        const dailyBtn = document.querySelector('[onclick="toggleChart(\'daily\')"]');
        const chartContainer = document.getElementById('chartContainer');

        if (period === 'monthly') {
            monthlyBtn.classList.add('text-indigo-600', 'border-indigo-600');
            monthlyBtn.classList.remove('text-gray-500', 'hover:text-indigo-600', 'border-transparent');
            
            dailyBtn.classList.remove('text-indigo-600', 'border-indigo-600');
            dailyBtn.classList.add('text-gray-500', 'hover:text-indigo-600', 'border-transparent');
            
            chartContainer.innerHTML = '[ Placeholder for Monthly Chart Data ]';
        } else {
            dailyBtn.classList.add('text-indigo-600', 'border-indigo-600');
            dailyBtn.classList.remove('text-gray-500', 'hover:text-indigo-600', 'border-transparent');
            
            monthlyBtn.classList.remove('text-indigo-600', 'border-indigo-600');
            monthlyBtn.classList.add('text-gray-500', 'hover:text-indigo-600', 'border-transparent');
            
            chartContainer.innerHTML = '[ Placeholder for Daily Chart Data ]';
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadDashboardStats();
        toggleChart('monthly'); // Initialize chart view
    });
</script>

<?php include 'includes/footer.php'; ?>
