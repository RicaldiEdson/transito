<?php

$paginaActual =
basename($_SERVER['PHP_SELF']);

?>

<style>

:root{
--verde-principal:#0b6b3a;
--verde-secundario:#138a4a;
--verde-claro:#1ea85b;
--verde-hover:#0f7d44;
--blanco:#ffffff;
--gris:#e9ecef;
}

.sidebar{
width:280px;
height:100vh;
position:fixed;
left:0;
top:0;
background:linear-gradient(
180deg,
#0b6b3a 0%,
#0f7d44 50%,
#138a4a 100%
);
overflow-y:auto;
box-shadow:
5px 0px 25px rgba(0,0,0,.20);
z-index:1000;
}

.sidebar::-webkit-scrollbar{
width:6px;
}

.sidebar::-webkit-scrollbar-thumb{
background:rgba(255,255,255,.3);
border-radius:20px;
}

.logo{
padding:25px 15px;
text-align:center;
border-bottom:1px solid rgba(255,255,255,.15);
}

.logo-icon{
width:80px;
height:80px;
border-radius:50%;
background:white;
display:flex;
align-items:center;
justify-content:center;
margin:auto;
margin-bottom:10px;
box-shadow:0 4px 15px rgba(0,0,0,.25);
}

.logo-icon i{
font-size:40px;
color:var(--verde-principal);
}

.logo h4{
color:white;
margin:0;
font-weight:700;
letter-spacing:.5px;
}

.logo small{
color:rgba(255,255,255,.85);
}

.usuario{
padding:20px;
text-align:center;
color:white;
}

.usuario img{
width:100px;
height:100px;
border-radius:50%;
border:2px solid rgba(255,255,255,.25);
background:white;
padding:2px;
object-fit:cover;
box-shadow:0 20px 15px rgba(0,0,0,.20);
}

.usuario h6{
margin-top:15px;
font-weight:600;
font-size:16px;
}

.usuario small{
opacity:.9;
}

.rol{
display:inline-block;
margin-top:10px;
padding:6px 16px;
border-radius:30px;
background:rgba(255,255,255,.15);
backdrop-filter:blur(5px);
font-size:12px;
font-weight:600;
border:1px solid rgba(255,255,255,.2);
}

.menu-titulo{
padding:10px 25px;
font-size:12px;
font-weight:bold;
color:rgba(255,255,255,.65);
letter-spacing:1px;
text-transform:uppercase;
}

.sidebar a{
display:flex;
align-items:center;
padding:14px 22px;
color:white;
text-decoration:none;
font-size:15px;
transition:.3s;
margin:3px 10px;
border-radius:12px;
}

.sidebar a i{
font-size:18px;
width:30px;
}

.sidebar a:hover{
background:rgba(255,255,255,.12);
transform:translateX(4px);
}

.sidebar .active{
background:white;
color:var(--verde-principal)!important;
font-weight:700;
box-shadow:0 5px 15px rgba(0,0,0,.15);
}

.sidebar .active i{
color:var(--verde-principal);
}

.logout{
margin-top:15px;
background:rgba(255,255,255,.08);
}

.logout:hover{
background:#dc3545 !important;
color:white !important;
}

.footer-sidebar{
padding:15px;
text-align:center;
color:rgba(255,255,255,.7);
font-size:12px;
border-top:1px solid rgba(255,255,255,.15);
margin-top:15px;
}

</style>

<div class="sidebar">



<div class="usuario">

<img
src="assets/img/user.png"
alt="Usuario">

<h6>

<?php

if($_SESSION['id_rol']==1)
{
    echo "Policía de Tránsito";
}
else
{
    $sqlEmpresa = "
    SELECT nombre
    FROM empresas
    WHERE id_empresa=?
    ";

    $stmtEmpresa = $conexion->prepare($sqlEmpresa);

    $stmtEmpresa->bind_param(
    "i",
    $_SESSION['id_empresa']
    );

    $stmtEmpresa->execute();

    $empresa = $stmtEmpresa
    ->get_result()
    ->fetch_assoc();

    echo $empresa['nombre'];
}

?>

</h6>

<small>

<?php
echo $_SESSION['usuario'];
?>

</small>

<br>

<span class="rol">

<?php

if($_SESSION['id_rol']==1)
{
echo "SUPERADMINISTRADOR";
}
elseif($_SESSION['id_rol']==2)
{
echo "ADMINISTRADOR";
}
else
{
echo "OPERADOR";
}

?>

</span>

</div>

<div class="menu-titulo">

Principal

</div>

<a
class="<?php if($paginaActual=='dashboard.php' || $paginaActual=='dashboard.php') echo 'active'; ?>"
href="<?php echo ($_SESSION['id_rol']==1)?'dashboard.php':'dashboard.php'; ?>">

