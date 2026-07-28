<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");
include("includes/auditoria.php");

$id_empresa =
$_SESSION['id_empresa'];

$conductores =
$conexion->query(
"
SELECT *
FROM conductores
WHERE estado='Activo'
AND id_empresa=$id_empresa
ORDER BY nombre
"
);

$vehiculos =
$conexion->query(
"
SELECT *
FROM vehiculos
WHERE estado='Activo'
AND id_empresa=$id_empresa
ORDER BY placa
"
);

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Nueva Asignación

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

</style>

</head>

<body>

<?php
include("includes/sidebar.php");
?>

<div class="content">

<div class="container-fluid">

<div class="page-header">

<h2>

<i class="bi bi-arrow-left-right"></i>

Nueva Asignación

</h2>

<p>

Dirección Nacional de Tránsito y Seguridad Vial

</p>

</div>

<div class="info-box">

<i class="bi bi-info-circle-fill"></i>

Asigne un conductor activo a un vehículo activo para habilitar el monitoreo SafeDrive.

</div>

<div class="card card-main">

<div class="card-header-custom">

<i class="bi bi-diagram-3-fill"></i>

Registro de Asignación

</div>

<div class="card-body">

<form
action="guardar_asignacion.php"
method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

<i class="bi bi-person-badge-fill"></i>

Conductor

</label>

<select
name="id_conductor"
class="form-select"
required>

<option value="">

Seleccione un Conductor

</option>

<?php
while($c=$conductores->fetch_assoc())
{
?>

<option
value="<?php echo $c['id_conductor']; ?>">

<?php

echo
$c['nombre']
.
' '
.
$c['apellido']
.
' | CI: '
.
$c['ci'];

?>

</option>

<?php
}
?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

<i class="bi bi-truck-front-fill"></i>

Vehículo

</label>

<select
name="id_vehiculo"
class="form-select"
required>

<option value="">

Seleccione un Vehículo

</option>

<?php
while($v=$vehiculos->fetch_assoc())
{
?>

<option
value="<?php echo $v['id_vehiculo']; ?>">

<?php

echo
$v['placa']
.
' | '
.
$v['marca']
.
' '
.
$v['modelo'];

?>

</option>

<?php
}
?>

</select>

</div>

</div>

<hr>

<button
type="submit"
class="btn btn-success btn-guardar">

<i class="bi bi-save-fill"></i>

Guardar Asignación

</button>

<a
href="asignaciones.php"
class="btn btn-secondary btn-volver">

<i class="bi bi-arrow-left-circle"></i>

Volver

</a>

</form>

</div>

</div>

</div>

</div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>