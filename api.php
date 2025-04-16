<?php
require_once 'vendor/autoload.php';  // Make sure Composer dependencies are loaded
require_once 'config.php';
require_once 'Model/MySQLHandler.php'; 

$db = new MySQLHandler("products");  

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $productData = $db->get_record_by_id($_GET['id']);
            echo json_encode($productData);
        } else {
            $products = $db->get_data(); // Get all products
            echo json_encode($products);
        }
        break;

    case 'POST':
        $inputData = json_decode(file_get_contents('php://input'), true);
        if (isset($inputData['name'], $inputData['price'], $inputData['units_in_stock'])) {
            $newProduct = [
                'name' => $inputData['name'],
                'price' => $inputData['price'],
                'units_in_stock' => $inputData['units_in_stock']
            ];
            $db->save($newProduct);
            echo json_encode(["message" => "Product created successfully"]);
        } else {
            echo json_encode(["message" => "Invalid input"]);
        }
        break;

    case 'PUT':
        if (isset($_GET['id'])) {
            $inputData = json_decode(file_get_contents('php://input'), true);
            $updatedProduct = [
                'name' => $inputData['name'],
                'price' => $inputData['price'],
                'units_in_stock' => $inputData['units_in_stock']
            ];
            $db->update($updatedProduct, $_GET['id']);
            echo json_encode(["message" => "Product updated successfully"]);
        } else {
            echo json_encode(["message" => "Product ID is required"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $db->delete($_GET['id']);
            echo json_encode(["message" => "Product deleted successfully"]);
        } else {
            echo json_encode(["message" => "Product ID is required"]);
        }
        break;

    default:
        echo json_encode(["message" => "Invalid request method"]);
        break;
}
