<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once '../conexao.php';
require_once '../log/adicionar.php';
$conexao = getConnection(); // deve retornar um objeto mysqli

try {
    $dados = json_decode(file_get_contents('php://input'), true);

    if (empty($dados['ID_Produto'])) {
        http_response_code(400);
        echo json_encode(['mensagem' => 'Algum valor está faltando']);
        exit;
    }

    $idProduto = $dados['ID_Produto'];

    // Buscar imagem associada ao produto
    $stmt = $conexao->prepare("SELECT imagem FROM produto WHERE ID_Produto = ?");
    $stmt->bind_param("i", $idProduto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($linha = $resultado->fetch_assoc()) {
        if (!empty($linha['imagem'])) {
            $caminho = '../../imagens/' . $linha['imagem'];
            if (file_exists($caminho)) {
                unlink($caminho);
            }
        }
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM produto_categoria WHERE ID_Produto = ?");
    $stmt->bind_param("i", $dados['ID_Produto']);
    $stmt->execute();
    $stmt->close();

    // Excluir produto
    $stmt = $conexao->prepare("DELETE FROM produto WHERE ID_Produto = ?");
    $stmt->bind_param("i", $idProduto);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode(['mensagem' => 'Produto deletado com sucesso']);
        setLog("Produto com id: ". $idProduto." deletado");
        
    } else {
        http_response_code(500);
        echo json_encode(['mensagem' => 'Erro ao deletar o produto']);
    }

    $stmt->close();
    $conexao->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['mensagem' => 'Erro do servidor: ' . $e->getMessage()]);
}
