<?php
require_once __DIR__ . '/../Core/Controller.php';

class ContentController extends Controller
{
    private $contentManager;


    private $audienceManager;
    private $zoneManager;

    public function __construct()
    {
        parent::__construct();
        AuthMiddleware::requireAuth();

        require_once __DIR__ . '/../Repository/ContentRepository.php';
        require_once __DIR__ . '/../Repository/AudienceRepository.php';
        require_once __DIR__ . '/../Repository/ZoneRepository.php';

        $this->contentManager = new ContentRepository();
        $this->audienceManager = new AudienceRepository();
        $this->zoneManager = new ZoneRepository();
    }

    public function index()
    {
        $contents = $this->contentManager->findAll();
        $audiences = $this->audienceManager->findAll();
        $zones = $this->zoneManager->findAll();

        $data = [
            'contents' => $contents,
            'audiences' => $audiences,
            'zones' => $zones,
            'success' => $_SESSION['content_success'] ?? null,
            'error' => $_SESSION['content_error'] ?? null,
            'csrf_token' => $_SESSION['csrf_token'] ?? bin2hex(random_bytes(16))
        ];

        $_SESSION['csrf_token'] = $data['csrf_token'];
        unset($_SESSION['content_success'], $_SESSION['content_error']);

        $this->render('contents/index', $data);
    }

