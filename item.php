<?php
error_reporting(-1);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');

require_once 'vendor/autoload.php';

$db = new MySQLHandler("products");
$products = $db->get_data();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Manager</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 30px;
        }

        h3 {
            color: #333;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-width: 500px;
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 10px;
        }

        select, button {
            padding: 10px;
            margin: 5px 5px 10px 0;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        .result-box {
            background-color: #fff;
            padding: 15px 20px;
            border-left: 5px solid #007bff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            max-width: 500px;
        }

        .query {
            font-family: monospace;
            color: #666;
            font-size: 14px;
            margin: 10px 0;
        }

        hr {
            margin: 30px 0;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>

<h2>Manage Products</h2>

<form method="POST">
    <label for="product">Choose a product:</label>
    <select name="product" id="product">
        <?php foreach ($products as $product): ?>
            <option value="<?= $product['id'] ?>">
                <?= htmlspecialchars($product['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>
    <button type="submit" name="action" value="get_info">Get Product Info</button>
    <button type="submit" name="action" value="delete">Delete Product</button>
    <button type="submit" name="action" value="update">Update Product</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['product'])) {
    $selectedProductId = $_POST['product'];
    $productData = $db->get_record_by_id($selectedProductId);

    echo "<div class='result-box'>";

    if ($productData) {
        $product = $productData[0];

        if ($_POST['action'] === 'get_info') {
            echo "<h3>Details for " . htmlspecialchars($product['name']) . ":</h3>";
            echo "<p>Price: " . htmlspecialchars($product['price']) . "</p>";
            echo "<p>Units in Stock: " . htmlspecialchars($product['units_in_stock']) . "</p>";
        }

        if ($_POST['action'] === 'delete') {
            $deleted = $db->delete($product['id']);
            echo $deleted
                ? "<p>Product '" . htmlspecialchars($product['name']) . "' has been deleted.</p>"
                : "<p> Error deleting product.</p>";
        }

        if ($_POST['action'] === 'update') {
            $newPrice = 150;
            $newStock = 20;

            $updatedValues = [
                'price' => $newPrice,
                'units_in_stock' => $newStock
            ];

            $updated = $db->update($updatedValues, $product['id']);
            echo $updated
                ? "<p> Product '" . htmlspecialchars($product['name']) . "' has been updated.</p>"
                : "<p> Error updating product.</p>";
        }
    } else {
        echo "<p>❌ Product not found.</p>";
    }

    echo "</div>";
}
?>

</body>
</html>
