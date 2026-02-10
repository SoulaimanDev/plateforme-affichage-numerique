<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Zones - Plume Vision CMS</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            max-width: 600px;
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
            margin-left: 26px;
        }

        .modal-footer {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
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
                        <h1 class="header-title">Zones de diffusion</h1>
                        <p class="header-subtitle">Gérez les zones géographiques de votre réseau</p>
                    </div>

                    <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'editor'])): ?>
                        <div class="header-actions">
                            <a href="<?= url('/') ?>" class="btn btn-ghost">
                                <i class="fas fa-arrow-left"></i> Retour Dashboard
                            </a>
                            <button onclick="openCreateModal()" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nouvelle Zone
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
                    <?php if (!empty($zones)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 80px;">Couleur</th>
                                    <th>Nom de la Zone</th>
                                    <th>Description</th>
                                    <th style="width: 120px;">Statut</th>
                                    <th style="width: 140px;">Date de création</th>
                                    <th style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($zones as $zone): ?>
                                    <?php
                                    $isActive = (int) $zone['is_active'];
                                    $createdAt = date('d/m/Y', strtotime($zone['created_at']));
                                    $zoneColor = htmlspecialchars($zone['color'] ?? '#3b82f6');
                                    ?>
                                    <tr style="opacity: <?= $isActive ? '1' : '0.6' ?>;">
                                        <td style="text-align: center;">
                                            <div
                                                style="width: 40px; height: 40px; border-radius: 6px; background: <?= $zoneColor ?>; border: 1px solid rgba(0,0,0,0.1); display: inline-block;">
                                            </div>
                                        </td>
                                        <td>
                                            <strong
                                                style="color: var(--text-primary); font-size: 1rem;"><?= htmlspecialchars($zone['name']) ?></strong>
                                            <div
                                                style="font-size: 0.8em; font-family: monospace; color: var(--text-light); margin-top: 2px;">
                                                <?= $zoneColor ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="color: var(--text-secondary); max-width: 350px;">
                                                <?= $zone['description'] ? htmlspecialchars($zone['description']) : '<em style="color: var(--text-light); font-size: 0.9em;">Aucune description</em>' ?>
                                            </div>
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
                                        <td style="font-size: 0.9em; color: var(--text-secondary);">
                                            <?= $createdAt ?>
                                        </td>
                                        <td>
                                            <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'editor'])): ?>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-edit"
                                                        onclick='editZone(<?= json_encode($zone) ?>)' title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn-action btn-delete"
                                                        onclick="confirmDelete(<?= $zone['id'] ?>, '<?= htmlspecialchars($zone['name'], ENT_QUOTES) ?>')"
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
                            <div class="empty-icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <h3>Aucune zone définie</h3>
                            <p>Ajoutez des zones géographiques pour organiser vos écrans.</p>
                            <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'editor'])): ?>
                                <button onclick="openCreateModal()" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Nouvelle Zone
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Create/Edit -->
    <div id="zoneModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Ajouter une zone</h3>
                <button onclick="closeModal()" class="modal-close">&times;</button>
            </div>

            <form id="zoneForm" method="POST" action="<?= url('/zones/store') ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="zone_id" id="zoneId">

                <div class="form-group">
                    <label class="form-label">Nom de la zone *</label>
                    <input type="text" name="name" id="zoneName" required
                        placeholder="Ex: Cafeteria, Couloir principal..." class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="zoneDescription" rows="2"
                        placeholder="Description optionnelle de la zone..." class="form-textarea"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Localisation</label>
                    <input type="text" name="location" id="zoneLocation" placeholder="Ex: Bâtiment A, 1er étage"
                        class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Couleur d'identification</label>
                    <input type="color" name="color" id="zoneColor" value="#3b82f6" class="form-input"
                        style="height: 50px; padding: 0.25rem;">
                </div>

                <div class="form-group">
                    <label class="form-checkbox-wrapper">
                        <input type="checkbox" name="is_active" id="zoneIsActive" checked class="form-checkbox">
                        <span class="form-label" style="margin: 0;">Zone active</span>
                    </label>
                    <small class="form-help">Les zones inactives ne peuvent pas recevoir de nouveaux écrans</small>
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

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Ajouter une zone';
            document.getElementById('zoneForm').action = `${BASE_URL}zones/store`;
            document.getElementById('zoneId').value = '';
            document.getElementById('zoneName').value = '';
            document.getElementById('zoneDescription').value = '';
            document.getElementById('zoneLocation').value = '';
            document.getElementById('zoneColor').value = '#3b82f6';
            document.getElementById('zoneIsActive').checked = true;
            document.getElementById('zoneModal').classList.add('active');
        }

        function editZone(data) {
            document.getElementById('modalTitle').textContent = 'Modifier la zone';
            document.getElementById('zoneForm').action = `${BASE_URL}zones/update`;
            document.getElementById('zoneId').value = data.id;
            document.getElementById('zoneName').value = data.name;
            document.getElementById('zoneDescription').value = data.description || '';
            document.getElementById('zoneLocation').value = data.location || '';
            document.getElementById('zoneColor').value = data.color || '#3b82f6';
            document.getElementById('zoneIsActive').checked = data.is_active == 1;
            document.getElementById('zoneModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('zoneModal').classList.remove('active');
        }

        function confirmDelete(id, name) {
            if (confirm(`Voulez-vous vraiment supprimer la zone "${name}" ?\n\nCette action est irréversible.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${BASE_URL}zones/delete`;

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'zone_id';
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
    </script>
</body>

</html>