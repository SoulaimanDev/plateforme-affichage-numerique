<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvel Utilisateur - Plume Vision CMS</title>

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
            --danger-color: #ef4444;
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

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
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
        .form-container {
            margin: 2rem;
            padding: 0;
            width: auto;
        }

        /* Carte de formulaire */
        .form-card {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            animation: fadeIn 0.6s ease-out;
            max-width: 900px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-description {
            color: var(--text-secondary);
        }

        /* Grille de formulaire */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .form-group input,
        .form-group select {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.875rem;
            background: var(--bg-primary);
            transition: all 0.2s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .password-input {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 0.25rem;
        }

        .form-help {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-top: 0.25rem;
        }

        .required {
            color: var(--danger-color);
        }

        /* Actions du formulaire */
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
        }

        /* Animations */
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

        /* Responsive */
        @media (max-width: 768px) {
            .form-container {
                padding: 1rem;
                margin: 1rem auto;
            }

            .form-card {
                padding: 1.5rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }

            .form-actions {
                flex-direction: column;
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
    <!-- Header moderne -->
    <header class="modern-header">
        <div class="header-content">
            <div>
                <h1 class="header-title">Nouvel Utilisateur</h1>
                <p class="header-subtitle">Créer un nouveau compte utilisateur</p>
            </div>
            <div>
                <a href="<?= url('/users') ?>" class="btn btn-ghost">
                    <i class="fas fa-arrow-left"></i>
                    Retour à la liste
                </a>
            </div>
        </div>
    </header>

    <div class="form-container">
        <div class="form-card">
            <!-- Header du formulaire -->
            <div class="form-header">
                <div class="form-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2 class="form-title">Créer un utilisateur</h2>
                <p class="form-description">Remplissez les informations ci-dessous pour créer un nouveau compte</p>
            </div>

            <!-- Formulaire -->
            <form method="POST" action="<?= url('/users/store') ?>" id="userForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="email">
                            Adresse email <span class="required">*</span>
                        </label>
                        <input type="email" id="email" name="email" required placeholder="exemple@domaine.com">
                        <div class="form-help">L'email servira d'identifiant de connexion</div>
                    </div>

                    <div class="form-group">
                        <label for="firstname">
                            Prénom <span class="required">*</span>
                        </label>
                        <input type="text" id="firstname" name="firstname" required placeholder="Prénom">
                    </div>

                    <div class="form-group">
                        <label for="lastname">
                            Nom <span class="required">*</span>
                        </label>
                        <input type="text" id="lastname" name="lastname" required placeholder="Nom">
                    </div>

                    <div class="form-group">
                        <label for="password">
                            Mot de passe <span class="required">*</span>
                        </label>
                        <div class="password-input">
                            <input type="password" id="password" name="password" required minlength="6"
                                placeholder="••••••••">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>
                        <div class="form-help">Minimum 6 caractères</div>
                    </div>

                    <div class="form-group">
                        <label for="role_id">
                            Rôle <span class="required">*</span>
                        </label>
                        <select id="role_id" name="role_id" required>
                            <option value="">Sélectionner un rôle</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= $role['id'] ?>">
                                    <?= htmlspecialchars(ucfirst($role['name'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-help">Définit les permissions de l'utilisateur</div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="<?= url('/users') ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i>
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Toggle mot de passe
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }

        // Validation en temps réel
        document.getElementById('userForm').addEventListener('submit', function (e) {
            const submitBtn = document.getElementById('submitBtn');

            // Désactiver le bouton pour éviter les doubles soumissions
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';

            // Réactiver après 3 secondes en cas d'erreur
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Créer l\'utilisateur';
            }, 3000);
        });

        // Validation des champs en temps réel
        document.getElementById('email').addEventListener('blur', function () {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email && !emailRegex.test(email)) {
                this.style.borderColor = 'var(--danger-color)';
            } else {
                this.style.borderColor = 'var(--border-color)';
            }
        });

        document.getElementById('password').addEventListener('input', function () {
            const password = this.value;

            if (password.length > 0 && password.length < 6) {
                this.style.borderColor = 'var(--danger-color)';
            } else {
                this.style.borderColor = 'var(--border-color)';
            }
        });
    </script>
</body>

</html>