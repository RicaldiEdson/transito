<?php

include("includes/verificar_admin.php");
include("database/conexion.php");
include("includes/auditoria.php");

/*====================================================
OBTENER DATOS
====================================================*/

$id_vehiculo = (int)($_POST['id_vehiculo'] ?? 0);

$id_empresa = $_SESSION['id_empresa'];

$placa = strtoupper(trim($_POST['placa'] ?? ''));
$marca = trim($_POST['marca'] ?? '');
$modelo = trim($_POST['modelo'] ?? '');
$anio = trim($_POST['anio'] ?? '');
$color = trim($_POST['color'] ?? '');
$capacidad = trim($_POST['capacidad'] ?? '');
$estado = trim($_POST['estado'] ?? '');


/*====================================================
VALIDACIONES
====================================================*/

if(
    empty($placa) ||
    empty($marca) ||
    empty($modelo) ||
    empty($anio) ||
    empty($color) ||
    empty($capacidad) ||
    empty($estado)
)
{
    header("Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Todos%20los%20campos%20son%20obligatorios.&volver=editar_vehiculo.php?id=".$id_vehiculo);
    exit();
}


/*====================================================
PLACA
====================================================*/

if(strlen($placa) < 5 || strlen($placa) > 10)
{
    header("Location: mensaje.php?tipo=error&titulo=Placa%20inválida&mensaje=La%20placa%20debe%20tener%20entre%205%20y%2010%20caracteres.&volver=editar_vehiculo.php?id=".$id_vehiculo);
    exit();
}


/*====================================================
AÑO
====================================================*/

if(!ctype_digit($anio))
{
    header("Location: mensaje.php?tipo=error&titulo=Año%20inválido&mensaje=El%20año%20debe%20ser%20numérico.&volver=editar_vehiculo.php?id=".$id_vehiculo);
    exit();
}

$anioActual = date("Y");

if(
    $anio < 1950 ||
    $anio > ($anioActual + 1)
)
{
    header("Location: mensaje.php?tipo=error&titulo=Año%20inválido&mensaje=El%20año%20ingresado%20no%20es%20válido.&volver=editar_vehiculo.php?id=".$id_vehiculo);
    exit();
}


/*====================================================
CAPACIDAD
====================================================*/

if(!is_numeric($capacidad))
{
    header("Location: mensaje.php?tipo=error&titulo=Capacidad%20inválida&mensaje=La%20capacidad%20debe%20ser%20numérica.&volver=editar_vehiculo.php?id=".$id_vehiculo);
    exit();
}

if($capacidad <= 0)
{
    header("Location: mensaje.php?tipo=error&titulo=Capacidad%20inválida&mensaje=La%20capacidad%20debe%20ser%20mayor%20a%20cero.&volver=editar_vehiculo.php?id=".$id_vehiculo);
    exit();
}


/*====================================================
VALIDAR PLACA DUPLICADA
====================================================*/

$sql = "
SELECT id_vehiculo
FROM vehiculos
WHERE placa=?
AND id_empresa=?
AND id_vehiculo<>?
LIMIT 1
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20validar%20la%20placa.&volver=editar_vehiculo.php?id=".$id_vehiculo);
    exit();
}

$stmt->bind_param(
"sii",
$placa,
$id_empresa,
$id_vehiculo
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    $stmt->close();

    header("Location: mensaje.php?tipo=error&titulo=Placa%20duplicada&mensaje=La%20placa%20ya%20se%20encuentra%20registrada%20en%20otro%20vehículo.&volver=editar_vehiculo.php?id=".$id_vehiculo);
    exit();
}

$stmt->close();


/*====================================================
ACTUALIZAR VEHÍCULO
====================================================*/

$sql = "
UPDATE vehiculos
SET
placa=?,
marca=?,
modelo=?,
anio=?,
color=?,
capacidad=?,
estado=?
WHERE id_vehiculo=?
AND id_empresa=?
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20preparar%20la%20actualización.&volver=editar_vehiculo.php?id=".$id_vehiculo);
    exit();
}

$stmt->bind_param(
"sssisssii",
$placa,
$marca,
$modelo,
$anio,
$color,
$capacidad,
$estado,
$id_vehiculo,
$id_empresa
);

if(!$stmt->execute())
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20actualizar%20el%20vehículo.&volver=editar_vehiculo.php?id=".$id_vehiculo);
    exit();
}


/*====================================================
AUDITORÍA
====================================================*/

registrarAuditoria(
    $conexion,
    "Actualizó vehículo ".$placa,
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

header("Location: mensaje.php?tipo=success&titulo=Vehículo%20actualizado&mensaje=El%20vehículo%20se%20actualizó%20correctamente.&volver=vehiculos.php");
exit();

?>