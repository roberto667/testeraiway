<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Inclua o arquivo de configuração da conexão com o banco de dados
require_once 'config.php'; 

// Recebe o código da empresa via GET
$cod_empresa = isset($_GET['cod_empresa']) ? intval($_GET['cod_empresa']) : 0;

if ($cod_empresa <= 0) {
    // Retorna um erro se o código da empresa for inválido
    echo json_encode(["erro" => "cod_empresa inválido"]);
    exit;
}

try {
    $con = Conexao::conectar();

    // Consulta SQL para selecionar as informações das filiais
    $query = $con->prepare("
        SELECT
            codigo_id,
            cod_filial,
            descricao,
            status_cxa,
            usa_chamador,
            hora_abe_cx,
            data_abe_cx,
            imp_padrao
        FROM filial
        WHERE cod_empresa = ?
        ORDER BY cod_filial ASC
    ");

    $query->bind_param("i", $cod_empresa);
    $query->execute();
    $result = $query->get_result();

    $filiais = [];
    while ($row = $result->fetch_assoc()) {
        // Converte o status_cxa e usa_chamador para booleanos ou inteiros (dependendo de como estão no seu DB)
        // Presumindo que são strings '1' ou '0' ou números. Aqui mantemos como estão, mas o Flutter irá tratar.
        $filiais[] = $row;
    }

    // Retorna os dados das filiais em formato JSON
    echo json_encode($filiais, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    // Captura e exibe erros de conexão ou consulta
    echo json_encode(["erro" => "Erro de banco de dados: " . $e->getMessage()]);
}
