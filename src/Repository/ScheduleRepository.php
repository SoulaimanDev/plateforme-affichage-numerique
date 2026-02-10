<?php
require_once __DIR__ . '/BaseRepository.php';

class ScheduleRepository extends BaseRepository
{
    public function findAll()
    {
        $sql = "SELECT s.*, c.title as content_title, p.name as playlist_name, z.name as zone_name
                FROM schedules s
                LEFT JOIN contents c ON s.content_id = c.id
                LEFT JOIN playlists p ON s.playlist_id = p.id
                LEFT JOIN zones z ON s.zone_id = z.id
                ORDER BY s.start_date DESC, s.start_time ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM schedules WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO schedules (content_id, playlist_id, zone_id, start_date, end_date, start_time, end_time, day_of_week, priority, is_active, created_by) 
                VALUES (:content_id, :playlist_id, :zone_id, :start_date, :end_date, :start_time, :end_time, :day_of_week, :priority, :is_active, :created_by)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':content_id' => !empty($data['content_id']) ? $data['content_id'] : null,
            ':playlist_id' => !empty($data['playlist_id']) ? $data['playlist_id'] : null,
            ':zone_id' => $data['zone_id'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':start_time' => $data['start_time'],
            ':end_time' => $data['end_time'],
            ':day_of_week' => $data['day_of_week'],
            ':priority' => $data['priority'] ?? 50,
            ':is_active' => $data['is_active'] ?? 1,
            ':created_by' => $data['created_by']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE schedules SET content_id = :content_id, playlist_id = :playlist_id, zone_id = :zone_id, start_date = :start_date, end_date = :end_date,
                start_time = :start_time, end_time = :end_time, day_of_week = :day_of_week, priority = :priority, is_active = :is_active 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':content_id' => !empty($data['content_id']) ? $data['content_id'] : null,
            ':playlist_id' => !empty($data['playlist_id']) ? $data['playlist_id'] : null,
            ':zone_id' => $data['zone_id'],
            ':start_date' => $data['start_date'],
            ':end_date' => $data['end_date'],
            ':start_time' => $data['start_time'],
            ':end_time' => $data['end_time'],
            ':day_of_week' => $data['day_of_week'],
            ':priority' => $data['priority'] ?? 50,
            ':is_active' => $data['is_active'] ?? 1
        ]);
    }

    public function findCurrentContent($zoneId)
    {
        // Récupère le contenu actif pour la zone donnée, à l'heure actuelle
        $now = date('Y-m-d H:i:s');
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        $currentDay = date('l'); // Monday, Tuesday...

        // Mapping Day Name -> Number to match Controller's storage format (e.g., "12345")
        $dayMap = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        ];
        $currentDayNum = $dayMap[$currentDay] ?? 0;

        // On récupère les infos de la schedule, + infos contenu éventuel, + infos playlist éventuelle
        $sql = "SELECT s.*, 
                       c.title as content_title, c.content_type, c.file_path, c.text_content, c.duration as content_duration,
                       p.name as playlist_name
                FROM schedules s
                LEFT JOIN contents c ON s.content_id = c.id
                LEFT JOIN playlists p ON s.playlist_id = p.id
                WHERE s.zone_id = ?
                AND s.is_active = 1
                AND s.start_date <= ? AND s.end_date >= ?
                AND s.start_time <= ? AND s.end_time >= ?
                AND s.day_of_week LIKE ?
                ORDER BY s.priority DESC, s.created_at DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$zoneId, $currentDate, $currentDate, $currentTime, $currentTime, "%{$currentDayNum}%"]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM schedules WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Récupère les stats de diffusion pour les X derniers jours
     */
    public function getDailyStats($days = 7)
    {
        $stats = [];
        $today = new DateTime();

        // Mapping anglais -> français pour les jours
        $daysTranslation = [
            'Monday' => 'Lun',
            'Tuesday' => 'Mar',
            'Wednesday' => 'Mer',
            'Thursday' => 'Jeu',
            'Friday' => 'Ven',
            'Saturday' => 'Sam',
            'Sunday' => 'Dim'
        ];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = clone $today;
            $date->modify("-{$i} days");
            $dateStr = $date->format('Y-m-d');
            $dayName = $date->format('l'); // Monday, Tuesday...

            // Compter les programmations actives pour ce jour
            $sql = "SELECT COUNT(*) 
                    FROM schedules 
                    WHERE start_date <= ? AND end_date >= ?
                    AND is_active = 1
                    AND (day_of_week IS NULL OR FIND_IN_SET(?, day_of_week))";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$dateStr, $dateStr, $dayName]);
            $count = $stmt->fetchColumn();

            $stats[] = [
                'date' => $dateStr,
                'count' => (int) $count,
                'label' => $daysTranslation[$dayName] ?? $dayName
            ];
        }

        return $stats;
    }
}
