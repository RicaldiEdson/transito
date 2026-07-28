<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");
include("includes/auditoria.php");

$id =
$_GET['id'];

/*
=========================================
OBTENER DATOS PARA AUDITORIA
=========================================
*/

$sqlDatos = "
SELECT
c.nombre,
c.apellido,
v.placa
FROM asignacion_conductor ac
INNER JOIN conductores c
ON ac.id_conductor = c.id_conductor
INNER JOIN vehiculos v
ON ac.id_vehiculo = v.id_vehiculo
WHERE ac.id_asignacion = ?
";

$stmtDatos = $conexion->prepare($sqlDatos);
$stmtDatos->bind_param("i", $id);
$stmtDatos->execute();

$datos = $stmtDatos
->get_result()
->fetch_assoc();

$sql =
"
UPDATE asignacion_conductor
SET
estado='Finalizada',
fecha_fin=NOW()
WHERE id_asignacion=?
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
    "Eliminó asignación de ".$datos['nombre']." ".$datos['apellido']." al vehículo ".$datos['placa'],
    $_SESSION['id_usuario']
);

header(
"Location: asignaciones.php"
);

?>