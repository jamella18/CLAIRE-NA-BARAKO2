<?php
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login(ROLE_ADMIN);

$metrics = [
    'sales' => db()->query('SELECT COALESCE(SUM(total),0) AS total FROM orders WHERE payment_status = "paid"')->fetch()['total'],
    'orders' => db()->query('SELECT COUNT(*) AS c FROM orders')->fetch()['c'],
    'customers' => db()->query('SELECT COUNT(*) AS c FROM users WHERE role = "customer"')->fetch()['c'],
];
?>

<section class="card">
    <h1>Admin Dashboard</h1>
    <div class="grid grid-3">
        <div class="card">
            <h3>Sales</h3>
            <p><?php echo format_currency((float) $metrics['sales']); ?></p>
        </div>
        <div class="card">
            <h3>Orders</h3>
            <p><?php echo $metrics['orders']; ?></p>
        </div>
        <div class="card">
            <h3>Customers</h3>
            <p><?php echo $metrics['customers']; ?></p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>

