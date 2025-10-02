<?php
// This file assumes $current_user is set by the including handler (keygen.php)

// --- MOCK DATA for Products and Durations ---
// In a production app, this data should be fetched from the database via api.php
// We define it here to make the client-side JavaScript calculation work immediately.

$mock_products = [
    ['id' => 1, 'name' => 'WinStar PRO', 'base_price' => 15.00],
    ['id' => 2, 'name' => 'Nexus Elite', 'base_price' => 10.00]
];

$mock_durations = [
    ['id' => 'd1', 'label' => '1 Day', 'multiplier' => 1],
    ['id' => 'd7', 'label' => '7 Days', 'multiplier' => 7],
    ['id' => 'd30', 'label' => '30 Days', 'multiplier' => 30]
];

// Helper to convert PHP arrays to JSON for JavaScript use
$js_prices = [];
foreach ($mock_products as $p) {
    foreach ($mock_durations as $d) {
        $key = "{$p['id']}_{$d['id']}";
        $js_prices[$key] = $p['base_price'] * $d['multiplier'];
    }
}
$js_prices_json = json_encode($js_prices);

$current_wallet = $current_user['wallet_balance'] ?? 0.00;
?>

<div id="content-keygen" class="p-6 md:p-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Generate Keys</h2>
    
    <!-- Status message placeholder -->
    <div id="keygen-status-message" class="hidden bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
        <p class="font-bold">Success!</p>
        <p id="keygen-status-text"></p>
    </div>

    <div class="bg-white p-8 rounded-xl card-shadow max-w-lg mx-auto border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-gray-800">New Key Purchase</h3>
            <span class="text-lg font-bold text-indigo-600">Wallet: ₹<?php echo number_format($current_wallet, 2); ?></span>
        </div>

        <form id="keygen-form" method="POST" action="keygen.php">
            <input type="hidden" name="action" value="generate_key">
            
            <div class="space-y-6">
                <!-- Select Product -->
                <div>
                    <label for="product" class="block text-sm font-medium text-gray-700 mb-1">Select Cheat</label>
                    <select id="product" name="product_id" required onchange="calculateTotal()" 
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select product</option>
                        <?php foreach($mock_products as $p): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo $p['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Select Duration -->
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Select Duration</label>
                    <select id="duration" name="duration_id" required onchange="calculateTotal()"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select duration</option>
                        <?php foreach($mock_durations as $d): ?>
                            <option value="<?php echo $d['id']; ?>"><?php echo $d['label']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Select Quantity</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" required oninput="calculateTotal()"
                        class="w-full border border-gray-300 rounded-lg p-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <!-- Total Amount Display (Image 4) -->
                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-lg font-semibold text-gray-800">Total Amount: <span id="totalAmountDisplay">₹0.00</span></p>
                </div>

                <button type="submit" id="purchaseKeyButton" class="glowing-button w-full py-3 text-lg transition duration-200" disabled>
                    Purchase Key
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Data Structure defined by PHP
    const prices = <?php echo $js_prices_json; ?>;
    const currentWallet = <?php echo $current_wallet; ?>;

    function calculateTotal() {
        const product = document.getElementById('product').value;
        const duration = document.getElementById('duration').value;
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        
        const key = `${product}_${duration}`;
        const unitPrice = prices[key];
        
        let totalCost = 0;
        if (unitPrice && quantity > 0) {
            totalCost = unitPrice * quantity;
        }

        const display = document.getElementById('totalAmountDisplay');
        const button = document.getElementById('purchaseKeyButton');
        
        display.textContent = `₹${totalCost.toFixed(2)}`;

        // Check wallet balance and selection validity
        if (totalCost > 0 && totalCost <= currentWallet) {
            button.disabled = false;
            button.classList.remove('opacity-50', 'bg-red-500');
            display.classList.remove('text-red-600');
            display.classList.add('text-indigo-600');
        } else {
            button.disabled = true;
            button.classList.add('opacity-50');
            if (totalCost > currentWallet) {
                 display.classList.add('text-red-600');
                 display.classList.remove('text-indigo-600');
            } else if (totalCost === 0) {
                 display.classList.remove('text-red-600');
                 display.classList.add('text-indigo-600');
            }
        }
    }

    // Call on page load to ensure initial state is correct
    document.addEventListener('DOMContentLoaded', calculateTotal);
    
    // Check URL for success message after form submission
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'success') {
        const key = urlParams.get('key');
        document.getElementById('keygen-status-text').textContent = `Key generated: ${key}. Balance deducted.`;
        document.getElementById('keygen-status-message').classList.remove('hidden');
    }
</script>
