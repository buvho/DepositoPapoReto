<?php
require '../conexao.php';
require_once '../log/adicionar.php';

try{
    $conn = getConnection();
    $dados = json_decode(file_get_contents("php://input"), true);

    if(!isset($dados['nome'], $dados['descricao'], $dados['id_categoria'])){
        http_response_code(400);
        echo json_encode(['erro' =>'ID da categoria não informado']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM categoria WHERE ID_Categoria = ?");
    $stmt->bind_param("i", $dados['id_categoria']);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if($result->num_rows < 1){
        http_response_code(404);
        echo json_encode(['erro' => 'Essa categoria não esta cadastrada']);
    }
    else{
        $stmt = $conn->prepare("UPDATE categoria SET nome=?, descricao=? WHERE ID_Categoria=?");
        $stmt->bind_param("ssi", $dados['nome'], $dados['descricao'], $dados['id_categoria']);
        
        if($stmt->execute()){
            http_response_code(200);
            echo json_encode(['mensagem' => 'Categoria atualizada com sucesso!']);
            setLog($dados['nome']." alterada com sucesso");
        }
        else{
            http_response_code(422);
            echo json_encode(['mensagem' => 'Algo deu errado']);
        }
        $stmt->close();
    }
    
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro no servidor: '. $e->getMessage()]);
}
?>