<?php



include("includes/verificar_sesion.php");
include("includes/verificar_superadmin.php");
include("database/conexion.php");

require('fpdf/fpdf.php');

if($_SESSION['id_rol'] != 1)
{
    die("Acceso denegado");
}

/*
=========================================
PARAMETROS
=========================================
*/

$inicio =
$_GET['inicio'] ?? date('Y-m-01');

$fin =
$_GET['fin'] ?? date('Y-m-d');

$idEmpresa =
$_GET['empresa'] ?? 0;

$tipo =
$_GET['tipo'] ?? 'general';

/*
=========================================
EMPRESA
=========================================
*/

$nombreEmpresa =
"Todas las Empresas";

if($idEmpresa > 0)
{
    $stmt =
    $conexion->prepare(
    "
    SELECT nombre
    FROM empresas
    WHERE id_empresa=?
    "
    );

    $stmt->bind_param(
    "i",
    $idEmpresa
    );

    $stmt->execute();

    $empresa =
    $stmt
    ->get_result()
    ->fetch_assoc();

    $nombreEmpresa =
    $empresa['nombre'];
}

/*
=========================================
FILTRO EMPRESA
=========================================
*/

$filtroEmpresa = "";

if($idEmpresa > 0)
{
    $filtroEmpresa =
    " AND id_empresa=".$idEmpresa;
}

/*
=========================================
ESTADISTICAS GENERALES
=========================================
*/

$totalConductores =
$conexion
->query(
"
SELECT COUNT(*) total
FROM conductores
WHERE 1=1
$filtroEmpresa
"
)
->fetch_assoc()['total'];

$conductoresActivos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM conductores
WHERE estado='Activo'
$filtroEmpresa
"
)
->fetch_assoc()['total'];

$conductoresInactivos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM conductores
WHERE estado='Inactivo'
$filtroEmpresa
"
)
->fetch_assoc()['total'];

/*=====================================*/

$totalVehiculos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM vehiculos
WHERE 1=1
$filtroEmpresa
"
)
->fetch_assoc()['total'];

$vehiculosActivos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM vehiculos
WHERE estado='Activo'
$filtroEmpresa
"
)
->fetch_assoc()['total'];

$vehiculosInactivos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM vehiculos
WHERE estado='Inactivo'
$filtroEmpresa
"
)
->fetch_assoc()['total'];

/*=====================================*/

$totalDispositivos =
$conexion
->query(
"
SELECT COUNT(*) total
FROM dispositivos
WHERE 1=1
$filtroEmpresa
"
)
->fetch_assoc()['total'];

$dispositivosOnline =
$conexion
->query(
"
SELECT COUNT(*) total
FROM dispositivos
WHERE estado='Online'
$filtroEmpresa
"
)
->fetch_assoc()['total'];

$dispositivosOffline =
$conexion
->query(
"
SELECT COUNT(*) total
FROM dispositivos
WHERE estado='Offline'
$filtroEmpresa
"
)
->fetch_assoc()['total'];

/*=====================================*/

$totalAlcohol =
$conexion
->query(
"
SELECT COUNT(*) total
FROM eventos_alcohol
WHERE DATE(fecha_hora)
BETWEEN '$inicio' AND '$fin'
$filtroEmpresa
"
)
->fetch_assoc()['total'];

$totalSomnolencia =
$conexion
->query(
"
SELECT COUNT(*) total
FROM eventos_somnolencia
WHERE DATE(fecha_hora)
BETWEEN '$inicio' AND '$fin'
$filtroEmpresa
"
)
->fetch_assoc()['total'];
$filtroAlcohol = "";
$filtroSomnolencia = "";

