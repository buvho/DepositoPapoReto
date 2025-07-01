<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
require_once '../log/adicionar.php';
$conexao = getConnection();
try{
    $dados = json_decode(file_get_contents('php://input'), true);
    $conexao->begin_transaction();

    if ( !isset($dados['status']) ||  !isset($dados['nome'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'algum valor esta faltando']);
        exit;
    } 

    $stmt = $conexao->prepare("INSERT INTO pedido (status, valor, nome_cliente) VALUES (?,0,?)");
    $stmt->bind_param("ss", $dados['status'], $dados['nome']);

    if (!$stmt->execute()){
        http_response_code(501);
        echo json_encode(['mensagem' => 'N TA FUNCIONADO SE VIRA ']);
    }
    

    $idPedido = $conexao->insert_id;
    $stmt->close();

    $valor = 0;
    foreach($dados["itens"] as $produto){
        $stmt = $conexao->prepare("INSERT INTO pedido_produto (ID_Pedido, ID_Produto, quantidade) VALUES (?,?,?)");
        $stmt->bind_param("iii", $idPedido, $produto['ID_Produto'],$produto['quantidade']);
        $valor += $produto['quantidade'] * $produto['preco'];
        $stmt->execute();
        $stmt->close();

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

    $stmt = $conexao->prepare("UPDATE pedido SET valor = ? WHERE ID_Pedido = ?");
    $stmt->bind_param("di", $valor, $idPedido);
    $stmt->execute();
    $stmt->close();
    
    $nome = $dados['nome'];
    setLog("o pedido de $nome foi adcionado adicionados");
    $conexao->commit();
    $conexao->close();

} catch (Exception $e){
    http_response_code(500);
    echo json_encode(['mensagem' => 'algo deu errado ' . $e ]);
}