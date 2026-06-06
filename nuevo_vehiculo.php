<?php

include("includes/verificar_admin.php");
include("database/conexion.php");

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Nuevo Vehículo

</title>

<link
href="assets/vendor/bootstrap/css/bootstrap.min.css"
rel="stylesheet">

<link
href="assets/vendor/bootstrap-icons/bootstrap-icons.css"
rel="stylesheet">

<style>

body{
background:#f1f5f9;
font-family:'Segoe UI',sans-serif;
}

.content{
margin-left:280px;
padding:30px;
min-height:100vh;
}

/*=========================
ENCABEZADO
=========================*/

.page-header{
background:white;
padding:25px;
border-radius:18px;
margin-bottom:25px;
box-shadow:0 5px 18px rgba(0,0,0,.08);
border-left:6px solid #0b6b3a;
}

.page-header h2{
font-weight:700;
color:#0b6b3a;
margin-bottom:5px;
}

.page-header p{
margin:0;
color:#6c757d;
}

/*=========================
CARD PRINCIPAL
=========================*/

.card-main{
border:none;
border-radius:18px;
overflow:hidden;
box-shadow:0 5px 18px rgba(0,0,0,.08);
}

.card-header-custom{
background:#0b6b3a;
color:white;
padding:18px;
font-size:18px;
font-weight:600;
}

.card-body{
padding:30px;
}

.form-label{
font-weight:600;
color:#495057;
}

.form-control,
.form-select{
border-radius:10px;
padding:10px;
}

.form-control:focus,
.form-select:focus{
border-color:#0b6b3a;
box-shadow:0 0 0 .15rem rgba(11,107,58,.20);
}

.btn-guardar{
background:#0b6b3a;
border:none;
padding:12px 25px;
font-weight:600;
}

.btn-guardar:hover{
background:#084f2b;
}

.btn-volver{
padding:12px 25px;
font-weight:600;
}

.info-box{
background:#eef8f2;
border-left:5px solid #0b6b3a;
padding:15px;
border-radius:10px;
margin-bottom:25px;
}

.section-title{
font-size:18px;
font-weight:700;
color:#0b6b3a;
margin-bottom:20px;
}

</style>

</head>

<body>

<?php
include("includes/sidebar.php");
?>

<div class="content">

<div class="container-fluid">

<!-- ENCABEZADO -->

<div class="page-header">

<h2>

<i class="bi bi-truck-front-fill"></i>

Registro de Nuevo Vehículo

</h2>

<p>

Dirección Nacional de Tránsito y Seguridad Vial

</p>

</div>

<div class="info-box">

<i class="bi bi-info-circle-fill"></i>

Complete los datos del vehículo para incorporarlo al sistema nacional SafeDrive.

</div>

<div class="card card-main">

<div class="card-header-custom">

<i class="bi bi-plus-circle-fill"></i>

Datos del Vehículo

</div>

<div class="card-body">
    
<form
action="guardar_vehiculo.php"
method="POST">

<h5 class="section-title">

<i class="bi bi-truck"></i>

Información del Vehículo

</h5>

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Placa

</label>

<input
type="text"
name="placa"
class="form-control"
required
maxlength="15"
style="text-transform:uppercase">

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Marca

</label>

<input
type="text"
name="marca"
class="form-control"
required>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Modelo

</label>

<input
type="text"
name="modelo"
class="form-control"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Año

</label>

<input
type="number"
name="anio"
class="form-control"
required
min="1950"
max="2100">

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Color

</label>

<input
type="text"
name="color"
class="form-control"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Capacidad (Tn)

</label>

<input
type="text"
name="capacidad"
class="form-control"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Estado

</label>

<select
name="estado"
class="form-select"
required>

<option value="Activo">

Activo

</option>

<option value="Mantenimiento">

Mantenimiento

</option>

<option value="Inactivo">

Inactivo

</option>

</select>

</div>

</div>

<hr>

<div class="d-flex gap-3">

<button
type="submit"
class="btn btn-success btn-guardar">

<i class="bi bi-save-fill"></i>

Guardar Vehículo

</button>

<a
href="vehiculos.php"
class="btn btn-secondary btn-volver">

<i class="bi bi-arrow-left-circle"></i>

Volver

</a>

</div>

</form>

</div>

</div>

</div>

</div>

<script
src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js">
</script>

</body>

</html>