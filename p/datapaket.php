<?php
require_once('../config.php');

// Get API Key and origin city from database
$query = "SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('rajaongkir_key', 'shipping_origin')";
$result = $conn->query($query);

if (!$result) {
    die("Error querying database: " . $conn->error);
}

$settings = [];
while($row = $result->fetch_assoc()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Debug settings
if (!isset($settings['rajaongkir_key']) || !isset($settings['shipping_origin'])) {
    die("Settings tidak lengkap di database");
}

$api_key = trim($settings['rajaongkir_key']); // Pastikan tidak ada whitespace
$origin = trim($settings['shipping_origin']);

// Debug API key
if (empty($api_key)) {
    die("API key kosong");
}

// Validasi input
if (!isset($_POST["ekspedisi"]) || !isset($_POST["distrik"]) || !isset($_POST["berat"])) {
    die("Data tidak lengkap. Ekspedisi: " . isset($_POST["ekspedisi"]) . 
        ", Distrik: " . isset($_POST["distrik"]) . 
        ", Berat: " . isset($_POST["berat"]));
}

$ekspedisi = $_POST["ekspedisi"];
$distrik = $_POST["distrik"];
$berat = $_POST["berat"];

// Debug input values
echo "<!-- Debug Input: ekspedisi=$ekspedisi, distrik=$distrik, berat=$berat -->";

$curl = curl_init();

$postfields = http_build_query([
    'origin' => $origin,
    'destination' => $distrik,
    'weight' => $berat,
    'courier' => $ekspedisi
]);

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $postfields,
    CURLOPT_HTTPHEADER => array(
        "content-type: application/x-www-form-urlencoded",
        "key: " . $api_key
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

// Debug curl info
$info = curl_getinfo($curl);
echo "<!-- Debug Curl Info: " . print_r($info, true) . " -->";

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    $array_response = json_decode($response, TRUE);
    
    // Debug full response
    echo "<!-- Debug Response: " . print_r($array_response, true) . " -->";

    if (!isset($array_response['rajaongkir']['results'][0]['costs'])) {
        if (isset($array_response['rajaongkir']['status']['description'])) {
            die("Error Raja Ongkir: " . $array_response['rajaongkir']['status']['description']);
        }
        die("Tidak dapat memuat data paket");
    }

    $paket = $array_response["rajaongkir"]["results"][0]["costs"];

    echo "<option value=''>-- Pilih Paket --</option>";
    foreach ($paket as $key => $tiap_paket) {
        echo "<option 
            value='".$tiap_paket["service"]."'
            paket='".$tiap_paket["service"]."'
            ongkir='".$tiap_paket["cost"][0]["value"]."'
            etd='".$tiap_paket["cost"][0]["etd"]."'>";
        echo $tiap_paket["service"]." ";
        echo "Rp ".number_format($tiap_paket["cost"][0]["value"],0,',','.')." ";
        echo $tiap_paket["cost"][0]["etd"]." HARI";
        echo "</option>";
    }
}