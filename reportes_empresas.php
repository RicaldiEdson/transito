<?php

include("includes/verificar_sesion.php");
include("includes/verificar_superadmin.php");
include("database/conexion.php");

if($_SESSION['id_rol'] != 1)
{
    die("Acceso denegado");
}

/*
=========================================
EMPRESAS
=========================================
*/

$sql = "

SELECT
e.id_empresa,
e.nombre,

(
SELECT COUNT(*)
FROM conductores c
WHERE c.id_empresa=e.id_empresa
) conductores,

(
SELECT COUNT(*)
FROM vehiculos v
WHERE v.id_empresa=e.id_empresa
) vehiculos,

(
SELECT COUNT(*)
FROM dispositivos d
WHERE d.id_empresa=e.id_empresa
) dispositivos,

(
SELECT COUNT(*)
FROM eventos_alcohol ea
WHERE ea.id_empresa=e.id_empresa
) alcohol,

(
SELECT COUNT(*)
FROM eventos_somnolencia es
WHERE es.id_empresa=e.id_empresa
) somnolencia,

(
SELECT COUNT(*)
FROM alertas a
WHERE a.id_empresa=e.id_empresa
) alertas

FROM empresas e

ORDER BY e.nombre

";

$resultado =
$conexion->query($sql);

/*
=========================================
TOTALES NACIONALES
=========================================
*/

$totalEmpresas =
$conexion
->query("
SELECT COUNT(*) total
FROM empresas
")
->fetch_assoc()['total'];

$totalConductores =
$conexion
->query("
SELECT COUNT(*) total
FROM conductores
")
->fetch_assoc()['total'];

$totalVehiculos =
$conexion
->query("
SELECT COUNT(*) total
FROM vehiculos
")
->fetch_assoc()['total'];

$totalDispositivos =
$conexion
->query("
SELECT COUNT(*) total
FROM dispositivos
")
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

Reportes por Empresa

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

<i class="bi bi-bar-chart-line-fill"></i>

Reportes Nacionales por Empresa

</h2>

<p class="text-muted">

Monitoreo centralizado del sistema SafeDrive

</p>

</div>

</div>

</div>

<div class="row mb-4">

<div class="col-md-3">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Empresas</h6>

<h2>

<?php echo $totalEmpresas; ?>

</h2>

</div>

<i class="bi bi-buildings-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Conductores</h6>

<h2>

<?php echo $totalConductores; ?>

</h2>

</div>

<i class="bi bi-person-vcard-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Vehículos</h6>

<h2>

<?php echo $totalVehiculos; ?>

</h2>

</div>

<i class="bi bi-truck-front-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Dispositivos</h6>

<h2>

<?php echo $totalDispositivos; ?>

</h2>

</div>

<i class="bi bi-cpu-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

</div>
<div class="card card-main mb-4">

<div class="card-header bg-white">

<h5>

<i class="bi bi-file-earmark-pdf-fill text-danger"></i>

Generador de Reportes Institucionales

</h5>

</div>

<div class="card-body">

<form
action="reporte_empresa_pdf.php"
method="GET"
target="_blank">

<div class="row">

<div class="col-md-3 mb-3">

<label class="form-label">

Fecha Inicio

</label>

<input
type="date"
name="inicio"
class="form-control"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Fecha Fin

</label>

<input
type="date"
name="fin"
class="form-control"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Empresa

</label>

<select
name="empresa"
class="form-select"
required>

<option value="0">

Todas las Empresas

</option>

<?php

$empresasReporte =
$conexion->query(
"
SELECT
id_empresa,
nombre
FROM empresas
WHERE estado='Activo'
ORDER BY nombre
"
);

while(
$empresa=
$empresasReporte->fetch_assoc()
)
{
?>

<option
value="<?php echo $empresa['id_empresa']; ?>">

<?php echo $empresa['nombre']; ?>

</option>

<?php
}
?>

</select>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Tipo

</label>

<select
name="tipo"
class="form-select">

<option value="general">

Reporte General

</option>

<option value="alcohol">

Alcohol

</option>

<option value="somnolencia">

Somnolencia

</option>

<option value="alertas">

Alertas

</option>

</select>

</div>

</div>

<hr>

<button
type="submit"
class="btn btn-danger">

<i class="bi bi-file-earmark-pdf-fill"></i>

Generar PDF

</button>

</form>

</div>

</div>
<div class="card card-main">

<div class="card-header bg-white">

<div class="row">

<div class="col-md-6">

<h5>

<i class="bi bi-table"></i>

Resumen por Empresa

</h5>

</div>

<div class="col-md-6 text-end">

<input
type="text"
id="buscar"
class="form-control search-box d-inline"
placeholder="Buscar empresa...">

</div>

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table
id="tablaEmpresas"
class="table table-hover align-middle">

<thead>

<tr>

<th>Empresa</th>
<th>Conductores</th>
<th>Vehículos</th>
<th>Dispositivos</th>
<th>Alcohol</th>
<th>Somnolencia</th>
<th>Alertas</th>

</tr>

</thead>

<tbody>

<?php
while($fila=$resultado->fetch_assoc())
{
?>

<tr>

<td>

<strong>

<?php echo $fila['nombre']; ?>

</strong>

</td>

<td>

<?php echo $fila['conductores']; ?>

</td>

<td>

<?php echo $fila['vehiculos']; ?>

</td>

<td>

<?php echo $fila['dispositivos']; ?>

</td>

<td>

<span class="badge bg-danger">

<?php echo $fila['alcohol']; ?>

</span>

</td>

<td>

<span class="badge bg-warning text-dark">

<?php echo $fila['somnolencia']; ?>

</span>

</td>

<td>

<span class="badge bg-success">

<?php echo $fila['alertas']; ?>

</span>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<script>

document
.getElementById("buscar")
.addEventListener(
"keyup",
function()
{

let filtro =
this.value.toLowerCase();

let filas =
document.querySelectorAll(
"#tablaEmpresas tbody tr"
);

filas.forEach(
fila =>
{
fila.style.display =
fila.innerText
.toLowerCase()
.includes(filtro)
?
""
:
"none";
}
);

}
);

</script>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>