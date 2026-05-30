<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");
include("includes/auditoria.php");

/*
====================================================
SOLO SUPERADMINISTRADOR
====================================================
*/

if($_SESSION['id_rol'] != 1)
{
    header("Location: mensaje.php?tipo=error&titulo=Acceso%20denegado&mensaje=No%20tiene%20permisos%20para%20realizar%20esta%20acción.&volver=empresas.php");
    exit();
}

/*
====================================================
VALIDAR ID
====================================================
*/

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($id <= 0)
{
    header("Location: mensaje.php?tipo=error&titulo=Empresa%20inválida&mensaje=El%20identificador%20de%20la%20empresa%20no%20es%20válido.&volver=empresas.php");
    exit();
}

/*
====================================================
BUSCAR EMPRESA
====================================================
*/

$sql = "
SELECT
    nombre,
    estado
FROM empresas
WHERE id_empresa=?
LIMIT 1
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i",$id);
$stmt->execute();

$empresa = $stmt->get_result()->fetch_assoc();

$stmt->close();

if(!$empresa)
{
    header("Location: mensaje.php?tipo=error&titulo=Empresa%20no%20encontrada&mensaje=La%20empresa%20seleccionada%20no%20existe.&volver=empresas.php");
    exit();
}

if($empresa['estado']=="Inactivo")
{
    header("Location: mensaje.php?tipo=warning&titulo=Empresa%20ya%20desactivada&mensaje=La%20empresa%20ya%20se%20encuentra%20desactivada.&volver=empresas.php");
    exit();
}

/*
====================================================
INICIAR TRANSACCIÓN
====================================================
*/

$conexion->begin_transaction();

try
{

    /*
    =====================================
    DESACTIVAR EMPRESA
    =====================================
    */

    $sql="
    UPDATE empresas
    SET estado='Inactivo'
    WHERE id_empresa=?
    ";

    $stmt=$conexion->prepare($sql);
    $stmt->bind_param("i",$id);

    if(!$stmt->execute())
    {
        throw new Exception();
    }

    $stmt->close();


    /*
    =====================================
    DESACTIVAR USUARIOS
    =====================================
    */

    $sql="
    UPDATE usuarios
    SET estado='Inactivo'
    WHERE id_empresa=?
    ";

    $stmt=$conexion->prepare($sql);
    $stmt->bind_param("i",$id);

    if(!$stmt->execute())
    {
        throw new Exception();
    }

    $stmt->close();


    /*
    =====================================
    DESACTIVAR CONDUCTORES
    =====================================
    */

    $sql="
    UPDATE conductores
    SET estado='Inactivo'
    WHERE id_empresa=?
    ";

    $stmt=$conexion->prepare($sql);
    $stmt->bind_param("i",$id);

    if(!$stmt->execute())
    {
        throw new Exception();
    }

    $stmt->close();


    /*
    =====================================
    DESACTIVAR VEHÍCULOS
    =====================================
    */

    $sql="
    UPDATE vehiculos
    SET estado='Inactivo'
    WHERE id_empresa=?
    ";

    $stmt=$conexion->prepare($sql);
    $stmt->bind_param("i",$id);

    if(!$stmt->execute())
    {
        throw new Exception();
    }

    $stmt->close();


    /*
    =====================================
    DISPOSITIVOS OFFLINE
    =====================================
    */

    $sql="
    UPDATE dispositivos
    SET estado='Offline'
    WHERE id_empresa=?
    ";

    $stmt=$conexion->prepare($sql);
    $stmt->bind_param("i",$id);

    if(!$stmt->execute())
    {
        throw new Exception();
    }

    $stmt->close();


    /*
    =====================================
    FINALIZAR ASIGNACIONES
    =====================================
    */

    $sql="
    UPDATE asignacion_conductor
    SET
        estado='Finalizada',
        fecha_fin=NOW()
    WHERE estado='Activa'
    AND id_vehiculo IN
    (
        SELECT id_vehiculo
        FROM vehiculos
        WHERE id_empresa=?
    )
    ";

    $stmt=$conexion->prepare($sql);
    $stmt->bind_param("i",$id);

    if(!$stmt->execute())
    {
        throw new Exception();
    }

    $stmt->close();


    /*
    =====================================
    AUDITORÍA
    =====================================
    */

    registrarAuditoria(
        $conexion,
        "Desactivó empresa ".$empresa['nombre'],
        $_SESSION['id_usuario']
    );


    /*
    =====================================
    CONFIRMAR CAMBIOS
    =====================================
    */

    $conexion->commit();


    /*
    =====================================
    MENSAJE DE ÉXITO
    =====================================
    */

    header("Location: mensaje.php?tipo=success&titulo=Empresa%20desactivada&mensaje=La%20empresa%20fue%20desactivada%20correctamente.%20Todos%20sus%20usuarios,%20conductores,%20vehículos,%20dispositivos%20y%20asignaciones%20fueron%20actualizados.&volver=empresas.php");
    exit();

}
catch(Exception $e)
{

    /*
    =====================================
    DESHACER CAMBIOS
    =====================================
    */

    $conexion->rollback();

    header("Location: mensaje.php?tipo=error&titulo=Error&mensaje=Ocurrió%20un%20error%20durante%20la%20desactivación%20de%20la%20empresa.%20No%20se%20realizó%20ningún%20cambio.&volver=empresas.php");
    exit();

}

?>