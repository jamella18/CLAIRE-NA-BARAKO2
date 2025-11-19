<?php
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login(ROLE_ADMIN);

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int) ($_POST['inventory_id'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $threshold = (int) ($_POST['threshold'] ?? 0);

    $stmt = db()->prepare('UPDATE inventory SET stock = :stock, threshold = :threshold WHERE id = :id');
    $stmt->execute([':stock' => $stock, ':threshold' => $threshold, ':id' => $id]);
    $message = 'Inventory updated.';
}

$inventory = fetch_inventory();
?>

<section class="card">
    <h1>Inventory Management</h1>
    <?php if ($message): ?>
        <div class="alert" data-flash><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <table>
        <thead>
        <tr>
            <th>Product</th>
            <th>Stock</th>
            <th>Threshold</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($inventory as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                <td><?php echo $item['stock']; ?></td>
                <td><?php echo $item['threshold']; ?></td>
                <td>
                    <form method="post" class="inline-form">
                        <input type="hidden" name="inventory_id" value="<?php echo $item['id']; ?>">
                        <input type="number" name="stock" value="<?php echo $item['stock']; ?>">
                        <input type="number" name="threshold" value="<?php echo $item['threshold']; ?>">
                        <button class="btn-secondary" type="submit">Save</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>

