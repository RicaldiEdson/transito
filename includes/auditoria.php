<?php

function registrarAuditoria(
    $conexion,
    $accion,
    $idUsuario,
    $tablaAfectada = null,
    $registroAfectado = null,
    $detalle = null
)
{

    $sql = "
    INSERT INTO auditoria
    (
        accion,
        tabla_afectada,
        registro_afectado,
        detalle,
        id_usuario
    )
    VALUES
    (
        ?, ?, ?, ?, ?
    )
    ";

    $stmt = $conexion->prepare($sql);

    if(!$stmt)
    {
        error_log("Error al preparar auditoría: ".$conexion->error);
        return false;
    }

    $registroAfectado = ($registroAfectado !== null)
        ? (string)$registroAfectado
        : null;

    $stmt->bind_param(
        "ssssi",
        $accion,
        $tablaAfectada,
        $registroAfectado,
        $detalle,
        $idUsuario
    );

    if(!$stmt->execute())
    {
        error_log("Error al ejecutar auditoría: ".$stmt->error);
        $stmt->close();
        return false;
    }

    $stmt->close();

    return true;
}

?>