if($idEmpresa > 0)
{
    $filtroAlcohol =
    " AND ea.id_empresa=".$idEmpresa;

    $filtroSomnolencia =
    " AND es.id_empresa=".$idEmpresa;
}
if($tipo=="alcohol")
{

$sql = "

SELECT
ea.fecha_hora fecha,

CONCAT(
c.nombre,
' ',
c.apellido
) conductor,

e.nombre empresa,

ea.nivel_alcohol valor,

'MQ-3 (Alcohol)' sensor

FROM eventos_alcohol ea

INNER JOIN conductores c
ON ea.id_conductor=c.id_conductor

LEFT JOIN empresas e
ON ea.id_empresa=e.id_empresa

WHERE DATE(ea.fecha_hora)
BETWEEN '$inicio' AND '$fin'

$filtroAlcohol

ORDER BY ea.fecha_hora DESC

";

$tituloTabla =
"EVENTOS DE ALCOHOL";

}

elseif($tipo=="somnolencia")
{

$sql = "

SELECT
es.fecha_hora fecha,

CONCAT(
c.nombre,
' ',
c.apellido
) conductor,

e.nombre empresa,

es.nivel_riesgo valor,
'Sensor IR / Cámara ESP32-CAM' sensor

FROM eventos_somnolencia es

INNER JOIN conductores c
ON es.id_conductor=c.id_conductor

LEFT JOIN empresas e
ON es.id_empresa=e.id_empresa

WHERE DATE(es.fecha_hora)
BETWEEN '$inicio' AND '$fin'

$filtroSomnolencia

ORDER BY es.fecha_hora DESC

";

$tituloTabla =
"EVENTOS DE SOMNOLENCIA";

}
else
{

$sql = "

SELECT
ea.fecha_hora fecha,

CONCAT(
c.nombre,
' ',
c.apellido
) conductor,

e.nombre empresa,

ea.nivel_alcohol valor,

'MQ-3 (Alcohol)' sensor

FROM eventos_alcohol ea

INNER JOIN conductores c
ON ea.id_conductor=c.id_conductor

LEFT JOIN empresas e
ON ea.id_empresa=e.id_empresa

WHERE DATE(ea.fecha_hora)
BETWEEN '$inicio' AND '$fin'

$filtroAlcohol

UNION ALL

SELECT
es.fecha_hora fecha,

CONCAT(
c.nombre,
' ',
c.apellido
) conductor,

e.nombre empresa,

es.nivel_riesgo valor,

'Sensor IR / Cámara ESP32-CAM' sensor

FROM eventos_somnolencia es

INNER JOIN conductores c
ON es.id_conductor=c.id_conductor

LEFT JOIN empresas e
ON es.id_empresa=e.id_empresa

WHERE DATE(es.fecha_hora)
BETWEEN '$inicio' AND '$fin'

$filtroSomnolencia

ORDER BY fecha DESC

";

$tituloTabla =
"RESUMEN GENERAL";

}

$detalle =
$conexion->query($sql);
if(!$detalle)
{
    die($conexion->error);
}

/*
=========================================
PDF
=========================================
*/

$pdf =
new FPDF(
'L',
'mm',
'A4'
);


$pdf->AddPage();

$pdf->Image(
'assets/img/user.png',
10,
10,
15
);

$pdf->SetFont(
'Arial',
'B',
18
);

$pdf->Cell(
0,
10,
utf8_decode('DIRECCIÓN NACIONAL DE TRÁNSITO Y SEGURIDAD VIAL'),
0,
1,
'C'
);

$pdf->SetFont(
'Arial',
'B',
14
);

$pdf->Cell(
0,
8,
utf8_decode('SISTEMA NACIONAL DE MONITOREO SAFEDRIVE'),
0,
1,
'C'
);
$pdf->Ln(5);
$pdf->SetFont(
'Arial',
'',
10
);

$pdf->Cell(
70,
7,
utf8_decode('Fecha Generación'),
1
);

$pdf->Cell(
90,
7,
date('d/m/Y H:i:s'),
1
);

$pdf->Ln();

$pdf->Cell(
70,
7,
utf8_decode('Empresa'),
1
);

$pdf->Cell(
90,
7,
utf8_decode($nombreEmpresa),
1
);

$pdf->Ln();

$pdf->Cell(
70,
7,
utf8_decode('Periodo'),
1
);

$pdf->Cell(
90,
7,
$inicio.' al '.$fin,
1
);

$pdf->Ln();

$pdf->Cell(
70,
7,
utf8_decode('Tipo Reporte'),
1
);

