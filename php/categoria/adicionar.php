<?php
require '../conexao.php';
require_once '../log/adicionar.php';

try{
    $conn = getConnection();
    $dados = json_decode(file_get_contents("php://input"), true);

    if(!isset($dados['nome'], $dados['descricao'])){
        http_response_code(400);
        echo json_encode(['erro' =>'Campos obrigatorios: nome, descricao']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM categoria WHERE nome = ?");
    $stmt->bind_param("s", $dados['nome']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if($result){
        if($result->num_rows > 0){
            http_response_code(409);
            echo json_encode(['erro' => 'Essa categoria ja esta cadastrada']);
        }
        else{
            $stmt = $conn->prepare("INSERT INTO categoria (nome, descricao) VALUES (?, ?)");
            $stmt->bind_param("ss", $dados['nome'], $dados['descricao']);
            $stmt->execute();
            $id_categoria = $conn->insert_id;
            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Categoria cadastrada com sucesso!',
                'id_categoria' => $id_categoria
            ]);
            setLog($dados['nome']." adicionada");
            $stmt->close();
        }
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no servidor: '. $e->getMessage()]);
}
?>