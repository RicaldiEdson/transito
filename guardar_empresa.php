<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");
include("includes/auditoria.php");

$nombre     = trim($_POST['nombre']);
$nit        = trim($_POST['nit']);
$direccion  = trim($_POST['direccion']);
$telefono   = trim($_POST['telefono']);
$correo     = trim($_POST['correo']);


/*=====================================================
VALIDACIONES
=====================================================*/

if(
    empty($nombre) ||
    empty($nit) ||
    empty($direccion) ||
    empty($telefono) ||
    empty($correo)
){
    header("Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Debe%20completar%20todos%20los%20campos.&volver=nueva_empresa.php");
    exit();
}

if(!ctype_digit($nit))
{
    header("Location: mensaje.php?tipo=error&titulo=NIT%20inválido&mensaje=El%20NIT%20solo%20debe%20contener%20números.&volver=nueva_empresa.php");
    exit();
}

if(!ctype_digit($telefono))
{
    header("Location: mensaje.php?tipo=error&titulo=Teléfono%20inválido&mensaje=El%20teléfono%20solo%20debe%20contener%20números.&volver=nueva_empresa.php");
    exit();
}

if(!filter_var($correo,FILTER_VALIDATE_EMAIL))
{
    header("Location: mensaje.php?tipo=error&titulo=Correo%20inválido&mensaje=Debe%20ingresar%20un%20correo%20electrónico%20válido.&volver=nueva_empresa.php");
    exit();
}


/*=====================================================
VALIDAR NOMBRE
=====================================================*/

$sql = "
SELECT id_empresa
FROM empresas
WHERE nombre=?
LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s",$nombre);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=Empresa%20duplicada&mensaje=Ya%20existe%20una%20empresa%20registrada%20con%20ese%20nombre.&volver=nueva_empresa.php");
    exit();
}

$stmt->close();


/*=====================================================
VALIDAR NIT
=====================================================*/

$sql = "
SELECT id_empresa
FROM empresas
WHERE nit=?
LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s",$nit);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=NIT%20duplicado&mensaje=El%20NIT%20ya%20se%20encuentra%20registrado.&volver=nueva_empresa.php");
    exit();
}

$stmt->close();


/*=====================================================
VALIDAR CORREO
=====================================================*/

$sql = "
SELECT id_empresa
FROM empresas
WHERE correo=?
LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("s",$correo);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=Correo%20duplicado&mensaje=El%20correo%20electrónico%20ya%20está%20registrado.&volver=nueva_empresa.php");
    exit();
}

$stmt->close();


/*=====================================================
INSERTAR EMPRESA
=====================================================*/

$sql = "
INSERT INTO empresas
(
nombre,
nit,
direccion,
telefono,
correo
)
VALUES
(
?,
?,
?,
?,
?
)
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
"sssss",
$nombre,
$nit,
$direccion,
$telefono,
$correo
);

if($stmt->execute())
{

    registrarAuditoria(
        $conexion,
        "Registró empresa ".$nombre,
        $_SESSION['id_usuario']
    );

    header("Location: mensaje.php?tipo=success&titulo=Empresa%20registrada&mensaje=La%20empresa%20se%20registró%20correctamente.&volver=empresas.php");
    exit();

}
else
{

    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=Ocurrió%20un%20error%20al%20registrar%20la%20empresa.&volver=nueva_empresa.php");
    exit();

}

?>