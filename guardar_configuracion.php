<?php

include("includes/verificar_admin.php");
include("database/conexion.php");
include("includes/auditoria.php");

$id_empresa =
$_SESSION['id_empresa'];

$sql = "

UPDATE configuracion

SET

limite_alcohol=?,
porcentaje_somnolencia=?,
tiempo_ojos_cerrados=?,
ip_esp32=?,
puerto_esp32=?,
puerto_arduino=?,
baudrate=?

WHERE id_empresa=?

";

$stmt =
$conexion->prepare($sql);

$stmt->bind_param(
"ddisssii",
$_POST['limite_alcohol'],
$_POST['porcentaje_somnolencia'],
$_POST['tiempo_ojos_cerrados'],
$_POST['ip_esp32'],
$_POST['puerto_esp32'],
$_POST['puerto_arduino'],
$_POST['baudrate'],
$id_empresa
);

$stmt->execute();

registrarAuditoria(
    $conexion,
    "Actualizó vehículo placa ".$placa,
    $_SESSION['id_usuario']
);

header(
"Location: configuracion.php?ok=1"
);

exit();

?>