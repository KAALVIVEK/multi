<?php
// This file is included by dashboard.php, which already handles secure_page() and fetches $current_user.

// --- MOCK DATA for dashboard statistics ---
// In a production application, this would be replaced by data fetched via api.php
// However, we need mock data for the client-side design to work.

$mock_stats = [
    'quantity' => 45,
    'orders' => 38,
    'top_product' => 'WinStar PRO',
    'monthly_target_percent' => 75,
    'monthly_sales' => 1250.50,
    'today_sales' => 55.00
];

// PHP function to get the status color class
function get_status_class($value) {
    return ($value > 0) ? 'text-green-600' : 'text-red-600';
}
?>

<div id="content-dashboard" class="p-6 md:p-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">MyDash Statics</h2>

    <!-- Top Metric Cards (Images 6) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Card 1: Quantity -->
        <div class="p-6 rounded-xl card-shadow bg-indigo-600 text-white hover:bg-indigo-700 transition duration-200">
            <div class="flex items-center justify-between">
                <i data-lucide="users" class="w-8 h-8 opacity-75"></i>
                <span class="text-sm font-semibold uppercase opacity-90">Quantity</span>
            </div>
            <p class="text-4xl font-extrabold mt-3"><?php echo $mock_stats['quantity']; ?></p>
            <div class="flex items-center mt-2 text-sm font-medium">
                <i data-lucide="arrow-up-right" class="w-4 h-4 mr-1"></i>
                <span>+100% (Since Last Month)</span>
            </div>
        </div>

        <!-- Card 2: Orders -->
        <div class="p-6 rounded-xl card-shadow bg-pink-600 text-white hover:bg-pink-700 transition duration-200">
            <div class="flex items-center justify-between">
                <i data-lucide="package" class="w-8 h-8 opacity-75"></i>
                <span class="text-sm font-semibold uppercase opacity-90">Orders</span>
            </div>
            <p class="text-4xl font-extrabold mt-3"><?php echo $mock_stats['orders']; ?></p>
            <div class="flex items-center mt-2 text-sm font-medium">
                <i data-lucide="arrow-up-right" class="w-4 h-4 mr-1"></i>
                <span>+100% (Since Last Month)</span>
            </div>
        </div>

        <!-- Card 3: Avg Month Sale (Custom Metric) -->
        <div class="p-6 rounded-xl card-shadow bg-white border border-gray-200">
            <span class="text-sm font-medium text-gray-500">Avg Month Sale</span>
            <p class="text-3xl font-bold text-gray-800 mt-2">₹1250.50</p>
            <p class="text-xs text-gray-400 mt-1">Based on last 30 days data</p>
        </div>

        <!-- Card 4: Today Sales (Custom Metric) -->
        <div class="p-6 rounded-xl card-shadow bg-white border border-gray-200">
            <span class="text-sm font-medium text-gray-500">Today's Revenue</span>
            <p class="text-3xl font-bold <?php echo get_status_class($mock_stats['today_sales']); ?> mt-2">₹<?php echo number_format($mock_stats['today_sales'], 2); ?></p>
            <p class="text-xs text-gray-400 mt-1">Real-time data stream</p>
        </div>
    </div>

    <!-- Sales Statistics & Top Seller (Images 6, 8, 10) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Column 1: Sales Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl card-shadow border border-gray-200">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                <h3 class="text-xl font-semibold text-gray-800">Sales Statistics</h3>
                <div class="flex space-x-2 text-sm font-medium">
                    <button id="monthly-tab" class="px-3 py-1 rounded-full bg-indigo-500 text-white shadow-md">Monthly</button>
                    <button id="daily-tab" class="px-3 py-1 rounded-full text-gray-600 hover:bg-gray-100">Daily</button>
                </div>
            </div>

            <div id="chart-container" class="h-64">
                <!-- Placeholder for Chart.js Bar Chart -->
                <canvas id="salesChart" class="w-full h-full"></canvas>
            </div>

            <!-- Monthly Target (Image 10) -->
            <div class="mt-8 pt-4 border-t border-gray-100">
                <h4 class="text-lg font-semibold text-gray-700 mb-4">Monthly Target</h4>
                <div class="flex items-center space-x-4">
                    <!-- Gauge Simulation -->
                    <?php
                    $angle = (180 * $mock_stats['monthly_target_percent'] / 100);
                    $rotation = $angle > 180 ? 90 : $angle - 90;
                    ?>
                    <div class="relative w-24 h-12 overflow-hidden">
                        <!-- Background arc -->
                        <div class="absolute inset-0 border-t-2 border-l-2 border-r-2 border-gray-200 rounded-t-full"></div>
                        <!-- Needle Simulation -->
                        <div style="transform: rotate(<?php echo $rotation; ?>deg); transform-origin: bottom center; transition: transform 0.5s;" class="absolute bottom-0 left-1/2 w-0.5 h-full bg-indigo-600"></div>
                    </div>
                    <div>
                        <p class="text-2xl font-bold <?php echo get_status_class($mock_stats['monthly_target_percent'] - 100); ?>"><?php echo $mock_stats['monthly_target_percent']; ?>%</p>
                        <p class="text-xs text-gray-500">Sales: ₹<?php echo number_format($mock_stats['monthly_sales'], 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column 2: Top Selling Products -->
        <div class="lg:col-span-1 p-6 rounded-xl card-shadow bg-white border border-gray-200">
            <div class="flex items-center space-x-3 mb-4">
                <i data-lucide="trophy" class="w-6 h-6 text-yellow-500"></i>
                <h3 class="text-xl font-semibold text-gray-800">Top Selling Products</h3>
            </div>
            <p class="text-sm text-gray-500 mb-6">This month's most popular items in your domain.</p>

            <!-- Top Product Card (Custom Unique Design) -->
            <div class="p-4 bg-indigo-50 border-l-4 border-indigo-500 rounded-lg shadow-md">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-indigo-700"><?php echo $mock_stats['top_product']; ?></span>
                    <span class="px-3 py-1 bg-yellow-400 text-yellow-900 text-xs font-extrabold rounded-full shadow-inner">#1</span>
                </div>
                <p class="text-sm text-gray-600 mt-2">Keep pushing! Your top sellers are driving amazing results this month.</p>
            </div>

            <p class="text-xs text-gray-400 mt-4 text-center">Data refreshed every 5 minutes.</p>
        </div>
    </div>
</div>

<!-- Placeholder for Chart.js Initialization (must be run after DOM loads) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');
        
        // Mock data matching the general shape shown in Image 10
        const chartData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Monthly Sales (₹)',
                data: [1500, 1800, 2200, 1900, 2500, 3000, 2800, 3500, 3200, 4000, 3800, 4500],
                backgroundColor: 'rgba(99, 102, 241, 0.7)', // Indigo-500
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 1,
                borderRadius: 4,
            }]
        };

        const salesChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(200, 200, 200, 0.2)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Sales: ₹' + context.formattedValue;
                            }
                        }
                    }
                }
            }
        });
        
        // Simple tab switching logic (no data change, just visual)
        document.getElementById('monthly-tab').addEventListener('click', () => {
            document.getElementById('monthly-tab').classList.add('bg-indigo-500', 'text-white');
            document.getElementById('daily-tab').classList.remove('bg-indigo-500', 'text-white');
            document.getElementById('daily-tab').classList.add('text-gray-600', 'hover:bg-gray-100');
            // In a real application, this would trigger an AJAX call to api.php to refresh chart data for the month.
            console.log("Monthly Tab clicked. Chart data should refresh here.");
        });
        
        document.getElementById('daily-tab').addEventListener('click', () => {
            document.getElementById('daily-tab').classList.add('bg-indigo-500', 'text-white');
            document.getElementById('monthly-tab').classList.remove('bg-indigo-500', 'text-white');
            document.getElementById('monthly-tab').classList.add('text-gray-600', 'hover:bg-gray-100');
             // In a real application, this would trigger an AJAX call to api.php to refresh chart data for the day.
             console.log("Daily Tab clicked. Chart data should refresh here.");
        });
    });
</script>
