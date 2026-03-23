<?php
session_start();
require_once __DIR__ . '/includes/db.php';

/*
|--------------------------------------------------------------------------
| Check if data exists
|--------------------------------------------------------------------------
*/

if (!isset($_GET['data'])) {

    header("Location: index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Decode eSewa Response
|--------------------------------------------------------------------------
*/

$response = json_decode(
    base64_decode($_GET['data']),
    true
);

if (
    !isset($response['transaction_uuid']) ||
    !isset($response['status'])
) {

    header("Location: index.php");
    exit;
}

$transaction_uuid = $response['transaction_uuid'];
$status = $response['status'];


/*
|--------------------------------------------------------------------------
| Process Only If Payment COMPLETE
|--------------------------------------------------------------------------
*/

if ($status === "COMPLETE") {

    // Start transaction for safety
    $mysqli->begin_transaction();

    try {

        // Get order
        $stmt = $mysqli->prepare(
            "SELECT id FROM orders 
             WHERE transaction_uuid=? 
             AND payment_status='Pending'"
        );

        $stmt->bind_param("s", $transaction_uuid);
        $stmt->execute();

        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if (!$order) {

            throw new Exception("Order not found or already processed.");
        }

        $order_id = $order['id'];

        // Get ordered items
        $items = $mysqli->prepare(
            "SELECT product_id, qty FROM order_items WHERE order_id=?"
        );

        $items->bind_param("i", $order_id);
        $items->execute();

        $items_result = $items->get_result();

        while ($item = $items_result->fetch_assoc()) {

            // Decrease stock
            $update_stock = $mysqli->prepare(
                "UPDATE products 
                 SET stock = stock - ? 
                 WHERE id=? AND stock >= ?"
            );

            $update_stock->bind_param(
                "iii",
                $item['qty'],
                $item['product_id'],
                $item['qty']
            );

            $update_stock->execute();

            if ($update_stock->affected_rows == 0) {

                throw new Exception("Insufficient stock.");
            }
        }

        // Update order status
        $update_order = $mysqli->prepare(
            "UPDATE orders 
             SET payment_status='Paid', status='Confirmed' 
             WHERE id=?"
        );

        $update_order->bind_param("i", $order_id);
        $update_order->execute();

        // Commit transaction
        $mysqli->commit();

        // Clear cart
        unset($_SESSION['cart']);

        header("Location: success.php?payment=eSewa");
        exit;

    } catch (Exception $e) {

        $mysqli->rollback();

        echo "Error processing order: " . $e->getMessage();
        exit;
    }

} else {

    header("Location: esewa_failed.php");
    exit;
}
