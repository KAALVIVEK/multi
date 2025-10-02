<?php
include_once 'auth.php';
secure_page();

$current_user = get_current_user();
$user_id = $current_user['id'];
$transactions = [];
$error = '';

// --- Database Fetch Logic ---
if (!$pdo) {
    $error = "Database connection is not configured.";
} else {
    try {
        // Select transaction history for the current user, ordered by date descending
        $stmt = $pdo->prepare("SELECT id, type, amount, date FROM transactions WHERE user_id = ? ORDER BY date DESC");
        $stmt->execute([$user_id]);
        $transactions = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Could not load transaction history: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div id="content-transactions" class="p-6 md:p-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Wallet Transactions</h2>
    
    <?php if ($error): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md" role="alert">
            <p class="font-bold">Database Error</p>
            <p><?php echo htmlspecialchars($error); ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white p-8 rounded-xl card-shadow border border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-semibold text-gray-800">Transaction History</h3>
            <span class="text-lg font-bold text-indigo-600">Current Wallet: ₹<?php echo number_format($current_user['wallet_balance'], 2); ?></span>
        </div>

        <!-- Transaction Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">No transactions found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): 
                            $amount_class = ($tx['amount'] > 0) ? 'text-green-600' : 'text-red-600';
                            $signed_amount = ($tx['amount'] > 0) ? "+₹" . number_format($tx['amount'], 2) : "-₹" . number_format(abs($tx['amount']), 2);
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 text-sm text-indigo-600 font-mono">#<?php echo htmlspecialchars($tx['id']); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-500"><?php echo date("Y-m-d H:i", strtotime($tx['date'])); ?></td>
                            <td class="py-2 px-4 text-sm text-gray-700"><?php echo ucwords(str_replace('_', ' ', htmlspecialchars($tx['type']))); ?></td>
                            <td class="py-2 px-4 text-sm font-semibold text-right <?php echo $amount_class; ?>"><?php echo $signed_amount; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Placeholder -->
        <div class="mt-6 flex justify-between items-center text-sm text-gray-600 border-t pt-4">
            <span>Page 1 of 1</span>
            <div>
                <button class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-50" disabled>Prev</button>
                <button class="px-3 py-1 border rounded-lg hover:bg-gray-100 disabled:opacity-50" disabled>Next</button>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
