<?php

include("database/conexion.php");
include("includes/auditoria.php");
include("includes/verificar_sesion.php");

$id = $_GET['id'];

$sql = "
SELECT
    placa,
    marca,
    modelo,
    anio
FROM vehiculos
WHERE id_vehiculo=?
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$vehiculo =
$stmt
->get_result()
->fetch_assoc();

$sql =
"
UPDATE asignacion_conductor
SET
estado='Finalizada',
fecha_fin=NOW()
WHERE id_vehiculo=?
AND estado='Activa'
";

$stmt =
$conexion->prepare($sql);

$stmt->bind_param(
"i",
$id
);

$stmt->execute();

$sql =
"
UPDATE vehiculos
SET estado='Inactivo'
WHERE id_vehiculo=?
";

$stmt =
$conexion->prepare($sql);

$stmt->bind_param(
"i",
$id
);

$stmt->execute();

registrarAuditoria(
    $conexion,
    "Eliminó vehículo ".$vehiculo['placa'],
    $_SESSION['id_usuario']
);

header(
"Location: vehiculos.php"
);

?>