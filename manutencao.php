<?php
header("Content-Type: application/json");

require "conexao.php";

$metodo = $_SERVER["REQUEST_METHOD"];


if($metodo == "POST"){

    $json = file_get_contents("php://input");

    $dados = json_decode($json,true);

    if (!is_array($dados) || empty($dados)) {
        echo json_encode(["Mensagem" => "Erro: Corpo da requisição inválido ou JSON vazio."]);
        exit;
    }

    if (empty($dados["equipamento"]) || empty($dados["setor"]) || empty($dados["descricao"]) || empty($dados["prioridade"]) || empty($dados["status"])) {
        echo json_encode(["Mensagem" => "Erro: Todos os campos são obrigatórios."]);
        exit;
    }

    if (! in_array($dados["prioridade"], ["baixa","media","alta"], true )){

        echo json_encode(["Mensagem" =>"Erro: Prioridade inválida! Use baixa, media ou alta."]);
        exit;
    }

    if ( ! in_array($dados["status"], ["aberto", "em andamento", "concluido"], true )){

        echo json_encode(["Mensagem" => "Erro: Status inválido! Use aberto, em andamento ou concluido."]);
        exit;
    }

    $sql = "INSERT INTO manutencao (equipamento,setor,descricao,prioridade,status_atual) VALUES (?,?,?,?,?)";

    $comando = $pdo -> prepare($sql);

    $comando -> execute([
        $dados["equipamento"],
        $dados["setor"],
        $dados["descricao"],
        $dados["prioridade"],
        $dados["status"]
    ]);   

    echo json_encode(["Mensagem"=>"Chamado cadastrado com sucesso!"]);

}


if ($metodo == "GET"){

    $sql = "SELECT * FROM manutencao ORDER BY id";

    $comando = $pdo ->  query($sql);

    $manutencao = $comando -> fetchALL(PDO::FETCH_ASSOC);

    echo json_encode($manutencao);
}

if ($metodo == "PUT"){

    $json = file_get_contents("php://input");

    $dados = json_decode($json,true);

    if (!is_array($dados) || empty($dados)) {
        echo json_encode(["Mensagem" => "Erro: Corpo da requisição inválido ou JSON vazio."]);
        exit;
    }

    if (empty($dados["id"]) || empty($dados["equipamento"]) || empty($dados["setor"]) || empty($dados["descricao"]) || empty($dados["prioridade"]) || empty($dados["status"])) {
        echo json_encode(["Mensagem" => "Erro: Todos os campos, incluindo o id, são obrigatórios para atualizar."]);
        exit;
    }

    if (! in_array($dados["prioridade"], ["baixa","media","alta"], true )){
    
        echo json_encode(["Mensagem" =>"Erro: Prioridade inválida! Use baixa, media ou alta."]);
        exit;
    }

    if ( ! in_array($dados["status"], ["aberto", "em andamento", "concluido"], true )){

        echo json_encode(["Mensagem" => "Erro: Status inválido! Use aberto, em andamento ou concluido."]);
        exit;
    }

    $sql = "UPDATE manutencao SET equipamento=?, setor=?, descricao=?, prioridade=?, status_atual=? WHERE id=?";

    $comando = $pdo -> prepare($sql);

    $comando -> execute([
        $dados["equipamento"],
        $dados["setor"],
        $dados["descricao"],
        $dados["prioridade"],
        $dados["status"],
        $dados["id"]
    ]);

    echo json_encode(["Mensagem"=>"Chamado atualizado com sucesso!"]);
}

if($metodo == "DELETE"){

    $json = file_get_contents("php://input");

    $dados = json_decode($json,true) ;

    if (!is_array($dados) || empty($dados["id"])) {
        echo json_encode(["Mensagem" => "Erro: É necessário informar o id para excluir o chamado."]);
        exit;
    }

    $sql = "DELETE FROM manutencao WHERE id=?";

    $comando = $pdo -> prepare($sql);

    $comando -> execute([
        $dados["id"]
    ]);

    echo json_encode(["Mensagem"=>"Chamado deletado com sucesso"]);
}