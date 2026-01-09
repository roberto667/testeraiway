<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config.php';

$empresa_id = isset($_GET['cod_empresa']) ? intval($_GET['cod_empresa']) : 0;
$ano = isset($_GET['ano']) ? intval($_GET['ano']) : 0;
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : 0;
$dia = isset($_GET['dia']) ? intval($_GET['dia']) : 0;

if ($empresa_id <= 0 || $ano <= 0 || $mes <= 0 || $dia <= 0) {
    echo json_encode([]);
    exit;
}

$con = Conexao::conectar();

$query = $con->prepare("
    SELECT
        g.codigo_id AS grupo_id,
        g.descricao AS nomegrupo,
        d.produto,
        d.nomeprod,
        d.prctotal
    FROM delivery_det d
    INNER JOIN grupo g 
        ON g.codigo = d.cod_grupo
       AND g.cod_empresa = d.cod_empresa
    WHERE d.cod_empresa = ?
      AND YEAR(d.datainicial) = ?
      AND MONTH(d.datainicial) = ?
      AND DAY(d.datainicial) = ?
    ORDER BY g.descricao, d.prctotal DESC
");

$query->bind_param("iiii", $empresa_id, $ano, $mes, $dia);
$query->execute();

$result = $query->get_result();

$grupos = [];

while ($row = $result->fetch_assoc()) {

    $grupoId = $row['grupo_id'];

    if (!isset($grupos[$grupoId])) {
        $grupos[$grupoId] = [
            'nomegrupo' => $row['nomegrupo'],
            'prctotal'  => 0,
            'produtos'  => []
        ];
    }

    $grupos[$grupoId]['prctotal'] += (float)$row['prctotal'];

    $grupos[$grupoId]['produtos'][] = [
        'produto' => (int)$row['produto'],
        'nome'    => $row['nomeprod'],
        'lucro'   => (float)$row['prctotal']
    ];
}

echo json_encode(array_values($grupos), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
