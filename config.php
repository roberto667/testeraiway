<?php
class Conexao
{
    private static $con;

    public static function conectar()
    {
        if (!isset(self::$con)) {
            $serv = "localhost";
            $user = "root";
            $senha = "";
            $db = "rest_doc_web";

            self::$con = new mysqli($serv, $user, $senha, $db);

            if (self::$con->connect_error) {
                echo json_encode(["erro" => "Conexão falhou: " . self::$con->connect_error]);
                exit;
            }
        }

        return self::$con;
    }
}

