<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");
include("includes/auditoria.php");

/*====================================================
OBTENER DATOS
====================================================*/

$id_conductor = (int)($_POST['id_conductor'] ?? 0);
$id_vehiculo = (int)($_POST['id_vehiculo'] ?? 0);


/*====================================================
VALIDAR CAMPOS OBLIGATORIOS
====================================================*/

if(
    empty($id_conductor) ||
    empty($id_vehiculo)
)
{
    header("Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Debe%20seleccionar%20un%20conductor%20y%20un%20vehículo.&volver=nueva_asignacion.php");
    exit();
}


/*====================================================
OBTENER DATOS PARA AUDITORÍA
====================================================*/

$sql = "
SELECT
c.nombre,
c.apellido,
v.placa
FROM conductores c,
vehiculos v
WHERE c.id_conductor=?
AND v.id_vehiculo=?
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20obtener%20los%20datos%20de%20la%20asignación.&volver=nueva_asignacion.php");
    exit();
}

$stmt->bind_param(
"ii",
$id_conductor,
$id_vehiculo
);

$stmt->execute();

$datos = $stmt->get_result()->fetch_assoc();

$stmt->close();

if(!$datos)
{
    header("Location: mensaje.php?tipo=error&titulo=Datos%20inválidos&mensaje=El%20conductor%20o%20el%20vehículo%20seleccionado%20no%20existen.&volver=nueva_asignacion.php");
    exit();
}


/*====================================================
FINALIZAR ASIGNACIÓN ACTIVA DEL CONDUCTOR
====================================================*/

$sql = "
UPDATE asignacion_conductor
SET
estado='Finalizada',
fecha_fin=NOW()
WHERE id_conductor=?
AND estado='Activa'
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20actualizar%20la%20asignación%20del%20conductor.&volver=nueva_asignacion.php");
    exit();
}

$stmt->bind_param(
"i",
$id_conductor
);

if(!$stmt->execute())
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20finalizar%20la%20asignación%20del%20conductor.&volver=nueva_asignacion.php");
    exit();
}

$stmt->close();


/*====================================================
FINALIZAR ASIGNACIÓN ACTIVA DEL VEHÍCULO
====================================================*/

$sql = "
UPDATE asignacion_conductor
SET
estado='Finalizada',
fecha_fin=NOW()
WHERE id_vehiculo=?
AND estado='Activa'
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20actualizar%20la%20asignación%20del%20vehículo.&volver=nueva_asignacion.php");
    exit();
}

$stmt->bind_param(
"i",
$id_vehiculo
);

if(!$stmt->execute())
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20finalizar%20la%20asignación%20del%20vehículo.&volver=nueva_asignacion.php");
    exit();
}

$stmt->close();


/*====================================================
REGISTRAR ASIGNACIÓN
====================================================*/

$sql = "
INSERT INTO asignacion_conductor
(
id_conductor,
id_vehiculo
)
VALUES
(
?,
?
)
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20preparar%20la%20asignación.&volver=nueva_asignacion.php");
    exit();
}

$stmt->bind_param(
"ii",
$id_conductor,
$id_vehiculo
);

if(!$stmt->execute())
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20registrar%20la%20asignación.&volver=nueva_asignacion.php");
    exit();
}


/*====================================================
AUDITORÍA
====================================================*/

registrarAuditoria(
    $conexion,
    "Registró asignación de ".$datos['nombre']." ".$datos['apellido']." al vehículo ".$datos['placa'],
    $_SESSION['id_usuario']
);


/*====================================================
LIBERAR RECURSOS
====================================================*/

$stmt->close();
$conexion->close();


/*====================================================
MENSAJE DE ÉXITO
====================================================*/

header("Location: mensaje.php?tipo=success&titulo=Asignación%20registrada&mensaje=La%20asignación%20se%20registró%20correctamente.&volver=asignaciones.php");
exit();

?>