$pdf->Cell(
90,
7,
strtoupper($tipo),
1
);

$pdf->Ln(15);
/*
=========================================
RESUMEN
=========================================
*/

$pdf->SetFont(
'Arial',
'B',
12
);

$pdf->Cell(
0,
8,
utf8_decode('RESUMEN ESTADÍSTICO GENERAL'),
0,
1
);

$pdf->Ln(3);

$pdf->SetFont(
'Arial',
'B',
10
);

$pdf->Cell(80,8,'MODULO',1);
$pdf->Cell(35,8,'TOTAL',1);
$pdf->Cell(35,8,'ACTIVOS',1);
$pdf->Cell(35,8,'INACTIVOS',1);

$pdf->Ln();

$pdf->SetFont(
'Arial',
'',
10
);

/* CONDUCTORES */

$pdf->Cell(80,8,'Conductores',1);
$pdf->Cell(35,8,$totalConductores,1);
$pdf->Cell(35,8,$conductoresActivos,1);
$pdf->Cell(35,8,$conductoresInactivos,1);
$pdf->Ln();

/* VEHICULOS */

$pdf->Cell(80,8,'Vehiculos',1);
$pdf->Cell(35,8,$totalVehiculos,1);
$pdf->Cell(35,8,$vehiculosActivos,1);
$pdf->Cell(35,8,$vehiculosInactivos,1);
$pdf->Ln();

/* DISPOSITIVOS */

$pdf->Cell(80,8,'Dispositivos',1);
$pdf->Cell(35,8,$totalDispositivos,1);
$pdf->Cell(35,8,$dispositivosOnline,1);
$pdf->Cell(35,8,$dispositivosOffline,1);
$pdf->Ln();

/* ALCOHOL */

$pdf->Cell(80,8,'Eventos Alcohol',1);
$pdf->Cell(35,8,$totalAlcohol,1);
$pdf->Cell(35,8,'-',1);
$pdf->Cell(35,8,'-',1);
$pdf->Ln();

/* SOMNOLENCIA */

$pdf->Cell(80,8,'Eventos Somnolencia',1);
$pdf->Cell(35,8,$totalSomnolencia,1);
$pdf->Cell(35,8,'-',1);
$pdf->Cell(35,8,'-',1);

$pdf->Ln(15);


/*
=========================================
DETALLE
=========================================
*/

$pdf->SetFont(
'Arial',
'B',
12
);

$pdf->Cell(
0,
8,
utf8_decode($tituloTabla),
0,
1
);

$pdf->SetFont(
'Arial',
'B',
10
);

$pdf->Cell(50,8,'Fecha',1);
$pdf->Cell(40,8,'Conductor',1);
$pdf->Cell(55,8,'Empresa',1);
$pdf->Cell(40,8,'Valor Sensor',1);
$pdf->Cell(60,8,'Sensor',1);

$pdf->Ln();

$pdf->SetFont(
'Arial',
'',
10
);



while(
$fila=
$detalle->fetch_assoc()
)
{

$pdf->Cell(
50,
7,
substr(
$fila['fecha'],
0,
16
),
1
);

$pdf->Cell(
40,
7,
utf8_decode(
substr(
(string)$fila['conductor'],
0,
20
)
),
1
);

$pdf->Cell(
55,
7,
utf8_decode(
substr(
(string)$fila['empresa'],
0,
30
)
),
1
);

$pdf->Cell(
40,
7,
utf8_decode(
(string)$fila['valor']
),
1
);

$pdf->Cell(
60,
7,
utf8_decode(
(string)$fila['sensor']
),
1
);

$pdf->Ln();

}

/*
=========================================
FIRMA
=========================================
*/

$pdf->SetFont(
'Arial',
'I',
9
);

$pdf->Cell(
0,
6,
utf8_decode(
'Documento generado automáticamente por SafeDrive'
),
0,
1,
'C'
);

$pdf->Cell(
0,
6,
utf8_decode(
'Dirección Nacional de Tránsito y Seguridad Vial'
),
0,
1,
'C'
);
$pdf->Output(
'I',
'reporte_safedrive.pdf'
);

exit();