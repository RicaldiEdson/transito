<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");
include("includes/auditoria.php");

/*
====================================================
CONTROL DE ACCESO
====================================================
*/

if(
    $_SESSION['id_rol'] != 1 &&
    $_SESSION['id_rol'] != 2
)
{
    header("Location: mensaje.php?tipo=error&titulo=Acceso%20denegado&mensaje=No%20tiene%20permisos%20para%20realizar%20esta%20acción.&volver=dashboard.php");
    exit();
}


/*
====================================================
OBTENER DATOS
====================================================
*/

$id_usuario = (int)$_POST['id_usuario'];

$nombre = trim($_POST['nombre']);
$apellido = trim($_POST['apellido']);
$usuario = trim($_POST['usuario']);
$password = trim($_POST['password']);
$correo = trim($_POST['correo']);
$estado = trim($_POST['estado']);

$id_rol = (int)$_POST['id_rol'];

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
    empty($correo) ||
    empty($estado)
)
{
    header("Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Todos%20los%20campos%20son%20obligatorios.&volver=editar_usuario.php?id=".$id_usuario);
    exit();
}

if(!filter_var($correo,FILTER_VALIDATE_EMAIL))
{
    header("Location: mensaje.php?tipo=error&titulo=Correo%20inválido&mensaje=Debe%20ingresar%20un%20correo%20electrónico%20válido.&volver=editar_usuario.php?id=".$id_usuario);
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
AND id_usuario<>?
LIMIT 1
";

$stmt=$conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20preparar%20la%20consulta.&volver=usuarios.php");
    exit();
}

$stmt->bind_param(
"si",
$usuario,
$id_usuario
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=Usuario%20duplicado&mensaje=El%20nombre%20de%20usuario%20ya%20está%20registrado.&volver=editar_usuario.php?id=".$id_usuario);
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
AND id_usuario<>?
LIMIT 1
";

$stmt=$conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20preparar%20la%20consulta.&volver=usuarios.php");
    exit();
}

$stmt->bind_param(
"si",
$correo,
$id_usuario
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=Correo%20duplicado&mensaje=El%20correo%20electrónico%20ya%20está%20registrado.&volver=editar_usuario.php?id=".$id_usuario);
    exit();
}

$stmt->close();

/*
====================================================
SI LA EMPRESA ESTÁ INACTIVA
EL USUARIO TAMBIÉN
====================================================
*/

if($id_empresa != NULL)
{

$sqlEmpresa="
SELECT estado
FROM empresas
WHERE id_empresa=?
LIMIT 1
";

$stmt=$conexion->prepare($sqlEmpresa);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20consultar%20la%20empresa.&volver=usuarios.php");
    exit();
}

$stmt->bind_param(
"i",
$id_empresa
);

$stmt->execute();

$resultadoEmpresa=$stmt->get_result();

if($resultadoEmpresa->num_rows>0)
{

    $empresa=$resultadoEmpresa->fetch_assoc();

    if($empresa['estado']=="Inactivo")
    {
        $estado="Inactivo";
    }

}

$stmt->close();

}


/*
====================================================
ENCRIPTAR CONTRASEÑA SOLO SI CAMBIÓ
====================================================
*/

if(!empty($password))
{

    $password=password_hash(
        $password,
        PASSWORD_DEFAULT
    );

}
else
{

$sql="
SELECT password
FROM usuarios
WHERE id_usuario=?
LIMIT 1
";

$stmt=$conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20obtener%20la%20contraseña%20actual.&volver=usuarios.php");
    exit();
}

$stmt->bind_param(
"i",
$id_usuario
);

$stmt->execute();

$resultado=$stmt->get_result();

if($resultado->num_rows==0)
{
    header("Location: mensaje.php?tipo=error&titulo=Usuario%20no%20encontrado&mensaje=No%20se%20encontró%20el%20usuario%20a%20actualizar.&volver=usuarios.php");
    exit();
}

$fila=$resultado->fetch_assoc();

$password=$fila['password'];

$stmt->close();

}


/*
====================================================
ACTUALIZAR USUARIO
====================================================
*/

$sql="
UPDATE usuarios
SET
nombre=?,
apellido=?,
usuario=?,
password=?,
correo=?,
estado=?,
id_rol=?,
id_empresa=?
WHERE id_usuario=?
";

$stmt=$conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20se%20pudo%20preparar%20la%20actualización.&volver=usuarios.php");
    exit();
}

$stmt->bind_param(
"ssssssiii",
$nombre,
$apellido,
$usuario,
$password,
$correo,
$estado,
$id_rol,
$id_empresa,
$id_usuario
);

/*
====================================================
EJECUTAR ACTUALIZACIÓN
====================================================
*/

if(!$stmt->execute())
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20actualizar%20el%20usuario.&volver=editar_usuario.php?id=".$id_usuario);
    exit();
}

$stmt->close();


/*
====================================================
AUDITORÍA
====================================================
*/

registrarAuditoria(
    $conexion,
    "Actualizó usuario ".$usuario,
    $_SESSION['id_usuario']
);


/*
====================================================
CERRAR CONEXIÓN
====================================================
*/

$conexion->close();


/*
====================================================
MENSAJE DE ÉXITO
====================================================
*/

header("Location: mensaje.php?tipo=success&titulo=Usuario%20actualizado&mensaje=El%20usuario%20se%20actualizó%20correctamente.&volver=usuarios.php");
exit();

?>