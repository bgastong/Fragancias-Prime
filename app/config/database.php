<?php 

class DataBase{
    private $db = "fragancias_prime";
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $charset = "utf8mb4";

    public function getConnection() {
        try {
            return new PDO(
                "mysql:host=localhost;dbname=fragancias_prime;charset=utf8",
                "root",
                ""
            );
        } catch (PDOException $e) {
            die("Error en la conexión: " . $e->getMessage());
        }
    }

}
