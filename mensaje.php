<?php

$titulo = $_GET['titulo'] ?? 'Mensaje';
$mensaje = $_GET['mensaje'] ?? '';
$tipo = $_GET['tipo'] ?? 'info';
$volver = $_GET['volver'] ?? 'index.php';
$textoBoton = $_GET['textoBoton'] ?? 'Volver';

switch ($tipo) {

    case "success":
        $color = "success";
        $icono = "bi-check-circle-fill";
        break;

    case "warning":
        $color = "warning";
        $icono = "bi-exclamation-triangle-fill";
        break;

    case "danger":
    case "error":
        $color = "danger";
        $icono = "bi-x-octagon-fill";
        break;

    default:
        $color = "primary";
        $icono = "bi-info-circle-fill";
        break;
}

?>
<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>INCOS</title>

<link
href="assets/vendor/bootstrap/css/bootstrap.min.css"
rel="stylesheet">

<link
href="assets/vendor/bootstrap-icons/bootstrap-icons.css"
rel="stylesheet">

<style>

body{
    background:#eef2f6;
    font-family:'Segoe UI',sans-serif;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    width:100%;
    max-width:650px;
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 10px 35px rgba(0,0,0,.15);
}

.header{
    padding:25px;
    color:#fff;
    text-align:center;
    font-size:24px;
    font-weight:bold;
}

.header.success{
    background:#198754;
}

.header.warning{
    background:#ffc107;
    color:#212529;
}

.header.danger{
    background:#dc3545;
}

.header.primary{
    background:#0b6b3a;
}

.body{
    padding:35px;
    text-align:center;
}

.icono{
    font-size:80px;
    margin-bottom:20px;
}

.mensaje{
    font-size:18px;
    color:#444;
    line-height:1.6;
    margin-bottom:30px;
}

.btn-custom{
    padding:12px 35px;
    font-size:17px;
    border-radius:12px;
}

.footer{
    background:#f8f9fa;
    padding:15px;
    text-align:center;
    color:#777;
    font-size:13px;
}

</style>

</head>

<body>

<div class="card">

<div class="header <?php echo $color; ?>">

<?php echo htmlspecialchars($titulo); ?>

</div>

<div class="body">

<div class="icono text-<?php echo $color; ?>">

<i class="bi <?php echo $icono; ?>"></i>

</div>

<div class="mensaje">

<?php echo nl2br(htmlspecialchars($mensaje)); ?>

</div>

<a
href="<?php echo htmlspecialchars($volver); ?>"
class="btn btn-<?php echo $color; ?> btn-custom">

<i class="bi bi-arrow-left-circle-fill"></i>

<?php echo htmlspecialchars($textoBoton); ?>

</a>

</div>

<div class="footer">

<strong>INCOS</strong><br>

Sistemas Informáticos<br>

© <?php echo date("Y"); ?>

</div>

</div>

</body>

</html>