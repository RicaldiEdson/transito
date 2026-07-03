<?php

include("includes/verificar_admin.php");
include("database/conexion.php");
include("includes/auditoria.php");


/*====================================================
OBTENER DATOS
====================================================*/

$id = (int)$_POST['id_conductor'];

$id_empresa = $_SESSION['id_empresa'];

$ci = trim($_POST['ci']);
$nombre = trim($_POST['nombre']);
$apellido = trim($_POST['apellido']);
$telefono = trim($_POST['telefono']);
$direccion = trim($_POST['direccion']);
$licencia = trim($_POST['licencia']);
$categoria = trim($_POST['categoria']);
$estado = trim($_POST['estado']);


/*====================================================
VALIDAR CAMPOS OBLIGATORIOS
====================================================*/

if(
    empty($ci) ||
    empty($nombre) ||
    empty($apellido) ||
    empty($telefono) ||
    empty($direccion) ||
    empty($licencia) ||
    empty($categoria) ||
    empty($estado)
)
{
    header("Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Todos%20los%20campos%20son%20obligatorios.&volver=editar_conductor.php?id=".$id);
    exit();
}


/*====================================================
VALIDAR CI
====================================================*/

if(!ctype_digit($ci))
{
    header("Location: mensaje.php?tipo=error&titulo=CI%20inválido&mensaje=El%20CI%20solo%20debe%20contener%20números.&volver=editar_conductor.php?id=".$id);
    exit();
}


/*====================================================
VALIDAR TELÉFONO
====================================================*/

if(!preg_match('/^[0-9]{8}$/',$telefono))
{
    header("Location: mensaje.php?tipo=error&titulo=Teléfono%20inválido&mensaje=El%20teléfono%20debe%20tener%208%20dígitos.&volver=editar_conductor.php?id=".$id);
    exit();
}


/*====================================================
VALIDAR LONGITUD CI
====================================================*/

if(strlen($ci)<5 || strlen($ci)>15)
{
    header("Location: mensaje.php?tipo=error&titulo=CI%20inválido&mensaje=El%20CI%20no%20tiene%20un%20formato%20válido.&volver=editar_conductor.php?id=".$id);
    exit();
}


/*====================================================
VALIDAR LICENCIA
====================================================*/

if(strlen($licencia)<5 || strlen($licencia)>30)
{
    header("Location: mensaje.php?tipo=error&titulo=Licencia%20inválida&mensaje=El%20número%20de%20licencia%20no%20es%20válido.&volver=editar_conductor.php?id=".$id);
    exit();
}


/*====================================================
VALIDAR CATEGORÍA
====================================================*/

$categoriasPermitidas=[
"A",
"B",
"C",
"M",
"P"
];

if(!in_array($categoria,$categoriasPermitidas))
{
    header("Location: mensaje.php?tipo=error&titulo=Categoría%20inválida&mensaje=Seleccione%20una%20categoría%20válida.&volver=editar_conductor.php?id=".$id);
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
AND id_conductor<>?
LIMIT 1
";

$stmt=$conexion->prepare($sql);

$stmt->bind_param(
"sii",
$ci,
$id_empresa,
$id
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=CI%20duplicado&mensaje=Ya%20existe%20otro%20conductor%20registrado%20con%20ese%20CI.&volver=editar_conductor.php?id=".$id);
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
AND id_conductor<>?
LIMIT 1
";

$stmt=$conexion->prepare($sql);

$stmt->bind_param(
"sii",
$licencia,
$id_empresa,
$id
);

$stmt->execute();
$stmt->store_result();

if($stmt->num_rows>0)
{
    header("Location: mensaje.php?tipo=error&titulo=Licencia%20duplicada&mensaje=La%20licencia%20ya%20se%20encuentra%20registrada.&volver=editar_conductor.php?id=".$id);
    exit();
}

$stmt->close();

/*====================================================
ACTUALIZAR CONDUCTOR
====================================================*/

$sql="

UPDATE conductores
SET
ci=?,
nombre=?,
apellido=?,
telefono=?,
direccion=?,
licencia=?,
categoria=?,
estado=?
WHERE id_conductor=?
AND id_empresa=?

";

$stmt=$conexion->prepare($sql);

if(!$stmt)
{
    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20preparar%20la%20consulta.&volver=editar_conductor.php?id=".$id);
    exit();
}

$stmt->bind_param(
"ssssssssii",
$ci,
$nombre,
$apellido,
$telefono,
$direccion,
$licencia,
$categoria,
$estado,
$id,
$id_empresa
);

if(!$stmt->execute())
{

    if($conexion->errno==1062)
    {

        if(stripos($conexion->error,"uk_ci_empresa")!==false)
        {
            header("Location: mensaje.php?tipo=error&titulo=CI%20duplicado&mensaje=Ya%20existe%20otro%20conductor%20registrado%20con%20ese%20CI.&volver=editar_conductor.php?id=".$id);
            exit();
        }

        if(stripos($conexion->error,"uk_licencia_empresa")!==false)
        {
            header("Location: mensaje.php?tipo=error&titulo=Licencia%20duplicada&mensaje=La%20licencia%20ya%20se%20encuentra%20registrada.&volver=editar_conductor.php?id=".$id);
            exit();
        }

    }

    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20actualizar%20el%20conductor.&volver=editar_conductor.php?id=".$id);
    exit();

}

/*====================================================
AUDITORÍA
====================================================*/

registrarAuditoria(
    $conexion,
    "Actualizó conductor ".$nombre." ".$apellido,
    $_SESSION['id_usuario']
);


/*====================================================
MENSAJE DE ÉXITO
====================================================*/

header("Location: mensaje.php?tipo=success&titulo=Conductor%20actualizado&mensaje=El%20conductor%20se%20actualizó%20correctamente.&volver=conductores.php");
exit();

$stmt->close();
$conexion->close();

?>