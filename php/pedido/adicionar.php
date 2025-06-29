<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection();
try{
    $dados = json_decode(file_get_contents('php://input'), true);
    $conexao->begin_transaction();

    if (empty($dados['Status'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'algum valor esta faltando']);
        exit;
    }

    $stmt = $conexao->prepare("INSERT INTO pedido (Status, valor) VALUES (?,0)");
    $stmt->bind_param("s", $dados['Status']);
    $stmt->execute();
    $idPedido = $conexao->insert_id;
    $stmt->close();

    foreach($dados["itens"] as $produto){
        $stmt = $conexao->prepare("INSERT INTO pedido_produto (ID_Pedido,ID_Produto) VALUES (?,?)");
        $stmt->bind_param("ii", $idPedido, $produto['ID_Produto']);
        $stmt->execute();
        $stmt->close();
    }
    $conexao->commit();
    $conexao->close();

} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['mensagem' => 'algo deu errado ' . $e ]);
}