<?php
require '../conexao.php';
require_once '../log/adicionar.php';

try{
    $conn = getConnection();
    $dados = json_decode(file_get_contents("php://input"), true);

    if(!isset($dados['id_categoria'])){
        http_response_code(400);
        echo json_encode(['erro' =>'ID da categoria não informado']);
        exit;
    } 

    $stmt = $conn->prepare("DELETE FROM produto_categoria WHERE ID_Categoria = ?");
    $stmt->bind_param("i", $dados['id_categoria']);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM categoria WHERE ID_Categoria = ?");
    $stmt->bind_param("i", $dados['id_categoria']);

    if($stmt->execute()){
        http_response_code(200);
        echo json_encode(['mensagem' => 'Categoria excluída com sucesso']);
        setLog("categoria com id: ".$dados['id_categoria']." deletada com sucesso");
    }
    else{
        http_response_code(422);
        echo json_encode(['mensagem' => 'Falha ao excluir a categoria']);
    }

    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no servidor: '. $e->getMessage()]);
}
?>