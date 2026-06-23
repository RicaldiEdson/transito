<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");

$id_rol =
$_SESSION['id_rol'];

$id_empresa =
$_SESSION['id_empresa'];

/*
=================================
CONSULTA PRINCIPAL
=================================
*/

if($id_rol==1)
{

$sql =
"
SELECT
c.*,
e.nombre empresa

FROM conductores c

INNER JOIN empresas e
ON c.id_empresa=e.id_empresa

ORDER BY c.id_conductor DESC
";

$resultado =
$conexion->query($sql);

$total =
$conexion
->query(
"
SELECT COUNT(*) total
FROM conductores
"
)
->fetch_assoc()['total'];

$activos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM conductores
WHERE estado='Activo'
"
)
->fetch_assoc()['total'];

$inactivos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM conductores
WHERE estado='Inactivo'
"
)
->fetch_assoc()['total'];

}
else
{

$sql =
"
SELECT *
FROM conductores
WHERE id_empresa=?
ORDER BY id_conductor DESC
";

$stmt =
$conexion->prepare($sql);

$stmt->bind_param(
"i",
$id_empresa
);

$stmt->execute();

$resultado =
$stmt->get_result();

$total =
$conexion
->query(
"
SELECT COUNT(*) total
FROM conductores
WHERE id_empresa=$id_empresa
"
)
->fetch_assoc()['total'];

$activos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM conductores
WHERE estado='Activo'
AND id_empresa=$id_empresa
"
)
->fetch_assoc()['total'];

$inactivos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM conductores
WHERE estado='Inactivo'
AND id_empresa=$id_empresa
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

Conductores

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

<i class="bi bi-person-badge-fill"></i>

Gestión de Conductores

</h2>

<p class="text-muted mb-0">

Sistema Nacional de Monitoreo Vehicular

</p>

</div>

<div class="col-md-4 text-end">

<?php if($_SESSION['id_rol']==2){ ?>

<a
href="nuevo_conductor.php"
class="btn btn-success">

<i class="bi bi-plus-circle"></i>

Nuevo Conductor

</a>

<?php } ?>

</div>

</div>

</div>

<div class="row mb-4">

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Total Conductores</h6>

<h2>

<?php echo $total; ?>

</h2>

</div>

<i class="bi bi-people-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Activos</h6>

<h2 class="text-success">

<?php echo $activos; ?>

</h2>

</div>

<i class="bi bi-person-check-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Inactivos</h6>

<h2 class="text-danger">

<?php echo $inactivos; ?>

</h2>

</div>

<i class="bi bi-person-x-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

</div>

<div class="card card-main">

<div class="card-header bg-white">

<div class="row">

<div class="col-md-6">

<h5>

<i class="bi bi-table"></i>

Listado Oficial

</h5>

</div>

<div class="col-md-6 text-end">

<input
type="text"
id="buscar"
class="form-control search-box d-inline"
placeholder="Buscar conductor...">

</div>

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table
id="tablaConductores"
class="table table-hover">

<thead>

<tr>

<th>ID</th>
<th>CI</th>
<th>Conductor</th>
<?php
if($_SESSION['id_rol']==1)
{
?>
<th width="180">
Empresa
</th>
<?php
}
?>
<th>Licencia</th>
<th>Categoría</th>
<th>Estado</th>

<?php
if($_SESSION['id_rol']==2)
{
?>
<th width="180">
Acciones
</th>
<?php
}
?>

</tr>

</thead>

<tbody>

<?php
while($fila=$resultado->fetch_assoc())
{
?>

<tr>

<td>

<?php echo $fila['id_conductor']; ?>

</td>

<td>

<?php echo $fila['ci']; ?>

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


<?php if($id_rol==1){ ?>
<td>

<?php echo $fila['empresa']; ?>

</td>
<?php } ?>



<td>

<?php echo $fila['licencia']; ?>

</td>

<td>

<?php echo $fila['categoria']; ?>

</td>

<td>

<?php

if($fila['estado']=="Activo")
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

<?php
if($_SESSION['id_rol']==2)
{
?>

<td>

<a
href="editar_conductor.php?id=<?php echo $fila['id_conductor']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

</a>

<a
href="eliminar_conductor.php?id=<?php echo $fila['id_conductor']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('¿Dar de baja al conductor?')">

<i class="bi bi-trash"></i>

</a>

</td>

<?php
}
?>

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
"#tablaConductores tbody tr"
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

</body>

</html>