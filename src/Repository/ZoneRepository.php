<?php
require_once __DIR__ . '/BaseRepository.php';

class ZoneRepository extends BaseRepository
{

    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM zones ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM zones WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO zones (name, description, location, color, is_active) 
                VALUES (:name, :description, :location, :color, :is_active)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':location' => $data['location'] ?? null,
            ':color' => $data['color'] ?? '#3b82f6',
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE zones SET name = :name, description = :description, location = :location, color = :color, is_active = :is_active
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':location' => $data['location'] ?? null,
            ':color' => $data['color'] ?? '#3b82f6',
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM zones WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
