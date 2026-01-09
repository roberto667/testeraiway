<?php
require 'config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$con = Conexao::conectar();
$data = json_decode(file_get_contents("php://input"));

// Verifica se os campos vieram corretamente
if (!isset($data->usuario, $data->senha)) {
    echo json_encode(["erro" => "Campos obrigatórios ausentes."]);
    exit;
}

$usuario = $con->real_escape_string($data->usuario);
$senha = $con->real_escape_string($data->senha);

// Busca o usuário no banco
$sql = "SELECT * FROM usuario WHERE usuario = '$usuario' AND senha = '$senha'";
$result = $con->query($sql);

// Se não encontrar o usuário
if ($result->num_rows === 0) {
    echo json_encode(["erro" => "Usuário ou senha incorretos."]);
    exit;
}

// Se encontrar, retorna os dados
$user = $result->fetch_assoc();

echo json_encode([
    "sucesso" => true,
    "usuario" => [
        "usuario" => $user['usuario'],
        "tipo" => $user['tipo'],
        "cod_empresa" => $user['cod_empresa'],
    ]
]);
exit;
?>
