<?php

session_start();

include("../database/conexion.php");

$usuario = trim($_POST['usuario']);
$password = trim($_POST['password']);

$sql = "
SELECT
    u.*,
    r.nombre AS rol,
    e.estado AS estado_empresa
FROM usuarios u

INNER JOIN roles r
    ON u.id_rol = r.id_rol

LEFT JOIN empresas e
    ON u.id_empresa = e.id_empresa

WHERE
    u.usuario = ?
    AND u.estado = 'Activo'
    AND
    (
        u.id_rol = 1

        OR

        (
            u.id_empresa IS NOT NULL
            AND e.estado = 'Activo'
        )
    )

LIMIT 1
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("s",$usuario);

$stmt->execute();

$resultado = $stmt->get_result();

if($resultado->num_rows == 1)
{

    $datos = $resultado->fetch_assoc();

    /*
    ============================================
    VERIFICAR CONTRASEÑA ENCRIPTADA
    ============================================
    */

    if(password_verify($password,$datos['password']))
    {

        $_SESSION['id_usuario'] = $datos['id_usuario'];
        $_SESSION['nombre'] = $datos['nombre'];
        $_SESSION['apellido'] = $datos['apellido'];
        $_SESSION['usuario'] = $datos['usuario'];
        $_SESSION['correo'] = $datos['correo'];
        $_SESSION['id_rol'] = $datos['id_rol'];
        $_SESSION['id_empresa'] = $datos['id_empresa'];

        header("Location: ../dashboard.php");
        exit();

    }

}

header("Location: ../login.php?error=1");
exit();

?>