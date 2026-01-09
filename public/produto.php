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

// ✅ Consulta corrigida: usa YEAR() e MONTH() para filtrar
$query = $con->prepare("
    SELECT 
        DAY(datainicial) AS dia, 
        SUM(prctotal) AS prctotal
    FROM delivery_det
    WHERE cod_empresa = ? 
      AND YEAR(datainicial) = ? 
      AND MONTH(datainicial) = ?
    GROUP BY DAY(datainicial)
    ORDER BY dia ASC
");

$query->bind_param("iii", $empresa_id, $ano, $mes);
$query->execute();
$result = $query->get_result();

$lucros = [];
while ($row = $result->fetch_assoc()) {
    $lucros[] = [
        'dia' => intval($row['dia']),
        'prctotal' => floatval($row['prctotal'])
    ];
}

echo json_encode($lucros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
