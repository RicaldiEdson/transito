<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");

$idRol =
$_SESSION['id_rol'];

$idEmpresa =
$_SESSION['id_empresa'];

/*
====================================
CONDUCTORES
====================================
*/

if($idRol==1)
{

$conductoresActivos =
$conexion
->query("
SELECT COUNT(*) total
FROM conductores
WHERE estado='Activo'
")
->fetch_assoc()['total'];

$conductoresInactivos =
$conexion
->query("
SELECT COUNT(*) total
FROM conductores
WHERE estado='Inactivo'
")
->fetch_assoc()['total'];

}
else
{

$conductoresActivos =
$conexion
->query("
SELECT COUNT(*) total
FROM conductores
WHERE estado='Activo'
AND id_empresa=$idEmpresa
")
->fetch_assoc()['total'];

$conductoresInactivos =
$conexion
->query("
SELECT COUNT(*) total
FROM conductores
WHERE estado='Inactivo'
AND id_empresa=$idEmpresa
")
->fetch_assoc()['total'];

}

/*
====================================
VEHICULOS
====================================
*/

if($idRol==1)
{

$vehiculosActivos =
$conexion
->query("
SELECT COUNT(*) total
FROM vehiculos
WHERE estado='Activo'
")
->fetch_assoc()['total'];

$vehiculosMantenimiento =
$conexion
->query("
SELECT COUNT(*) total
FROM vehiculos
WHERE estado='Mantenimiento'
")
->fetch_assoc()['total'];

$vehiculosInactivos =
$conexion
->query("
SELECT COUNT(*) total
FROM vehiculos
WHERE estado='Inactivo'
")
->fetch_assoc()['total'];

}
else
{

$vehiculosActivos =
$conexion
->query("
SELECT COUNT(*) total
FROM vehiculos
WHERE estado='Activo'
AND id_empresa=$idEmpresa
")
->fetch_assoc()['total'];

$vehiculosMantenimiento =
$conexion
->query("
SELECT COUNT(*) total
FROM vehiculos
WHERE estado='Mantenimiento'
AND id_empresa=$idEmpresa
")
->fetch_assoc()['total'];

$vehiculosInactivos =
$conexion
->query("
SELECT COUNT(*) total
FROM vehiculos
WHERE estado='Inactivo'
AND id_empresa=$idEmpresa
")
->fetch_assoc()['total'];

}

/*
====================================
DISPOSITIVOS
====================================
*/

if($idRol==1)
{

$online =
$conexion
->query("
SELECT COUNT(*) total
FROM dispositivos
WHERE estado='Online'
")
->fetch_assoc()['total'];

$offline =
$conexion
->query("
SELECT COUNT(*) total
FROM dispositivos
WHERE estado='Offline'
")
->fetch_assoc()['total'];

}
else
{

$online =
$conexion
->query("
SELECT COUNT(*) total
FROM dispositivos
WHERE estado='Online'
AND id_empresa=$idEmpresa
")
->fetch_assoc()['total'];

$offline =
$conexion
->query("
SELECT COUNT(*) total
FROM dispositivos
WHERE estado='Offline'
AND id_empresa=$idEmpresa
")
->fetch_assoc()['total'];

}

if($idRol==1)
{

$empresas =
$conexion
->query(
"SELECT COUNT(*) total FROM empresas"
)
->fetch_assoc()['total'];

$usuarios =
$conexion
->query(
"SELECT COUNT(*) total FROM usuarios"
)
->fetch_assoc()['total'];

$conductores =
$conexion
->query(
"SELECT COUNT(*) total FROM conductores"
)
->fetch_assoc()['total'];

$vehiculos =
$conexion
->query(
"SELECT COUNT(*) total FROM vehiculos"
)
->fetch_assoc()['total'];

$dispositivos =
$conexion
->query(
"SELECT COUNT(*) total FROM dispositivos"
)
->fetch_assoc()['total'];

}
else
{

$conductores =
$conexion
->query(
"
SELECT COUNT(*) total
FROM conductores
WHERE id_empresa=$idEmpresa
"
)
->fetch_assoc()['total'];

$vehiculos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM vehiculos
WHERE id_empresa=$idEmpresa
"
)
->fetch_assoc()['total'];

$dispositivos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM dispositivos
WHERE id_empresa=$idEmpresa
"
)
->fetch_assoc()['total'];

}
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

$alertasPendientes =
$conexion
->query(
"
SELECT COUNT(*) total
FROM alertas
WHERE estado='Pendiente'
"
)
->fetch_assoc()['total'];

$alertasAtendidas =
$conexion
->query(
"
SELECT COUNT(*) total
FROM alertas
WHERE estado='Atendida'
"
)
->fetch_assoc()['total'];

$somnolenciaHoy =
$conexion
->query(
"
SELECT COUNT(*) total
FROM eventos_somnolencia
WHERE DATE(fecha_hora)=CURDATE()
"
)
->fetch_assoc()['total'];

$alcoholHoy =
$conexion
->query(
"
SELECT COUNT(*) total
FROM eventos_alcohol
WHERE DATE(fecha_hora)=CURDATE()
"
)
->fetch_assoc()['total'];

$ultimasAlertas =
$conexion
->query(
"
SELECT
a.*,
c.nombre,
c.apellido

FROM alertas a
LEFT JOIN conductores c
ON a.id_conductor=c.id_conductor
ORDER BY a.fecha_hora DESC
LIMIT 10
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

Dashboard SafeDrive

</title>

<link
href="assets/vendor/bootstrap/css/bootstrap.min.css"
rel="stylesheet">

<link
href="assets/vendor/bootstrap-icons/bootstrap-icons.css"
rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

body{
background:#f4f7fc;
}

.content{
margin-left:260px;
padding:25px;
}

.card-dashboard{
border:none;
border-radius:15px;
box-shadow:0px 4px 12px rgba(0,0,0,.08);
transition:.3s;
}

.card-dashboard:hover{
transform:translateY(-3px);
}

.numero{
font-size:32px;
font-weight:bold;
}

.icono{
font-size:45px;
margin-bottom:10px;
}

.titulo{
color:#1B5E20;
font-weight:bold;
}

</style>

</head>

<body>

<?php
include("includes/sidebar.php");
?>

<div class="content">

<div class="container-fluid">

<h2 class="titulo">

<i class="bi bi-shield-check"></i>

INCOS

</h2>

<p class="text-muted">

Dirección Nacional de Transito

</p>



<div class="row">

<!-- CONDUCTORES -->

<div class="col-lg-4 col-md-6 mb-4">

<div class="card card-dashboard">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<h6 class="text-muted">
Conductores Registrados
</h6>

<h1 class="numero">
<?php echo $conductores; ?>
</h1>

</div>

<i class="bi bi-person-vcard icono text-primary"></i>

</div>

<hr>

<div class="bloque-estados">

<div class="estado-item">

<span class="text-success">
<i class="bi bi-check-circle-fill"></i>
Activos
</span>

<strong>
<?php echo $conductoresActivos; ?>
</strong>

</div>

<div class="estado-item">

<span class="text-danger">
<i class="bi bi-x-circle-fill"></i>
Inactivos
</span>

<strong>
<?php echo $conductoresInactivos; ?>
</strong>

</div>

</div>

</div>

</div>

</div>

<!-- VEHICULOS -->

<div class="col-lg-4 col-md-6 mb-4">

<div class="card card-dashboard">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<h6 class="text-muted">
Vehículos Registrados
</h6>

<h1 class="numero">
<?php echo $vehiculos; ?>
</h1>

</div>

<i class="bi bi-truck icono text-success"></i>

</div>

<hr>

<div class="bloque-estados">

<div class="estado-item">

<span class="text-success">
<i class="bi bi-check-circle-fill"></i>
Activos
</span>

<strong>
<?php echo $vehiculosActivos; ?>
</strong>

</div>

<div class="estado-item">

<span class="text-warning">
<i class="bi bi-tools"></i>
Mantenimiento
</span>

<strong>
<?php echo $vehiculosMantenimiento; ?>
</strong>

</div>

<div class="estado-item">

<span class="text-danger">
<i class="bi bi-x-circle-fill"></i>
Inactivos
</span>

<strong>
<?php echo $vehiculosInactivos; ?>
</strong>

</div>

</div>

</div>

</div>

</div>

<!-- DISPOSITIVOS -->

<div class="col-lg-4 col-md-6 mb-4">

<div class="card card-dashboard">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<h6 class="text-muted">
Dispositivos Instalados
</h6>

<h1 class="numero">
<?php echo $dispositivos; ?>
</h1>

</div>

<i class="bi bi-cpu-fill icono text-warning"></i>

</div>

<hr>

<div class="bloque-estados">

<div class="estado-item">

<span class="text-success">
<i class="bi bi-wifi"></i>
Online
</span>

<strong>
<?php echo $online; ?>
</strong>

</div>

<div class="estado-item">

<span class="text-danger">
<i class="bi bi-wifi-off"></i>
Offline
</span>

<strong>
<?php echo $offline; ?>
</strong>

</div>

</div>

</div>

</div>

</div>

<?php if($idRol==1){ ?>

<!-- EMPRESAS -->

<div class="col-lg-4 col-md-6 mb-4">

<div class="card card-dashboard">

<div class="card-body text-center">

<i class="bi bi-buildings icono text-success"></i>

<h6 class="text-muted mt-2">
Empresas Registradas
</h6>

<h1 class="numero">
<?php echo $empresas; ?>
</h1>

</div>

</div>

</div>

<!-- USUARIOS -->

<div class="col-lg-4 col-md-6 mb-4">

<div class="card card-dashboard">

<div class="card-body text-center">

<i class="bi bi-people-fill icono text-primary"></i>

<h6 class="text-muted mt-2">
Usuarios del Sistema
</h6>

<h1 class="numero">
<?php echo $usuarios; ?>
</h1>

</div>

</div>

</div>

<?php } ?>

</div>




<div class="row">

<div class="col-md-6 mb-3">

<div class="card border-warning">

<div class="card-body text-center">

<i
class="bi bi-eye-fill"
style="font-size:50px;color:#ffc107;">
</i>

<h4 class="mt-2">

Eventos de Somnolencia Hoy

</h4>

<h1 class="text-warning">

<?php echo $somnolenciaHoy; ?>

</h1>

</div>

</div>

</div>

<div class="col-md-6 mb-3">

<div class="card border-danger">

<div class="card-body text-center">

<i
class="bi bi-cup-hot-fill"
style="font-size:50px;color:#dc3545;">
</i>

<h4 class="mt-2">

Eventos de Alcohol Hoy

</h4>

<h1 class="text-danger">

<?php echo $alcoholHoy; ?>

</h1>

</div>

</div>

</div>

</div>

<div class="card">

<div class="card-header bg-danger text-white">

<i class="bi bi-bell-fill"></i>

Últimas Alertas Registradas

</div>

<div class="card-body">

<table class="table table-hover table-bordered">

<thead class="table-dark">

<tr>

<th>Fecha</th>

<th>Conductor</th>

<th>Tipo</th>



</tr>

</thead>

<tbody>

<?php

while(
$fila=
$ultimasAlertas->fetch_assoc()
)
{

?>

<tr>

<td>

<?php echo $fila['fecha_hora']; ?>

</td>

<td>

<?php

echo
$fila['nombre']
.
' '
.
$fila['apellido'];

?>

</td>

<td>

<?php echo $fila['tipo_alerta']; ?>

</td>



<?php


}

?>

</tbody>

</table>

</div>

</div>
<script>

new Chart(
document.getElementById('graficoEventos'),
{
type:'bar',
data:{
labels:[
'Alcohol',
'Somnolencia',
'Pendientes'
],
datasets:[
{
label:'Eventos',
data:[
<?php echo $alcoholHoy; ?>,
<?php echo $somnolenciaHoy; ?>,
<?php echo $alertasPendientes; ?>
]
}
]
}
}
);

new Chart(
document.getElementById('graficoEstado'),
{
type:'pie',
data:{
labels:[
'Online',
'Offline'
],
datasets:[
{
data:[
<?php echo $online; ?>,
<?php echo $offline; ?>
]
}
]
}
}
);

</script>
<script
src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js">
</script>

<meta
http-equiv="refresh"
content="30">

</body>

</html>