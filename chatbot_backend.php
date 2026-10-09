<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/chatbot_respuestas.php';

header('Content-Type: application/json; charset=utf-8');

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

try {
    if (!isset($_SESSION['chat_historial']) || !is_array($_SESSION['chat_historial'])) {
        $_SESSION['chat_historial'] = [];
    }

    $accion = $_GET['accion'] ?? '';

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $accion === 'historial') {
        jsonResponse(['historial' => array_slice($_SESSION['chat_historial'], -50)]);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $accion === 'limpiar') {
        $_SESSION['chat_historial'] = [];
        jsonResponse(['ok' => true]);
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse(['error' => 'Método no permitido.'], 405);
    }

    $mensaje = trim($_POST['mensaje'] ?? '');
    if ($mensaje === '') {
        jsonResponse(['error' => 'Escribí un mensaje.'], 400);
    }

    $mensaje = mb_substr($mensaje, 0, 300, 'UTF-8');
    $respuesta = generarRespuesta($pdo, $mensaje);

    $_SESSION['chat_historial'][] = [
        'remitente' => 'usuario',
        'mensaje' => $mensaje,
        'enlaces' => []
    ];

    $_SESSION['chat_historial'][] = [
        'remitente' => 'bot',
        'mensaje' => $respuesta['texto'],
        'enlaces' => $respuesta['enlaces'] ?? []
    ];

    $_SESSION['chat_historial'] = array_slice($_SESSION['chat_historial'], -50);

    jsonResponse($respuesta);
} catch (Throwable $e) {
    error_log('AutoMarket chatbot: ' . $e->getMessage());
    jsonResponse(['error' => 'El asistente no está disponible. Revisá la conexión con la base de datos.'], 500);
}
