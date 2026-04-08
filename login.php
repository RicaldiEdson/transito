<?php

session_start();

if(isset($_SESSION['id_usuario']))
{
    header("Location: dashboard.php");
    exit();
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

SafeDrive - Iniciar Sesión

</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

<link
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
rel="stylesheet">

<style>

:root{
--verde-principal:#0b6b3a;
--verde-secundario:#138a4a;
--verde-hover:#0f7d44;
}

body{

margin:0;
padding:0;
height:100vh;

background:
linear-gradient(
rgba(0,0,0,.55),
rgba(0,0,0,.55)
),
url('assets/img/transporte.jpg');

background-size:cover;
background-position:center;
background-repeat:no-repeat;

display:flex;
justify-content:center;
align-items:center;

font-family:'Segoe UI',sans-serif;

}

.login-container{
width:100%;
max-width:430px;
padding:15px;
}

.login-card{

border:none;
border-radius:22px;
overflow:hidden;

box-shadow:
0 10px 35px rgba(0,0,0,.35);

background:white;

}

.login-header{

background:
linear-gradient(
135deg,
var(--verde-principal),
var(--verde-secundario)
);

padding:30px;
text-align:center;
color:white;

}

.logo{

width:100px;
height:100px;

border-radius:50%;
background:white;

padding:8px;
margin:auto;
margin-bottom:15px;

object-fit:cover;

box-shadow:
0 5px 15px rgba(0,0,0,.25);

}

.login-header h3{

font-weight:700;
margin-bottom:5px;

}

.login-header p{

margin:0;
opacity:.9;

}

.login-body{

padding:30px;

}

.form-label{

font-weight:600;

}

.form-control{

height:50px;
border-radius:12px;

}

.input-group-text{

border-radius:12px 0 0 12px;
background:#f8f9fa;

}

.btn-login{

height:50px;

border:none;
border-radius:12px;

background:var(--verde-principal);

font-weight:600;
font-size:16px;

transition:.3s;

}

.btn-login:hover{

background:var(--verde-hover);

}

.alert{

border-radius:12px;

}

.footer-login{

margin-top:20px;
text-align:center;

color:white;

font-size:13px;

text-shadow:
0 1px 3px rgba(0,0,0,.4);

}

.footer-login strong{

font-size:14px;

}

</style>

</head>

<body>

<div class="login-container">

<div class="card login-card">

<div class="login-header">

<img
src="assets/img/user.png"
class="logo"
alt="Policía de Tránsito">

<h3>

INCOS

</h3>

<p>

Policía Boliviana - Tránsito

</p>

</div>

<div class="login-body">

<?php
if(isset($_GET['error']))
{
?>

<div class="alert alert-danger">

<i class="bi bi-exclamation-triangle-fill"></i>

Usuario o contraseña incorrectos.

</div>

<?php
}
?>

<?php
if(isset($_GET['logout']))
{
?>

<div class="alert alert-success">

<i class="bi bi-check-circle-fill"></i>

Sesión cerrada correctamente.

</div>

<?php
}
?>

<?php

if(isset($_GET['bloqueado']))
{
?>

<div class="alert alert-danger">

<i class="bi bi-shield-lock-fill"></i>

Demasiados intentos fallidos.

Su cuenta ha sido bloqueada durante 15 minutos.

</div>

<?php
}

?>

<form
action="api/login.php"
method="POST">

<div class="mb-3">

<label class="form-label">

Usuario

</label>

<div class="input-group">

<span class="input-group-text">

<i class="bi bi-person-fill"></i>

</span>

<input
type="text"
name="usuario"
class="form-control"
placeholder="Ingrese su usuario"
required>

</div>

</div>

<div class="mb-4">

<label class="form-label">

Contraseña

</label>

<div class="input-group">

<span class="input-group-text">

<i class="bi bi-lock-fill"></i>

</span>

<input
type="password"
name="password"
class="form-control"
placeholder="Ingrese su contraseña"
required>

</div>

</div>

<button
type="submit"
class="btn btn-success btn-login w-100">

<i class="bi bi-box-arrow-in-right"></i>

Ingresar al Sistema

</button>

</form>

<hr>

<div class="text-center">

<a
href="index.html"
class="text-decoration-none text-success fw-bold">

<i class="bi bi-house-fill"></i>

Inicio

</a>

</div>

</div>

</div>

<div class="footer-login">



<br>

Sistema Inteligente de Prevención de Accidentes

<br>

Dirección Nacional de Tránsito 

</div>

</div>

</body>

</html>