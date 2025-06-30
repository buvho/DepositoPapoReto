<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection();

try {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['ID_Produto']) || empty($dados['quantidade'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Algum valor está faltando']);
        exit;
    }

    $stmt = $conexao->prepare("UPDATE produto SET quantidade = quantidade + ? WHERE ID_Produto = ?");
    $stmt->bind_param("ii", $dados['quantidade'], $dados['ID_Produto']);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['mensagem' => 'Quantidade somada com sucesso']);
    } else {
        http_response_code(422);
        echo json_encode(['mensagem' => 'Algo deu errado :/']);
    }

    $stmt->close();
    $conexao->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['mensagem' => 'Algo deu errado: ' . $e->getMessage()]);
}
