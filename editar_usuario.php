<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");

if($_SESSION['id_rol']!=1)
{
die("Acceso denegado");
}

$id=$_GET['id'];

$sql="
SELECT *
FROM usuarios
WHERE id_usuario=?
";

$stmt=
$conexion->prepare($sql);

$stmt->bind_param(
"i",
$id
);

$stmt->execute();

$usuario=
$stmt
->get_result()
->fetch_assoc();

if($usuario['id_rol']==1)
{
    die("El SuperAdministrador no puede ser modificado.");
}

$roles =
$conexion->query(
"
SELECT *
FROM roles
WHERE id_rol <> 1
ORDER BY nombre
"
);

$empresas=
$conexion->query(
"
SELECT *
FROM empresas
WHERE estado='Activo'
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

Editar Usuario

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
padding:25px;
border-radius:15px;
margin-bottom:20px;
box-shadow:0 4px 15px rgba(0,0,0,.08);
}

.page-header h2{
font-weight:700;
color:#0b6b3a;
}

.page-header p{
margin:0;
color:#6c757d;
}

.card-form{
border:none;
border-radius:15px;
box-shadow:0 4px 15px rgba(0,0,0,.08);
}

.card-header-custom{
background:#0b6b3a;
color:white;
padding:15px 20px;
border-radius:15px 15px 0 0;
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

.section-title{
font-size:15px;
font-weight:700;
color:#0b6b3a;
margin-bottom:15px;
padding-bottom:8px;
border-bottom:2px solid #e9ecef;
}

</style>

</head>

<body>

<?php
include("includes/sidebar.php");
?>

<div class="content">

<div class="page-header">

<h2>

<i class="bi bi-person-gear"></i>

Editar Usuario

</h2>

<p>

Dirección Nacional de Tránsito y Seguridad Vial

</p>

</div>

<div class="card card-form">

<div class="card-header-custom">

<h5 class="mb-0">

<i class="bi bi-pencil-square"></i>

Actualización de Datos del Usuario

</h5>

</div>

<div class="card-body p-4">
<form
action="actualizar_usuario.php"
method="POST"
class="needs-validation"
novalidate>

<input
type="hidden"
name="id_usuario"
value="<?php echo $usuario['id_usuario']; ?>">

<div class="section-title">

Datos Personales

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Nombre

</label>

<input
type="text"
name="nombre"
class="form-control"
required
value="<?php echo $usuario['nombre']; ?>">

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
value="<?php echo $usuario['apellido']; ?>">

</div>

</div>

<div class="section-title mt-3">

Credenciales de Acceso

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Usuario

</label>

<input
type="text"
name="usuario"
class="form-control"
required
value="<?php echo $usuario['usuario']; ?>">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Contraseña

</label>

<input
type="text"
name="password"
class="form-control"
required
value="<?php echo $usuario['password']; ?>">

</div>

</div>

<div class="section-title mt-3">

Información Institucional

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Correo Electrónico

</label>

<input
type="email"
name="correo"
class="form-control"
required
pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
title="Debe ingresar un correo Gmail válido"
value="<?php echo $usuario['correo']; ?>">

<div class="form-text">

Ejemplo: usuario@gmail.com

</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Rol del Sistema

</label>

<select
name="id_rol"
class="form-select"
required>

<?php
while($rol=$roles->fetch_assoc())
{
?>

<option
value="<?php echo $rol['id_rol']; ?>"

<?php
if(
$rol['id_rol']
==
$usuario['id_rol']
)
{
echo "selected";
}
?>

>

<?php echo $rol['nombre']; ?>

</option>

<?php
}
?>

</select>

</div>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Empresa

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
value="<?php echo $empresa['id_empresa']; ?>"

<?php
if(
$empresa['id_empresa']
==
$usuario['id_empresa']
)
{
echo "selected";
}
?>

>

<?php echo $empresa['nombre']; ?>

</option>

<?php
}
?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Estado

</label>

<select
name="estado"
class="form-select"
required>

<option
value="Activo"

<?php
if($usuario['estado']=="Activo")
echo "selected";
?>

>

Activo

</option>

<option
value="Inactivo"

<?php
if($usuario['estado']=="Inactivo")
echo "selected";
?>

>

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

Actualizar Usuario

</button>

<a
href="usuarios.php"
class="btn btn-secondary btn-volver">

<i class="bi bi-arrow-left-circle"></i>

Volver

</a>

</div>

</form>

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