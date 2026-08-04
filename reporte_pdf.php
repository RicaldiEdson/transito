<?php

include("includes/verificar_sesion.php");
include("database/conexion.php");

require('fpdf/fpdf.php');

$id_empresa =
$_SESSION['id_empresa'];

$sqlEmpresa =
"
SELECT nombre
FROM empresas
WHERE id_empresa=?
";

$stmt =
$conexion->prepare($sqlEmpresa);

$stmt->bind_param(
"i",
$id_empresa
);

$stmt->execute();

$empresa =
$stmt
->get_result()
->fetch_assoc();

$nombreEmpresa =
$empresa['nombre'];

$inicio =
$_GET['inicio'];

$fin =
$_GET['fin'];

$tipo =
$_GET['tipo'];

$pdf =
new FPDF('L','mm','A4');

$pdf->AddPage();

/*
=========================================
LOGO
=========================================
*/

$pdf->Image(
'assets/img/user.png',
10,
8,
18
);

/*
=========================================
ENCABEZADO
=========================================
*/

$pdf->SetFont(
'Arial',
'B',
16
);

$pdf->Cell(
0,
10,
utf8_decode('REPORTE SAFEDRIVE'),
0,
1,
'C'
);

$pdf->SetFont(
'Arial',
'',
10
);

$pdf->Cell(
0,
6,
utf8_decode('Sistema Inteligente de Prevención de Accidentes'),
0,
1,
'C'
);

$pdf->Ln(5);

/*
=========================================
DATOS DEL REPORTE
=========================================
*/

$pdf->SetFont(
'Arial',
'B',
10
);

$pdf->Cell(
35,
8,
'Fecha Inicio:'
);

$pdf->SetFont(
'Arial',
'',
10
);

$pdf->Cell(
50,
8,
$inicio
);

$pdf->SetFont(
'Arial',
'B',
10
);

$pdf->Cell(
30,
8,
'Fecha Fin:'
);

$pdf->SetFont(
'Arial',
'',
10
);

$pdf->Cell(
50,
8,
$fin
);





$pdf->SetFont(
'Arial',
'B',
10
);

$pdf->Cell(
20,
8,
'Empresa:'
);
$pdf->SetFont(
'Arial',
'',
10
);

$pdf->Cell(
40,
8,
strtoupper($nombreEmpresa)
);









$pdf->SetFont(
'Arial',
'B',
10
);

$pdf->Cell(
20,
8,
'Tipo:'
);

$pdf->SetFont(
'Arial',
'',
10
);

$pdf->Cell(
40,
8,
strtoupper($tipo)
);

$pdf->Ln(15);

/*
=========================================
CONSULTA
=========================================
*/

if($tipo=='alcohol')
{

$sql = "

SELECT
ea.nivel_alcohol valor,
ea.resultado,
ea.fecha_hora,
c.nombre,
c.apellido

FROM eventos_alcohol ea

INNER JOIN conductores c
ON ea.id_conductor=c.id_conductor

WHERE ea.id_empresa=?
AND DATE(ea.fecha_hora)
BETWEEN ? AND ?

ORDER BY ea.fecha_hora DESC

";

}
else
{

$sql = "

SELECT
es.nivel_riesgo valor,
'RIESGO DETECTADO' resultado,
es.fecha_hora,
c.nombre,
c.apellido

FROM eventos_somnolencia es

INNER JOIN conductores c
ON es.id_conductor=c.id_conductor

WHERE es.id_empresa=?
AND DATE(es.fecha_hora)
BETWEEN ? AND ?

ORDER BY es.fecha_hora DESC

";

}

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

/*
=========================================
TABLA
=========================================
*/

$pdf->SetFont(
'Arial',
'B',
10
);

$pdf->Cell(15,10,'#',1);
$pdf->Cell(70,10,'Conductor',1);
$pdf->Cell(35,10,'Valor',1);
$pdf->Cell(60,10,'Resultado',1);
$pdf->Cell(70,10,'Fecha Hora',1);

$pdf->Ln();

$pdf->SetFont(
'Arial',
'',
9
);

$contador = 1;

while(
$fila =
$res->fetch_assoc()
)
{

$pdf->Cell(
15,
8,
$contador,
1
);

$pdf->Cell(
70,
8,
utf8_decode(
$fila['nombre']
.' '.
$fila['apellido']
),
1
);

$pdf->Cell(
35,
8,
$fila['valor'],
1
);

$pdf->Cell(
60,
8,
utf8_decode(
$fila['resultado']
),
1
);

$pdf->Cell(
70,
8,
$fila['fecha_hora'],
1
);

$pdf->Ln();

$contador++;

}

/*
=========================================
PIE
=========================================
*/

$pdf->Ln(10);

$pdf->SetFont(
'Arial',
'I',
8
);

$pdf->Cell(
0,
5,
utf8_decode(
'Reporte generado automáticamente por SafeDrive - '
.date('d/m/Y H:i:s')
),
0,
1,
'C'
);

$pdf->Output();

?>