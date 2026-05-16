<?php

include("includes/verificar_sesion.php");

if($_SESSION['id_rol']!=1)
{
die("Acceso denegado");
}

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>

Nueva Empresa

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

.form-control{
border-radius:10px;
padding:10px;
}

.form-control:focus{
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

<i class="bi bi-building-fill-add"></i>

Registro de Nueva Empresa

</h2>

<p>

Dirección Nacional de Tránsito y Seguridad Vial

</p>

</div>

<div class="info-box">

<i class="bi bi-info-circle-fill"></i>

Registre una nueva empresa de transporte autorizada para utilizar la plataforma SafeDrive.

</div>

<div class="card card-main">

<div class="card-header-custom">

<i class="bi bi-buildings-fill"></i>

Información de la Empresa

</div>

<div class="card-body">

<form
action="guardar_empresa.php"
method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Nombre de la Empresa

</label>

<input
type="text"
name="nombre"
class="form-control"
required
maxlength="150">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

NIT

</label>

<input
type="text"
name="nit"
class="form-control"
required
maxlength="20"
pattern="[0-9]+"
title="El NIT solo debe contener números">

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

Correo Electrónico

</label>

<input
type="email"
name="correo"
class="form-control"
placeholder="usuario@gmail.com"
pattern=".+@gmail\.com$"
title="Debe ingresar un correo Gmail válido"
required>

<small class="text-muted">

Ejemplo: usuario@gmail.com

</small>

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

<hr>

<button
type="submit"
class="btn btn-success btn-guardar">

<i class="bi bi-save-fill"></i>

Guardar Empresa

</button>

<a
href="empresas.php"
class="btn btn-secondary btn-volver">

<i class="bi bi-arrow-left-circle"></i>

Volver

</a>

</form>

<script>

document
.querySelector('input[name="nit"]')
.addEventListener(
'input',
function()
{
this.value =
this.value.replace(/\D/g,'');
}
);

document
.querySelector('input[name="telefono"]')
.addEventListener(
'input',
function()
{
this.value =
this.value.replace(/\D/g,'');
}
);

</script>
</div>

</div>

</div>

</div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>