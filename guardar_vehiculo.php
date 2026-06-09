<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");
include("includes/auditoria.php");


/*====================================================
VERIFICAR MÉTODO DE ENVÍO
====================================================*/

if($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Acceso%20inválido&mensaje=La%20solicitud%20no%20es%20válida.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
OBTENER DATOS DE SESIÓN
====================================================*/

$id_empresa =
$_SESSION['id_empresa'] ?? 0;

$id_usuario =
$_SESSION['id_usuario'] ?? 0;


/*====================================================
OBTENER DATOS DEL FORMULARIO
====================================================*/

$placa =
trim($_POST['placa'] ?? '');

$marca =
trim($_POST['marca'] ?? '');

$modelo =
trim($_POST['modelo'] ?? '');

$anio =
trim($_POST['anio'] ?? '');

$color =
trim($_POST['color'] ?? '');

$capacidad =
trim($_POST['capacidad'] ?? '');

$estado =
trim($_POST['estado'] ?? '');


/*====================================================
NORMALIZAR DATOS
====================================================*/

$placa =
strtoupper($placa);

$marca =
trim($marca);

$modelo =
trim($modelo);

$color =
trim($color);

$estado =
trim($estado);


/*====================================================
VALIDAR CAMPOS OBLIGATORIOS
====================================================*/

if(
    $placa === '' ||
    $marca === '' ||
    $modelo === '' ||
    $anio === '' ||
    $color === '' ||
    $capacidad === '' ||
    $estado === ''
)
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Campos%20incompletos&mensaje=Todos%20los%20campos%20son%20obligatorios.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
VALIDAR EMPRESA
====================================================*/

if($id_empresa <= 0)
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Sesión%20inválida&mensaje=No%20se%20pudo%20determinar%20la%20empresa%20del%20usuario.&volver=vehiculos.php"
    );

    exit();
}


/*====================================================
VALIDAR PLACA
====================================================*/

if(strlen($placa) < 5 || strlen($placa) > 15)
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Placa%20inválida&mensaje=La%20placa%20ingresada%20no%20es%20válida.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
VALIDAR FORMATO DE PLACA
====================================================*/

if(!preg_match('/^[A-Z0-9\-]+$/', $placa))
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Placa%20inválida&mensaje=La%20placa%20solo%20puede%20contener%20letras%2C%20números%20y%20guiones.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
VALIDAR AÑO
====================================================*/

if(!ctype_digit($anio))
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Año%20inválido&mensaje=El%20año%20debe%20ser%20numérico.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
CONVERTIR AÑO A ENTERO
====================================================*/

$anio =
(int)$anio;

$anioActual =
(int)date("Y") + 1;


/*====================================================
VALIDAR RANGO DEL AÑO
====================================================*/

if($anio < 1980 || $anio > $anioActual)
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Año%20inválido&mensaje=El%20año%20del%20vehículo%20no%20es%20válido.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
VALIDAR CAPACIDAD
====================================================*/

if(!is_numeric($capacidad))
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Capacidad%20inválida&mensaje=La%20capacidad%20debe%20ser%20numérica.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
CONVERTIR CAPACIDAD
====================================================*/

$capacidad =
(float)$capacidad;


/*====================================================
VALIDAR CAPACIDAD POSITIVA
====================================================*/

if($capacidad <= 0)
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Capacidad%20inválida&mensaje=La%20capacidad%20debe%20ser%20mayor%20a%20cero.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
VALIDAR ESTADO
====================================================*/

$estadosPermitidos = [

    "Activo",
    "Mantenimiento",
    "Inactivo"

];


if(!in_array($estado,$estadosPermitidos,true))
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Estado%20inválido&mensaje=Seleccione%20un%20estado%20válido.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
VERIFICAR PLACA DUPLICADA
====================================================*/

$sql = "
SELECT id_vehiculo
FROM vehiculos
WHERE placa = ?
AND id_empresa = ?
LIMIT 1
";


$stmt =
$conexion->prepare($sql);


if(!$stmt)
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20preparar%20la%20consulta.&volver=nuevo_vehiculo.php"
    );

    exit();
}


$stmt->bind_param(
    "si",
    $placa,
    $id_empresa
);


$stmt->execute();

$stmt->store_result();


/*====================================================
VALIDAR DUPLICADO
====================================================*/

if($stmt->num_rows > 0)
{
    $stmt->close();

    header(
        "Location: mensaje.php?tipo=error&titulo=Placa%20duplicada&mensaje=Ya%20existe%20un%20vehículo%20registrado%20con%20esa%20placa.&volver=nuevo_vehiculo.php"
    );

    exit();
}


$stmt->close();


/*====================================================
INSERTAR VEHÍCULO
====================================================*/

$sql = "
INSERT INTO vehiculos
(
    placa,
    marca,
    modelo,
    anio,
    color,
    capacidad,
    estado,
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


$stmt =
$conexion->prepare($sql);


if(!$stmt)
{
    header(
        "Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20preparar%20el%20registro%20del%20vehículo.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
ASIGNAR PARÁMETROS
====================================================*/

$stmt->bind_param(
    "sssisdsi",
    $placa,
    $marca,
    $modelo,
    $anio,
    $color,
    $capacidad,
    $estado,
    $id_empresa
);


/*====================================================
EJECUTAR REGISTRO
====================================================*/

if(!$stmt->execute())
{

    if($stmt->errno == 1062)
    {
        $stmt->close();

        header(
            "Location: mensaje.php?tipo=error&titulo=Placa%20duplicada&mensaje=Ya%20existe%20un%20vehículo%20registrado%20con%20esa%20placa.&volver=nuevo_vehiculo.php"
        );

        exit();
    }


    $stmt->close();

    header(
        "Location: mensaje.php?tipo=error&titulo=Error&mensaje=No%20fue%20posible%20registrar%20el%20vehículo.&volver=nuevo_vehiculo.php"
    );

    exit();
}


/*====================================================
CERRAR SENTENCIA
====================================================*/

$stmt->close();


/*====================================================
AUDITORÍA
====================================================*/

registrarAuditoria(
    $conexion,
    "Registró vehículo ".$placa,
    $id_usuario
);


/*====================================================
CERRAR CONEXIÓN
====================================================*/

$conexion->close();


/*====================================================
MENSAJE DE ÉXITO
====================================================*/

header(
    "Location: mensaje.php?tipo=success&titulo=Vehículo%20registrado&mensaje=El%20vehículo%20se%20registró%20correctamente.&volver=vehiculos.php"
);

exit();

?>