<i class="bi bi-speedometer2"></i>

Dashboard

</a>

<?php

if($_SESSION['id_rol']==1)
{

?>

<div class="menu-titulo">

Control Central

</div>

<a
class="<?php if($paginaActual=='empresas.php') echo 'active'; ?>"
href="empresas.php">

<i class="bi bi-building-fill"></i>

Empresas

</a>

<a
class="<?php if($paginaActual=='usuarios.php') echo 'active'; ?>"
href="usuarios.php">

<i class="bi bi-people-fill"></i>

Usuarios

</a>

<a
class="<?php if($paginaActual=='auditoria.php') echo 'active'; ?>"
href="auditoria.php">

<i class="bi bi-clipboard-data-fill"></i>

Auditoría

</a>

 <div class="menu-titulo">

Monitoreo Nacional

</div>

<a
class="<?php if($paginaActual=='conductores.php') echo 'active'; ?>"
href="conductores.php">

<i class="bi bi-person-badge-fill"></i>

Conductores

</a>

<a
class="<?php if($paginaActual=='vehiculos.php') echo 'active'; ?>"
href="vehiculos.php">

<i class="bi bi-truck-front-fill"></i>

Vehículos

</a>

<a
class="<?php if($paginaActual=='dispositivos.php') echo 'active'; ?>"
href="dispositivos.php">

<i class="bi bi-cpu-fill"></i>

Dispositivos

</a>

<a
class="<?php if($paginaActual=='eventos.php') echo 'active'; ?>"
href="eventos.php">

<i class="bi bi-exclamation-triangle-fill"></i>

Eventos Nacionales

</a>

<a
class="<?php if($paginaActual=='reportes_empresas.php') echo 'active'; ?>"
href="reportes_empresas.php">

<i class="bi bi-bar-chart-line-fill"></i>

Reportes por Empresa

</a>


<?php

}
elseif($_SESSION['id_rol']==1)
{

?>

<div class="menu-titulo">

Administración

</div>

<a
class="<?php if($paginaActual=='conductores.php') echo 'active'; ?>"
href="conductores.php">

<i class="bi bi-person-vcard-fill"></i>

Conductores

</a>

<a
class="<?php if($paginaActual=='vehiculos.php') echo 'active'; ?>"
href="vehiculos.php">

<i class="bi bi-truck-front-fill"></i>

Vehículos

</a>

<a
class="<?php if($paginaActual=='dispositivos.php') echo 'active'; ?>"
href="dispositivos.php">

<i class="bi bi-cpu-fill"></i>

Dispositivos

</a>

<a
class="<?php if($paginaActual=='asignaciones.php') echo 'active'; ?>"
href="asignaciones.php">

<i class="bi bi-arrow-left-right"></i>

Asignaciones

</a>

<a
class="<?php if($paginaActual=='eventos.php') echo 'active'; ?>"
href="eventos.php">

<i class="bi bi-exclamation-triangle-fill"></i>

Eventos

</a>

<a
class="<?php if($paginaActual=='reportes.php') echo 'active'; ?>"
href="reportes.php">

<i class="bi bi-file-earmark-bar-graph-fill"></i>

Reportes

</a>



<?php

}
else
{

?>

<div class="menu-titulo">

Consulta

</div>

<a
class="<?php if($paginaActual=='conductores.php') echo 'active'; ?>"
href="conductores.php">

<i class="bi bi-person-vcard-fill"></i>

Conductores

</a>

<a
class="<?php if($paginaActual=='vehiculos.php') echo 'active'; ?>"
href="vehiculos.php">

<i class="bi bi-truck-front-fill"></i>

Vehículos

</a>

<a
class="<?php if($paginaActual=='dispositivos.php') echo 'active'; ?>"
href="dispositivos.php">

<i class="bi bi-cpu-fill"></i>

Dispositivos

</a>



<a
class="<?php if($paginaActual=='asignaciones.php') echo 'active'; ?>"
href="asignaciones.php">

<i class="bi bi-arrow-left-right"></i>

Asignaciones

</a>

<a
class="<?php if($paginaActual=='eventos.php') echo 'active'; ?>"
href="eventos.php">

<i class="bi bi-exclamation-triangle-fill"></i>

Eventos

</a>

<a
class="<?php if($paginaActual=='reportes.php') echo 'active'; ?>"
href="reportes.php">

<i class="bi bi-file-earmark-bar-graph-fill"></i>

Reportes

</a>

<?php

}

?>

<a
href="api/logout.php"
class="logout">

<i class="bi bi-box-arrow-right"></i>

Cerrar Sesión

</a>

<div class="footer-sidebar">

INCOS<br>

Sistema Inteligente de Prevención de Accidentes

</div>

</div>