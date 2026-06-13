<?php

include("includes/verificar_admin.php");
include("database/conexion.php");

$id =
$_GET['id'];

$id_empresa =
$_SESSION['id_empresa'];

$sql = "

SELECT *
FROM vehiculos

WHERE id_vehiculo=?
AND id_empresa=?

";

$stmt =
$conexion->prepare($sql);

$stmt->bind_param(
"ii",
$id,
$id_empresa
);

$stmt->execute();

$datos =
$stmt
->get_result()
->fetch_assoc();

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Editar Vehículo

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

<div class="page-header">

<h2>

<i class="bi bi-pencil-square"></i>

Editar Vehículo

</h2>

<p>

Dirección Nacional de Tránsito y Seguridad Vial

</p>

</div>

<div class="info-box">

<i class="bi bi-info-circle-fill"></i>

Actualice la información del vehículo registrado en SafeDrive.

</div>

<div class="card card-main">

<div class="card-header-custom">

<i class="bi bi-truck-front-fill"></i>

Información del Vehículo

</div>

<div class="card-body">

<form
action="actualizar_vehiculo.php"
method="POST"
class="needs-validation"
novalidate>

<input
type="hidden"
name="id_vehiculo"
value="<?php echo $datos['id_vehiculo']; ?>">

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Placa

</label>

<input
type="text"
name="placa"
class="form-control"
value="<?php echo $datos['placa']; ?>"
maxlength="15"
required>

<div class="form-text">
La placa debe ser única dentro de la empresa.
</div>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Marca

</label>

<input
type="text"
name="marca"
class="form-control"
value="<?php echo $datos['marca']; ?>"
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
value="<?php echo $datos['modelo']; ?>"
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
value="<?php echo $datos['anio']; ?>"
min="1950"
max="2100"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Color

</label>

<input
type="text"
name="color"
class="form-control"
value="<?php echo $datos['color']; ?>"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Capacidad

</label>

<input
type="number"
name="capacidad"
class="form-control"
value="<?php echo $datos['capacidad']; ?>"
min="1"
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

<option
value="Activo"
<?php if($datos['estado']=="Activo") echo "selected"; ?>>

Activo

</option>

<option
value="Inactivo"
<?php if($datos['estado']=="Inactivo") echo "selected"; ?>>

Inactivo

</option>

<option
value="Mantenimiento"
<?php if($datos['estado']=="Mantenimiento") echo "selected"; ?>>

Mantenimiento

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

Actualizar Vehículo

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

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>

(() =>
{
'use strict';

const forms =
document.querySelectorAll('.needs-validation');

Array.from(forms)
.forEach(form =>
{
form.addEventListener(
'submit',
event =>
{
if(!form.checkValidity())
{
event.preventDefault();
event.stopPropagation();

form
.querySelector(':invalid')
.focus();
}

form.classList.add('was-validated');
},
false
);
});
})();

</script>
</body>

</html>