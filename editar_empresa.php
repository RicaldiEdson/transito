<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");
include("includes/seguridad.php");

if($_SESSION['id_rol']!=1)
{
die("Acceso denegado");
}

$id =
$_GET['id'];

$sql =
"
SELECT *
FROM empresas
WHERE id_empresa=?
";

$stmt =
$conexion->prepare($sql);

$stmt->bind_param(
"i",
$id
);

$stmt->execute();

$empresa =
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

Editar Empresa

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

<i class="bi bi-building-fill-gear"></i>

Editar Empresa

</h2>

<p>

Dirección Nacional de Tránsito y Seguridad Vial

</p>

</div>

<div class="info-box">

<i class="bi bi-info-circle-fill"></i>

Actualice la información de la empresa registrada dentro del sistema SafeDrive.

</div>

<div class="card card-main">

<div class="card-header-custom">

<i class="bi bi-buildings-fill"></i>

Información Empresarial

</div>

<div class="card-body">
<form
action="actualizar_empresa.php"
method="POST"
class="needs-validation"
novalidate>

<input
type="hidden"
name="id_empresa"
value="<?php echo $empresa['id_empresa']; ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">
Nombre de la Empresa
</label>

<input
type="text"
name="nombre"
class="form-control"
value="<?php echo $empresa['nombre']; ?>"
required>

<div class="invalid-feedback">
Debe ingresar el nombre de la empresa.
</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">
NIT
</label>

<input
type="text"
name="nit"
class="form-control"
value="<?php echo $empresa['nit']; ?>"
required
pattern="[0-9]+"
oninput="this.value=this.value.replace(/[^0-9]/g,'')">

<div class="invalid-feedback">
Ingrese un NIT válido (solo números).
</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">
Teléfono
</label>

<input
type="text"
name="telefono"
class="form-control"
value="<?php echo $empresa['telefono']; ?>"
required
pattern="[0-9]+"
oninput="this.value=this.value.replace(/[^0-9]/g,'')">

<div class="invalid-feedback">
Ingrese un teléfono válido (solo números).
</div>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">
Correo Electrónico
</label>

<input
type="email"
name="correo"
class="form-control"
value="<?php echo $empresa['correo']; ?>"
required
pattern=".+@gmail\.com$"
title="Debe ingresar un correo Gmail válido">

<div class="invalid-feedback">
Debe ingresar un correo Gmail válido.
</div>

</div>

<div class="col-md-12 mb-3">

<label class="form-label">
Dirección
</label>

<textarea
name="direccion"
class="form-control"
rows="3"
required><?php echo $empresa['direccion']; ?></textarea>

<div class="invalid-feedback">
Debe ingresar una dirección.
</div>

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
value="Activo"
<?php if($empresa['estado']=="Activo") echo "selected"; ?>>

Activo

</option>

<option
value="Inactivo"
<?php if($empresa['estado']=="Inactivo") echo "selected"; ?>>

Inactivo

</option>

</select>

<div class="invalid-feedback">
Seleccione un estado.
</div>

</div>

</div>

<hr>

<button
type="submit"
class="btn btn-success btn-guardar">

<i class="bi bi-save-fill"></i>

Actualizar Empresa

</button>

<a
href="empresas.php"
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
<script>

(() => {

'use strict';

const forms =
document.querySelectorAll('.needs-validation');

Array.from(forms).forEach(form => {

form.addEventListener('submit', event => {

if (!form.checkValidity()) {

event.preventDefault();
event.stopPropagation();

const primerError =
form.querySelector(':invalid');

if(primerError)
{
primerError.focus();
}

}

form.classList.add('was-validated');

}, false);

});

})();

</script>
</body>

</html>