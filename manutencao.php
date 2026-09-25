<?php
header("Content-Type: application/json");

require "conexao.php";

$metodo = $_SERVER["REQUEST_METHOD"];

if($metodo == "POST"){

    $json = file_get_contents("php://input");

    $dados = json_decode($json,true);

    $sql = "INSERT INTO produtos (equipamento,setor,descricao,prioridade,status_atual) VALUES (?,?,?,?,?)";

    $comando = $pdo -> prepare($sql);

    $comando -> execute([
        $dados["equipamento"],
        $dados["setor"],
        $dados["descricao"],
        $dados["prioridade "],
        $dados["status"]
        ]);
        
    echo json_encode(["Mensagem"=>"Chamado cadastrado com sucesso!"]);

    //continuar daqui fazer os IF e ELSE da prioridade e status, o da prioridade abaixo, depois adicionar os chamados e testar
} if $dados["prioridade"] != "baixa" OR "media" OR "alta"{
    
};

if ($metodo == "GET"){

    $sql = "SELECT * FROM manutencao ORDER BY id";

    $comando = $pdo ->  query($sql);

    $manutencao = $comando -> fetchALL(PDO::FETCH_ASSOC);

    echo json_encode($manutencao);
};

if ($metodo == "PUT"){

    $json = file_get_contents("php://input");

    $dados = json_decode($json,true);

    $sql = "UPDATE manutencao SET equipamento=?, setor=?, descricao=?, prioridade=?, status_atual=? WHERE id=?";

    $comando = $pdo -> prepare($sql);

    $comando -> execute([
        $dados["equipamento"],
        $dados["setor"],
        $dados["descricao"],
        $dados["prioridade "],
        $dados["status"],
        $dados["id"]
    ]);

    echo json_encode(["Mensagem"=>"Chamado atualizado com sucesso!"]);
};

if($metodo == "DELETE"){

    $json = file_get_contents("php://input");

    $dados = json_decode($json,true) ;

    $sql = "DELETE FROM manutencao WHERE id=?";

    $comando = $pdo -> prepare($sql);

    $comando -> execute([
        $dados["id"]
    ]);

    echo json_encode(["Mensagem"=>"Chamado deletado com sucesso"]);
};
