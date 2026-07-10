<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");

$id_rol =
$_SESSION['id_rol'];

$id_empresa =
$_SESSION['id_empresa'];

if($id_rol==1)
{

$sql =
"
SELECT
d.*,
v.placa,
e.nombre empresa

FROM dispositivos d

LEFT JOIN vehiculos v
ON d.id_vehiculo=v.id_vehiculo

INNER JOIN empresas e
ON d.id_empresa=e.id_empresa

ORDER BY d.id_dispositivo DESC
";

$resultado =
$conexion->query($sql);

$total =
$conexion
->query(
"
SELECT COUNT(*) total
FROM dispositivos
"
)
->fetch_assoc()['total'];

$online =
$conexion
->query(
"
SELECT COUNT(*) total
FROM dispositivos
WHERE estado='Online'
"
)
->fetch_assoc()['total'];

$offline =
$conexion
->query(
"
SELECT COUNT(*) total
FROM dispositivos
WHERE estado='Offline'
"
)
->fetch_assoc()['total'];

}
else
{

$sql =
"
SELECT
d.*,
v.placa

FROM dispositivos d

LEFT JOIN vehiculos v
ON d.id_vehiculo=v.id_vehiculo

WHERE d.id_empresa=?

ORDER BY d.id_dispositivo DESC
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
FROM dispositivos
WHERE id_empresa=$id_empresa
"
)
->fetch_assoc()['total'];

$online =
$conexion
->query(
"
SELECT COUNT(*) total
FROM dispositivos
WHERE estado='Online'
AND id_empresa=$id_empresa
"
)
->fetch_assoc()['total'];

$offline =
$conexion
->query(
"
SELECT COUNT(*) total
FROM dispositivos
WHERE estado='Offline'
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

Dispositivos

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

.badge-online{
background:#198754;
padding:8px 15px;
font-size:12px;
font-weight:600;
}

.badge-offline{
background:#dc3545;
padding:8px 15px;
font-size:12px;
font-weight:600;
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

<i class="bi bi-cpu-fill"></i>

Centro de Monitoreo de Dispositivos

</h2>

<p class="text-muted mb-0">

Sistema Inteligente de Seguridad Vehicular

</p>

</div>

<div class="col-md-4 text-end">

<?php if($_SESSION['id_rol']==2){ ?>

<a
href="nuevo_dispositivo.php"
class="btn btn-success">

<i class="bi bi-plus-circle"></i>

Nuevo Dispositivo

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

<h6>Total Dispositivos</h6>

<h2>

<?php echo $total; ?>

</h2>

</div>

<i class="bi bi-hdd-network-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Online</h6>

<h2 class="text-success">

<?php echo $online; ?>

</h2>

</div>

<i class="bi bi-wifi kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Offline</h6>

<h2 class="text-danger">

<?php echo $offline; ?>

</h2>

</div>

<i class="bi bi-wifi-off kpi-icon"></i>

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

<i class="bi bi-router-fill"></i>

Infraestructura de Monitoreo

</h5>

</div>

<div class="col-md-6 text-end">

<input
type="text"
id="buscar"
class="form-control search-box d-inline"
placeholder="Buscar dispositivo...">

</div>

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table
id="tablaDispositivos"
class="table table-hover align-middle">

<thead>

<tr>

<th>ID</th>
<th>Código</th>
<th>Tipo</th>
<th>IP</th>
<th>Puerto</th>
<th>Vehículo</th>
<th>Estado</th>
<th>Última Conexión</th>

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
while(
$fila=
$resultado->fetch_assoc()
)
{
?>

<tr>

<td>

<?php echo $fila['id_dispositivo']; ?>

</td>

<td>

<strong>

<?php echo $fila['codigo']; ?>

</strong>

</td>

<td>

<?php echo $fila['tipo']; ?>

</td>

<td>

<?php echo $fila['ip']; ?>

</td>

<td>

<?php echo $fila['puerto']; ?>

</td>

<td>

<?php echo $fila['placa']; ?>

</td>

<td>

<?php

if($fila['estado']=="Online")
{
?>

<span class="badge badge-online">

<span class="pulso verde"></span>

Online

</span>

<?php
}
else
{
?>

<span class="badge badge-offline">

<span class="pulso rojo"></span>

Offline

</span>

<?php
}

?>

</td>

<td>

<?php

if(
!empty($fila['ultima_conexion'])
)
{
echo date(
'd/m/Y H:i',
strtotime(
$fila['ultima_conexion']
)
);
}
else
{
echo
'<span class="text-muted">Sin conexión</span>';
}

?>

</td>

<?php
if($_SESSION['id_rol']==2)
{
?>

<td>

<a
href="editar_dispositivo.php?id=<?php echo $fila['id_dispositivo']; ?>"
class="btn btn-warning btn-sm">

<i class="bi bi-pencil-square"></i>

</a>

<a
href="eliminar_dispositivo.php?id=<?php echo $fila['id_dispositivo']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('¿Dar de baja el dispositivo?')">

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
"#tablaDispositivos tbody tr"
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

setTimeout(
function()
{
location.reload();
},
60000
);

</script>

</body>

</html>