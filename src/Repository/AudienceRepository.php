<?php
require_once __DIR__ . '/BaseRepository.php';

class AudienceRepository extends BaseRepository
{
    public function findAll()
    {
        $sql = "SELECT * FROM audiences ORDER BY name ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM audiences WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO audiences (name, description, color, is_active) 
                VALUES (:name, :description, :color, :is_active)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':color' => $data['color'] ?? '#3b82f6',
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE audiences SET name = :name, description = :description, color = :color, is_active = :is_active 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':color' => $data['color'] ?? '#3b82f6',
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function delete($id)
    {
        // On pourrait vérifier s'il est utilisé dans content_audiences avant de supprimer
        // Mais pour l'instant on fait un delete simple
        $stmt = $this->db->prepare("DELETE FROM audiences WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
