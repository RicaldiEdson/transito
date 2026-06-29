<?php

include("includes/verificar_admin.php");
include("database/conexion.php");
include("includes/auditoria.php");

/*====================================================
OBTENER DATOS
====================================================*/

$id_empresa = $_SESSION['id_empresa'];

$ci        = trim($_POST['ci']);
$nombre    = trim($_POST['nombre']);
$apellido  = trim($_POST['apellido']);
$telefono  = trim($_POST['telefono']);
$direccion = trim($_POST['direccion']);
$licencia  = trim($_POST['licencia']);
$categoria = trim($_POST['categoria']);


/*====================================================
VALIDACIONES
====================================================*/

if(
    empty($ci) ||
    empty($nombre) ||
    empty($apellido) ||
    empty($telefono) ||
    empty($direccion) ||
    empty($licencia) ||
    empty($categoria)
)
{
    header("Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Todos%20los%20campos%20son%20obligatorios.&volver=nuevo_conductor.php");
    exit();
}

if(!ctype_digit($ci))
{
    header("Location: mensaje.php?tipo=error&titulo=CI%20inválido&mensaje=El%20CI%20solo%20debe%20contener%20números.&volver=nuevo_conductor.php");
    exit();
}

if(!ctype_digit($telefono))
{
    header("Location: mensaje.php?tipo=error&titulo=Teléfono%20inválido&mensaje=El%20teléfono%20solo%20debe%20contener%20números.&volver=nuevo_conductor.php");
    exit();
}


/*====================================================
VALIDAR CI DUPLICADO
====================================================*/

$sql = "
SELECT id_conductor
FROM conductores
WHERE ci=?
AND id_empresa=?
LIMIT 1
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "si",
    $ci,
    $id_empresa
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0)
{
    header("Location: mensaje.php?tipo=error&titulo=CI%20duplicado&mensaje=El%20número%20de%20CI%20ya%20se%20encuentra%20registrado.&volver=nuevo_conductor.php");
    exit();
}

$stmt->close();


/*====================================================
VALIDAR LICENCIA DUPLICADA
====================================================*/

$sql = "
SELECT id_conductor
FROM conductores
WHERE licencia=?
AND id_empresa=?
LIMIT 1
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "si",
    $licencia,
    $id_empresa
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0)
{
    header("Location: mensaje.php?tipo=error&titulo=Licencia%20duplicada&mensaje=La%20licencia%20ya%20se%20encuentra%20registrada.&volver=nuevo_conductor.php");
    exit();
}

$stmt->close();

/*====================================================
VALIDAR CATEGORÍA
====================================================*/

$categoriasPermitidas = [
    "A",
    "B",
    "C",
    "M",
    "P"
];

if(!in_array($categoria,$categoriasPermitidas))
{
    header("Location: mensaje.php?tipo=error&titulo=Categoría%20inválida&mensaje=La%20categoría%20de%20licencia%20seleccionada%20no%20es%20válida.&volver=nuevo_conductor.php");
    exit();
}


/*====================================================
VALIDAR LONGITUD DEL CI
====================================================*/

if(strlen($ci) < 5 || strlen($ci) > 15)
{
    header("Location: mensaje.php?tipo=error&titulo=CI%20inválido&mensaje=El%20CI%20debe%20tener%20entre%205%20y%2015%20dígitos.&volver=nuevo_conductor.php");
    exit();
}


/*====================================================
VALIDAR LONGITUD DEL TELÉFONO
====================================================*/

if(!preg_match('/^[0-9]{8}$/', $telefono))
{
    header("Location: mensaje.php?tipo=error&titulo=Teléfono%20inválido&mensaje=El%20teléfono%20debe%20tener%208%20dígitos%20numéricos.&volver=nuevo_conductor.php");
    exit();
}

/*====================================================
VALIDAR LONGITUD LICENCIA
====================================================*/

if(strlen($licencia) < 5 || strlen($licencia) > 30)
{
    header("Location: mensaje.php?tipo=error&titulo=Licencia%20inválida&mensaje=El%20número%20de%20licencia%20no%20es%20válido.&volver=nuevo_conductor.php");
    exit();
}

/*====================================================
VALIDAR CI DUPLICADO
====================================================*/

$sql="
SELECT id_conductor
FROM conductores
WHERE ci=?
AND id_empresa=?
LIMIT 1
";

$stmt=$conexion->prepare($sql);

$stmt->bind_param(
"si",
$ci,
$id_empresa
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=CI%20duplicado&mensaje=Ya%20existe%20un%20conductor%20registrado%20con%20ese%20CI.&volver=nuevo_conductor.php");
    exit();
}

$stmt->close();


/*====================================================
VALIDAR LICENCIA DUPLICADA
====================================================*/

$sql="
SELECT id_conductor
FROM conductores
WHERE licencia=?
AND id_empresa=?
LIMIT 1
";

$stmt=$conexion->prepare($sql);

$stmt->bind_param(
"si",
$licencia,
$id_empresa
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=Licencia%20duplicada&mensaje=La%20licencia%20ya%20se%20encuentra%20registrada.&volver=nuevo_conductor.php");
    exit();
}

$stmt->close();

/*====================================================
REGISTRAR CONDUCTOR
====================================================*/

$sql="
INSERT INTO conductores
(
ci,
nombre,
apellido,
telefono,
direccion,
licencia,
categoria,
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
?,
?
)
";

$stmt=$conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20preparar%20la%20consulta.&volver=nuevo_conductor.php");
    exit();
}

$stmt->bind_param(
"sssssssi",
$ci,
$nombre,
$apellido,
$telefono,
$direccion,
$licencia,
$categoria,
$id_empresa
);

if(!$stmt->execute())
{

    /*
    ========================================
    SI POR ALGUNA RAZÓN LA BD DETECTA
    UN DUPLICADO (SEGURIDAD EXTRA)
    ========================================
    */

    if($conexion->errno==1062)
    {

        if(stripos($conexion->error,"uk_ci_empresa")!==false)
        {
            header("Location: mensaje.php?tipo=error&titulo=CI%20duplicado&mensaje=Ya%20existe%20un%20conductor%20registrado%20con%20ese%20CI.&volver=nuevo_conductor.php");
            exit();
        }

        if(stripos($conexion->error,"uk_licencia_empresa")!==false)
        {
            header("Location: mensaje.php?tipo=error&titulo=Licencia%20duplicada&mensaje=La%20licencia%20ya%20se%20encuentra%20registrada.&volver=nuevo_conductor.php");
            exit();
        }

    }

    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20registrar%20el%20conductor.&volver=nuevo_conductor.php");
    exit();

}


/*====================================================
AUDITORÍA
====================================================*/

registrarAuditoria(
    $conexion,
    "Registró conductor ".$nombre." ".$apellido,
    $_SESSION['id_usuario']
);


/*====================================================
MENSAJE DE ÉXITO
====================================================*/

header("Location: mensaje.php?tipo=success&titulo=Conductor%20registrado&mensaje=El%20conductor%20se%20registró%20correctamente.&volver=conductores.php");
exit();


$stmt->close();
$conexion->close();

?>