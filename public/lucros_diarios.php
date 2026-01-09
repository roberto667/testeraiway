<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config.php';

$empresa_id = isset($_GET['cod_empresa']) ? intval($_GET['cod_empresa']) : 0;
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : 0;
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : 0;

if ($empresa_id <= 0 || $ano <= 0 || $mes <= 0) {
    echo json_encode([]);
    exit;
}

$con = Conexao::conectar();

/*
    Retorna SOMENTE:
    - dia
    - lucro total do dia
*/
$query = $con->prepare("
    SELECT 
        DAY(d.datainicial) AS dia,
        SUM(d.prctotal) AS prctotal
    FROM delivery_det d
    WHERE d.cod_empresa = ?
      AND YEAR(d.datainicial) = ?
      AND MONTH(d.datainicial) = ?
    GROUP BY DAY(d.datainicial)
    ORDER BY dia ASC
");

$query->bind_param("iii", $empresa_id, $ano, $mes);
$query->execute();
$result = $query->get_result();

$response = [];

while ($row = $result->fetch_assoc()) {
    $response[] = [
        'dia' => intval($row['dia']),
        'prctotal' => floatval($row['prctotal'])
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
