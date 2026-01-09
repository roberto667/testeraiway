<?php


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require 'config.php';

$con = Conexao::conectar();

// Recebe dados do Flutter (JSON)
$data = json_decode(file_get_contents("php://input"));

// Validação
if (!isset($data->nome, $data->idade, $data->cidade, $data->email, $data->senha)) {
    echo json_encode(["erro" => "Todos os campos são obrigatórios."]);
    exit;
}

$nome = $con->real_escape_string($data->nome);
$idade = (int)$data->idade;
$cidade = $con->real_escape_string($data->cidade);
$email = $con->real_escape_string($data->email);
$senhaHash = password_hash($data->senha, PASSWORD_DEFAULT);

// Verifica se o e-mail já existe
$check = $con->query("SELECT id FROM donos WHERE email = '$email'");
if ($check->num_rows > 0) {
    echo json_encode(["erro" => "E-mail já cadastrado."]);
    exit;
}

// Insere o novo usuário
$sql = "INSERT INTO donos (nome, idade, cidade, email, senha) 
        VALUES ('$nome', $idade, '$cidade', '$email', '$senhaHash')";

if ($con->query($sql)) {
    echo json_encode(["sucesso" => true, "mensagem" => "Usuário cadastrado com sucesso!"]);
} else {
    echo json_encode(["erro" => "Erro ao cadastrar: " . $con->error]);
}

