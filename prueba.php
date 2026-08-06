<?php

$url =
"http://localhost/policiatransito/api/recibir_datos.php";

$datos = [

"codigo" => "123456",
"alcohol" => "8.00",
"somnolencia" => "96"

];

$ch = curl_init();

curl_setopt(
$ch,
CURLOPT_URL,
$url
);

curl_setopt(
$ch,
CURLOPT_POST,
true
);

curl_setopt(
$ch,
CURLOPT_POSTFIELDS,
http_build_query($datos)
);

curl_setopt(
$ch,
CURLOPT_RETURNTRANSFER,
true
);

$respuesta =
curl_exec($ch);

if(curl_errno($ch))
{
    echo "Error CURL: " .
    curl_error($ch);
}
else
{
    echo "<pre>";
    echo $respuesta;
    echo "</pre>";
}

curl_close($ch);

?>