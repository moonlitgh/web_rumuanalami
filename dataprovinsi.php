<?php
require_once('config.php');

// Get API Key from database
$query = "SELECT setting_value FROM settings WHERE setting_key='rajaongkir_key'";
$result = $conn->query($query);
$api_key = $result->fetch_assoc()['setting_value'];

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.rajaongkir.com/starter/province",
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "key: " . $api_key
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
    $array_response = json_decode($response,TRUE);
    $dataprovinsi = $array_response["rajaongkir"]["results"];

    echo "<option value=''>-- Pilih Provinsi --</option>";

    foreach ($dataprovinsi as $key => $tiap_provinsi) {
        echo "<option value='".$tiap_provinsi["province_id"]."' id_provinsi='".$tiap_provinsi["province_id"]."'>".$tiap_provinsi["province"]."</option>";
    }
}
?>
