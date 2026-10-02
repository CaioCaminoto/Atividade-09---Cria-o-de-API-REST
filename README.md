# API REST em PHP - Sistema de Gestão de Chamados de Manutenção

DOCUMENTAÇÃO TÉCNICA E GUIA DE IMPLEMENTAÇÃO

---

## 1. Proposta da Atividade

Esta aplicação consiste em uma API REST desenvolvida em PHP orientada a objetos com PDO (PHP Data Objects) e banco de dados PostgreSQL. O objetivo principal é disponibilizar uma interface de comunicação para controle de chamados de manutenção, permitindo operações completas de CRUD (Create, Read, Update, Delete).

Toda a troca de dados entre a aplicação cliente (como Postman, Insomnia ou aplicações web/mobile) e o servidor backend é realizada exclusivamente no formato JSON.

---

## 2. Tecnologias e Dependências

* Linguagem Backend: PHP (versão 8.0 ou superior)
* Banco de Dados: PostgreSQL
* Camada de Abstração de Dados: PDO (PHP Data Objects)
* Formato de Intercâmbio de Dados: JSON
* Servidor Web de Desenvolvimento: Servidor embutido do PHP (`php -S`)

---

## 3. Mapeamento da Base de Dados

A conexão é gerenciada através do arquivo `conexao.php`, que estabelece a comunicação com o banco de dados `manutencao`. A estrutura da tabela principal no PostgreSQL é definida pelos seguintes campos:

| Coluna | Tipo SQL | Descrição |
| --- | --- | --- |
| id | INTEGER / SERIAL | Chave primária identificadora única |
| equipamento | VARCHAR(255) | Nome/Modelo do equipamento em manutenção |
| setor | VARCHAR(100) | Setor solicitante na organização |
| descricao | TEXT | Detalhamento da falha ou serviço necessário |
| prioridade | VARCHAR(20) | Nível de criticidade (baixa, media, alta) |
| status_atual | VARCHAR(50) | Estado do chamado (aberto, em andamento, concluido) |

*Observação Técnica:* A coluna foi nomeada como `status_atual` na tabela para prevenir potenciais ambiguidades ou conflitos com palavras reservadas do sistema gerenciador de banco de dados.

---

## 4. Arquitetura do Código e Conceitos-Chave

### Manipulação de JSON e Protocolo de Saída

O PHP manipula dados em memória por meio de Arrays Associativos (estruturados com a sintaxe `chave => valor`). No entanto, o protocolo de comunicação HTTP exige a transmissão em JSON (formatado com `chave: valor`). A conversão do Array para texto JSON e o envio para a camada de resposta acontecem ao associar o método `json_encode()` com a instrução `echo`.

### Mecanismo de Segurança e Validação de Dados

1. Checagem de Estrutura: A validação `!is_array($dados) || empty($dados)` impede a execução de trechos subsequentes do código caso o JSON enviado no corpo da requisição contenha erros de sintaxe ou chegue vazio.
2. Verificação de Campos Obrigatórios: A função `empty()` avalia se as chaves necessárias estão devidamente preenchidas antes de acionar a camada de persistência.
3. Validação Estrita de Domínio: O método `in_array($valor, $opcoes, true)` garante que os campos `prioridade` e `status` recebam apenas os valores pré-determinados pela regra de negócio, com o parâmetro `true` forçando a verificação do tipo de dado (Strict Mode).

---

## 5. Implementação do Script Principal (`manutencao.php`)

