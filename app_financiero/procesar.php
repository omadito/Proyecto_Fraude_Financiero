<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Inicializar un array para almacenar errores
    $errors = [];

    // Validar y capturar cada campo con el tipo de dato correcto
    $monto = isset($input['monto']) ? (float)$input['monto'] : null;
    $saldoAntiguoOrg = isset($input['saldoAntiguoOrg']) ? (float)$input['saldoAntiguoOrg'] : null;
    $nuevoSaldoOrg = isset($input['nuevoSaldoOrg']) ? (float)$input['nuevoSaldoOrg'] : null;
    $saldoAntiguoDest = isset($input['saldoAntiguoDest']) ? (float)$input['saldoAntiguoDest'] : null;
    $nuevoSaldoDest = isset($input['nuevoSaldoDest']) ? (float)$input['nuevoSaldoDest'] : null;
    $tipo_CASH_OUT = isset($input['tipo_CASH_OUT']) ? (int)$input['tipo_CASH_OUT'] : null;
    $tipo_DEBIT = isset($input['tipo_DEBIT']) ? (int)$input['tipo_DEBIT'] : null;
    $tipo_PAYMENT = isset($input['tipo_PAYMENT']) ? (int)$input['tipo_PAYMENT'] : null;
    $tipo_TRANSFER = isset($input['tipo_TRANSFER']) ? (int)$input['tipo_TRANSFER'] : null;

    // Verificar si hay campos faltantes
    if (is_null($monto)) $errors[] = 'El campo "monto" es requerido.';
    if (is_null($saldoAntiguoOrg)) $errors[] = 'El campo "saldoAntiguoOrg" es requerido.';
    if (is_null($nuevoSaldoOrg)) $errors[] = 'El campo "nuevoSaldoOrg" es requerido.';
    if (is_null($saldoAntiguoDest)) $errors[] = 'El campo "saldoAntiguoDest" es requerido.';
    if (is_null($nuevoSaldoDest)) $errors[] = 'El campo "nuevoSaldoDest" es requerido.';
    if (is_null($tipo_CASH_OUT)) $errors[] = 'El campo "tipo_CASH_OUT" es requerido.';
    if (is_null($tipo_DEBIT)) $errors[] = 'El campo "tipo_DEBIT" es requerido.';
    if (is_null($tipo_PAYMENT)) $errors[] = 'El campo "tipo_PAYMENT" es requerido.';
    if (is_null($tipo_TRANSFER)) $errors[] = 'El campo "tipo_TRANSFER" es requerido.';

    // Si hay errores, devolverlos en la respuesta
    if (!empty($errors)) {
        echo json_encode(['resultado' => 'Error en los datos proporcionados', 'errores' => $errors]);
        exit;
    }

    // Preparar los datos para la solicitud a la API
    $data = [
        'monto' => $monto,
        'saldo_antes_origen' => $saldoAntiguoOrg,
        'saldo_despues_origen' => $nuevoSaldoOrg,
        'saldo_antes_destino' => $saldoAntiguoDest,
        'saldo_despues_destino' => $nuevoSaldoDest,
        'tipo_transaccion_CASH_OUT' => $tipo_CASH_OUT,
        'tipo_transaccion_DEBIT' => $tipo_DEBIT,
        'tipo_transaccion_PAYMENT' => $tipo_PAYMENT,
        'tipo_transaccion_TRANSFER' => $tipo_TRANSFER
    ];

    // Realizar la solicitud a la API
    $api_url = 'http://127.0.0.1:5000/prediccion';

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo json_encode(['resultado' => 'Error al conectar con la API', 'detalle' => curl_error($ch)]);
    } else {
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code != 200) {
            echo json_encode(['resultado' => 'Error en la respuesta de la API', 'http_code' => $http_code, 'respuesta' => $result]);
        } else {
            $response = json_decode($result, true);
            // Verificar si la decodificación inicial fue exitosa
            if (json_last_error() === JSON_ERROR_NONE) {
                echo json_encode(['resultado' => $response]);
            } else {
                // Intentar decodificar nuevamente si es un JSON adicionalmente escapado
                $response = json_decode(stripslashes($result), true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo json_encode(['resultado' => $response]);
                } else {
                    echo json_encode(['resultado' => 'Respuesta inesperada de la API', 'respuesta' => $result]);
                }
            }
        }
    }

    curl_close($ch);
}
?>
