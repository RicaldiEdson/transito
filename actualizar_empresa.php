<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");
include("includes/auditoria.php");

if($_SESSION['id_rol'] != 1)
{
    header("Location: mensaje.php?tipo=error&titulo=Acceso%20denegado&mensaje=No%20tiene%20permisos%20para%20realizar%20esta%20acción.&volver=dashboard.php");
    exit();
}

$id_empresa = trim($_POST['id_empresa']);
$nombre     = trim($_POST['nombre']);
$nit        = trim($_POST['nit']);
$direccion  = trim($_POST['direccion']);
$telefono   = trim($_POST['telefono']);
$correo     = trim($_POST['correo']);
$estado     = trim($_POST['estado']);


/*====================================================
VALIDACIONES
====================================================*/

if(
    empty($nombre) ||
    empty($nit) ||
    empty($direccion) ||
    empty($telefono) ||
    empty($correo) ||
    empty($estado)
)
{
    header("Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Todos%20los%20campos%20son%20obligatorios.&volver=editar_empresa.php?id=".$id_empresa);
    exit();
}

if(!ctype_digit($nit))
{
    header("Location: mensaje.php?tipo=error&titulo=NIT%20inválido&mensaje=El%20NIT%20solo%20debe%20contener%20números.&volver=editar_empresa.php?id=".$id_empresa);
    exit();
}

if(!ctype_digit($telefono))
{
    header("Location: mensaje.php?tipo=error&titulo=Teléfono%20inválido&mensaje=El%20teléfono%20solo%20debe%20contener%20números.&volver=editar_empresa.php?id=".$id_empresa);
    exit();
}

if(!filter_var($correo,FILTER_VALIDATE_EMAIL))
{
    header("Location: mensaje.php?tipo=error&titulo=Correo%20inválido&mensaje=Debe%20ingresar%20un%20correo%20electrónico%20válido.&volver=editar_empresa.php?id=".$id_empresa);
    exit();
}


/*====================================================
VALIDAR NOMBRE DUPLICADO
====================================================*/

$sql = "
SELECT id_empresa
FROM empresas
WHERE nombre=?
AND id_empresa<>?
LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("si",$nombre,$id_empresa);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=Empresa%20duplicada&mensaje=Ya%20existe%20otra%20empresa%20registrada%20con%20ese%20nombre.&volver=editar_empresa.php?id=".$id_empresa);
    exit();
}

$stmt->close();


/*====================================================
VALIDAR NIT DUPLICADO
====================================================*/

$sql = "
SELECT id_empresa
FROM empresas
WHERE nit=?
AND id_empresa<>?
LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("si",$nit,$id_empresa);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=NIT%20duplicado&mensaje=El%20NIT%20ya%20se%20encuentra%20registrado.&volver=editar_empresa.php?id=".$id_empresa);
    exit();
}

$stmt->close();


/*====================================================
VALIDAR CORREO DUPLICADO
====================================================*/

$sql = "
SELECT id_empresa
FROM empresas
WHERE correo=?
AND id_empresa<>?
LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("si",$correo,$id_empresa);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=Correo%20duplicado&mensaje=El%20correo%20electrónico%20ya%20está%20registrado.&volver=editar_empresa.php?id=".$id_empresa);
    exit();
}

$stmt->close();


/*====================================================
ACTUALIZAR EMPRESA
====================================================*/

$sql = "
UPDATE empresas
SET
nombre=?,
nit=?,
direccion=?,
telefono=?,
correo=?,
estado=?
WHERE id_empresa=?
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
"ssssssi",
$nombre,
$nit,
$direccion,
$telefono,
$correo,
$estado,
$id_empresa
);

if(!$stmt->execute())
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20actualizar%20la%20empresa.&volver=editar_empresa.php?id=".$id_empresa);
    exit();
}


/*====================================================
EMPRESA INACTIVA
====================================================*/

if($estado == "Inactivo")
{

$conexion->query("
UPDATE usuarios
SET estado='Inactivo'
WHERE id_empresa=$id_empresa
");

$conexion->query("
UPDATE conductores
SET estado='Inactivo'
WHERE id_empresa=$id_empresa
");

$conexion->query("
UPDATE vehiculos
SET estado='Inactivo'
WHERE id_empresa=$id_empresa
");

$conexion->query("
UPDATE dispositivos
SET estado='Offline'
WHERE id_empresa=$id_empresa
");

$conexion->query("
UPDATE asignacion_conductor
SET
estado='Finalizada',
fecha_fin=NOW()
WHERE estado='Activa'
AND id_vehiculo IN
(
SELECT id_vehiculo
FROM vehiculos
WHERE id_empresa=$id_empresa
)
");

}


/*====================================================
EMPRESA ACTIVA
====================================================*/

if($estado == "Activo")
{

$conexion->query("
UPDATE usuarios
SET estado='Activo'
WHERE id_empresa=$id_empresa
");

$conexion->query("
UPDATE conductores
SET estado='Activo'
WHERE id_empresa=$id_empresa
");

$conexion->query("
UPDATE vehiculos
SET estado='Activo'
WHERE id_empresa=$id_empresa
");

}


/*====================================================
AUDITORÍA
====================================================*/

registrarAuditoria(
    $conexion,
    "Actualizó empresa ".$nombre,
    $_SESSION['id_usuario']
);


/*====================================================
MENSAJE DE ÉXITO
====================================================*/

header("Location: mensaje.php?tipo=success&titulo=Empresa%20actualizada&mensaje=La%20empresa%20se%20actualizó%20correctamente.&volver=empresas.php");
exit();

?>