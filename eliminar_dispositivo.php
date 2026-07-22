<?php

include("includes/verificar_admin.php");
include("database/conexion.php");
include("includes/auditoria.php");
include("includes/verificar_sesion.php");

$id =
$_GET['id'];

/*
=========================================
OBTENER DATOS DEL DISPOSITIVO
=========================================
*/

$stmtDatos = $conexion->prepare(
"
SELECT codigo
FROM dispositivos
WHERE id_dispositivo=?
"
);

$stmtDatos->bind_param(
"i",
$id
);

$stmtDatos->execute();

$datos = $stmtDatos
->get_result()
->fetch_assoc();

$codigo = $datos['codigo'];



$sql = "
SELECT
nombre,
apellido,
usuario
FROM usuarios
WHERE id_usuario=?
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$datos =
$stmt
->get_result()
->fetch_assoc();



$sql = "

UPDATE dispositivos

SET

estado='Offline',
id_vehiculo=NULL

WHERE id_dispositivo=?

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
    "Eliminó dispositivo ".$codigo,
    $_SESSION['id_usuario']
);

header(
"Location: dispositivos.php"
);

exit();

?>