<?php
require_once __DIR__ . '/BaseRepository.php';

class PlaylistRepository extends BaseRepository
{
    public function findAll()
    {
        $sql = "SELECT p.*, z.name as zone_name, CONCAT(u.firstname, ' ', u.lastname) as creator_name
                FROM playlists p
                LEFT JOIN zones z ON p.zone_id = z.id
                LEFT JOIN users u ON p.created_by = u.id
                ORDER BY p.created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM playlists WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO playlists (name, description, zone_id, is_active, created_by) 
                VALUES (:name, :description, :zone_id, :is_active, :created_by)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':zone_id' => $data['zone_id'],
            ':is_active' => $data['is_active'] ?? 1,
            ':created_by' => $data['created_by']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE playlists SET name = :name, description = :description, zone_id = :zone_id, is_active = :is_active 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':zone_id' => $data['zone_id'],
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM playlists WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // ============================================================
    // GESTION DES CONTENUS DE LA PLAYLIST
    // ============================================================

    public function getContents($playlistId)
    {
        // On récupère la durée du contenu par défaut (c.duration) car pas de surcharge dans la table pivot
        $sql = "SELECT c.*, pc.id as association_id, pc.order_index as display_order, c.duration as override_duration
                FROM contents c
                JOIN playlist_contents pc ON c.id = pc.content_id
                WHERE pc.playlist_id = ?
                ORDER BY pc.order_index ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$playlistId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addContent($playlistId, $contentId, $duration = 10, $order = 0)
    {
        // Note: La colonne duration n'existe pas dans la table pivot actuelle
        $sql = "INSERT INTO playlist_contents (playlist_id, content_id, order_index) 
                VALUES (:playlist_id, :content_id, :order_index)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':playlist_id' => $playlistId,
            ':content_id' => $contentId,
            ':order_index' => $order
        ]);
    }

    public function removeContent($playlistId, $contentId)
    {
        $sql = "DELETE FROM playlist_contents WHERE playlist_id = ? AND content_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$playlistId, $contentId]);
    }
}
