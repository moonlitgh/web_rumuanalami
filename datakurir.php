<?php
require_once('config.php');

// Get available couriers from database
$query = "SELECT * FROM settings WHERE setting_key='available_couriers'";
$result = $conn->query($query);
$couriers = explode(',', $result->fetch_assoc()['setting_value']);

echo "<option value=''>-- Pilih Kurir --</option>";
foreach($couriers as $courier) {
    $courier = trim($courier);
    echo "<option value='".strtolower($courier)."'>".strtoupper($courier)."</option>";
}
?>