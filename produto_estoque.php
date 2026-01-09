<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once 'config.php';
$empresa_id = isset($_GET['cod_empresa']) ? intval($_GET['cod_empresa']) : 0;

if ($empresa_id <= 0) {
    echo json_encode([]);
    exit;
}

$con = Conexao::conectar();

$query = $con->prepare("
    SELECT 
        p.produto,
        REPLACE(p.quantidade, '.', '') AS quantidade,
        g.descricao AS grupo_nome
    FROM produto p
    LEFT JOIN grupo g ON g.codigo = p.grupo
    WHERE p.cod_empresa = ?
    ORDER BY g.descricao ASC, p.produto ASC
");

$query->bind_param("i", $empresa_id);
$query->execute();

$result = $query->get_result();

$grupoProdutos = [];

// AGRUPAR
while ($row = $result->fetch_assoc()) {

    $grupo = $row['grupo_nome'] ?? "SEM GRUPO";

    if (!isset($grupoProdutos[$grupo])) {
        $grupoProdutos[$grupo] = [];
    }

    unset($row['grupo_nome']); // remove para não poluir

    $grupoProdutos[$grupo][] = $row;
}

echo json_encode($grupoProdutos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
