<?php

include("includes/verificar_sesion.php");
include("includes/verificar_superadmin.php");
include("database/conexion.php");

function limpiarSalida($dato)
{
    return htmlspecialchars($dato ?? '', ENT_QUOTES, 'UTF-8');
}

if($_SESSION['id_rol']!=1)
{
die("Acceso denegado");
}

$sql=
"
SELECT *
FROM empresas
ORDER BY id_empresa DESC
";

$resultado=
$conexion->query($sql);

$totalEmpresas =
$conexion
->query(
"
SELECT COUNT(*) total
FROM empresas
"
)
->fetch_assoc()['total'];

$activas =
$conexion
->query(
"
SELECT COUNT(*) total
FROM empresas
WHERE estado='Activo'
"
)
->fetch_assoc()['total'];

$inactivas =
$conexion
->query(
"
SELECT COUNT(*) total
FROM empresas
WHERE estado='Inactivo'
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

Empresas

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

<i class="bi bi-shield-check"></i>

Centro Nacional de Empresas Supervisadas

</h2>

<p class="text-muted">

Policía Boliviana • Dirección Nacional de Tránsito y Seguridad Vial

</p>

</div>

<div class="col-md-4 text-end">

<a
href="nueva_empresa.php"
class="btn btn-success btn-lg">

<i class="bi bi-building-add"></i>

Registrar Empresa

</a>

</div>

</div>

</div>

<div class="row mb-4">

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Total Empresas</h6>

<h2>

<?php echo $totalEmpresas; ?>

</h2>

</div>

<i class="bi bi-buildings-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Empresas Activas</h6>

<h2 class="text-success">

<?php echo $activas; ?>

</h2>

</div>

<i class="bi bi-check-circle-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Empresas Inactivas</h6>

<h2 class="text-danger">

<?php echo $inactivas; ?>

</h2>

</div>

<i class="bi bi-x-circle-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

</div>

<div class="card card-main">

<div class="card-header bg-white">

<div class="row">

<div class="col-md-6">

<h5>

<i class="bi bi-list-ul"></i>

Empresas Registradas

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

<th>ID</th>
<th>Empresa</th>
<th>NIT</th>
<th>Correo</th>
<th>Estado</th>
<th width="240">

Acciones

</th>

</tr>

</thead>

<tbody>

<?php
while($fila=$resultado->fetch_assoc())
{
?>

<tr>

<td>

<?php echo limpiarSalida($fila['id_empresa']); ?>

</td>

<td>

<i class="bi bi-building text-success"></i>

<strong>

<?php echo limpiarSalida($fila['nombre']); ?>

</strong>

</td>

<td>

<?php echo limpiarSalida($fila['nit']); ?>

</td>

<td>

<?php echo limpiarSalida($fila['correo']); ?>

</td>

<td>

<?php

if($fila['estado']=="Activo")
{
?>

<span class="badge badge-activo">

Activa

</span>

<?php
}
else
{
?>

<span class="badge badge-inactivo">

Inactiva

</span>

<?php
}

?>

</td>

<td>

<a
href="editar_empresa.php?id=<?php echo limpiarSalida($fila['id_empresa']); ?>"
class="btn btn-warning btn-sm btn-action">

<i class="bi bi-pencil-square"></i>

Editar

</a>

<a
href="eliminar_empresa.php?id=<?php echo limpiarSalida($fila['id_empresa']); ?>"
class="btn btn-danger btn-sm btn-action"
onclick="return confirm('¿Desea desactivar esta empresa?')">

<i class="bi bi-slash-circle"></i>

Desactivar

</a>

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

</body>

</html>