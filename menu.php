<?php
require_once __DIR__ . '/../includes/admin_header.php';
require_once __DIR__ . '/../includes/helpers.php';

require_login(ROLE_ADMIN);

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $category = trim($_POST['category'] ?? 'General');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $available = isset($_POST['is_available']) ? 1 : 0;

    if ($id) {
        $stmt = db()->prepare(
            'UPDATE products SET name = :name, price = :price, category = :category, description = :description, image = :image, is_available = :available WHERE id = :id'
        );
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':category' => $category,
            ':description' => $description,
            ':image' => $image,
            ':available' => $available,
            ':id' => $id,
        ]);
        $message = 'Product updated.';
        redirect_to('admin/menu.php');
    } else {
        $stmt = db()->prepare(
            'INSERT INTO products (name, price, category, description, image, is_available) VALUES (:name, :price, :category, :description, :image, :available)'
        );
        $stmt->execute([
            ':name' => $name,
            ':price' => $price,
            ':category' => $category,
            ':description' => $description,
            ':image' => $image,
            ':available' => $available,
        ]);
        $message = 'Product added.';
        redirect_to('admin/menu.php');
    }
}

$products = fetch_products();
$editingId = (int) ($_GET['edit'] ?? 0);
$editingProduct = null;

if ($editingId) {
    $stmt = db()->prepare('SELECT * FROM products WHERE id = :id');
    $stmt->execute([':id' => $editingId]);
    $editingProduct = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = db()->prepare('DELETE FROM products WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $message = 'Product deleted.';
    redirect_to('admin/menu.php');
}
?>

<section class="card">
    <h1>Menu Management</h1>
    <?php if ($message): ?>
        <div class="alert" data-flash><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post" class="form">
        <input type="hidden" name="id" value="<?php echo $editingProduct['id'] ?? ''; ?>">
        <label>Name<input type="text" name="name" required value="<?php echo htmlspecialchars($editingProduct['name'] ?? ''); ?>"></label>
        <label>Price<input type="number" step="0.01" name="price" required value="<?php echo $editingProduct['price'] ?? ''; ?>"></label>
        <label>Category<input type="text" name="category" required value="<?php echo htmlspecialchars($editingProduct['category'] ?? ''); ?>"></label>
        <label>Description<textarea name="description" rows="2"><?php echo htmlspecialchars($editingProduct['description'] ?? ''); ?></textarea></label>
        <label>Image Path<input type="text" name="image" value="<?php echo htmlspecialchars($editingProduct['image'] ?? ''); ?>" placeholder="assets/images/product.svg"></label>
        <label><input type="checkbox" name="is_available" <?php echo ($editingProduct['is_available'] ?? 1) ? 'checked' : ''; ?>> Available</label>
        <button class="btn" type="submit"><?php echo $editingProduct ? 'Update Product' : 'Add Product'; ?></button>
        <?php if ($editingProduct): ?>
            <a class="btn-secondary" href="<?php echo site_url('admin/menu.php'); ?>">Cancel</a>
        <?php endif; ?>
    </form>
</section>

<section class="card">
    <h2>Existing Products</h2>
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['category']); ?></td>
                <td><?php echo format_currency((float) $product['price']); ?></td>
                <td><?php echo $product['is_available'] ? 'Available' : 'Hidden'; ?></td>
                <td>
                    <a class="btn-secondary" href="<?php echo site_url('admin/menu.php?edit=' . $product['id']); ?>">Edit</a>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this product?');">
                        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                        <button class="btn-secondary" type="submit" name="delete" value="1">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>

