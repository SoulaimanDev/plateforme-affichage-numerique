<!DOCTYPE html>
<html lang="fr" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - Plume Vision CMS</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= url('/css/login-pro.css') ?>">
</head>

<body>
    <div class="login-container">

        <!-- Section formulaire -->
        <div class="login-form-section">
            <!-- Header -->
            <div class="login-header">
                <div class="logo-container">
                    <div class="logo-icon">
                        <i class="fas fa-tv"></i>
                    </div>
                    <div class="logo-text">
                        <h1 class="logo-title">Plume Vision</h1>
                        <p class="logo-subtitle">plateforme d’affichage numérique scolaire</p>
                    </div>
                </div>

                <button class="theme-toggle" onclick="toggleTheme()" title="Changer de thème">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>
            </div>

            <!-- Formulaire -->
            <div class="login-form-container">
                <div class="form-header">
                    <h2 class="form-title">Nouveau mot de passe</h2>
                    <p class="form-subtitle">Choisissez un mot de passe sécurisé</p>
                </div>

                <!-- Messages -->
                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($is_valid) && $is_valid): ?>
                    <!-- Formulaire -->
                    <form method="POST" action="<?= url('/update-password') ?>" class="login-form" id="resetForm">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                        <div class="form-group">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <div class="input-container">
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" id="password" name="password" class="form-input"
                                    placeholder="Minimum 6 caractères..." required minlength="6">
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                </button>
                            </div>
                            <div class="field-error" id="passwordError"></div>
                            <div class="password-strength" id="passwordStrength">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strengthFill"></div>
                                </div>
                                <div class="strength-text" id="strengthText">Entrez un mot de passe</div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                            <div class="input-container">
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-input"
                                    placeholder="••••••••" required>
                                <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye" id="confirmPasswordToggleIcon"></i>
                                </button>
                            </div>
                            <div class="field-error" id="confirmPasswordError"></div>
                        </div>

                        <button type="submit" class="login-btn" id="updateBtn" disabled>
                            <span class="btn-text">Mettre à jour</span>
                            <div class="btn-loader">
                                <div class="loader-spinner"></div>
                            </div>
                            <i class="fas fa-check btn-icon"></i>
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-error" style="margin-top: 1rem;">
                        <i class="fas fa-times-circle"></i>
                        <span>Ce lien est invalide ou a expiré.</span>
                    </div>
                <?php endif; ?>

                <!-- Retour à la connexion -->
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="/login" class="forgot-link">
                        <i class="fas fa-arrow-left"></i>
                        Retour à la connexion
                    </a>
                </div>
            </div>
        </div>

        <!-- Section illustration -->
        <div class="login-illustration-section">
            <div class="illustration-container">
                <div class="illustration-content">
                    <div class="illustration-icon">
                        <i class="fas fa-shield-alt"></i>
                        <div class="icon-glow"></div>
                    </div>
                    <h3 class="illustration-title">Sécurité renforcée</h3>
                    <p class="illustration-text">
                        Choisissez un mot de passe fort avec au moins 6 caractères,
                        incluant des lettres, chiffres et symboles.
                    </p>

                    <div class="stats-preview">
                        <div class="stat-item">
                            <div class="stat-number">6+</div>
                            <div class="stat-label">Caractères min</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">🔤</div>
                            <div class="stat-label">Lettres</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">🔢</div>
                            <div class="stat-label">Chiffres</div>
                        </div>
                    </div>
                </div>

                <div class="floating-elements">
                    <div class="floating-element" style="--delay: 0s; --duration: 3s;"></div>
                    <div class="floating-element" style="--delay: 1s; --duration: 4s;"></div>
                    <div class="floating-element" style="--delay: 2s; --duration: 5s;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentTheme = localStorage.getItem('theme') || 'light';
        let isSubmitting = false;

        document.addEventListener('DOMContentLoaded', function () {
            initializeTheme();
            initializeForm();

            // Focus automatique
            document.getElementById('password').focus();
        });

        function initializeTheme() {
            document.documentElement.setAttribute('data-theme', currentTheme);
            updateThemeIcon();
        }

        function toggleTheme() {
            currentTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', currentTheme);
            localStorage.setItem('theme', currentTheme);
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const icon = document.getElementById('themeIcon');
            icon.className = currentTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }

        function initializeForm() {
            const form = document.getElementById('resetForm');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');

            passwordInput.addEventListener('input', function () {
                checkPasswordStrength(this.value);
                validateForm();
            });

            confirmPasswordInput.addEventListener('input', validateForm);
            form.addEventListener('submit', handleFormSubmit);
        }

        function checkPasswordStrength(password) {
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');

            let strength = 0;
            let text = '';
            let color = '';

            if (password.length >= 6) strength += 1;
            if (password.match(/[a-z]/)) strength += 1;
            if (password.match(/[A-Z]/)) strength += 1;
            if (password.match(/[0-9]/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 1;

            switch (strength) {
                case 0:
                case 1:
                    text = 'Très faible';
                    color = '#ef4444';
                    break;
                case 2:
                    text = 'Faible';
                    color = '#f59e0b';
                    break;
                case 3:
                    text = 'Moyen';
                    color = '#eab308';
                    break;
                case 4:
                    text = 'Fort';
                    color = '#22c55e';
                    break;
                case 5:
                    text = 'Très fort';
                    color = '#10b981';
                    break;
            }

            strengthFill.style.width = (strength * 20) + '%';
            strengthFill.style.backgroundColor = color;
            strengthText.textContent = text;
            strengthText.style.color = color;
        }

        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const updateBtn = document.getElementById('updateBtn');

            // Validation mot de passe
            const passwordValid = password.length >= 6
                && /[A-Za-z]/.test(password)
                && /[0-9]/.test(password);

            let errorMsg = 'Le mot de passe doit contenir :';
            if (password.length < 6) errorMsg += ' 6 chars min,';
            if (!/[A-Za-z]/.test(password)) errorMsg += ' lettres,';
            if (!/[0-9]/.test(password)) errorMsg += ' chiffres';
            if (passwordValid) errorMsg = '';

            updateFieldValidation('password', passwordValid, errorMsg.replace(/,$/, ''));

            // Validation confirmation
            const confirmValid = confirmPassword === password && confirmPassword.length > 0;
            updateFieldValidation('confirmPassword', confirmValid, 'Les mots de passe ne correspondent pas');

            const formValid = passwordValid && confirmValid;
            updateBtn.disabled = !formValid || isSubmitting;

            return formValid;
        }

        function updateFieldValidation(fieldName, isValid, errorMessage) {
            const input = document.getElementById(fieldName.replace('Password', '_password'));
            const errorDiv = document.getElementById(fieldName + 'Error');
            const container = input.closest('.input-container');

            if (input.value && !isValid) {
                container.classList.add('error');
                errorDiv.textContent = errorMessage;
                errorDiv.style.display = 'block';
            } else {
                container.classList.remove('error');
                errorDiv.style.display = 'none';
            }

            if (input.value && isValid) {
                container.classList.add('valid');
            } else {
                container.classList.remove('valid');
            }
        }

        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId === 'password' ? 'passwordToggleIcon' : 'confirmPasswordToggleIcon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        function handleFormSubmit(event) {
            if (isSubmitting) {
                event.preventDefault();
                return;
            }

            if (!validateForm()) {
                event.preventDefault();
                return;
            }

            isSubmitting = true;
            const updateBtn = document.getElementById('updateBtn');
            updateBtn.classList.add('loading');
            updateBtn.disabled = true;
        }

        window.toggleTheme = toggleTheme;
        window.togglePassword = togglePassword;
    </script>

    <style>
        .password-strength {
            margin-top: 0.5rem;
        }

        .strength-bar {
            width: 100%;
            height: 4px;
            background: var(--border-color);
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.5rem;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-text {
            font-size: 0.75rem;
            font-weight: 500;
            transition: color 0.3s ease;
        }
    </style>
</body>

</html>