```php
<?php
header("Content-Type: application/json");

require "conexao.php";

$metodo = $_SERVER["REQUEST_METHOD"];

// OPERACAO DE INSERCAO (POST)
if ($metodo == "POST") {

    $json = file_get_contents("php://input");
    $dados = json_decode($json, true);

    if (!is_array($dados) || empty($dados)) {
        echo json_encode(["Mensagem" => "Erro: Corpo da requisição inválido ou JSON vazio."]);
        exit;
    }

    if (empty($dados["equipamento"]) || empty($dados["setor"]) || empty($dados["descricao"]) || empty($dados["prioridade"]) || empty($dados["status"])) {
        echo json_encode(["Mensagem" => "Erro: Todos os campos são obrigatórios."]);
        exit;
    }

    if (!in_array($dados["prioridade"], ["baixa", "media", "alta"], true)) {
        echo json_encode(["Mensagem" => "Erro: Prioridade inválida! Use baixa, media ou alta."]);
        exit;
    }

    if (!in_array($dados["status"], ["aberto", "em andamento", "concluido"], true)) {
        echo json_encode(["Mensagem" => "Erro: Status inválido! Use aberto, em andamento ou concluido."]);
        exit;
    }

    $sql = "INSERT INTO manutencao (equipamento, setor, descricao, prioridade, status_atual) VALUES (?, ?, ?, ?, ?)";
    $comando = $pdo->prepare($sql);
    $comando->execute([
        $dados["equipamento"],
        $dados["setor"],
        $dados["descricao"],
        $dados["prioridade"],
        $dados["status"]
    ]);   

    echo json_encode(["Mensagem" => "Chamado cadastrado com sucesso!"]);
}

// OPERACAO DE CONSULTA (GET)
if ($metodo == "GET") {

    $sql = "SELECT * FROM manutencao ORDER BY id";
    $comando = $pdo->query($sql);
    $manutencao = $comando->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($manutencao);
}

// OPERACAO DE ATUALIZACAO (PUT)
if ($metodo == "PUT") {

    $json = file_get_contents("php://input");
    $dados = json_decode($json, true);

    if (!is_array($dados) || empty($dados)) {
        echo json_encode(["Mensagem" => "Erro: Corpo da requisição inválido ou JSON vazio."]);
        exit;
    }

    if (empty($dados["id"]) || empty($dados["equipamento"]) || empty($dados["setor"]) || empty($dados["descricao"]) || empty($dados["prioridade"]) || empty($dados["status"])) {
        echo json_encode(["Mensagem" => "Erro: Todos os campos, incluindo o id, são obrigatórios para atualizar."]);
        exit;
    }

    if (!in_array($dados["prioridade"], ["baixa", "media", "alta"], true)) {
        echo json_encode(["Mensagem" => "Erro: Prioridade inválida! Use baixa, media ou alta."]);
        exit;
    }

    if (!in_array($dados["status"], ["aberto", "em andamento", "concluido"], true)) {
        echo json_encode(["Mensagem" => "Erro: Status inválido! Use aberto, em andamento ou concluido."]);
        exit;
    }

    $sql = "UPDATE manutencao SET equipamento=?, setor=?, descricao=?, prioridade=?, status_atual=? WHERE id=?";
    $comando = $pdo->prepare($sql);
    $comando->execute([
        $dados["equipamento"],
        $dados["setor"],
        $dados["descricao"],
        $dados["prioridade"],
        $dados["status"],
        $dados["id"]
    ]);

    echo json_encode(["Mensagem" => "Chamado atualizado com sucesso!"]);
}

// OPERACAO DE DELECAO (DELETE)
if ($metodo == "DELETE") {

    $json = file_get_contents("php://input");
    $dados = json_decode($json, true);

    if (!is_array($dados) || empty($dados["id"])) {
        echo json_encode(["Mensagem" => "Erro: É necessário informar o id para excluir o chamado."]);
        exit;
    }

    $sql = "DELETE FROM manutencao WHERE id=?";
    $comando = $pdo->prepare($sql);
    $comando->execute([
        $dados["id"]
    ]);

    echo json_encode(["Mensagem" => "Chamado deletado com sucesso!"]);
}

```

---

## 6. Procedimento para Execução do Projeto

1. Certifique-se de que o serviço do banco de dados PostgreSQL esteja ativo no ambiente de execução.
2. Configure os parâmetros de acesso no arquivo `conexao.php` (`host`, `dbname`, `usuario`, `senha`).
3. Na pasta raiz do projeto, inicie o servidor embutido do PHP via terminal com o comando:
`php -S localhost:8000`
4. Realize os testes de integração efetuando chamadas para o endereço `http://localhost:8000/manutencao.php` utilizando os métodos HTTP correspondentes.