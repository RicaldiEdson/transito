<?php

include("includes/verificar_sesion.php");
include("includes/verificar_superadmin.php");
include("database/conexion.php");

if($_SESSION['id_rol']!=1)
{
die("Acceso denegado");
}

$sql="
SELECT
a.*,
u.nombre,
u.apellido,
u.usuario,
r.nombre AS rol,
e.nombre AS empresa

FROM auditoria a

LEFT JOIN usuarios u
ON a.id_usuario=u.id_usuario

LEFT JOIN roles r
ON u.id_rol=r.id_rol

LEFT JOIN empresas e
ON u.id_empresa=e.id_empresa

ORDER BY a.id_auditoria DESC
";

$resultado=
$conexion->query($sql);

$totalRegistros=
$conexion
->query(
"
SELECT COUNT(*) total
FROM auditoria
"
)
->fetch_assoc()['total'];

$hoy=
$conexion
->query(
"
SELECT COUNT(*) total
FROM auditoria
WHERE DATE(fecha)=CURDATE()
"
)
->fetch_assoc()['total'];

?>

<!DOCTYPE html>

<html lang="es">

<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1">

<title>

Auditoría del Sistema

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
border-bottom:1px solid #e5e5e5;
padding:18px;
}

.table thead{
background:#0b6b3a;
color:white;
}

.table tbody tr:hover{
background:#f8fbf9;
}

.search-box{
max-width:350px;
border-radius:10px;
}

.badge-auditoria{
background:#198754;
padding:8px 14px;
font-size:12px;
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

<i class="bi bi-shield-lock-fill"></i>

Centro Nacional de Auditoría

</h2>

<p class="text-muted">

Policía Boliviana • Dirección Nacional de Tránsito y Seguridad Vial

</p>

</div>

<div class="col-md-4 text-end">



</div>

</div>

</div>

<div class="row mb-4">

<div class="col-md-6">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>

Total de Registros

</h6>

<h2>

<?php echo $totalRegistros; ?>

</h2>

</div>

<i class="bi bi-journal-text kpi-icon"></i>

</div>

</div>

</div>

</div>

<div class="col-md-6">

<div class="card card-kpi">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6>

Movimientos Hoy

</h6>

<h2 class="text-success">

<?php echo $hoy; ?>

</h2>

</div>

<i class="bi bi-activity kpi-icon"></i>

</div>

</div>

</div>

</div>

</div>



<div class="card card-main mb-4">

<div class="card-header bg-white">

<h5>

<i class="bi bi-file-earmark-pdf-fill text-danger"></i>

Generador de Reportes de Auditoría

</h5>

</div>

<div class="card-body">

<form
action="reporte_auditoria_pdf.php"
method="GET"
target="_blank">

<div class="row">

<div class="col-md-3 mb-3">

<label class="form-label">

Fecha Inicio

</label>

<input
type="date"
name="inicio"
class="form-control"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Fecha Fin

</label>

<input
type="date"
name="fin"
class="form-control"
required>

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Empresa

</label>

<select
name="empresa"
class="form-select">

<option value="0">

Todas las Empresas

</option>

<?php

$empresasReporte =
$conexion->query(
"
SELECT *
FROM empresas
ORDER BY nombre
"
);

while($empresa=$empresasReporte->fetch_assoc())
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

</div>

<div class="col-md-3 mb-3">

<label class="form-label">

Usuario

</label>

<select
name="usuario"
class="form-select">

<option value="0">

Todos los Usuarios

</option>

<?php

$usuariosReporte =
$conexion->query(
"
SELECT
id_usuario,
nombre,
apellido
FROM usuarios
ORDER BY nombre
"
);

while($u=$usuariosReporte->fetch_assoc())
{
?>

<option
value="<?php echo $u['id_usuario']; ?>">

<?php
echo $u['nombre']." ".$u['apellido'];
?>

</option>

<?php
}
?>

</select>

</div>

</div>

<hr>

<button
type="submit"
class="btn btn-danger">

<i class="bi bi-file-earmark-pdf-fill"></i>

Generar Reporte PDF

</button>

</form>

</div>

</div>




<div class="card card-main">

<div class="card-header">

<div class="row align-items-center">

<div class="col-md-6">

<h5>

<i class="bi bi-clipboard-data-fill"></i>

Registro de Actividades del Sistema

</h5>

</div>

<div class="col-md-6 text-end">

<input
type="text"
id="buscar"
class="form-control search-box d-inline"
placeholder="Buscar acción o usuario...">

</div>

</div>

</div>

<div class="card-body">

<div class="table-responsive">

<table
id="tablaAuditoria"
class="table table-hover align-middle">

<thead>

<tr>

<th width="5%">ID</th>

<th>Acción Realizada</th>

<th>Fecha</th>

<th>Usuario</th>

<th>Empresa</th>

</tr>

</thead>

<tbody>

<?php
while($fila=$resultado->fetch_assoc())
{
?>

<tr>

<td>
<strong>
#<?php echo $fila['id_auditoria']; ?>
</strong>
</td>

<td>
<i class="bi bi-check-circle-fill text-success"></i>
<?php echo $fila['accion']; ?>
</td>

<td>
<?php echo $fila['fecha']; ?>
</td>

<td>

<?php

if($fila['nombre'])
{
    echo $fila['nombre'].' '.$fila['apellido'];
}
else
{
    echo 'Sistema';
}

?>

<br>

<small class="text-muted">
<?php echo $fila['usuario']; ?>
</small>

</td>

<td>

<?php

if($fila['rol']=='SuperAdministrador')
{
    echo '-';
}
else
{
    echo $fila['empresa'];
}

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

let filtro=
this.value.toLowerCase();

let filas=
document.querySelectorAll(
"#tablaAuditoria tbody tr"
);

filas.forEach(
fila =>
{
fila.style.display=
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