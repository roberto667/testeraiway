<?php
class Conexao
{
    private static $con;

    public static function conectar()
    {
        if (!isset(self::$con)) {

            $host = getenv('MYSQLHOST');
            $port = getenv('MYSQLPORT');
            $db   = getenv('MYSQLDATABASE');
            $user = getenv('MYSQLUSER');
            $pass = getenv('MYSQLPASSWORD');

            self::$con = new mysqli($host, $user, $pass, $db, $port);

            if (self::$con->connect_error) {
                header('Content-Type: application/json');
                echo json_encode(["erro" => "Falha ao conectar no banco"]);
                exit;
            }

            self::$con->set_charset("utf8mb4");
        }

        return self::$con;
    }
}
