<?php

include("includes/verificar_admin.php");
include("database/conexion.php");

$id =
$_GET['id'];

$id_empresa =
$_SESSION['id_empresa'];

$sql = "

SELECT *
FROM conductores
WHERE id_conductor=?
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

if(!$datos)
{
die("Conductor no encontrado");
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

Editar Conductor

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
font-family:'Segoe UI',sans-serif;
}

.content{
margin-left:280px;
padding:25px;
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

<i class="bi bi-pencil-square"></i>

Editar Conductor

</h2>

<p>

Actualización de datos del conductor registrado

</p>

</div>

<div class="info-box">

<i class="bi bi-info-circle-fill"></i>

Modifique la información necesaria y guarde los cambios.

</div>

<div class="card card-main">

<div class="card-header-custom">

<i class="bi bi-person-vcard-fill"></i>

Información del Conductor

</div>

<div class="card-body">

<form
action="actualizar_conductor.php"
method="POST"
class="needs-validation"
novalidate>

<input
type="hidden"
name="id_conductor"
value="<?php echo $datos['id_conductor']; ?>">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

CI

</label>

<input
type="text"
name="ci"
class="form-control"
value="<?php echo $datos['ci']; ?>"
required
maxlength="15"
pattern="[0-9]+"
title="Solo números">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Licencia

</label>

<input
type="text"
name="licencia"
class="form-control"
value="<?php echo $datos['licencia']; ?>"
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
value="<?php echo $datos['nombre']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Apellido

</label>

<input
type="text"
name="apellido"
class="form-control"
value="<?php echo $datos['apellido']; ?>"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">

Teléfono

</label>

<input
type="text"
name="telefono"
class="form-control"
value="<?php echo $datos['telefono']; ?>"
required
maxlength="20"
pattern="[0-9]+"
title="Solo números">

</div>

<div class="col-md-6 mb-3">

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
required><?php echo $datos['direccion']; ?></textarea>

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
if($datos['estado']=='Activo')
{
echo "selected";
}
?>>

Activo

</option>

<option
value="Inactivo"
<?php
if($datos['estado']=='Inactivo')
{
echo "selected";
}
?>>

Inactivo

</option>

</select>

</div>

</div>

<hr>

<button
type="submit"
class="btn btn-success btn-guardar">

<i class="bi bi-save-fill"></i>

Guardar Cambios

</button>

<a
href="conductores.php"
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

document
.querySelector('[name="ci"]')
.addEventListener(
'input',
function()
{
this.value =
this.value.replace(/\D/g,'');
}
);

document
.querySelector('[name="telefono"]')
.addEventListener(
'input',
function()
{
this.value =
this.value.replace(/\D/g,'');
}
);

</script>
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