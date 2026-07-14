<?php

include("includes/verificar_admin.php");
include("database/conexion.php");
include("includes/auditoria.php");

/*====================================================
OBTENER DATOS
====================================================*/

$id_empresa = $_SESSION['id_empresa'];

$codigo = trim($_POST['codigo'] ?? '');
$tipo = trim($_POST['tipo'] ?? '');
$estado = trim($_POST['estado'] ?? '');
$ip = trim($_POST['ip'] ?? '');
$puerto = trim($_POST['puerto'] ?? '');
$id_vehiculo = (int)($_POST['id_vehiculo'] ?? 0);


/*====================================================
VALIDAR CAMPOS OBLIGATORIOS
====================================================*/

if(
    empty($codigo) ||
    empty($tipo) ||
    empty($estado) ||
    empty($ip) ||
    empty($puerto) ||
    empty($id_vehiculo)
)
{
    header("Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Todos%20los%20campos%20son%20obligatorios.&volver=nuevo_dispositivo.php");
    exit();
}


/*====================================================
VALIDAR IP
====================================================*/

if(!filter_var($ip,FILTER_VALIDATE_IP))
{
    header("Location: mensaje.php?tipo=error&titulo=IP%20inválida&mensaje=La%20dirección%20IP%20no%20es%20válida.&volver=nuevo_dispositivo.php");
    exit();
}


/*====================================================
VALIDAR PUERTO
====================================================*/

if(!is_numeric($puerto))
{
    header("Location: mensaje.php?tipo=error&titulo=Puerto%20inválido&mensaje=El%20puerto%20debe%20ser%20numérico.&volver=nuevo_dispositivo.php");
    exit();
}

if(
    $puerto < 1 ||
    $puerto > 65535
)
{
    header("Location: mensaje.php?tipo=error&titulo=Puerto%20inválido&mensaje=El%20puerto%20debe%20estar%20entre%201%20y%2065535.&volver=nuevo_dispositivo.php");
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
LIMIT 1
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20validar%20el%20código.&volver=nuevo_dispositivo.php");
    exit();
}

$stmt->bind_param(
"si",
$codigo,
$id_empresa
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    $stmt->close();

    header("Location: mensaje.php?tipo=error&titulo=Código%20duplicado&mensaje=Ya%20existe%20un%20dispositivo%20con%20ese%20código%20dentro%20de%20esta%20empresa.&volver=nuevo_dispositivo.php");
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
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20validar%20el%20vehículo.&volver=nuevo_dispositivo.php");
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

    header("Location: mensaje.php?tipo=error&titulo=Vehículo%20inválido&mensaje=El%20vehículo%20seleccionado%20no%20es%20válido.&volver=nuevo_dispositivo.php");
    exit();
}

$stmt->close();


/*====================================================
REGISTRAR DISPOSITIVO
====================================================*/

$sql = "
INSERT INTO dispositivos
(
codigo,
tipo,
ip,
puerto,
estado,
id_vehiculo,
id_empresa
)
VALUES
(
?,
?,
?,
?,
?,
?,
?
)
";

$stmt = $conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20preparar%20la%20consulta.&volver=nuevo_dispositivo.php");
    exit();
}

$stmt->bind_param(
"sssssii",
$codigo,
$tipo,
$ip,
$puerto,
$estado,
$id_vehiculo,
$id_empresa
);

if(!$stmt->execute())
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20registrar%20el%20dispositivo.&volver=nuevo_dispositivo.php");
    exit();
}


/*====================================================
AUDITORÍA
====================================================*/

registrarAuditoria(
    $conexion,
    "Registró dispositivo ".$codigo,
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

header("Location: mensaje.php?tipo=success&titulo=Dispositivo%20registrado&mensaje=El%20dispositivo%20se%20registró%20correctamente.&volver=dispositivos.php");
exit();

?>