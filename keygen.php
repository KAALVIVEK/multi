<?php
include_once 'auth.php';
secure_page();

// Get current user data
$current_user = get_current_user();
$current_wallet = $current_user['wallet_balance'];

// --- Key Generation/Purchase Logic ---
$status_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_key') {
    // Collect and sanitize inputs (client-side validation is supported by JavaScript)
    $product_id = sanitize_input($_POST['product']);
    $duration = sanitize_input($_POST['duration']);
    $quantity = (int)sanitize_input($_POST['quantity']);
    $total_amount = (float)sanitize_input($_POST['total_amount']);

    if ($quantity <= 0 || $total_amount <= 0) {
        $error_message = "Invalid quantity or amount.";
    } elseif ($total_amount > $current_wallet) {
        $error_message = "Insufficient wallet balance. Please deposit funds.";
    } else {
        // --- Call API to process transaction and key creation ---
        // NOTE: In a real app, you would pass these variables to api.php for DB safety.
        
        // Mock Key Generation and Transaction Success:
        $key_code = bin2hex(random_bytes(16));
        
        // Mock Deduction of Wallet Balance:
        $new_wallet = $current_wallet - $total_amount;
        
        // SUCCESS: Redirect with the generated key for display
        // In production, this data would come back from the api.php after DB insert/update.
        redirect('keygen.php?status=success&key=' . urlencode($key_code) . '&amount=' . $total_amount);
    }
}

// Check for success message after redirection
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $key = htmlspecialchars($_GET['key']);
    $amount_spent = htmlspecialchars($_GET['amount']);
    $status_message = "License key generated successfully! Key: **{$key}** (Cost: ₹{$amount_spent})";
}

// MOCK DATA for Dropdowns (In production, load this from the 'products' table via API)
$mock_products = [
    ['id' => 1, 'name' => 'WinStar PRO', 'base_price' => 15.00],
    ['id' => 2, 'name' => 'Nexus Elite', 'base_price' => 10.00],
];
$mock_durations = [
    ['id' => 'd1', 'label' => '1 Day', 'factor' => 1],
    ['id' => 'd7', 'label' => '7 Days', 'factor' => 4], // Price factor for longer duration
];

include 'includes/header.php';
?>

<div id="content-keygen" class="p-6 md:p-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Generate Keys</h2>
    
    <?php if ($status_message): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg shadow-md" role="alert">
            <p class="font-bold">Purchase Complete!</p>
            <p><?php echo $status_message; ?></p>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg shadow-md" role="alert">
            <p class="font-bold">Error!</p>
            <p><?php echo $error_message; ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white p-8 rounded-xl card-shadow max-w-lg mx-auto border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-gray-800">New Key Purchase</h3>
            <span id="currentWalletBalance" class="text-lg font-bold text-indigo-600">Wallet: ₹<?php echo number_format($current_wallet, 2); ?></span>
        </div>

        <form method="POST" action="keygen.php" id="keyGenForm">
            <input type="hidden" name="action" value="generate_key">
            <input type="hidden" name="total_amount" id="totalAmountInput" value="0.00">
            
            <div class="space-y-6">
                <!-- Select Product -->
                <div>
                    <label for="product" class="block text-sm font-medium text-gray-700 mb-2">Select Product</label>
                    <select id="product" name="product" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <option value="" data-price="0">Select product</option>
                        <?php foreach ($mock_products as $p): ?>
                            <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['base_price']; ?>">
                                <?php echo htmlspecialchars($p['name']); ?> (Base: ₹<?php echo number_format($p['base_price'], 2); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Select Duration -->
                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Select Duration</label>
                    <select id="duration" name="duration" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <option value="" data-factor="0">Select duration</option>
                        <?php foreach ($mock_durations as $d): ?>
                            <option value="<?php echo $d['id']; ?>" data-factor="<?php echo $d['factor']; ?>">
                                <?php echo htmlspecialchars($d['label']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Quantity -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Select Quantity</label>
                    <input type="number" id="quantity" name="quantity" value="1" min="1" required class="w-full border border-gray-300 rounded-lg p-3 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                </div>
                
                <!-- Total Amount Display -->
                <div class="bg-indigo-50 p-4 rounded-lg text-center border border-indigo-200">
                    <p class="text-lg font-semibold text-gray-800">Total Amount: <span id="totalAmountDisplay" class="text-indigo-700">₹0.00</span></p>
                </div>

                <button type="submit" id="purchaseButton" class="glowing-button w-full py-3 text-lg transition duration-200 shadow-md">
                    Purchase Key
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
    // Client-side calculator logic
    const walletBalance = <?php echo $current_wallet; ?>;

    function calculateTotal() {
        const productSelect = document.getElementById('product');
        const durationSelect = document.getElementById('duration');
        const quantityInput = document.getElementById('quantity');
        
        const selectedProductOption = productSelect.options[productSelect.selectedIndex];
        const selectedDurationOption = durationSelect.options[durationSelect.selectedIndex];

        const basePrice = parseFloat(selectedProductOption.getAttribute('data-price')) || 0;
        const durationFactor = parseFloat(selectedDurationOption.getAttribute('data-factor')) || 0;
        const quantity = parseInt(quantityInput.value) || 0;
        
        // Simple mock calculation: Price = Base * Factor * Quantity
        const total = basePrice * durationFactor * quantity;
        
        const displayElement = document.getElementById('totalAmountDisplay');
        const inputElement = document.getElementById('totalAmountInput');
        const buttonElement = document.getElementById('purchaseButton');
        
        displayElement.textContent = `₹${total.toFixed(2)}`;
        inputElement.value = total.toFixed(2);

        // --- Purchase Button & Color Logic ---
        
        // 1. Set text color based on affordability
        if (total > walletBalance) {
            displayElement.classList.add('text-red-600');
            displayElement.classList.remove('text-indigo-700');
        } else {
            displayElement.classList.add('text-indigo-700');
            displayElement.classList.remove('text-red-600');
        }

        // 2. Disable button if total is 0 or unaffordable
        if (total === 0 || total > walletBalance) {
            buttonElement.disabled = true;
            buttonElement.classList.add('opacity-50', 'cursor-not-allowed');
            buttonElement.classList.remove('glowing-button');
        } else {
            buttonElement.disabled = false;
            buttonElement.classList.remove('opacity-50', 'cursor-not-allowed');
            buttonElement.classList.add('glowing-button');
        }
    }

    // Attach listeners
    document.getElementById('product').addEventListener('change', calculateTotal);
    document.getElementById('duration').addEventListener('change', calculateTotal);
    document.getElementById('quantity').addEventListener('input', calculateTotal);

    // Initial calculation on load
    document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
