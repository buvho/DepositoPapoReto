<?php
require_once '../conexao.php';
function setLog($string) {
    
    $conexao = getConnection();
    if($string != ""){
        $stmt = $conexao->prepare("INSERT INTO registro (tipo) VALUES (?)");
        $stmt->bind_param("s", $string);
        $stmt->execute();
    }
}
?>