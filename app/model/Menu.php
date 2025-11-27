<?php

require_once __DIR__ . '/../config/database.php';

class Menu
{
    private $conn;

    public function __construct()
    {
        $db = new DataBase();
        $this->conn = $db->getConnection();
    }

    public function tableExists()
    {
        try {
            $stmt = $this->conn->query("SHOW TABLES LIKE 'menu'");
            return (bool)$stmt->fetch();
        } catch (Exception $e) {
            return false;
        }
    }

    public function getAll()
    {
        $sql = "SELECT idmenu, menombre, medescripcion, idpadre, medeshabilitado, tipo, href, icon, orden, visible_para FROM menu WHERE COALESCE(medeshabilitado,0) = 0 ORDER BY idpadre ASC, orden ASC, idmenu ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener items públicos (sin restricción por rol) o explícitamente públicos mediante visible_para
    public function getPublicItems()
    {
        $sql = "SELECT m.idmenu, m.menombre, m.medescripcion, m.idpadre, m.medeshabilitado, m.tipo, m.href, m.icon, m.orden, m.visible_para
                FROM menu m
                LEFT JOIN menurol mr ON m.idmenu = mr.idmenu
                WHERE COALESCE(m.medeshabilitado,0) = 0
                  AND (LOWER(TRIM(COALESCE(m.visible_para,''))) = 'all' OR LOWER(TRIM(COALESCE(m.visible_para,''))) = 'anon' OR mr.idmenu IS NULL)
                GROUP BY m.idmenu
                ORDER BY m.idpadre ASC, m.orden ASC, m.idmenu ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByRoleIds(array $roleIds)
    {
        if (empty($roleIds)) {
            return $this->getAll();
        }

        // construir placeholders para la consulta 
        $placeholders = implode(',', array_fill(0, count($roleIds), '?'));
        $sql = "SELECT DISTINCT m.idmenu, m.menombre, m.medescripcion, m.idpadre, m.medeshabilitado, m.tipo, m.href, m.icon, m.orden, m.visible_para
                FROM menu m
                INNER JOIN menurol mr ON m.idmenu = mr.idmenu
                WHERE mr.idrol IN ($placeholders)
                    AND COALESCE(m.medeshabilitado,0) = 0
                ORDER BY m.idpadre ASC, m.orden ASC, m.idmenu ASC";

        $stmt = $this->conn->prepare($sql);
        foreach (array_values($roleIds) as $i => $val) {
            $stmt->bindValue($i + 1, $val, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
