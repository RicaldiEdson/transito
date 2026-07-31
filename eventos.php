<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");

$id_empresa =
$_SESSION['id_empresa'];

$tipo =
$_GET['tipo'] ?? 'alcohol';

/*
=========================================
ESTADISTICAS
=========================================
*/

if($_SESSION['id_rol']==1)
{

    $totalAlcohol =
    $conexion
    ->query(
    "
    SELECT COUNT(*) total
    FROM eventos_alcohol
    "
    )
    ->fetch_assoc()['total'];

    $totalSomnolencia =
    $conexion
    ->query(
    "
    SELECT COUNT(*) total
    FROM eventos_somnolencia
    "
    )
    ->fetch_assoc()['total'];

    $alertas =
    $conexion
    ->query(
    "
    SELECT COUNT(*) total
    FROM alertas
    "
    )
    ->fetch_assoc()['total'];

}
else
{

    $totalAlcohol =
    $conexion
    ->query(
    "
    SELECT COUNT(*) total
    FROM eventos_alcohol
    WHERE id_empresa=$id_empresa
    "
    )
    ->fetch_assoc()['total'];

    $totalSomnolencia =
    $conexion
    ->query(
    "
    SELECT COUNT(*) total
    FROM eventos_somnolencia
    WHERE id_empresa=$id_empresa
    "
    )
    ->fetch_assoc()['total'];

    $alertas =
    $conexion
    ->query(
    "
    SELECT COUNT(*) total
    FROM alertas
    WHERE id_empresa=$id_empresa
    "
    )
    ->fetch_assoc()['total'];

}

/*
=========================================
CONSULTA
=========================================
*/

if($tipo=='alcohol')
{

if($_SESSION['id_rol']==1)
{

$sql = "

SELECT
ea.id_evento_alcohol id,
'ALCOHOL' tipo,
ea.nivel_alcohol valor,
ea.resultado,
ea.fecha_hora,
c.nombre,
c.apellido,
d.codigo,
e.nombre AS empresa

FROM eventos_alcohol ea

INNER JOIN conductores c
ON ea.id_conductor=c.id_conductor

INNER JOIN dispositivos d
ON ea.id_dispositivo=d.id_dispositivo

LEFT JOIN empresas e
ON ea.id_empresa=e.id_empresa

ORDER BY ea.fecha_hora DESC

";

$stmt =
$conexion->prepare($sql);

}
else
{

$sql = "

SELECT
ea.id_evento_alcohol id,
'ALCOHOL' tipo,
ea.nivel_alcohol valor,
ea.resultado,
ea.fecha_hora,
c.nombre,
c.apellido,
d.codigo

FROM eventos_alcohol ea

INNER JOIN conductores c
ON ea.id_conductor=c.id_conductor

INNER JOIN dispositivos d
ON ea.id_dispositivo=d.id_dispositivo

WHERE ea.id_empresa=?

ORDER BY ea.fecha_hora DESC

";

$stmt =
$conexion->prepare($sql);

$stmt->bind_param(
"i",
$id_empresa
);

}

}

else
{

if($_SESSION['id_rol']==1)
{

$sql = "

SELECT
es.id_evento_somnolencia id,
'SOMNOLENCIA' tipo,
es.nivel_riesgo valor,
es.resultado,
es.fecha_hora,
c.nombre,
c.apellido,
d.codigo,
e.nombre AS empresa

FROM eventos_somnolencia es

INNER JOIN conductores c
ON es.id_conductor=c.id_conductor

INNER JOIN dispositivos d
ON es.id_dispositivo=d.id_dispositivo

LEFT JOIN empresas e
ON es.id_empresa=e.id_empresa

ORDER BY es.fecha_hora DESC

";

$stmt =
$conexion->prepare($sql);

}
else
{

$sql = "

SELECT
es.id_evento_somnolencia id,
'SOMNOLENCIA' tipo,
es.nivel_riesgo valor,
es.resultado,
es.fecha_hora,
c.nombre,
c.apellido,
d.codigo

FROM eventos_somnolencia es

INNER JOIN conductores c
ON es.id_conductor=c.id_conductor

INNER JOIN dispositivos d
ON es.id_dispositivo=d.id_dispositivo

WHERE es.id_empresa=?

ORDER BY es.fecha_hora DESC

";

$stmt =
$conexion->prepare($sql);

$stmt->bind_param(
"i",
$id_empresa
);

}

}
$stmt->execute();

$resultado =
$stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Centro de Eventos

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

.badge-alcohol{
background:#dc3545;
font-size:12px;
padding:8px 12px;
}

.badge-somnolencia{
background:#ffc107;
color:black;
font-size:12px;
padding:8px 12px;
}
</style>

</head>

<body>

<?php
include("includes/sidebar.php");
?>

<div class="content">

<div class="page-header">

<div class="row">

<div class="col-md-8">

<h2>

<i class="bi bi-shield-exclamation"></i>

Centro de Eventos

</h2>

<p class="text-muted">

Monitoreo de Alcohol y Somnolencia

</p>

</div>

<div class="col-md-4 text-end">

<a
href="?tipo=alcohol"
class="btn btn-danger">

<i class="bi bi-cup-straw"></i>

Alcohol

</a>

<a
href="?tipo=somnolencia"
class="btn btn-warning">

<i class="bi bi-eye-fill"></i>

Somnolencia

</a>

</div>

</div>

</div>

<div class="row mb-4">

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Eventos Alcohol</h6>

<h2 class="text-danger">

<?php echo $totalAlcohol; ?>

</h2>

</div>

<i class="bi bi-cup-hot-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Eventos Somnolencia</h6>

<h2 class="text-warning">

<?php echo $totalSomnolencia; ?>

</h2>

</div>

<i class="bi bi-eye-fill kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>Alertas Generadas</h6>

<h2 class="text-success">

<?php echo $alertas; ?>

</h2>

</div>

<i class="bi bi-bell-fill kpi-icon"></i>

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

<i class="bi bi-list-ul"></i>

Historial de Eventos

</h5>

</div>

<div class="col-md-6 text-end">

<input
type="text"
id="buscar"
class="form-control search-box d-inline"
placeholder="Buscar evento...">

</div>

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table
id="tablaEventos"
class="table table-hover align-middle">

<thead>

<tr>

<th>ID</th>
<th>Tipo</th>
<th>Valor</th>
<th>Resultado</th>
<th>Conductor</th>
<?php if($_SESSION['id_rol']==1){ ?>
<th>Empresa</th>
<?php } ?>
<th>Dispositivo</th>
<th>Fecha</th>

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

<?php echo $fila['id']; ?>

</td>

<td>

<?php
if($fila['tipo']=="ALCOHOL")
{
?>

<span class="badge badge-alcohol">

ALCOHOL

</span>

<?php
}
else
{
?>

<span class="badge badge-somnolencia">

SOMNOLENCIA

</span>

<?php
}
?>

</td>

<td>

<strong>

<?php echo $fila['valor']; ?>

</strong>

</td>

<td>

<?php echo $fila['resultado']; ?>

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

<?php if($_SESSION['id_rol']==1){ ?>

<td>

<?php echo $fila['empresa']; ?>

</td>

<?php } ?>

<td>

<?php echo $fila['codigo']; ?>

</td>

<td>

<?php

echo date(
'd/m/Y H:i:s',
strtotime(
$fila['fecha_hora']
)
);

?>

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
"#tablaEventos tbody tr"
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