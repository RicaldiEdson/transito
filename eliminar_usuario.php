<?php

include("database/conexion.php");
include("includes/verificar_sesion.php");
include("includes/auditoria.php");

$id=
$_GET['id'];


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



$sql="
UPDATE usuarios
SET estado='Inactivo'
WHERE id_usuario=?
";

$stmt=
$conexion->prepare($sql);

$stmt->bind_param(
"i",
$id
);

$stmt->execute();

registrarAuditoria(
    $conexion,
    "Eliminó usuario ".$datos['nombre'],
    $_SESSION['id_usuario']
);

header(
"Location: usuarios.php"
);

?>