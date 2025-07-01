<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
$conexao = getConnection(); // deve retornar um objeto mysqli

try {
    $dados = json_decode(file_get_contents('php://input'), true);
    $conexao->begin_transaction();

    foreach($dados["itens"] as $produto){
        $stmt = $conexao->prepare("SELECT quantidade FROM produto WHERE ID_Produto = ?");
        $stmt->bind_param("i", $produto['ID_Produto']);
        $stmt->execute();
        $stmt->bind_result($quantidadeAtual);
        $stmt->fetch();
        $stmt->close();

        if ($quantidadeAtual < $produto['quantidade']) {
            $conexao->rollback();
            http_response_code(400);
            echo json_encode([
                'mensagem' => "Estoque insuficiente para algum produto"
            ]);
            exit;
        } else {
            $stmt = $conexao->prepare("UPDATE produto SET quantidade = quantidade - ? WHERE ID_Produto = ?");
            $stmt->bind_param("ii", $produto['quantidade'], $produto['ID_Produto']);
            $stmt->execute();
            $stmt->close();
        }
    }
    $nome = $dados['nome']
    setLog("o pedido de $nome foi concluido");
    $conexao->commit();
    $conexao->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['mensagem' => 'Erro do servidor: ' . $e->getMessage()]);
}