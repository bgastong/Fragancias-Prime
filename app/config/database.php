<?php

class DataBase
{
    private $db = "if0_42131498_XXX";
    private $host = "sql308.infinityfree.com";
    private $user = "if0_42131498";
    private $pass = "ZDU3k247SkSn";
    private $charset = "utf8mb4";

    public function getConnection()
    {
        try {
            return new PDO(
                "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}",
                $this->user,
                $this->pass
            );
        } catch (PDOException $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }
}