    public function store()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/contents');
            return;
        }

        // Si post_max_size est dépassé, $_POST et $_FILES peuvent être vides
        if (empty($_POST) && empty($_FILES) && $_SERVER['CONTENT_LENGTH'] > 0) {
            $_SESSION['content_error'] = 'La taille du fichier dépasse la limite autorisée par le serveur (post_max_size).';
            $this->redirect('/contents');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = $_POST['type'] ?? 'text';
        $duration = (int) ($_POST['duration'] ?? 30);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $audiences = $_POST['audiences'] ?? [];
        $zones = $_POST['zones'] ?? [];

        // Validation
        if (empty($name)) {
            $_SESSION['content_error'] = 'Le titre est obligatoire';
            $this->redirect('/contents');
            return;
        }

        $value = '';
        $uploadError = false;

        // Gestion selon le type
        if ($type === 'text') {
            $value = trim($_POST['value'] ?? '');
            if (empty($value)) {
                $_SESSION['content_error'] = 'Le texte est obligatoire';
                $this->redirect('/contents');
                return;
            }
        } elseif ($type === 'text_image') {
            // Texte + Image
            $value = trim($_POST['value'] ?? '');
            if (empty($value)) {
                $_SESSION['content_error'] = 'Le texte est obligatoire pour le mode Texte + Image';
                $this->redirect('/contents');
                return;
            }

            // Et fichier obligatoire aussi
            if (!isset($_FILES['content_file']) || $_FILES['content_file']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['content_error'] = 'Une image est obligatoire pour le mode Texte + Image';
                $this->redirect('/contents');
                return;
            }

            $uploadResult = $this->handleFileUpload($_FILES['content_file'], 'image'); // On force le type image
            if ($uploadResult['success']) {
                $filePath = $uploadResult['path'];
            } else {
                $_SESSION['content_error'] = $uploadResult['error'];
                $this->redirect('/contents');
                return;
            }

        } elseif ($type === 'pdf') {
            // NOUVEAU: Gestion PDF
            if (!isset($_FILES['content_file']) || $_FILES['content_file']['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['content_error'] = 'Veuillez sélectionner un fichier PDF.';
                $this->redirect('/contents');
                return;
            }

            $uploadResult = $this->handleFileUpload($_FILES['content_file'], 'pdf');
            if (!$uploadResult['success']) {
                $_SESSION['content_error'] = $uploadResult['error'];
                $this->redirect('/contents');
                return;
            }

            $value = $uploadResult['path']; // On stocke juste le chemin du PDF

        } else {
            // Upload de fichier pour image/video
            // Vérification si le fichier a bien été envoyé (même avec erreur)
            if (isset($_FILES['content_file'])) {
                $fileError = $_FILES['content_file']['error'];

                // Si tout est OK
                if ($fileError === UPLOAD_ERR_OK) {
                    $uploadResult = $this->handleFileUpload($_FILES['content_file'], $type);
                    if ($uploadResult['success']) {
                        $value = $uploadResult['path'];
                    } else {
                        $_SESSION['content_error'] = $uploadResult['error'];
                        $uploadError = true;
                    }
                }
                // Gestion des erreurs spécifiques
                else {
                    $uploadError = true;
                    switch ($fileError) {
                        case UPLOAD_ERR_INI_SIZE:
                        case UPLOAD_ERR_FORM_SIZE:
                            $_SESSION['content_error'] = 'Le fichier dépasse la taille limite autorisée par le serveur (upload_max_filesize).';
                            break;
                        case UPLOAD_ERR_PARTIAL:
                            $_SESSION['content_error'] = 'Le fichier n\'a été que partiellement téléchargé.';
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            $_SESSION['content_error'] = 'Veuillez sélectionner un fichier.';
                            break;
                        case UPLOAD_ERR_NO_TMP_DIR:
                            $_SESSION['content_error'] = 'Dossier temporaire manquant.';
                            break;
                        case UPLOAD_ERR_CANT_WRITE:
                            $_SESSION['content_error'] = 'Échec de l\'écriture du fichier sur le disque.';
                            break;
                        default:
                            $_SESSION['content_error'] = 'Aucune donnée de fichier reçue (Vérifiez post_max_size).';
                            break;
                    }
                }
            } else {
                // Si $_FILES['content_file'] n'est pas défini du tout, c'est une erreur si le type n'est pas 'text'
                if ($type !== 'text') {
                    $_SESSION['content_error'] = 'Aucune donnée de fichier reçue (Vérifiez post_max_size).';
                    $uploadError = true;
                }
            }

            if ($uploadError) {
                $this->redirect(url('/contents'));
                return;
            }
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'type' => $type,
            'value' => $value, // Legacy pour text/file simple
            'text_content' => ($type === 'text' || $type === 'text_image') ? $value : null,
            'file_path' => ($type === 'image' || $type === 'video' || $type === 'pdf') ? $value : (($type === 'text_image') ? $filePath : null),
            'duration' => $duration,
            'is_active' => $is_active,
            'created_by' => $_SESSION['user_id'] ?? 1
        ];

        if ($this->contentManager->create($data)) {
            $contentId = $this->getLastInsertId();

            // Associations
            $this->contentManager->addAudiences($contentId, $audiences);
            $this->contentManager->addZones($contentId, $zones);

            $this->audit->log('CREATE', 'content', $contentId, "Création du contenu '{$name}'");

            $_SESSION['content_success'] = 'Contenu ajouté avec succès';
        } else {
            $_SESSION['content_error'] = 'Erreur lors de l\'ajout';
        }

        $this->redirect(url('/contents'));
    }

    public function update()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url('/contents'));
            return;
        }

        $id = (int) ($_POST['content_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $type = $_POST['type'] ?? 'text';
        $duration = (int) ($_POST['duration'] ?? 30);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        $audiences = $_POST['audiences'] ?? [];
        $zones = $_POST['zones'] ?? [];

        if ($id <= 0 || empty($name)) {
            $_SESSION['content_error'] = 'Données invalides';
            $this->redirect(url('/contents'));
            return;
        }

        // Récupérer le contenu existant
        $existing = $this->contentManager->findById($id);
        if (!$existing) {
            $_SESSION['content_error'] = 'Contenu introuvable';
            $this->redirect(url('/contents'));
            return;
        }

        $value = '';
        $uploadError = false;
        $filePath = null; // For text_image

        // Gestion selon le type
        if ($type === 'text') {
            $value = trim($_POST['value'] ?? '');
            if (empty($value)) {
                $_SESSION['content_error'] = 'Le texte est obligatoire';
                $this->redirect(url('/contents'));
                return;
            }
        } elseif ($type === 'text_image') {
            $value = trim($_POST['value'] ?? '');
            if (empty($value)) {
                $_SESSION['content_error'] = 'Le texte est obligatoire pour le mode Texte + Image';
                $this->redirect(url('/contents'));
                return;
            }

            if (isset($_FILES['content_file']) && $_FILES['content_file']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->handleFileUpload($_FILES['content_file'], 'image');
                if ($uploadResult['success']) {
                    $this->deleteFile($existing['file_path']);
                    $filePath = $uploadResult['path'];
                } else {
                    $_SESSION['content_error'] = $uploadResult['error'];
                    $uploadError = true;
                }
            } else {
                $filePath = $existing['file_path'] ?? null;
            }
        } else {
            // Vérifier si un nouveau fichier est uploadé
            if (isset($_FILES['content_file']) && $_FILES['content_file']['error'] === UPLOAD_ERR_OK) {
                // Determine file type category for validation
                $fileTypeCategory = ($type === 'pdf') ? 'pdf' : $type; // image, video or pdf

                $uploadResult = $this->handleFileUpload($_FILES['content_file'], $fileTypeCategory);
                if ($uploadResult['success']) {
                    // Supprimer l'ancien fichier
                    $this->deleteFile($existing['file_path']);
                    $value = $uploadResult['path'];
                } else {
                    $_SESSION['content_error'] = $uploadResult['error'];
                    $uploadError = true;
                }
            } else {
                // Garder l'ancien fichier
                $value = $existing['file_path'] ?? '';
            }

            if ($uploadError) {
                $this->redirect(url('/contents'));
                return;
            }
        }

        $data = [
            'name' => $name,
            'description' => $description,
            'type' => $type,
            'value' => $value,
            'text_content' => ($type === 'text' || $type === 'text_image') ? $value : null,
            'file_path' => ($type === 'image' || $type === 'video' || $type === 'pdf') ? $value : (($type === 'text_image') ? $filePath : null),
            'duration' => $duration,
            'is_active' => $is_active
        ];

        if ($this->contentManager->update($id, $data)) {
            // Mettre à jour les associations
            $this->contentManager->addAudiences($id, $audiences);
            $this->contentManager->addZones($id, $zones);

            $this->audit->log('UPDATE', 'content', $id, "Modification du contenu '{$name}'");

            $_SESSION['content_success'] = 'Contenu modifié avec succès';
        } else {
            $_SESSION['content_error'] = 'Erreur lors de la modification';
        }

        $this->redirect(url('/contents'));
    }

    public function delete()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(url('/contents'));
            return;
        }

        $id = (int) ($_POST['content_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['content_error'] = 'ID invalide';
            $this->redirect(url('/contents'));
            return;
        }

        if ($this->contentManager->delete($id)) {
            $this->audit->log('DELETE', 'content', $id, "Suppression d'un contenu");
            $_SESSION['content_success'] = 'Contenu supprimé avec succès';
        } else {
            $_SESSION['content_error'] = 'Erreur lors de la suppression';
        }

        $this->redirect(url('/contents'));
    }

    public function copy()
    {
        $this->requireEditorOrAdmin();
        $this->checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/contents');
            return;
        }

        $id = (int) ($_POST['content_id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['content_error'] = 'ID invalide';
            $this->redirect('/contents');
            return;
        }

        $original = $this->contentManager->findById($id);
        if (!$original) {
            $_SESSION['content_error'] = 'Contenu introuvable';
            $this->redirect('/contents');
            return;
        }

        $data = [
            'name' => $original['title'] . ' (Copie)',
            'description' => $original['description'],
            'type' => $original['content_type'],
            'value' => ($original['content_type'] === 'text') ? $original['text_content'] : $original['file_path'],
            'duration' => $original['duration'],
            'is_active' => 0, // Inactive by default
            'created_by' => $_SESSION['user_id'] ?? 1
        ];

        if ($this->contentManager->create($data)) {
            $_SESSION['content_success'] = 'Contenu copié avec succès';
        } else {
            $_SESSION['content_error'] = 'Erreur lors de la copie';
        }

        $this->redirect('/contents');
    }

    private function requireEditorOrAdmin()
    {
        AuthMiddleware::requireRole('editor');
    }

    private function checkCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';
        if (empty($token) || $token !== ($_SESSION['csrf_token'] ?? '')) {
            $_SESSION['content_error'] = 'Session expirée (CSRF)';
            $this->redirect('/contents');
            exit;
        }
    }

    /**
     * Gère l'upload d'un fichier
     * @param array $file Le fichier uploadé ($_FILES['name'])
     * @param string $type Le type de contenu (image/video)
     * @return array ['success' => bool, 'path' => string, 'error' => string]
     */
    private function handleFileUpload($file, $type)
    {
        $allowedExtensions = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
            'video' => ['mp4', 'webm'],
            'pdf' => ['pdf']
        ];

        $maxSize = 50 * 1024 * 1024; // 50 MB

        // Vérifier la taille
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'error' => 'Le fichier est trop volumineux (max 50 MB)'];
        }

        // Vérifier l'extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = $allowedExtensions[$type] ?? [];

        if (!in_array($extension, $allowed)) {
            return ['success' => false, 'error' => 'Type de fichier non autorisé'];
        }

        // Générer un nom de fichier sécurisé
        $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/uploads/contents/';
        $uploadPath = $uploadDir . $filename;

        // Créer le dossier si nécessaire
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return ['success' => true, 'path' => '/uploads/contents/' . $filename];
        } else {
            return ['success' => false, 'error' => 'Erreur lors de l\'upload du fichier'];
        }
    }

    /**
     * Supprime un fichier physique
     * @param string $filePath Le chemin relatif du fichier
     */
    private function deleteFile($filePath)
    {
        if (empty($filePath)) {
            return;
        }

        $fullPath = __DIR__ . '/../../public' . $filePath;
        if (file_exists($fullPath) && is_file($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Récupère le dernier ID inséré
     * @return int
     */
    private function getLastInsertId()
    {
        return $this->contentManager->getLastInsertId();
    }
}
