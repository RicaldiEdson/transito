<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");

if($_SESSION['id_rol']==3)
{
    die("Acceso denegado");
}

$idRol =
$_SESSION['id_rol'];

$idEmpresa =
$_SESSION['id_empresa'];

if($idRol==1)
{

$sql =
"
SELECT
u.*,
r.nombre AS rol,
e.nombre AS empresa
FROM usuarios u
INNER JOIN roles r
ON u.id_rol=r.id_rol
LEFT JOIN empresas e
ON u.id_empresa=e.id_empresa
ORDER BY u.id_usuario DESC
";

}
else
{

$sql =
"
SELECT
u.*,
r.nombre AS rol,
e.nombre AS empresa
FROM usuarios u
INNER JOIN roles r
ON u.id_rol=r.id_rol
LEFT JOIN empresas e
ON u.id_empresa=e.id_empresa
WHERE u.id_empresa=$idEmpresa
ORDER BY u.id_usuario DESC
";

}

$usuarios =
$conexion->query($sql);

/*
=========================================
ESTADISTICAS
=========================================
*/

if($idRol==1)
{

$totalUsuarios =
$conexion
->query(
"
SELECT COUNT(*) total
FROM usuarios
"
)
->fetch_assoc()['total'];

$activos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM usuarios
WHERE estado='Activo'
"
)
->fetch_assoc()['total'];

$inactivos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM usuarios
WHERE estado='Inactivo'
"
)
->fetch_assoc()['total'];

}
else
{

$totalUsuarios =
$conexion
->query(
"
SELECT COUNT(*) total
FROM usuarios
WHERE id_empresa=$idEmpresa
"
)
->fetch_assoc()['total'];

$activos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM usuarios
WHERE estado='Activo'
AND id_empresa=$idEmpresa
"
)
->fetch_assoc()['total'];

$inactivos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM usuarios
WHERE estado='Inactivo'
AND id_empresa=$idEmpresa
"
)
->fetch_assoc()['total'];

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Usuarios

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
padding:30px;
border-radius:18px;
margin-bottom:25px;
box-shadow:0 5px 18px rgba(0,0,0,.08);
border-left:6px solid #0b6b3a;
}

.page-header h2{
font-weight:700;
color:#0b6b3a;
}

.page-header p{
margin-bottom:0;
}

.card-kpi{
border:none;
border-radius:18px;
box-shadow:0 5px 18px rgba(0,0,0,.08);
transition:.3s;
}

.card-kpi:hover{
transform:translateY(-4px);
}

.kpi-icon{
font-size:48px;
color:#0b6b3a;
}

.card-main{
border:none;
border-radius:18px;
box-shadow:0 5px 18px rgba(0,0,0,.08);
}

.card-main .card-header{
background:white;
border-bottom:1px solid #e8e8e8;
padding:18px;
}

.table thead{
background:#0b6b3a;
color:white;
}

.table tbody tr:hover{
background:#f8fbf9;
}

.badge-activo{
background:#198754;
padding:8px 15px;
font-size:12px;
font-weight:600;
}

.badge-inactivo{
background:#dc3545;
padding:8px 15px;
font-size:12px;
font-weight:600;
}

.search-box{
max-width:350px;
border-radius:10px;
}

.btn-action{
margin-right:10px;
margin-bottom:5px;
}

.btn-action:last-child{
margin-right:0;
}
</style>

</head>

<body>

<?php
include("includes/sidebar.php");
?>

<div class="content">

<div class="page-header">

<div class="row align-items-center">

<div class="col-md-8">

<h2>

<i class="bi bi-people-fill"></i>

Administración de Usuarios

</h2>

<p class="text-muted">

Sistema Nacional de Monitoreo SafeDrive

</p>

</div>

<div class="col-md-4 text-end">

<a
href="nuevo_usuario.php"
class="btn btn-success btn-lg ">

<i class="bi bi-person-plus-fill"></i>

Nuevo Usuario

</a>

</div>

</div>

</div>

<div class="row mb-4">

<div class="col-md-4">

<div class="card kpi-card">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Total Usuarios</h6>

<h2>

<?php echo $totalUsuarios; ?>

</h2>

</div>

<i class="bi bi-people-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card kpi-card">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Usuarios Activos</h6>

<h2 class="text-success">

<?php echo $activos; ?>

</h2>

</div>

<i class="bi bi-check-circle-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card kpi-card">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Usuarios Inactivos</h6>

<h2 class="text-danger">

<?php echo $inactivos; ?>

</h2>

</div>

<i class="bi bi-x-circle-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

</div>

<div class="card main-card">

<div class="card-header bg-white">

<div class="row">

<div class="col-md-6">

<h5>

<i class="bi bi-list-ul"></i>

Listado de Usuarios

</h5>

</div>

<div class="col-md-6 text-end">

<input
type="text"
id="buscar"
class="form-control search-box d-inline"
placeholder="Buscar usuario...">

</div>

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table
id="tablaUsuarios"
class="table table-hover align-middle">

<thead>

<tr>

<th>ID</th>
<th>Nombre Completo</th>
<th>Usuario</th>
<th>Correo</th>
<th>Rol</th>
<th>Empresa</th>
<th>Estado</th>
<th width="220">

Acciones

</th>

</tr>

</thead>

<tbody>

<?php
while(
$fila=
$usuarios->fetch_assoc()
)
{
?>

<tr>

<td>

<?php echo $fila['id_usuario']; ?>

</td>

<td>

<strong>

<?php
echo
$fila['nombre']
.
' '
.
$fila['apellido'];
?>

</strong>

</td>

<td>

<?php echo $fila['usuario']; ?>

</td>

<td>

<?php echo $fila['correo']; ?>

</td>

<td>

<?php echo $fila['rol']; ?>

</td>

<td>

<?php

if($fila['id_rol']==1)
{
    echo '<span class="text-muted">No aplica</span>';
}
else
{
    echo $fila['empresa'];
}

?>

</td>

<td>

<?php
if($fila['estado']=='Activo')
{
?>

<span class="badge badge-activo">

Activo

</span>

<?php
}
else
{
?>

<span class="badge badge-inactivo">

Inactivo

</span>

<?php
}
?>

</td>

<td>

<?php if($fila['id_rol']==1){ ?>

<span class="badge bg-dark">

SuperAdministrador

</span>

<?php } else { ?>

<a
href="editar_usuario.php?id=<?php echo $fila['id_usuario']; ?>"
class="btn btn-warning btn-sm btn-action">

<i class="bi bi-pencil-square"></i>

Editar

</a>

<a
href="eliminar_usuario.php?id=<?php echo $fila['id_usuario']; ?>"
class="btn btn-danger btn-sm btn-action"
onclick="return confirm('¿Desea desactivar este usuario?')">

<i class="bi bi-person-x-fill"></i>

Desactivar

</a>

<?php } ?>

</td>

</tr>

<?php
}
?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<script>

document
.getElementById("buscar")
.addEventListener(
"keyup",
function()
{

let filtro =
this.value.toLowerCase();

let filas =
document.querySelectorAll(
"#tablaUsuarios tbody tr"
);

filas.forEach(
fila =>
{
fila.style.display =
fila.innerText
.toLowerCase()
.includes(filtro)
?
""
:
"none";
}
);

}
);

</script>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>

</html>