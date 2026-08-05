<?php

include("includes/verificar_sesion.php");
include("includes/verificar_superadmin.php");
include("database/conexion.php");

require("fpdf/fpdf.php");

$inicio =
$_GET['inicio'];

$fin =
$_GET['fin'];

$empresa =
$_GET['empresa'];

$usuario =
$_GET['usuario'];

$where =
" WHERE DATE(a.fecha)
BETWEEN '$inicio'
AND '$fin' ";

if($empresa != 0)
{
$where .=
" AND u.id_empresa=$empresa ";
}

if($usuario != 0)
{
$where .=
" AND a.id_usuario=$usuario ";
}

$sql = "

SELECT
a.*,
u.nombre,
u.apellido,
u.usuario,
e.nombre AS empresa

FROM auditoria a

LEFT JOIN usuarios u
ON a.id_usuario=u.id_usuario

LEFT JOIN empresas e
ON u.id_empresa=e.id_empresa

$where

ORDER BY a.fecha DESC

";

$resultado =
$conexion->query($sql);


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
16
);

$pdf->Cell(
0,
10,
utf8_decode('DIRECCIÓN NACIONAL DE TRÁNSITO Y SEGURIDAD VIAL'),
0,
1,
'C'
);

$pdf->Cell(
0,
8,
utf8_decode('SISTEMA NACIONAL DE AUDITORÍA SAFEDRIVE'),
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
0,
6,
utf8_decode('Fecha de generación: ')
.date('d/m/Y H:i:s'),
0,
1
);


$pdf->Cell(
0,
6,
utf8_decode('Periodo: ')
.$inicio.
' al '.
$fin,
0,
1
);


$pdf->Ln(5);

$pdf->SetFont(
'Arial',
'B',
9
);

$pdf->Cell(15,8,'ID',1);
$pdf->Cell(100,8,'ACCION',1);
$pdf->Cell(45,8,'FECHA',1);
$pdf->Cell(60,8,'USUARIO',1);
$pdf->Cell(55,8,'EMPRESA',1);

$pdf->Ln();

$pdf->SetFont(
'Arial',
'',
8
);

while(
$fila=
$resultado->fetch_assoc()
)
{

$pdf->Cell(
15,
8,
$fila['id_auditoria'],
1
);

$pdf->Cell(
100,
8,
utf8_decode(
substr(
$fila['accion'],
0,
60
)
),
1
);

$pdf->Cell(
45,
8,
$fila['fecha'],
1
);

$usuarioTexto =
(
$fila['nombre']
)
?
$fila['nombre'].' '.$fila['apellido'].' ('.$fila['usuario'].')'
:
'Sistema';

$pdf->Cell(
60,
8,
utf8_decode($usuarioTexto),
1
);

$pdf->Cell(
55,
8,
utf8_decode(
$fila['empresa']
??
'Policia de Transito'
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