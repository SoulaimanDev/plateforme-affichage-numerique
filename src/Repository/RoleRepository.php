<?php
require_once __DIR__ . '/BaseRepository.php';

class RoleRepository extends BaseRepository
{
    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM roles");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
