<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection();
try{
    $dados = json_decode(file_get_contents('php://input'), true);
    if (empty($dados['ID_Produto']) || empty($dados['nome']) || empty($dados['quantidade']) || empty($dados['preco'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'algum valor esta faltando']);
        exit;
    }

    $stmt = $conexao->prepare("UPDATE produto SET nome = ?, quantidade = ?, preco = ? WHERE ID_Produto = ?");
    $stmt->bind_param("siii", $dados['nome'],$dados['quantidade'],$dados['preco'],$dados['ID_Produto']);

    if($stmt->execute()){
        http_response_code(201);
        echo json_encode(['mensagem' => 'valor editado com sucesso']);
    } else {
        http_response_code(422);
        echo json_encode(['mensagem' => 'algo deu errado :/']);
    }

    $stmt->close();
    $conexao->close();
    
} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['mensagem' => 'algo deu errado ' . $e ]);
}