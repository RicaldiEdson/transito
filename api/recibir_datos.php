<?php
declare(strict_types=1);

date_default_timezone_set('America/La_Paz');
header('Content-Type: application/json; charset=utf-8');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . '/../database/conexion.php';

function responder(int $codigoHttp, string $estado, string $mensaje, array $extra = []): never
{
    http_response_code($codigoHttp);

    echo json_encode(
        array_merge(
            [
                'estado' => $estado,
                'mensaje' => $mensaje
            ],
            $extra
        ),
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    responder(405, 'error', 'Método no permitido.');
}

$codigo = trim((string) ($_POST['codigo'] ?? ''));
$tipo = strtolower(trim((string) ($_POST['tipo'] ?? '')));
$valorRecibido = trim((string) ($_POST['valor'] ?? ''));

if ($codigo === '' || $tipo === '' || $valorRecibido === '') {
    responder(400, 'error', 'Se requieren codigo, tipo y valor.');
}

if (!in_array($tipo, ['alcohol', 'somnolencia'], true)) {
    responder(400, 'error', 'Tipo de evento inválido.');
}

if (!is_numeric($valorRecibido)) {
    responder(400, 'error', 'El valor debe ser numérico.');
}

$valor = (float) $valorRecibido;

if ($tipo === 'alcohol' && ($valor < 0 || $valor > 5)) {
    responder(400, 'error', 'El nivel de alcohol debe estar entre 0 y 5.');
}

if ($tipo === 'somnolencia' && ($valor < 0 || $valor > 100)) {
    responder(400, 'error', 'El nivel de somnolencia debe estar entre 0 y 100.');
}

$tipoDispositivoEsperado = $tipo === 'alcohol' ? 'MQ-3' : 'ESP32-CAM';

try {
    /*
     * El código debe ser único globalmente.
     * Como tu base permite repetirlo entre empresas, se rechaza
     * cualquier código que devuelva más de un dispositivo.
     */
    $sqlDispositivo = '
        SELECT
            d.id_dispositivo,
            d.tipo,
            d.id_empresa,
            d.id_vehiculo
        FROM dispositivos d
        INNER JOIN vehiculos v
            ON v.id_vehiculo = d.id_vehiculo
        WHERE d.codigo = ?
          AND d.id_vehiculo IS NOT NULL
          AND v.estado = "Activo"
    ';

    $stmt = $conexion->prepare($sqlDispositivo);
    $stmt->bind_param('s', $codigo);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        responder(404, 'error', 'Dispositivo no registrado, sin vehículo o vehículo inactivo.');
    }

    if ($resultado->num_rows > 1) {
        responder(409, 'error', 'El código del dispositivo no es único.');
    }

    $dispositivo = $resultado->fetch_assoc();
    $stmt->close();

    if ($dispositivo['tipo'] !== $tipoDispositivoEsperado) {
        responder(
            403,
            'error',
            "El dispositivo no está autorizado para registrar {$tipo}."
        );
    }

    $idDispositivo = (int) $dispositivo['id_dispositivo'];
    $idEmpresa = (int) $dispositivo['id_empresa'];
    $idVehiculo = (int) $dispositivo['id_vehiculo'];

    /*
     * La asignación activa determina el conductor:
     * nunca se acepta id_conductor desde el dispositivo.
     */
    $sqlAsignacion = '
        SELECT ac.id_conductor
        FROM asignacion_conductor ac
        INNER JOIN conductores c
            ON c.id_conductor = ac.id_conductor
        WHERE ac.id_vehiculo = ?
          AND ac.estado = "Activa"
          AND c.estado = "Activo"
          AND c.id_empresa = ?
        LIMIT 1
    ';

    $stmt = $conexion->prepare($sqlAsignacion);
    $stmt->bind_param('ii', $idVehiculo, $idEmpresa);
    $stmt->execute();

    $asignacion = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$asignacion) {
        responder(409, 'error', 'No existe un conductor activo asignado al vehículo.');
    }

    $idConductor = (int) $asignacion['id_conductor'];

    $conexion->begin_transaction();

    if ($tipo === 'alcohol') {
        /*
         * resultado se incluye solo porque la columna es NOT NULL.
         * El trigger trg_evento_alcohol_bi lo calcula y reemplaza.
         * El valor 0.50 debe coincidir con la calibración real del MQ-3.
         */
        $resultadoInicial = 'Apto';

        $sqlEvento = '
            INSERT INTO eventos_alcohol (
                nivel_alcohol,
                resultado,
                id_vehiculo,
                id_conductor,
                id_dispositivo,
                id_empresa
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ';

        $stmt = $conexion->prepare($sqlEvento);
        $stmt->bind_param(
            'dsiiii',
            $valor,
            $resultadoInicial,
            $idVehiculo,
            $idConductor,
            $idDispositivo,
            $idEmpresa
        );
        $stmt->execute();

        $idEvento = $conexion->insert_id;
        $stmt->close();

        $resultadoCalculado = $valor >= 0.50 ? 'No Apto' : 'Apto';
    } else {
        /*
         * Igual que alcohol: el trigger calcula el resultado final.
         */
        $resultadoInicial = 'Normal';

        $sqlEvento = '
            INSERT INTO eventos_somnolencia (
                nivel_riesgo,
                resultado,
                id_vehiculo,
                id_conductor,
                id_dispositivo,
                id_empresa
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ';

        $stmt = $conexion->prepare($sqlEvento);
        $stmt->bind_param(
            'dsiiii',
            $valor,
            $resultadoInicial,
            $idVehiculo,
            $idConductor,
            $idDispositivo,
            $idEmpresa
        );
        $stmt->execute();

        $idEvento = $conexion->insert_id;
        $stmt->close();

        if ($valor >= 90) {
            $resultadoCalculado = 'Somnolencia Critica';
        } elseif ($valor >= 70) {
            $resultadoCalculado = 'Riesgo';
        } else {
            $resultadoCalculado = 'Normal';
        }
    }

    $sqlConexion = '
        UPDATE dispositivos
        SET estado = "Online",
            ultima_conexion = NOW()
        WHERE id_dispositivo = ?
    ';

    $stmt = $conexion->prepare($sqlConexion);
    $stmt->bind_param('i', $idDispositivo);
    $stmt->execute();
    $stmt->close();

    $conexion->commit();

    responder(201, 'ok', 'Evento registrado correctamente.', [
        'tipo' => $tipo,
        'valor' => $valor,
        'resultado' => $resultadoCalculado,
        'id_evento' => $idEvento,
        'id_conductor' => $idConductor,
        'id_vehiculo' => $idVehiculo
    ]);
} catch (mysqli_sql_exception $e) {
    if (isset($conexion) && $conexion->errno !== 0) {
        try {
            $conexion->rollback();
        } catch (Throwable $rollbackError) {
            // No se expone información interna.
        }
    }

    error_log('recibir_datos.php: ' . $e->getMessage());

    responder(500, 'error', 'No fue posible registrar el evento.');
}