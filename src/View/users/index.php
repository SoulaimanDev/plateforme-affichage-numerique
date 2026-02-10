<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Plume Vision CMS</title>

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

        .btn-secondary {
            background: var(--text-secondary);
            color: white;
        }

        .btn-secondary:hover {
            background: #475569;
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
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

        /* Statistiques rapides */
        .users-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
        }

        .stat-icon.success {
            background: linear-gradient(135deg, var(--success-color), #059669);
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
        }

        .stat-content {
            flex: 1;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
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

        /* Colonnes spécifiques */
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--success-color));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
        }

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

        /* Toggle de statut moderne */
        .status-toggle {
            position: relative;
        }

        .status-checkbox {
            display: none;
        }

        .status-label {
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
            padding-left: 3rem;
        }

        .status-label::before {
            content: '';
            position: absolute;
            left: 0;
            width: 44px;
            height: 24px;
            background: #cbd5e1;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .status-label::after {
            content: '';
            position: absolute;
            left: 2px;
            top: 2px;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .status-checkbox:checked+.status-label::before {
            background: var(--success-color);
        }

        .status-checkbox:checked+.status-label::after {
            transform: translateX(20px);
        }

        .status-checkbox:disabled+.status-label {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .status-text {
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Actions */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .btn-edit {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary-color);
        }

        .btn-edit:hover {
            background: var(--primary-color);
            color: white;
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
        }

        .btn-delete:hover {
            background: var(--danger-color);
            color: white;
        }

        .current-user {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .login-time {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .no-login {
            color: var(--text-light);
            font-style: italic;
            font-size: 0.875rem;
        }

        /* État vide */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--text-light);
            margin-bottom: 1rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            margin-bottom: 2rem;
        }

        /* Animations */
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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-container,
        .stat-card {
            animation: fadeIn 0.6s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .users-container {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .header-actions {
                justify-content: center;
            }

            .users-stats {
                grid-template-columns: 1fr;
            }

            /* Masquer le tableau sur mobile et afficher les cartes */
            .table-container .table {
                display: none;
            }

            .toast {
                left: 1rem;
                right: 1rem;
                top: 1rem;
            }
        }

        /* Cartes utilisateurs pour mobile */
        .user-cards {
            display: none;
        }

        @media (max-width: 768px) {
            .user-cards {
                display: block;
            }

            .user-card {
                background: var(--bg-secondary);
                border: 1px solid var(--border-color);
                border-radius: 12px;
                padding: 1.5rem;
                margin-bottom: 1rem;
                box-shadow: var(--shadow-sm);
            }

            .user-card-header {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .user-card-avatar {
                width: 48px;
                height: 48px;
                border-radius: 50%;
                background: linear-gradient(135deg, var(--primary-color), var(--success-color));
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1rem;
            }

            .user-card-info h4 {
                margin: 0;
                font-size: 1.1rem;
                font-weight: 600;
                color: var(--text-primary);
            }

            .user-card-info p {
                margin: 0.25rem 0 0 0;
                color: var(--text-secondary);
                font-size: 0.875rem;
            }

            .user-card-details {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .user-card-field {
                display: flex;
                flex-direction: column;
            }

            .user-card-field label {
                font-size: 0.75rem;
                color: var(--text-light);
                text-transform: uppercase;
                letter-spacing: 0.05em;
                margin-bottom: 0.25rem;
            }

            .user-card-field .value {
                font-weight: 500;
                color: var(--text-primary);
            }

            .user-card-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding-top: 1rem;
                border-top: 1px solid var(--border-color);
            }
        }

        /* Thème sombre */
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-light: #64748b;
            --border-color: #334155;
        }
    </style>
</head>

<body>
    <div class="dashboard-layout">
        <main class="main-content">
            <!-- Header moderne -->
            <header class="modern-header">
                <div class="header-content">
                    <div>
                        <h1 class="header-title">Gestion des Utilisateurs</h1>
                        <p class="header-subtitle">Gérez les comptes utilisateurs et leurs permissions</p>
                    </div>
                    <div class="header-actions">
                        <a href="<?= url('/') ?>" class="btn btn-ghost">
                            <i class="fas fa-arrow-left"></i>
                            Retour Dashboard
                        </a>
                        <a href="<?= url('/users/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Nouvel Utilisateur
                        </a>
                    </div>
                </div>
            </header>

            <div class="users-container">
                <!-- Messages de feedback -->
                <?php if (isset($success) && $success): ?>
                    <div class="toast toast-success" id="successToast">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars($success) ?></span>
                        <button onclick="closeToast('successToast')" class="toast-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error) && $error): ?>
                    <div class="toast toast-error" id="errorToast">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                        <button onclick="closeToast('errorToast')" class="toast-close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Statistiques rapides -->
                <div class="users-stats">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?= count($users) ?></div>
                            <div class="stat-label">Total Utilisateurs</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value"><?= count(array_filter($users, fn($u) => $u['is_active'])) ?></div>
                            <div class="stat-label">Utilisateurs Actifs</div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">
                                <?= count(array_filter($users, fn($u) => $u['role_name'] === 'admin')) ?>
                            </div>
                            <div class="stat-label">Administrateurs</div>
                        </div>
                    </div>
                </div>

                <!-- Tableau des utilisateurs -->
                <div class="table-container">
                    <?php if (!empty($users)): ?>
                        <!-- Tableau pour desktop/tablette -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Utilisateur</th>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Dernière connexion</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr data-user-id="<?= $user['id'] ?>"
                                        data-json="<?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>">
                                        <td>
                                            <span style="font-weight: 600; color: var(--text-secondary);">
                                                #<?= htmlspecialchars($user['id']) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <?= strtoupper(substr($user['email'], 0, 2)) ?>
                                                </div>
                                                <span style="font-weight: 500;"><?= htmlspecialchars($user['email']) ?></span>
                                            </div>
                                        </td>

                                        <td><?= htmlspecialchars($user['lastname']) ?></td>
                                        <td><?= htmlspecialchars($user['firstname']) ?></td>

                                        <td>
                                            <span
                                                class="role-badge role-<?= htmlspecialchars($user['role_name'] ?? $user['role'] ?? 'viewer') ?>">
                                                <?php
                                                $roleName = $user['role_name'] ?? $user['role'] ?? 'viewer';
                                                $icons = [
                                                    'admin' => 'crown',
                                                    'editor' => 'edit',
                                                    'viewer' => 'eye'
                                                ];
                                                $icon = $icons[$roleName] ?? 'eye';
                                                ?>
                                                <i class="fas fa-<?= $icon ?>"></i>
                                                <?= htmlspecialchars(ucfirst($roleName)) ?>
                                            </span>
                                        </td>

                                        <td>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="status-<?= $user['id'] ?>" class="status-checkbox"
                                                    <?= $user['is_active'] ? 'checked' : '' ?>
                                                    <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>
                                                    onchange="toggleUserStatus(<?= $user['id'] ?>, this.checked)">
                                                <label for="status-<?= $user['id'] ?>" class="status-label">
                                                    <span class="status-text">
                                                        <?= $user['is_active'] ? 'Actif' : 'Inactif' ?>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>

                                        <td>
                                            <?php if ($user['last_login']): ?>
                                                <span class="login-time"
                                                    title="<?= date('d/m/Y H:i:s', strtotime($user['last_login'])) ?>">
                                                    <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="no-login">Jamais connecté</span>
                                            <?php endif; ?>
                                        </td>

                                        <td>
                                            <div class="action-buttons">
                                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                    <button class="btn-action btn-edit" onclick="editUser(<?= $user['id'] ?>)"
                                                        title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </button>

                                                    <button class="btn-action btn-delete"
                                                        onclick="confirmDeleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>')"
                                                        title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="current-user">
                                                        <i class="fas fa-user"></i>
                                                        Vous
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Cartes pour mobile -->
                        <div class="user-cards">
                            <?php foreach ($users as $user): ?>
                                <div class="user-card" data-user-id="<?= $user['id'] ?>"
                                    data-json="<?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="user-card-header">
                                        <div class="user-card-avatar">
                                            <?= strtoupper(substr($user['email'], 0, 2)) ?>
                                        </div>
                                        <div class="user-card-info">
                                            <h4><?= htmlspecialchars($user['firstname']) ?>
                                                <?= htmlspecialchars($user['lastname']) ?>
                                            </h4>
                                            <p><?= htmlspecialchars($user['email']) ?></p>
                                        </div>
                                    </div>

                                    <div class="user-card-details">
                                        <div class="user-card-field">
                                            <label>Rôle</label>
                                            <div class="value">
                                                <span class="role-badge role-<?= htmlspecialchars($user['role_name']) ?>">
                                                    <i
                                                        class="fas fa-<?= $user['role_name'] === 'admin' ? 'crown' : ($user['role_name'] === 'editor' ? 'edit' : 'eye') ?>"></i>
                                                    <?= htmlspecialchars(ucfirst($user['role_name'])) ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="user-card-field">
                                            <label>Statut</label>
                                            <div class="value">
                                                <div class="status-toggle">
                                                    <input type="checkbox" id="status-mobile-<?= $user['id'] ?>"
                                                        class="status-checkbox" <?= $user['is_active'] ? 'checked' : '' ?>
                                                        <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>
                                                        onchange="toggleUserStatus(<?= $user['id'] ?>, this.checked)">
                                                    <label for="status-mobile-<?= $user['id'] ?>" class="status-label">
                                                        <span class="status-text">
                                                            <?= $user['is_active'] ? 'Actif' : 'Inactif' ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="user-card-field">
                                            <label>ID</label>
                                            <div class="value">#<?= htmlspecialchars($user['id']) ?></div>
                                        </div>

                                        <div class="user-card-field">
                                            <label>Dernière connexion</label>
                                            <div class="value">
                                                <?php if ($user['last_login']): ?>
                                                    <span class="login-time">
                                                        <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="no-login">Jamais</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="user-card-actions">
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <div class="action-buttons">
                                                <button class="btn-action btn-edit" onclick="editUser(<?= $user['id'] ?>)"
                                                    title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                                <button class="btn-action btn-delete"
                                                    onclick="confirmDeleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['email']) ?>')"
                                                    title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <span class="current-user">
                                                <i class="fas fa-user"></i>
                                                Vous
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3>Aucun utilisateur trouvé</h3>
                            <p>Commencez par créer votre premier utilisateur</p>
                            <p>Commencez par créer votre premier utilisateur</p>
                            <a href="<?= url('/users/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Créer un utilisateur
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // URL de base
        const BASE_URL = "<?= url('/') ?>";

        // CSRF token fourni par le contrôleur
        const CSRF_TOKEN = '<?= htmlspecialchars($csrf_token ?? $_SESSION['csrf_token'] ?? '') ?>';

        // Fermer les toasts automatiquement
        setTimeout(() => {
            const toasts = document.querySelectorAll('.toast');
            toasts.forEach(toast => {
                toast.style.animation = 'slideInRight 0.3s ease reverse';
                setTimeout(() => toast.remove(), 300);
            });
        }, 5000);

        // Fermer toast manuellement
        function closeToast(id) {
            const toast = document.getElementById(id);
            if (toast) {
                toast.style.animation = 'slideInRight 0.3s ease reverse';
                setTimeout(() => toast.remove(), 300);
            }
        }

        // Toggle statut utilisateur via POST fetch
        async function toggleUserStatus(userId, isActive) {
            const action = isActive ? 'activer' : 'désactiver';
            const confirmed = confirm(`Voulez-vous vraiment ${action} cet utilisateur ?`);

            if (!confirmed) {
                // Remettre le toggle dans son état précédent
                const checkbox = document.getElementById(`status-${userId}`) || document.getElementById(`status-mobile-${userId}`);
                if (checkbox) checkbox.checked = !isActive;
                return;
            }

            try {
                const formData = new FormData();
                formData.append('user_id', userId);
                formData.append('is_active', isActive ? 1 : 0);
                formData.append('csrf_token', CSRF_TOKEN);


                const resp = await fetch(`${BASE_URL}users/toggle-status`, { method: 'POST', body: formData });
                if (!resp.ok) throw new Error('Erreur réseau');

                // Recharger la page pour voir l'état à jour ou afficher un toast
                showToast(isActive ? 'Utilisateur activé avec succès' : 'Utilisateur désactivé avec succès', 'success');
                setTimeout(() => location.reload(), 800);
            } catch (err) {
                showToast('Erreur lors de la modification du statut', 'error');
                const checkbox = document.getElementById(`status-${userId}`) || document.getElementById(`status-mobile-${userId}`);
                if (checkbox) checkbox.checked = !isActive;
            }
        }

        // Modifier utilisateur - affiche le formulaire avec les données
        function editUser(userId) {
            // Récupérer les données de l'utilisateur depuis la ligne du tableau
            const row = document.querySelector(`tr[data-user-id="${userId}"]`) ||
                document.querySelector(`[data-user-id="${userId}"]`);

            if (row) {
                try {
                    const jsonStr = row.getAttribute('data-json');
                    if (jsonStr) {
                        const user = JSON.parse(jsonStr);
                        // Mapper les données pour le modal
                        const userData = {
                            id: user.id,
                            email: user.email,
                            lastname: user.lastname,
                            firstname: user.firstname,
                            // Gérer les différentes conventions de nommage possibles
                            role: user.role_name || user.role || 'viewer'
                        };
                        showEditModal(userData);
                        return;
                    }
                } catch (e) {
                    console.error('Erreur lors du parsing des données utilisateur:', e);
                }
            }

            // Fallback : récupérer depuis les éléments visibles si le JSON echoue ou si row n'est pas trouvé
            if (!row || !row.getAttribute('data-json')) {
                // Fallback : récupérer depuis les éléments visibles
                const userRows = document.querySelectorAll('tbody tr');
                let userData = null;

                userRows.forEach(tr => {
                    const cells = tr.querySelectorAll('td');
                    if (cells[0] && cells[0].textContent.includes(`#${userId}`)) {
                        userData = {
                            id: userId,
                            email: cells[1].querySelector('.user-info span').textContent.trim(),
                            lastname: cells[2].textContent.trim(),
                            firstname: cells[3].textContent.trim(),
                            role: cells[4].querySelector('.role-badge').textContent.toLowerCase().trim()
                        };
                    }
                });

                if (userData) {
                    showEditModal(userData);
                } else {
                    alert('Erreur : Impossible de récupérer les données utilisateur');
                }
            }
        }

        // Afficher le modal d'édition
        function showEditModal(userData) {
            // Créer le modal d'édition
            const modal = document.createElement('div');
            modal.id = 'editModal';
            modal.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center;">
                    <div style="background: var(--bg-secondary); border-radius: 12px; padding: 2rem; max-width: 500px; width: 90%; box-shadow: var(--shadow-lg);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                            <h3 style="margin: 0; color: var(--text-primary);">Modifier l'utilisateur</h3>
                            <button onclick="closeEditModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-light);">&times;</button>
                        </div>
                        
                        <form id="editUserForm" onsubmit="submitEdit(event)">
                            <input type="hidden" name="user_id" value="${userData.id}">
                            
                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Email</label>
                                <input type="email" name="email" value="${userData.email}" required 
                                       style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Prénom</label>
                                    <input type="text" name="firstname" value="${userData.firstname}" required 
                                           style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nom</label>
                                    <input type="text" name="lastname" value="${userData.lastname}" required 
                                           style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 1rem;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Nouveau mot de passe (optionnel)</label>
                                <input type="password" name="password" minlength="6" 
                                       placeholder="Laisser vide pour ne pas changer"
                                       style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
                                <small style="color: var(--text-light);">Minimum 6 caractères si renseigné</small>
                            </div>
                            
                            <div style="margin-bottom: 1.5rem;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Rôle</label>
                                <select name="role_id" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
                                    <option value="1" ${userData.role === 'admin' ? 'selected' : ''}>Admin</option>
                                    <option value="2" ${userData.role === 'editor' ? 'selected' : ''}>Editor</option>
                                    <option value="3" ${userData.role === 'viewer' ? 'selected' : ''}>Viewer</option>
                                </select>
                            </div>
                            
                            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                                <button type="button" onclick="closeEditModal()" 
                                        style="padding: 0.75rem 1.5rem; background: var(--text-secondary); color: white; border: none; border-radius: 8px; cursor: pointer;">Annuler</button>
                                <button type="submit" 
                                        style="padding: 0.75rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer;">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);
        }

        // Fermer le modal d'édition
        function closeEditModal() {
            const modal = document.getElementById('editModal');
            if (modal) modal.remove();
        }

        // Soumettre la modification
        function submitEdit(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            // Validation côté client
            const password = formData.get('password');
            if (password && password.length < 6) {
                alert('Le mot de passe doit contenir au moins 6 caractères');
                return;
            }

            // Créer le formulaire de soumission
            const submitForm = document.createElement('form');
            submitForm.method = 'POST';
            submitForm.action = `${BASE_URL}users/update`;

            submitForm.style.display = 'none';

            // Ajouter tous les champs
            for (let [key, value] of formData.entries()) {
                if (value) { // Ne pas envoyer les champs vides (mot de passe optionnel)
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = value;
                    submitForm.appendChild(input);
                }
            }

            // AJOUTER LE TOKEN CSRF (critique pour la sécurité)
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = CSRF_TOKEN;
            submitForm.appendChild(csrfInput);

            document.body.appendChild(submitForm);
            submitForm.submit();
        }

        // Confirmation suppression définitive
        function confirmDeleteUser(userId, email) {
            const answer = prompt(
                `⚠️ SUPPRESSION DÉFINITIVE ⚠️\n\nVoulez-vous supprimer DEFINITIVEMENT l'utilisateur "${email}" de la base de données ?\n\nCette action est IRRÉVERSIBLE.\n\nTapez SUPPRIMER pour confirmer :`
            );

            if (answer !== 'SUPPRIMER') {
                showToast('Suppression annulée', 'error');
                return;
            }

            // Utilise la route existante /users/delete
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `${BASE_URL}users/delete`;

            form.style.display = 'none';

            const userIdInput = document.createElement('input');
            userIdInput.type = 'hidden';
            userIdInput.name = 'user_id';
            userIdInput.value = userId;
            form.appendChild(userIdInput);

            // Ajouter le token CSRF
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = CSRF_TOKEN;
            form.appendChild(csrfInput);

            document.body.appendChild(form);

            showToast('Suppression en cours...', 'info');
            form.submit();
        }

        // Fonction pour afficher des toasts dynamiques
        function showToast(message, type = 'success') {
            // Supprimer les anciens toasts
            const existingToasts = document.querySelectorAll('.toast');
            existingToasts.forEach(toast => toast.remove());

            // Créer le nouveau toast
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="toast-close">
                    <i class="fas fa-times"></i>
                </button>
            `;

            document.body.appendChild(toast);

            // Supprimer automatiquement après 5 secondes
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.style.animation = 'slideInRight 0.3s ease reverse';
                    setTimeout(() => toast.remove(), 300);
                }
            }, 5000);
        }
    </script>
</body>

</html>