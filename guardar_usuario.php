<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");
include("includes/auditoria.php");

/*
====================================================
OBTENER DATOS
====================================================
*/

$nombre     = trim($_POST['nombre']);
$apellido   = trim($_POST['apellido']);
$usuario    = trim($_POST['usuario']);
$password   = trim($_POST['password']);
$correo     = trim($_POST['correo']);
$id_rol     = (int)$_POST['id_rol'];

$id_empresa = empty($_POST['id_empresa'])
    ? NULL
    : (int)$_POST['id_empresa'];


/*
====================================================
VALIDACIONES
====================================================
*/

if(
    empty($nombre) ||
    empty($apellido) ||
    empty($usuario) ||
    empty($password) ||
    empty($correo) ||
    empty($id_rol)
)
{
    header("Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Todos%20los%20campos%20son%20obligatorios.&volver=nuevo_usuario.php");
    exit();
}

if(!filter_var($correo,FILTER_VALIDATE_EMAIL))
{
    header("Location: mensaje.php?tipo=error&titulo=Correo%20inválido&mensaje=Debe%20ingresar%20un%20correo%20electrónico%20válido.&volver=nuevo_usuario.php");
    exit();
}


/*
====================================================
VALIDAR USUARIO DUPLICADO
====================================================
*/

$sql="
SELECT id_usuario
FROM usuarios
WHERE usuario=?
LIMIT 1
";

$stmt=$conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20preparar%20la%20consulta.&volver=usuarios.php");
    exit();
}

$stmt->bind_param("s",$usuario);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=Usuario%20duplicado&mensaje=El%20nombre%20de%20usuario%20ya%20existe.&volver=nuevo_usuario.php");
    exit();
}

$stmt->close();


/*
====================================================
VALIDAR CORREO DUPLICADO
====================================================
*/

$sql="
SELECT id_usuario
FROM usuarios
WHERE correo=?
LIMIT 1
";

$stmt=$conexion->prepare($sql);

$stmt->bind_param("s",$correo);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=Correo%20duplicado&mensaje=El%20correo%20electrónico%20ya%20se%20encuentra%20registrado.&volver=nuevo_usuario.php");
    exit();
}

$stmt->close();


/*
====================================================
ENCRIPTAR CONTRASEÑA
====================================================
*/

$passwordHash = password_hash($password,PASSWORD_DEFAULT);


/*
====================================================
GUARDAR USUARIO
====================================================
*/

$sql="
INSERT INTO usuarios
(
nombre,
apellido,
usuario,
password,
correo,
id_rol,
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

$stmt=$conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20preparar%20la%20consulta.&volver=usuarios.php");
    exit();
}

$stmt->bind_param(
"sssssii",
$nombre,
$apellido,
$usuario,
$passwordHash,
$correo,
$id_rol,
$id_empresa
);

if(!$stmt->execute())
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20registrar%20el%20usuario.&volver=nuevo_usuario.php");
    exit();
}


/*
====================================================
AUDITORÍA
====================================================
*/

registrarAuditoria(
    $conexion,
    "Registró usuario ".$usuario,
    $_SESSION['id_usuario']
);


/*
====================================================
MENSAJE DE ÉXITO
====================================================
*/

header("Location: mensaje.php?tipo=success&titulo=Usuario%20registrado&mensaje=El%20usuario%20se%20registró%20correctamente.&volver=usuarios.php");
exit();

?>