<?php
require_once __DIR__ . '/BaseRepository.php';

class ScreenRepository extends BaseRepository
{

    public function findAll()
    {
        $sql = "SELECT s.*, z.name as zone_name 
                FROM screens s 
                LEFT JOIN zones z ON s.zone_id = z.id 
                ORDER BY s.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM screens WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        // Générer une clé unique pour l'écran
        $screenKey = 'SCR_' . strtoupper(bin2hex(random_bytes(8)));

        $sql = "INSERT INTO screens (screen_key, name, zone_id, location, screen_type, resolution, orientation, is_active) 
                VALUES (:screen_key, :name, :zone_id, :location, :screen_type, :resolution, :orientation, :is_active)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':screen_key' => $screenKey,
            ':name' => $data['name'],
            ':zone_id' => $data['zone_id'],
            ':location' => $data['location'] ?? null,
            ':screen_type' => $data['screen_type'] ?? 'Standard',
            ':resolution' => $data['resolution'] ?? '1920x1080',
            ':orientation' => $data['orientation'] ?? 'landscape',
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE screens SET name = :name, zone_id = :zone_id, location = :location, 
                screen_type = :screen_type, resolution = :resolution, orientation = :orientation, is_active = :is_active 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':zone_id' => $data['zone_id'],
            ':location' => $data['location'] ?? null,
            ':screen_type' => $data['screen_type'] ?? 'Standard',
            ':resolution' => $data['resolution'] ?? '1920x1080',
            ':orientation' => $data['orientation'] ?? 'landscape',
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function findByKey($key)
    {
        $stmt = $this->db->prepare("SELECT * FROM screens WHERE screen_key = ? AND is_active = 1");
        $stmt->execute([$key]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateLastPing($id)
    {
        $stmt = $this->db->prepare("UPDATE screens SET last_ping = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM screens WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Compte les écrans actifs par zone
     */
    public function countActiveByZone()
    {
        $stmt = $this->db->query("
            SELECT z.name as zone_name, COUNT(s.id) as count
            FROM screens s
            JOIN zones z ON s.zone_id = z.id
            WHERE s.is_active = 1
            GROUP BY z.id, z.name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
