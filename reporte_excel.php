<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");

$id_empresa =
$_SESSION['id_empresa'];

$inicio =
$_GET['inicio'];

$fin =
$_GET['fin'];

$tipo =
$_GET['tipo'];

header(
"Content-Type: text/csv; charset=UTF-8"
);

header(
"Content-Disposition: attachment; filename=reporte.csv"
);

$salida =
fopen(
"php://output",
"w"
);

if(
$tipo=='alcohol'
)
{

fputcsv(
$salida,
[
'ID',
'Conductor',
'Alcohol',
'Resultado',
'Fecha'
]
);

$sql = "

SELECT
ea.*,
c.nombre,
c.apellido

FROM eventos_alcohol ea

INNER JOIN conductores c
ON ea.id_conductor=c.id_conductor

WHERE ea.id_empresa=?
AND DATE(ea.fecha_hora)
BETWEEN ? AND ?

";

$stmt =
$conexion->prepare($sql);

$stmt->bind_param(
"iss",
$id_empresa,
$inicio,
$fin
);

$stmt->execute();

$res =
$stmt->get_result();

while(
$fila=
$res->fetch_assoc()
)
{

fputcsv(
$salida,
[
$fila['id_evento_alcohol'],
$fila['nombre'].' '.$fila['apellido'],
$fila['nivel_alcohol'],
$fila['resultado'],
$fila['fecha_hora']
]
);

}

}
else
{

fputcsv(
$salida,
[
'ID',
'Conductor',
'Somnolencia',
'Fecha'
]
);

$sql = "

SELECT
es.*,
c.nombre,
c.apellido

FROM eventos_somnolencia es

INNER JOIN conductores c
ON es.id_conductor=c.id_conductor

WHERE es.id_empresa=?
AND DATE(es.fecha_hora)
BETWEEN ? AND ?

";

$stmt =
$conexion->prepare($sql);

$stmt->bind_param(
"iss",
$id_empresa,
$inicio,
$fin
);

$stmt->execute();

$res =
$stmt->get_result();

while(
$fila=
$res->fetch_assoc()
)
{

fputcsv(
$salida,
[
$fila['id_evento_somnolencia'],
$fila['nombre'].' '.$fila['apellido'],
$fila['nivel_riesgo'],
$fila['fecha_hora']
]
);

}

}

fclose(
$salida
);

exit();
?>