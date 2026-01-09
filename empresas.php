<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'config.php';

// Recebe o id do usuário
$cod_empresa = isset($_GET['cod_empresa']) ? intval($_GET['cod_empresa']) : 0;

if ($cod_empresa <= 0) {
    echo json_encode([]);
    exit;
}

$con = Conexao::conectar();

$query = $con->prepare("SELECT codigo_id, empresa FROM empresa WHERE codigo_id = ?");
$query->bind_param("i", $cod_empresa);
$query->execute();
$result = $query->get_result();

$empresas = [];
while ($row = $result->fetch_assoc()) {
    $empresas[] = $row;
}

echo json_encode($empresas);
