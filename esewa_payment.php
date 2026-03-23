<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['order_id']) || !isset($_SESSION['total'])) {
    header("Location: checkout.php");
    exit;
}

$order_id = $_SESSION['order_id'];
$total = $_SESSION['total'];

$product_code = "EPAYTEST";
$secret_key   = "8gBm/:&EnhH.1/q";

$transaction_uuid = "NIVARA_" . $order_id . "_" . time();

$total_amount = $total;

$base_url = "http://localhost/NIVARA/";

$success_url = $base_url . "esewa_success.php";
$failure_url = $base_url . "esewa_failed.php";

$signed_field_names = "total_amount,transaction_uuid,product_code";

$data = "total_amount=$total_amount,transaction_uuid=$transaction_uuid,product_code=$product_code";

$signature = base64_encode(
    hash_hmac('sha256', $data, $secret_key, true)
);

$stmt = $mysqli->prepare("UPDATE orders SET transaction_uuid=? WHERE id=?");
$stmt->bind_param("si", $transaction_uuid, $order_id);
$stmt->execute();
?>

<form id="esewaForm"
action="https://rc-epay.esewa.com.np/api/epay/main/v2/form"
method="POST">

<input type="hidden" name="amount" value="<?php echo $total_amount; ?>">
<input type="hidden" name="tax_amount" value="0">
<input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">
<input type="hidden" name="transaction_uuid" value="<?php echo $transaction_uuid; ?>">
<input type="hidden" name="product_code" value="<?php echo $product_code; ?>">
<input type="hidden" name="product_service_charge" value="0">
<input type="hidden" name="product_delivery_charge" value="0">
<input type="hidden" name="success_url" value="<?php echo $success_url; ?>">
<input type="hidden" name="failure_url" value="<?php echo $failure_url; ?>">
<input type="hidden" name="signed_field_names" value="<?php echo $signed_field_names; ?>">
<input type="hidden" name="signature" value="<?php echo $signature; ?>">

</form>

<script>
document.getElementById("esewaForm").submit();
</script>
