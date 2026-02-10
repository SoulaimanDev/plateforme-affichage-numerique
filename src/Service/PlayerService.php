<?php

require_once __DIR__ . '/../Repository/ScreenRepository.php';
require_once __DIR__ . '/../Repository/ScheduleRepository.php';
require_once __DIR__ . '/../Repository/ContentRepository.php';
require_once __DIR__ . '/../Repository/PlaylistRepository.php';

class PlayerService
{
    private $screenRepository;
    private $scheduleRepository;
    private $contentRepository;
    private $playlistRepository;

    public function __construct()
    {
        $this->screenRepository = new ScreenRepository();
        $this->scheduleRepository = new ScheduleRepository();
        $this->contentRepository = new ContentRepository();
        $this->playlistRepository = new PlaylistRepository();
    }

    /**
     * Valide et récupère l'écran par sa clé
     */
    public function getScreen($key)
    {
        $screen = $this->screenRepository->findByKey($key);

        if (!$screen) {
            return null;
        }

        // Mettre à jour le ping
        $this->screenRepository->updateLastPing($screen['id']);

        return $screen;
    }

    /**
     * Récupère le contenu à jouer pour cet écran
     */
    public function getContentForScreen($screen)
    {
        // 1. Chercher une programmation active pour la zone de l'écran
        $schedule = $this->scheduleRepository->findCurrentContent($screen['zone_id']);

        if ($schedule) {

            // CAS 1: PLAYLIST
            if (!empty($schedule['playlist_id'])) {
                $rawItems = $this->playlistRepository->getContents($schedule['playlist_id']);
                $items = [];

                foreach ($rawItems as $item) {
                    $items[] = [
                        'type' => $item['content_type'], // Attention, le repository retourne 'content_type' ou 'type' selon la query. Ici c'est "c.*" donc 'content_type'
                        'url' => function_exists('url') ? url($item['file_path'] ?? '') : ($item['file_path'] ?? ''),
                        'text' => $item['text_content'],
                        'duration' => $item['override_duration'] ?? $item['duration'] ?? 10,
                        'name' => $item['title']
                    ];
                }

                if (empty($items)) {
                    // Playlist vide ? Fallback
                    return $this->getDefaultContent("Playlist vide.");
                }

                return [
                    'mode' => 'playlist',
                    'items' => $items,
                    'schedule_id' => $schedule['id']
                ];
            }

            // CAS 2: CONTENU UNIQUE
            if (!empty($schedule['content_id'])) {
                return [
                    'mode' => 'single',
                    'items' => [
                        [
                            'type' => $schedule['content_type'],
                            'url' => function_exists('url') ? url($schedule['file_path'] ?? '') : ($schedule['file_path'] ?? ''),
                            'text' => $schedule['text_content'],
                            'duration' => 15, // Durée par défaut pour un contenu unique programmé (ou infinie ?)
                            'name' => $schedule['content_title']
                        ]
                    ],
                    'schedule_id' => $schedule['id']
                ];
            }
        }

        // 2. Si rien de programmé, contenu par défaut (fallback)
        return $this->getDefaultContent();
    }

    private function getDefaultContent($msg = 'Aucun contenu programmé.')
    {
        return [
            'mode' => 'single',
            'items' => [
                [
                    'type' => 'text',
                    'url' => null,
                    'text' => $msg,
                    'duration' => 60,
                    'name' => 'Default'
                ]
            ]
        ];
    }
}
