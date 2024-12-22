<?php
require_once 'config.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || !isset($data['product_code'])) {
        echo json_encode(["success" => false, "message" => "Invalid input data"]);
        exit;
    }

    $product_code = $data['product_code'];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE product_code = ?");
    $stmt->bind_param("s", $product_code);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $stmt = $conn->prepare(
            "UPDATE products SET
            product_name = ?,
            product_model = ?,
            production_date = ?,
            shelf_life = ?,
            expiration_date = ?,
            sticker_color = ?,
            reminder_date = ?,
            received_date = ?,
            quantity = ?,
            unit = ?,
            unit_cost = ?,
            sender_code = ?,
            sender_company = ?,
            recorder = ?,
            unit_price = ?,
            category = ?,
            updated_at = NOW()
        WHERE product_code = ?"
        );

        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $conn->error);
        }

        $product_model = $data['product_model'] ?? null;
        $production_date = $data['production_date'] ?? null;
        $shelf_life = $data['shelf_life'] ?? null;
        $expiration_date = $data['expiration_date'] ?? null;
        $sticker_color = $data['sticker_color'] ?? null;
        $reminder_date = $data['reminder_date'] ?? null;
        $received_date = $data['received_date'] ?? null;
        $quantity = $data['quantity'] ?? 0;
        $unit = $data['unit'] ?? null;
        $unit_cost = $data['unit_cost'] ?? 0.0;
        $sender_code = $data['sender_code'] ?? null;
        $sender_company = $data['sender_company'] ?? null;
        $recorder = $data['recorder'] ?? null;
        $unit_price = $data['unit_price'] ?? 0.0;
        $category = $data['category'] ?? null;

        $stmt->bind_param(
            "sssissssisdsssdss",
            $data['product_name'],
            $product_model,
            $production_date,
            $shelf_life,
            $expiration_date,
            $sticker_color,
            $reminder_date,
            $received_date,
            $quantity,
            $unit,
            $unit_cost,
            $sender_code,
            $sender_company,
            $recorder,
            $unit_price,
            $category,
            $product_code
        );

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }

        echo json_encode(["success" => true, "exists" => true, "message" => "Product updated successfully"]);
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO products (
                product_code, product_name, product_model, production_date, shelf_life,
                expiration_date, sticker_color, reminder_date, received_date, quantity,
                unit, unit_cost, sender_code, sender_company, recorder, unit_price, category, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
        );

        if (!$stmt) {
            throw new Exception("SQL Prepare Error: " . $conn->error);
        }

        $product_model = $data['product_model'] ?? null;
        $production_date = $data['production_date'] ?? null;
        $shelf_life = $data['shelf_life'] ?? null;
        $expiration_date = $data['expiration_date'] ?? null;
        $sticker_color = $data['sticker_color'] ?? null;
        $reminder_date = $data['reminder_date'] ?? null;
        $received_date = $data['received_date'] ?? null;
        $quantity = $data['quantity'] ?? 0;
        $unit = $data['unit'] ?? null;
        $unit_cost = $data['unit_cost'] ?? 0.0;
        $sender_code = $data['sender_code'] ?? null;
        $sender_company = $data['sender_company'] ?? null;
        $recorder = $data['recorder'] ?? null;
        $unit_price = $data['unit_price'] ?? 0.0;
        $category = $data['category'] ?? null;

        $stmt->bind_param(
            "ssssissssisdsssds",
            $data['product_code'],
            $data['product_name'],
            $product_model,
            $production_date,
            $shelf_life,
            $expiration_date,
            $sticker_color,
            $reminder_date,
            $received_date,
            $quantity,
            $unit,
            $unit_cost,
            $sender_code,
            $sender_company,
            $recorder,
            $unit_price,
            $category
        );

        if (!$stmt->execute()) {
            throw new Exception("SQL Execute Error: " . $stmt->error);
        }

        echo json_encode(["success" => true, "message" => "Product saved successfully"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
    exit;
}
