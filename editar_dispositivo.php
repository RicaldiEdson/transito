<?php

include("includes/verificar_admin.php");
include("database/conexion.php");

$id =
$_GET['id'];

$id_empresa =
$_SESSION['id_empresa'];

$sql = "

SELECT *
FROM dispositivos

WHERE id_dispositivo=?
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

Editar Dispositivo

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

.form-control,
.form-select{
border-radius:10px;
padding:10px;
}

.form-label{
font-weight:600;
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

<i class="bi bi-cpu-fill"></i>

Editar Dispositivo

</h2>

<p class="text-muted">

Actualización de información del dispositivo

</p>

</div>

<div class="card card-main">

<div class="card-header-custom">

<i class="bi bi-router-fill"></i>

Datos del Dispositivo

</div>

<div class="card-body">
<form
action="actualizar_dispositivo.php"
method="POST"
class="needs-validation"
novalidate>

<input
type="hidden"
name="id_dispositivo"
value="<?php echo $datos['id_dispositivo']; ?>">

<div class="row">

<div class="col-md-4 mb-3">

<label class="form-label">

Código

</label>

<input
type="text"
name="codigo"
class="form-control"
value="<?php echo $datos['codigo']; ?>"
required
maxlength="50">

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Tipo

</label>

<select
name="tipo"
class="form-select"
required>

<option
value="ESP32-CAM"
<?php if($datos['tipo']=="ESP32-CAM") echo "selected"; ?>>

ESP32-CAM

</option>

<option
value="MQ-3"
<?php if($datos['tipo']=="MQ-3") echo "selected"; ?>>

MQ-3

</option>



</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">

Estado

</label>

<select
name="estado"
class="form-select"
required>

<option
value="">

Seleccione Estado

</option>

<option
value="Online"
<?php if($datos['estado']=='Online') echo 'selected'; ?>>

Online

</option>

<option
value="Offline"
<?php if($datos['estado']=='Offline') echo 'selected'; ?>>

Offline

</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

IP

</label>

<input
type="text"
name="ip"
class="form-control"
value="<?php echo $datos['ip']; ?>"
required
pattern="^((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])$"
title="Ingrese una dirección IP válida. Ejemplo: 192.168.1.100">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Puerto

</label>

<input
type="number"
name="puerto"
class="form-control"
value="<?php echo $datos['puerto']; ?>"
required
min="1"
max="65535">

</div>

<div class="col-md-12 mb-3">

<label class="form-label">

Vehículo Asignado

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
value="<?php echo $v['id_vehiculo']; ?>"
<?php if($v['id_vehiculo']==$datos['id_vehiculo']) echo 'selected'; ?>>

<?php echo $v['placa']; ?>

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

Actualizar Dispositivo

</button>

<a
href="dispositivos.php"
class="btn btn-secondary">

<i class="bi bi-arrow-left-circle"></i>

Volver

</a>

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