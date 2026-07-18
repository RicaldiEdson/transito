<?php

include("includes/verificar_admin.php");
include("database/conexion.php");
include("includes/auditoria.php");

/*====================================================
OBTENER DATOS
====================================================*/

$id_dispositivo = (int)($_POST['id_dispositivo'] ?? 0);

$id_empresa = $_SESSION['id_empresa'];

$codigo = trim($_POST['codigo'] ?? '');
$tipo = trim($_POST['tipo'] ?? '');
$ip = trim($_POST['ip'] ?? '');
$puerto = trim($_POST['puerto'] ?? '');
$estado = trim($_POST['estado'] ?? '');
$id_vehiculo = (int)($_POST['id_vehiculo'] ?? 0);


/*====================================================
VALIDAR CAMPOS OBLIGATORIOS
====================================================*/

if(
    empty($codigo) ||
    empty($tipo) ||
    empty($ip) ||
    empty($puerto) ||
    empty($estado) ||
    empty($id_vehiculo)
)
{
    header("Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Todos%20los%20campos%20son%20obligatorios.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}


/*====================================================
VALIDAR IP
====================================================*/

if(!filter_var($ip,FILTER_VALIDATE_IP))
{
    header("Location: mensaje.php?tipo=error&titulo=IP%20inválida&mensaje=Ingrese%20una%20dirección%20IP%20válida.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}


/*====================================================
VALIDAR PUERTO
====================================================*/

if(
    !is_numeric($puerto) ||
    $puerto < 1 ||
    $puerto > 65535
)
{
    header("Location: mensaje.php?tipo=error&titulo=Puerto%20inválido&mensaje=El%20puerto%20debe%20estar%20entre%201%20y%2065535.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}


/*====================================================
VALIDAR CÓDIGO DUPLICADO
====================================================*/

$sql = "
SELECT id_dispositivo
FROM dispositivos
WHERE codigo=?
AND id_empresa=?
AND id_dispositivo<>?
LIMIT 1
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20validar%20el%20código.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}

$stmt->bind_param(
"sii",
$codigo,
$id_empresa,
$id_dispositivo
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    $stmt->close();

    header("Location: mensaje.php?tipo=error&titulo=Código%20duplicado&mensaje=El%20código%20del%20dispositivo%20ya%20existe%20en%20esta%20empresa.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}

$stmt->close();


/*====================================================
VALIDAR IP DUPLICADA
====================================================*/

$sql = "
SELECT id_dispositivo
FROM dispositivos
WHERE ip=?
AND id_empresa=?
AND id_dispositivo<>?
LIMIT 1
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20validar%20la%20dirección%20IP.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}

$stmt->bind_param(
"sii",
$ip,
$id_empresa,
$id_dispositivo
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    $stmt->close();

    header("Location: mensaje.php?tipo=error&titulo=IP%20duplicada&mensaje=La%20dirección%20IP%20ya%20está%20registrada%20en%20otro%20dispositivo.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}

$stmt->close();


/*====================================================
VALIDAR VEHÍCULO
====================================================*/

$sql = "
SELECT id_vehiculo
FROM vehiculos
WHERE id_vehiculo=?
AND id_empresa=?
LIMIT 1
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20validar%20el%20vehículo.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}

$stmt->bind_param(
"ii",
$id_vehiculo,
$id_empresa
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows==0)
{
    $stmt->close();

    header("Location: mensaje.php?tipo=error&titulo=Vehículo%20inválido&mensaje=El%20vehículo%20seleccionado%20no%20pertenece%20a%20su%20empresa.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}

$stmt->close();


/*====================================================
ACTUALIZAR DISPOSITIVO
====================================================*/

$sql = "
UPDATE dispositivos
SET
codigo=?,
tipo=?,
ip=?,
puerto=?,
estado=?,
id_vehiculo=?
WHERE id_dispositivo=?
AND id_empresa=?
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20preparar%20la%20actualización.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}

$stmt->bind_param(
"sssssiii",
$codigo,
$tipo,
$ip,
$puerto,
$estado,
$id_vehiculo,
$id_dispositivo,
$id_empresa
);

if(!$stmt->execute())
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20actualizar%20el%20dispositivo.&volver=editar_dispositivo.php?id=".$id_dispositivo);
    exit();
}


/*====================================================
AUDITORÍA
====================================================*/

registrarAuditoria(
    $conexion,
    "Actualizó dispositivo ".$codigo,
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

header("Location: mensaje.php?tipo=success&titulo=Dispositivo%20actualizado&mensaje=El%20dispositivo%20se%20actualizó%20correctamente.&volver=dispositivos.php");
exit();

?>