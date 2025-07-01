<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection(); // deve retornar um objeto mysqli

try {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['ID_Pedido'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Algum valor está faltando']);
        exit;
    }

    $stmt = $conexao->prepare("DELETE FROM pedido WHERE ID_Pedido = ?");
    $stmt->bind_param("i", $dados['ID_Pedido']);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['mensagem' => 'Pedido deletado com sucesso']);
    } else {
        http_response_code(500);
        echo json_encode(['mensagem' => 'Erro ao deletar o pedido']);
    }

    $stmt->close();
    $conexao->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['mensagem' => 'Erro do servidor: ' . $e->getMessage()]);
}
