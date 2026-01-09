<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config.php';

$cod_empresa = isset($_GET['cod_empresa']) ? intval($_GET['cod_empresa']) : 0;
$ano_inicial = isset($_GET['ano_inicial']) ? intval($_GET['ano_inicial']) : 2024;
$ano_final = isset($_GET['ano_final']) ? intval($_GET['ano_final']) : 2025;

if ($cod_empresa <= 0) {
    echo json_encode(["erro" => "cod_empresa inválido"]);
    exit;
}

$con = Conexao::conectar();

// ✅ AGRUPA por ano e mês
$query = $con->prepare("
    SELECT 
        YEAR(datainicial) AS ano,
        MONTH(datainicial) AS mes,
        SUM(prctotal) AS prctotal
    FROM delivery_det
    WHERE cod_empresa = ? 
      AND YEAR(datainicial) BETWEEN ? AND ?
    GROUP BY YEAR(datainicial), MONTH(datainicial)
    ORDER BY ano ASC, mes ASC
");

$query->bind_param("iii", $cod_empresa, $ano_inicial, $ano_final);
$query->execute();
$result = $query->get_result();

$lucros = [];
while ($row = $result->fetch_assoc()) {
    $row['prctotal'] = floatval($row['prctotal']);
    $lucros[] = $row;
}

echo json_encode($lucros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
