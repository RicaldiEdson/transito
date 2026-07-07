<?php

include("database/conexion.php");
include("includes/auditoria.php");

include("includes/verificar_sesion.php");

$id = $_GET['id'];

$sql = "
SELECT
    nombre,
    apellido,
    ci,
    licencia
FROM conductores
WHERE id_conductor=?
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "i",
    $id
);

$stmt->execute();

$conductor =
$stmt
->get_result()
->fetch_assoc();




$sql =
"
UPDATE asignacion_conductor
SET
estado='Finalizada',
fecha_fin=NOW()
WHERE id_conductor=?
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
UPDATE conductores
SET estado='Inactivo'
WHERE id_conductor=?
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
    "Eliminó conductor ".$conductor['nombre'],
    $_SESSION['id_usuario']
);
header(
"Location: conductores.php"
);

?>