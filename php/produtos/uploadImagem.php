<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

try {
    if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['erro' => 'Imagem não enviada']);
        exit;
    }

    $pasta = '../../imagens/';
    if (!file_exists($pasta)) {
        mkdir($pasta, 0777, true);
    }

    $nomeUnico = uniqid() . '-' . basename($_FILES['imagem']['name']);
    $caminhoCompleto = $pasta . $nomeUnico;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoCompleto)) {
        echo json_encode(['caminho' => $nomeUnico]);
    } else {
        http_response_code(500);
        echo json_encode(['erro' => 'Falha ao mover imagem']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
}
