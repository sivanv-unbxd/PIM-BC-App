<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.bigcommerce.com/stores/37kiq2b0u6/v3/catalog/categories/tree",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "content-type: application/json",
    "x-auth-client: jugwb7cxqybgbz297n4mcmup01xrvq",
    "x-auth-token: e1bwvnf256qt7j0eq3ky1t8yd71fkcv"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  //echo $response;
}

		$responsecats = json_decode($response);

		$responsecats = json_decode(json_encode($responsecats->data), true);

echo '<pre>';
print_r($responsecats);
function myfunction($value,$key)
{
echo "The key $key has the value $value<br>";


}

array_walk_recursive($responsecats,"myfunction");


?>