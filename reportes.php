<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");

$id_empresa =
$_SESSION['id_empresa'];

$totalAlcohol =
$conexion
->query(
"
SELECT COUNT(*) total
FROM eventos_alcohol
WHERE id_empresa=$id_empresa
"
)
->fetch_assoc()['total'];

$totalSomnolencia =
$conexion
->query(
"
SELECT COUNT(*) total
FROM eventos_somnolencia
WHERE id_empresa=$id_empresa
"
)
->fetch_assoc()['total'];

$totalAlertas =
$conexion
->query(
"
SELECT COUNT(*) total
FROM alertas
WHERE id_empresa=$id_empresa
"
)
->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Centro de Reportes

</title>

<link
href="assets/vendor/bootstrap/css/bootstrap.min.css"
rel="stylesheet">

<link
href="assets/vendor/bootstrap-icons/bootstrap-icons.css"
rel="stylesheet">

<style>
body{
background:#eef3f8;
font-family:'Segoe UI',sans-serif;
}

.content{
margin-left:280px;
padding:25px;
}

.page-header{
background:white;
padding:30px;
border-radius:18px;
margin-bottom:25px;
box-shadow:0 5px 18px rgba(0,0,0,.08);
border-left:6px solid #0b6b3a;
}

.page-header h2{
font-weight:700;
color:#0b6b3a;
}

.page-header p{
margin-bottom:0;
}

.card-kpi{
border:none;
border-radius:18px;
box-shadow:0 5px 18px rgba(0,0,0,.08);
transition:.3s;
}

.card-kpi:hover{
transform:translateY(-4px);
}

.kpi-icon{
font-size:48px;
color:#0b6b3a;
}

.card-main{
border:none;
border-radius:18px;
box-shadow:0 5px 18px rgba(0,0,0,.08);
}

.card-main .card-header{
background:white;
border-bottom:1px solid #e8e8e8;
padding:18px;
}

.table thead{
background:#0b6b3a;
color:white;
}

.table tbody tr:hover{
background:#f8fbf9;
}

.badge-activo{
background:#198754;
padding:8px 15px;
font-size:12px;
font-weight:600;
}

.badge-inactivo{
background:#dc3545;
padding:8px 15px;
font-size:12px;
font-weight:600;
}

.search-box{
max-width:350px;
border-radius:10px;
}

.btn-action{
margin-right:10px;
margin-bottom:5px;
}

.btn-action:last-child{
margin-right:0;
}
</style>

</head>

<body>

<?php
include("includes/sidebar.php");
?>

<div class="content">

<div class="page-header">

<div class="row align-items-center">

<div class="col-md-8">

<h2>

<i class="bi bi-file-earmark-bar-graph-fill"></i>

Centro de Inteligencia Vial

</h2>

<p class="text-muted">

Generación Oficial de Reportes y Estadísticas

</p>

</div>

<div class="col-md-4 text-end">

<span class="logo-header">

POLICÍA BOLIVIANA

</span>

<br>

<small class="text-muted">

Dirección Nacional de Tránsito

</small>

</div>

</div>

</div>

<div class="row mb-4">

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Eventos Alcohol</h6>

<h2 class="text-danger">

<?php echo $totalAlcohol; ?>

</h2>

</div>

<i class="bi bi-cup-hot-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Eventos Somnolencia</h6>

<h2 class="text-warning">

<?php echo $totalSomnolencia; ?>

</h2>

</div>

<i class="bi bi-eye-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Alertas Generadas</h6>

<h2 class="text-success">

<?php echo $totalAlertas; ?>

</h2>

</div>

<i class="bi bi-bell-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

</div>

<div class="row">

<div class="col-lg-6">

<div class="card card-main">

<div class="card-header">

<h5 class="mb-0">

<i class="bi bi-file-earmark-excel-fill"></i>

Reporte Excel

</h5>

</div>

<div class="card-body">

<form
action="reporte_excel.php"
method="GET"
target="_blank">

<div class="mb-3">

<label>

Fecha Inicio

</label>

<input
type="date"
name="inicio"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Fecha Fin

</label>

<input
type="date"
name="fin"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Tipo de Evento

</label>

<select
name="tipo"
class="form-select">

<option value="alcohol">

Alcohol

</option>

<option value="somnolencia">

Somnolencia

</option>

</select>

</div>

<button
type="submit"
class="btn btn-success w-100">

<i class="bi bi-file-earmark-excel"></i>

Generar Excel

</button>

</form>

</div>

</div>

</div>

<div class="col-lg-6">

<div class="card card-main">

<div class="card-header">

<h5 class="mb-0">

<i class="bi bi-file-earmark-pdf-fill"></i>

Reporte PDF

</h5>

</div>

<div class="card-body">

<form
action="reporte_pdf.php"
method="GET"
target="_blank">

<div class="mb-3">

<label>

Fecha Inicio

</label>

<input
type="date"
name="inicio"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Fecha Fin

</label>

<input
type="date"
name="fin"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

Tipo de Evento

</label>

<select
name="tipo"
class="form-select">

<option value="alcohol">

Alcohol

</option>

<option value="somnolencia">

Somnolencia

</option>

</select>

</div>

<button
type="submit"
class="btn btn-danger w-100">

<i class="bi bi-file-earmark-pdf"></i>

Generar PDF

</button>

</form>

</div>

</div>

</div>

</div>

<div class="card card-main mt-4">

<div class="card-body">

<h5>

<i class="bi bi-info-circle-fill"></i>

Información del Centro de Reportes

</h5>

<hr>

<div class="row">

<div class="col-md-4">

<div class="alert alert-success">

<strong>Excel</strong>

<br>

Exportación para análisis y auditoría.

</div>

</div>

<div class="col-md-4">

<div class="alert alert-danger">

<strong>PDF</strong>

<br>

Documentos oficiales imprimibles.

</div>

</div>

<div class="col-md-4">

<div class="alert alert-primary">

<strong>Histórico</strong>

<br>

Basado en eventos registrados.

</div>

</div>

</div>

</div>

</div>

</div>

</body>

</html>