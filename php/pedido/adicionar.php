<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection();
try{
    $dados = json_decode(file_get_contents('php://input'), true);
    $conexao->begin_transaction();

    if (empty($dados['status']) || empty($dados['nome'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'algum valor esta faltando']);
        exit;
    }

    $stmt = $conexao->prepare("INSERT INTO pedido (status, valor, nome_cliente) VALUES (?,0,?)");
    $stmt->bind_param("ss", $dados['status'], $dados['nome']);

    if (!$stmt->execute()){
        http_response_code(500);
        echo json_encode(['mensagem' => 'N TA FUNCIONADO SE VIRA ']);
    }
    

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