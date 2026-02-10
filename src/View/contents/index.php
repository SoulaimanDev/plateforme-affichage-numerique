<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Médiathèque - Plume Vision CMS</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= url('/css/global.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/dashboard.css') ?>">

    <style>
        /* Variables modernes */
        :root {
            --primary-color: #2563eb;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-light: #94a3b8;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        /* Layout moderne */
        .dashboard-layout {
            display: flex;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            justify-content: flex-start;
        }

        .main-content {
            flex: 1;
            background: var(--bg-primary);
            width: 100%;
            margin-left: 0;
            max-width: 100%;
        }

        /* Header moderne */
        .modern-header {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow-sm);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0;
            padding: 0 2rem;
            width: 100%;
            box-sizing: border-box;
        }

        .header-title {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .header-subtitle {
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        /* Boutons modernes */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-ghost {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-ghost:hover {
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        /* Container principal */
        .users-container {
            margin: 0;
            padding: 2rem;
            width: 100%;
            box-sizing: border-box;
        }

        /* Messages de feedback modernes */
        .toast {
            position: fixed;
            top: 2rem;
            right: 2rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 1000;
            animation: slideInRight 0.3s ease;
            max-width: 400px;
        }

        .toast-success {
            border-left: 4px solid var(--success-color);
        }

        .toast-error {
            border-left: 4px solid var(--danger-color);
        }

        .toast-close {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 0.25rem;
            margin-left: auto;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Tableau moderne */
        .table-container {
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: var(--bg-primary);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: var(--bg-primary);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges de rôle/type */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .role-admin {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .role-editor {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .role-viewer {
            background: rgba(100, 116, 139, 0.1);
            color: var(--text-secondary);
        }

        /* Actions */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--bg-primary);
            color: var(--text-secondary);
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-edit:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-copy:hover {
            background: var(--info-color);
            color: white;
        }

        .btn-delete:hover {
            background: var(--danger-color);
            color: white;
        }

        /* État vide */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: var(--bg-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--text-light);
        }

        .empty-state h3 {
            font-size: 1.25rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        /* Modal moderne */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.2s ease;
        }

        .modal-overlay.active {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 2rem;
            width: 90%;
            max-width: 700px;
            box-shadow: var(--shadow-lg);
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--text-light);
            transition: color 0.2s ease;
        }

        .modal-close:hover {
            color: var(--text-primary);
        }

        /* Formulaire moderne */
        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 0.875rem;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .form-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .form-help {
            color: var(--text-secondary);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .modal-footer {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        /* Checkbox group pour audiences/zones */
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 0.5rem;
            padding: 0.5rem;
            background: var(--bg-primary);
            border-radius: 8px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.2s ease;
        }

        .checkbox-label:hover {
            background: var(--bg-secondary);
        }

        .checkbox-label input[type="checkbox"] {
            cursor: pointer;
        }

        /* File upload styling */
        .file-upload-wrapper {
            position: relative;
        }

        .file-upload-input {
            width: 100%;
            padding: 0.75rem;
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-upload-input:hover {
            border-color: var(--primary-color);
            background: var(--bg-primary);
        }
    </style>
</head>

<body>
    <div class="dashboard-layout">
        <main class="main-content">
            <!-- Header -->
            <header class="modern-header">
                <div class="header-content">
                    <div>
                        <h1 class="header-title">Médiathèque</h1>
                        <p class="header-subtitle">Gérez vos textes, images et vidéos</p>
                    </div>

                    <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'editor'])): ?>
                        <div class="header-actions">
                            <a href="<?= url('/') ?>" class="btn btn-ghost">
                                <i class="fas fa-arrow-left"></i> Retour Dashboard
                            </a>
                            <button onclick="openCreateModal()" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nouveau Contenu
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </header>

            <div class="users-container">
                <!-- Feedback Messages -->
                <?php if (isset($success) && $success): ?>
                    <div class="toast toast-success" id="successToast">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars($success) ?></span>
                        <button onclick="this.parentElement.remove()" class="toast-close"><i
                                class="fas fa-times"></i></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error) && $error): ?>
                    <div class="toast toast-error" id="errorToast">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                        <button onclick="this.parentElement.remove()" class="toast-close"><i
                                class="fas fa-times"></i></button>
                    </div>
                <?php endif; ?>

                <!-- List Table -->
                <div class="table-container">
                    <?php if (!empty($contents)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Aperçu</th>
                                    <th>Titre</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th style="width: 100px;">Statut</th>
                                    <th style="width: 80px;">Durée</th>
                                    <th style="width: 120px;">Créé le</th>
                                    <th style="width: 200px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contents as $content): ?>
                                    <?php
                                    $type = $content['content_type'];
                                    $name = $content['title'];
                                    $description = $content['description'] ?? '';
                                    $value = ($type === 'text') ? $content['text_content'] : $content['file_path'];
                                    $isActive = (int) $content['is_active'];
                                    $createdAt = date('d/m/Y', strtotime($content['created_at']));

                                    $jsData = [
                                        'id' => $content['id'],
                                        'name' => $name,
                                        'description' => $description,
                                        'type' => $type,
                                        'value' => $value,
                                        'duration' => $content['duration'] ?? 30,
                                        'is_active' => $isActive
                                    ];
                                    ?>
                                    <tr style="opacity: <?= $isActive ? '1' : '0.6' ?>;">
                                        <td style="text-align: center;">
                                            <?php if ($type === 'image'): ?>
                                                <img src="<?= htmlspecialchars($value) ?>" alt="Aperçu"
                                                    style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border-color);"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div
                                                    style="display: none; width: 50px; height: 50px; align-items: center; justify-content: center; background: #fee; border-radius: 6px; color: #c33;">
                                                    <i class="fas fa-image-slash"></i>
                                                </div>
                                            <?php elseif ($type === 'video'): ?>
                                                <div
                                                    style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px; color: white;">
                                                    <i class="fas fa-video"></i>
                                                </div>
                                            <?php else: ?>
                                                <div
                                                    style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 6px; color: white;">
                                                    <i class="fas fa-font"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong style="color: var(--text-primary);"><?= htmlspecialchars($name) ?></strong>
                                            <?php if ($type === 'text' && strlen($value) > 0): ?>
                                                <div
                                                    style="font-size: 0.85em; color: var(--text-secondary); margin-top: 4px; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?= htmlspecialchars(substr(strip_tags($value), 0, 50)) ?>
                                                    <?= strlen($value) > 50 ? '...' : '' ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td style="max-width: 200px;">
                                            <span style="font-size: 0.9em; color: var(--text-secondary);">
                                                <?= $description ? htmlspecialchars($description) : '<em style="color: var(--text-light);">Aucune description</em>' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="role-badge role-<?= $type === 'video' ? 'admin' : ($type === 'image' ? 'editor' : 'viewer') ?>">
                                                <i
                                                    class="fas fa-<?= $type === 'video' ? 'video' : ($type === 'image' ? 'image' : 'font') ?>"></i>
                                                <?= ucfirst($type) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($isActive): ?>
                                                <span
                                                    style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; background: #dcfce7; color: #166534; border-radius: 6px; font-size: 0.85em; font-weight: 500;">
                                                    <i class="fas fa-check-circle"></i> Actif
                                                </span>
                                            <?php else: ?>
                                                <span
                                                    style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 8px; background: #fee; color: #991b1b; border-radius: 6px; font-size: 0.85em; font-weight: 500;">
                                                    <i class="fas fa-times-circle"></i> Inactif
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center; color: var(--text-secondary);">
                                            <?= $content['duration'] ?>s
                                        </td>
                                        <td style="font-size: 0.9em; color: var(--text-secondary);">
                                            <?= $createdAt ?>
                                        </td>
                                        <td>
                                            <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'editor'])): ?>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-edit"
                                                        onclick='editContent(<?= str_replace("'", "&#39;", json_encode($jsData)) ?>)'
                                                        title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn-action btn-copy"
                                                        onclick="copyContent(<?= $content['id'] ?>, '<?= htmlspecialchars($name, ENT_QUOTES) ?>')"
                                                        title="Copier">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <button class="btn-action btn-delete"
                                                        onclick="confirmDelete(<?= $content['id'] ?>, '<?= htmlspecialchars($name, ENT_QUOTES) ?>')"
                                                        title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-layer-group"></i></div>
                            <h3>La médiathèque est vide</h3>
                            <p>Ajoutez des textes, images ou vidéos pour commencer.</p>
                            <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'editor'])): ?>
                                <button onclick="openCreateModal()" class="btn btn-primary">Ajouter un contenu</button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Create/Edit -->
    <div id="contentModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Ajouter un contenu</h3>
                <button onclick="closeModal()" class="modal-close">&times;</button>
            </div>

            <form id="contentForm" method="POST" action="<?= url('/contents/store') ?>" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="content_id" id="contentId">

                <div class="form-group">
                    <label class="form-label">Titre *</label>
                    <input type="text" name="name" id="contentName" required placeholder="Ex: Logo Entreprise"
                        class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="contentDescription" rows="2"
                        placeholder="Description optionnelle du contenu..." class="form-textarea"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Type de contenu *</label>
                    <select name="type" id="contentType" onchange="updateFormFields()" class="form-select">
                        <option value="text">Texte</option>
                        <option value="image">Image</option>
                        <option value="video">Vidéo</option>
                        <option value="text_image">Texte + Image</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>

                <!-- Champ fichier (conditionnel) -->
                <div class="form-group" id="fileUploadGroup" style="display: none;">
                    <label class="form-label">Fichier *</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="content_file" id="contentFile"
                            accept=".jpg,.jpeg,.png,.gif,.webp,.mp4,.webm,.pdf" class="file-upload-input">
                    </div>
                    <small class="form-help">Formats acceptés : JPG, PNG, WEBP, MP4, WEBM, PDF - Taille max : 50
                        MB</small>
                </div>

                <!-- Champ texte (conditionnel) -->
                <div class="form-group" id="textContentGroup">
                    <label class="form-label" id="valueLabel">Texte à afficher *</label>
                    <textarea name="value" id="contentValue" rows="4" placeholder="Saisissez votre texte..."
                        class="form-textarea"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Durée d'affichage (secondes)</label>
                    <input type="number" name="duration" id="contentDuration" value="30" min="1" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-checkbox-wrapper">
                        <input type="checkbox" name="is_active" id="contentIsActive" checked class="form-checkbox">
                        <span class="form-label" style="margin: 0;">Contenu actif</span>
                    </label>
                    <small class="form-help">Les contenus inactifs ne seront pas diffusés</small>
                </div>

                <!-- Publics cibles -->
                <div class="form-group">
                    <label class="form-label">Publics cibles</label>
                    <div class="checkbox-group">
                        <?php foreach ($audiences as $audience): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="audiences[]" value="<?= $audience['id'] ?>"
                                    class="audience-checkbox">
                                <?= htmlspecialchars($audience['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Zones de diffusion -->
                <div class="form-group">
                    <label class="form-label">Zones de diffusion</label>
                    <div class="checkbox-group">
                        <?php foreach ($zones as $zone): ?>
                            <label class="checkbox-label">
                                <input type="checkbox" name="zones[]" value="<?= $zone['id'] ?>" class="zone-checkbox">
                                <?= htmlspecialchars($zone['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal()" class="btn btn-ghost">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const BASE_URL = "<?= url('/') ?>";
        setTimeout(() => document.querySelectorAll('.toast').forEach(t => t.remove()), 5000);

        function updateFormFields() {
            const type = document.getElementById('contentType').value;
            const fileGroup = document.getElementById('fileUploadGroup');
            const fileInput = document.getElementById('contentFile');
            const textGroup = document.getElementById('textContentGroup');
            const contentValue = document.getElementById('contentValue');
            const valueLabel = document.getElementById('valueLabel');

            // Reset required
            fileInput.required = false;
            contentValue.required = false;

            if (type === 'text') {
                fileGroup.style.display = 'none';
                textGroup.style.display = 'block';
                contentValue.required = true;
                valueLabel.innerText = "Texte à afficher *";
            } else if (type === 'image' || type === 'video' || type === 'pdf') {
                fileGroup.style.display = 'block';
                textGroup.style.display = 'none';
                if (!document.getElementById('contentId').value) {
                    fileInput.required = true;
                }

                if (type === 'image') fileInput.accept = ".jpg,.jpeg,.png,.gif,.webp";
                else if (type === 'video') fileInput.accept = ".mp4,.webm";
                else if (type === 'pdf') fileInput.accept = ".pdf";
            } else if (type === 'text_image') {
                fileGroup.style.display = 'block';
                textGroup.style.display = 'block';
                contentValue.required = true;
                valueLabel.innerText = "Texte à afficher (sous l'image) *";
                fileInput.accept = ".jpg,.jpeg,.png,.gif,.webp";
                if (!document.getElementById('contentId').value) {
                    fileInput.required = true;
                }
            }
        }

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Ajouter un contenu';
            document.getElementById('contentForm').action = `${BASE_URL}contents/store`;
            document.getElementById('contentId').value = '';
            document.getElementById('contentName').value = '';
            document.getElementById('contentDescription').value = '';
            document.getElementById('contentType').value = 'text';
            document.getElementById('contentValue').value = '';
            document.getElementById('contentFile').value = '';
            document.getElementById('contentDuration').value = '30';
            document.getElementById('contentIsActive').checked = true;

            // Décocher toutes les checkboxes
            document.querySelectorAll('.audience-checkbox, .zone-checkbox').forEach(cb => cb.checked = false);

            updateFormFields();
            document.getElementById('contentModal').classList.add('active');
        }

        function editContent(data) {
            document.getElementById('modalTitle').textContent = 'Modifier le contenu';
            document.getElementById('contentForm').action = `${BASE_URL}contents/update`;
            document.getElementById('contentId').value = data.id;
            document.getElementById('contentName').value = data.name;
            document.getElementById('contentDescription').value = data.description || '';
            document.getElementById('contentType').value = data.type;
            document.getElementById('contentValue').value = data.value;
            document.getElementById('contentDuration').value = data.duration;
            document.getElementById('contentIsActive').checked = data.is_active == 1;

            updateFormFields();
            document.getElementById('contentModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('contentModal').classList.remove('active');
        }

        function copyContent(id, name) {
            if (confirm(`Voulez-vous créer une copie de "${name}" ?`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${BASE_URL}contents/copy`;

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'content_id';
                idInput.value = id;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf_token';
                csrfInput.value = '<?= htmlspecialchars($csrf_token) ?>';

                form.appendChild(idInput);
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function confirmDelete(id, name) {
            if (confirm(`Voulez-vous vraiment supprimer "${name}" ?\n\nCette action est irréversible.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${BASE_URL}contents/delete`;

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'content_id';
                idInput.value = id;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf_token';
                csrfInput.value = '<?= htmlspecialchars($csrf_token) ?>';

                form.appendChild(idInput);
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Initialiser l'affichage au chargement
        updateFormFields();
    </script>
</body>

</html>