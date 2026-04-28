<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");

$roles =
$conexion->query(
"
SELECT *
FROM roles
WHERE id_rol <> 1
ORDER BY nombre
"
);

$empresas =
$conexion->query(
"
SELECT *
FROM empresas
WHERE estado='Activo'
ORDER BY nombre
"
);

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Nuevo Usuario

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
}

.page-header{
background:white;
padding:25px;
border-radius:15px;
margin-bottom:20px;
box-shadow:0 3px 15px rgba(0,0,0,.08);
border-left:6px solid #0b6b3a;
}

.page-header h2{
font-weight:700;
color:#0b6b3a;
margin-bottom:5px;
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

.form-label{
font-weight:600;
color:#495057;
}

.form-control,
.form-select{
border-radius:10px;
padding:10px;
}

.btn-guardar{
background:#0b6b3a;
border:none;
padding:10px 25px;
font-weight:600;
}

.btn-guardar:hover{
background:#084f2b;
}

.info-box{
background:#eef8f2;
border-left:5px solid #0b6b3a;
padding:15px;
border-radius:10px;
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

<i class="bi bi-person-plus-fill"></i>

Registro de Nuevo Usuario

</h2>

<p class="text-muted">

Administración Nacional de Usuarios SafeDrive

</p>

</div>

<div class="info-box">

<i class="bi bi-info-circle-fill"></i>

Complete la información del usuario. Los administradores podrán gestionar empresas y operadores según los permisos asignados.

</div>

<div class="card card-main">

<div class="card-header-custom">

<i class="bi bi-person-badge-fill"></i>

Datos del Usuario

</div>

<div class="card-body p-4">
<form
action="guardar_usuario.php"
method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label fw-semibold">

Nombre

</label>

<input
type="text"
name="nombre"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label fw-semibold">

Apellido

</label>

<input
type="text"
name="apellido"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label fw-semibold">

Usuario

</label>

<input
type="text"
name="usuario"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label fw-semibold">

Contraseña

</label>

<input
type="password"
name="password"
class="form-control"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label fw-semibold">

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

<div class="col-md-6 mb-3">

<label class="form-label fw-semibold">

Rol del Usuario

</label>

<select
name="id_rol"
class="form-select"
required>

<option value="">

Seleccione un rol

</option>

<?php
while($rol=$roles->fetch_assoc())
{
?>

<option
value="<?php echo $rol['id_rol']; ?>">

<?php echo $rol['nombre']; ?>

</option>

<?php
}
?>

</select>

</div>

<div class="col-md-12 mb-4">

<label class="form-label fw-semibold">

Empresa Asignada

</label>

<select
name="id_empresa"
class="form-select"
required>

<option value="">

Seleccione una empresa

</option>

<?php
while($empresa=$empresas->fetch_assoc())
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

<small class="text-muted">

Debe seleccionar una empresa para registrar el usuario.

</small>

</div>

</div>

<hr>

<div class="text-end">

<a
href="usuarios.php"
class="btn btn-secondary me-2">

<i class="bi bi-arrow-left-circle"></i>

Volver

</a>

<button
type="submit"
class="btn btn-success btn-guardar">

<i class="bi bi-save-fill"></i>

Guardar Usuario

</button>

</div>

</form>

</div>

</div>

</div>

</div>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>