<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Publics - Plume Vision CMS</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="<?= url('/css/global.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/dashboard.css') ?>">

    <style>
        /* Variables et Styles Modernes (Standardisé) */
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
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        /* Layout */
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

        /* Header */
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

        /* Boutons */
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
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
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

        /* Container */
        .users-container {
            padding: 2rem;
        }

        /* Tableau */
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
            border-bottom: 1px solid var(--border-color);
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: var(--bg-primary);
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
        }

        .btn-edit:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-delete:hover {
            background: var(--danger-color);
            color: white;
        }

        /* Éléments spécifiques Audiences */
        .color-badge {
            display: inline-block;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 0 1px var(--border-color);
            vertical-align: middle;
        }

        /* Modal */
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
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 2rem;
            width: 90%;
            max-width: 500px;
            box-shadow: var(--shadow-lg);
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.5rem;
            color: var(--text-primary);
        }

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
            font-family: 'Inter', sans-serif;
        }

        .color-input {
            width: 100%;
            height: 48px;
            padding: 4px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
        }

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
            gap: 0.75rem;
            z-index: 1000;
        }

        .toast-success {
            border-left: 4px solid var(--success-color);
        }

        .toast-error {
            border-left: 4px solid var(--danger-color);
        }

        .modal-footer {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 2rem;
            color: var(--text-light);
            margin-bottom: 1rem;
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
                        <h1 class="header-title">Publics Cibles</h1>
                        <p class="header-subtitle">Définissez vos audiences pour le ciblage</p>
                    </div>
                    <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'editor'])): ?>
                        <div class="header-actions">
                            <a href="<?= url('/') ?>" class="btn btn-ghost"><i class="fas fa-arrow-left"></i> Retour
                                Dashboard</a>
                            <button onclick="openCreateModal()" class="btn btn-primary"><i class="fas fa-plus"></i> Nouveau
                                Public</button>
                        </div>
                    <?php endif; ?>
                </div>
            </header>

            <div class="users-container">
                <!-- Feedback -->
                <?php if (isset($success) && $success): ?>
                    <div class="toast toast-success" onclick="this.remove()">
                        <i class="fas fa-check-circle"></i> <span>
                            <?= htmlspecialchars($success) ?>
                        </span>
                    </div>
                <?php endif; ?>
                <?php if (isset($error) && $error): ?>
                    <div class="toast toast-error" onclick="this.remove()">
                        <i class="fas fa-exclamation-circle"></i> <span>
                            <?= htmlspecialchars($error) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <!-- Table -->
                <div class="table-container">
                    <?php if (!empty($audiences)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Couleur</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Statut</th>
                                    <th>Date Création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($audiences as $aud): ?>
                                    <?php
                                    $isActive = (int) $aud['is_active'];
                                    $createdAt = date('d/m/Y', strtotime($aud['created_at']));
                                    ?>
                                    <tr style="opacity: <?= $isActive ? '1' : '0.6' ?>;">
                                        <td style="text-align: center;">
                                            <span class="color-badge"
                                                style="background-color: <?= htmlspecialchars($aud['color']) ?>;"></span>
                                        </td>
                                        <td>
                                            <strong style="color: var(--text-primary);">
                                                <?= htmlspecialchars($aud['name']) ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <div style="color: var(--text-secondary); max-width: 400px; font-size: 0.9em;">
                                                <?= $aud['description'] ? htmlspecialchars($aud['description']) : '<em style="color: var(--text-light);">Aucune description</em>' ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($isActive): ?>
                                                <span
                                                    style="padding: 4px 8px; background: #dcfce7; color: #166534; border-radius: 6px; font-size: 0.8em; font-weight: 600;">Actif</span>
                                            <?php else: ?>
                                                <span
                                                    style="padding: 4px 8px; background: #fee; color: #991b1b; border-radius: 6px; font-size: 0.8em; font-weight: 600;">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-size: 0.9em; color: var(--text-secondary);">
                                            <?= $createdAt ?>
                                        </td>
                                        <td>
                                            <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'editor'])): ?>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-edit"
                                                        onclick='editAudience(<?= json_encode($aud) ?>)'><i
                                                            class="fas fa-edit"></i></button>
                                                    <button class="btn-action btn-delete"
                                                        onclick="confirmDelete(<?= $aud['id'] ?>)"><i
                                                            class="fas fa-trash"></i></button>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-users"></i></div>
                            <h3>Aucun public défini</h3>
                            <p>Créez des groupes pour cibler vos contenus (ex: Étudiants, Visiteurs, Staff).</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="audienceModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Nouveau Public</h3>
                <button onclick="closeModal()" class="modal-close"
                    style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            <form id="audienceForm" method="POST" action="<?= url('/audiences/store') ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="audience_id" id="audienceId">

                <div class="form-group">
                    <label class="form-label">Nom du Public *</label>
                    <input type="text" name="name" id="audName" required placeholder="Ex: Étudiants L3"
                        class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" id="audDesc" rows="3" placeholder="Description courte..."
                        class="form-textarea"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Couleur d'identification *</label>
                    <input type="color" name="color" id="audColor" value="#3b82f6" class="color-input">
                </div>

                <div class="form-group">
                    <label style="display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="is_active" id="audActive" checked>
                        <span>Public actif (disponible pour le ciblage)</span>
                    </label>
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

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Nouveau Public';
            document.getElementById('audienceForm').action = `${BASE_URL}audiences/store`;
            document.getElementById('audienceId').value = '';
            document.getElementById('audName').value = '';
            document.getElementById('audDesc').value = '';
            document.getElementById('audColor').value = '#3b82f6';
            document.getElementById('audActive').checked = true;
            document.getElementById('audienceModal').classList.add('active');
        }

        function editAudience(data) {
            document.getElementById('modalTitle').textContent = 'Modifier le Public';
            document.getElementById('audienceForm').action = `${BASE_URL}audiences/update`;
            document.getElementById('audienceId').value = data.id;
            document.getElementById('audName').value = data.name;
            document.getElementById('audDesc').value = data.description || '';
            document.getElementById('audColor').value = data.color || '#3b82f6';
            document.getElementById('audActive').checked = data.is_active == 1;
            document.getElementById('audienceModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('audienceModal').classList.remove('active');
        }

        function confirmDelete(id) {
            if (confirm('Supprimer ce public ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${BASE_URL}audiences/delete`;
                form.innerHTML = `
                    <input type="hidden" name="audience_id" value="${id}">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        setTimeout(() => document.querySelectorAll('.toast').forEach(t => t.remove()), 5000);
    </script>
</body>

</html>