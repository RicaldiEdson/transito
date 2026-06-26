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
content="width=device-width, initial-scale=1">

<title>

Nuevo Conductor

</title>

<link
href="assets/vendor/bootstrap/css/bootstrap.min.css"
rel="stylesheet">

<link
href="assets/vendor/bootstrap-icons/bootstrap-icons.css"
rel="stylesheet">

<style>

body{
background:#f4f7fb;
}

.content{
margin-left:280px;
padding:25px;
min-height:100vh;
}

.page-header{
background:white;
padding:25px;
border-radius:15px;
margin-bottom:20px;
box-shadow:0 3px 15px rgba(0,0,0,.08);
}

.page-header h2{
font-weight:700;
color:#0b6b3a;
}

.card-main{
border:none;
border-radius:15px;
box-shadow:0 3px 15px rgba(0,0,0,.08);
overflow:hidden;
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

.form-control{
border-radius:10px;
padding:10px;
}

.form-control:focus{
border-color:#0b6b3a;
box-shadow:0 0 0 .2rem rgba(11,107,58,.15);
}

.btn-guardar{
background:#0b6b3a;
border:none;
padding:10px 25px;
font-weight:600;
}

.btn-guardar:hover{
background:#09552e;
}

.btn-volver{
padding:10px 25px;
font-weight:600;
}

.info-box{
background:#eef8f2;
border-left:5px solid #0b6b3a;
padding:15px;
border-radius:10px;
margin-bottom:20px;
}

.section-title{
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

<i class="bi bi-person-vcard-fill"></i>

Registro de Nuevo Conductor

</h2>

<p class="text-muted mb-0">

Administración de Conductores del Sistema SafeDrive

</p>

</div>

<div class="info-box">

<i class="bi bi-info-circle-fill"></i>

Complete correctamente los datos personales y de licencia del conductor para su incorporación al sistema nacional de monitoreo.

</div>

<div class="card card-main">

<div class="card-header-custom">

<i class="bi bi-person-plus-fill"></i>

Datos del Conductor

</div>

<div class="card-body">

<form
action="guardar_conductor.php"
method="POST"
id="formConductor">

<h5 class="section-title">

<i class="bi bi-person-badge-fill"></i>

Información Personal

</h5>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Carnet de Identidad

</label>

<input
type="text"
name="ci"
class="form-control"
required
maxlength="15"
pattern="[0-9]+"
title="El CI solo debe contener números">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Licencia de Conducir

</label>

<input
type="text"
name="licencia"
class="form-control"
required
maxlength="30">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Nombre

</label>

<input
type="text"
name="nombre"
class="form-control"
required
pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
title="Solo se permiten letras">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Apellido

</label>

<input
type="text"
name="apellido"
class="form-control"
required
pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
title="Solo se permiten letras">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Teléfono

</label>

<input
type="text"
name="telefono"
class="form-control"
required
maxlength="15"
pattern="[0-9]+"
title="El teléfono solo debe contener números">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Categoría de Licencia

</label>

<select
name="categoria"
class="form-select"
required>

<option value="">

Seleccione categoría

</option>

<option value="P">

P

</option>

<option value="A">

A

</option>

<option value="B">

B

</option>

<option value="C">

C

</option>


</select>

</div>

<div class="col-md-12 mb-3">

<label class="form-label">

Dirección

</label>

<textarea
name="direccion"
class="form-control"
rows="3"
required></textarea>

</div>

</div>

<hr class="my-4">

<div class="d-flex gap-3">

<button
type="submit"
class="btn btn-success btn-guardar">

<i class="bi bi-save-fill"></i>

Guardar Conductor

</button>

<a
href="conductores.php"
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
<script>

document
.getElementById("formConductor")
.addEventListener(
"submit",
function(e)
{

let ci =
document
.querySelector('[name="ci"]')
.value
.trim();

let telefono =
document
.querySelector('[name="telefono"]')
.value
.trim();

let nombre =
document
.querySelector('[name="nombre"]')
.value
.trim();

let apellido =
document
.querySelector('[name="apellido"]')
.value
.trim();

if(!/^[0-9]+$/.test(ci))
{
alert("El Carnet de Identidad solo debe contener números.");
e.preventDefault();
return;
}

if(!/^[0-9]+$/.test(telefono))
{
alert("El teléfono solo debe contener números.");
e.preventDefault();
return;
}

if(!/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(nombre))
{
alert("El nombre solo puede contener letras.");
e.preventDefault();
return;
}

if(!/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/.test(apellido))
{
alert("El apellido solo puede contener letras.");
e.preventDefault();
return;
}

}
);

</script>
</body>

</html>