<?php
require_once __DIR__ . '/BaseRepository.php';

class ContentRepository extends BaseRepository
{
    // Constructor inherited from BaseRepository

    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM contents ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM contents WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO contents 
            (title, description, content_type, text_content, file_path, duration, created_by, is_active) 
            VALUES 
            (:title, :description, :type, :text, :file, :duration, :created_by, :is_active)");

        // Use explicit fields if provided (new controller logic), otherwise fallback to old 'value' logic
        $text = $data['text_content'] ?? (($data['type'] === 'text') ? $data['value'] : null);
        $file = $data['file_path'] ?? (($data['type'] !== 'text') ? $data['value'] : null);

        return $stmt->execute([
            'title' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'text' => $text,
            'file' => $file,
            'duration' => $data['duration'] ?? 15,
            'created_by' => $data['created_by'] ?? 1,
            'is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE contents SET 
            title = :title,
            description = :description, 
            content_type = :type, 
            text_content = :text, 
            file_path = :file,
            duration = :duration,
            is_active = :is_active
            WHERE id = :id");

        $text = $data['text_content'] ?? (($data['type'] === 'text') ? $data['value'] : null);
        $file = $data['file_path'] ?? (($data['type'] !== 'text') ? $data['value'] : null);

        return $stmt->execute([
            'id' => $id,
            'title' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'text' => $text,
            'file' => $file,
            'duration' => $data['duration'] ?? 15,
            'is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM contents WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // ============================================================
    // GESTION DES ASSOCIATIONS AUDIENCES
    // ============================================================

    public function addAudiences($contentId, $audienceIds)
    {
        if (empty($audienceIds)) {
            return true;
        }

        $this->removeAudiences($contentId);

        $stmt = $this->db->prepare("INSERT INTO content_audiences (content_id, audience_id) VALUES (:content_id, :audience_id)");

        foreach ($audienceIds as $audienceId) {
            $stmt->execute([
                'content_id' => $contentId,
                'audience_id' => (int) $audienceId
            ]);
        }

        return true;
    }

    public function removeAudiences($contentId)
    {
        $stmt = $this->db->prepare("DELETE FROM content_audiences WHERE content_id = :content_id");
        return $stmt->execute(['content_id' => $contentId]);
    }

    public function getAudiences($contentId)
    {
        $stmt = $this->db->prepare("
            SELECT a.* 
            FROM audiences a
            INNER JOIN content_audiences ca ON a.id = ca.audience_id
            WHERE ca.content_id = :content_id
        ");
        $stmt->execute(['content_id' => $contentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ============================================================
    // GESTION DES ASSOCIATIONS ZONES
    // ============================================================

    public function addZones($contentId, $zoneIds)
    {
        if (empty($zoneIds)) {
            return true;
        }

        $this->removeZones($contentId);

        $stmt = $this->db->prepare("INSERT INTO content_zones (content_id, zone_id) VALUES (:content_id, :zone_id)");

        foreach ($zoneIds as $zoneId) {
            $stmt->execute([
                'content_id' => $contentId,
                'zone_id' => (int) $zoneId
            ]);
        }

        return true;
    }

    public function removeZones($contentId)
    {
        $stmt = $this->db->prepare("DELETE FROM content_zones WHERE content_id = :content_id");
        return $stmt->execute(['content_id' => $contentId]);
    }

    public function getZones($contentId)
    {
        $stmt = $this->db->prepare("
            SELECT z.* 
            FROM zones z
            INNER JOIN content_zones cz ON z.id = cz.zone_id
            WHERE cz.content_id = :content_id
        ");
        $stmt->execute(['content_id' => $contentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
