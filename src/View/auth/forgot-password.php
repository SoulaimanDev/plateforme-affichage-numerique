<!DOCTYPE html>
<html lang="fr" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Plume Vision CMS</title>

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
                    <h2 class="form-title">Mot de passe oublié ?</h2>
                    <p class="form-subtitle">Entrez votre email pour recevoir un lien de réinitialisation</p>
                </div>

                <!-- Messages -->
                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($success) && $success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div><?= $success ?></div>
                    </div>
                <?php endif; ?>

                <?php if (isset($internal_link) && $internal_link): ?>
                    <div class="alert alert-warning" style="word-break: break-all;">
                        <i class="fas fa-link"></i>
                        <strong>Lien (Interne) :</strong><br>
                        <a href="<?= $internal_link ?>"
                            style="color: inherit; text-decoration: underline;"><?= $internal_link ?></a>
                    </div>
                <?php endif; ?>

                <!-- Formulaire -->
                <form method="POST" action="<?= url('/send-reset') ?>" class="login-form" id="forgotForm">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                    <div class="form-group">
                        <label for="email" class="form-label">Adresse email</label>
                        <div class="input-container">
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="text" id="email" name="email" class="form-input"
                                placeholder="Entrez votre email" required>
                        </div>
                        <div class="field-error" id="emailError"></div>
                    </div>



                    <div class="form-actions">
                        <button type="submit" class="login-btn">
                            <i class="fas fa-paper-plane"></i> Envoyer le lien
                        </button>
                    </div>

                    <a href="<?= url('/login') ?>" class="forgot-link">
                        <i class="fas fa-arrow-left"></i> Retour à la connexion
                    </a>
                </form>
            </div>
        </div>

        <!-- Section illustration -->
        <div class="login-illustration-section">
            <div class="illustration-container">
                <div class="illustration-content">
                    <div class="illustration-icon">
                        <i class="fas fa-key"></i>
                        <div class="icon-glow"></div>
                    </div>
                    <h3 class="illustration-title">Récupération sécurisée</h3>
                    <p class="illustration-text">
                        Nous vous enverrons un lien sécurisé pour réinitialiser votre mot de passe.
                        Le lien expire automatiquement après 1 heure.
                    </p>

                    <div class="stats-preview">
                        <div class="stat-item">
                            <div class="stat-number">🔒</div>
                            <div class="stat-label">Sécurisé</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">⚡</div>
                            <div class="stat-label">Rapide</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">✅</div>
                            <div class="stat-label">Fiable</div>
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
            document.getElementById('email').focus();
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
            const form = document.getElementById('forgotForm');
            const emailInput = document.getElementById('email');
            const resetBtn = document.getElementById('resetBtn');

            emailInput.addEventListener('input', validateForm);
            form.addEventListener('submit', handleFormSubmit);
        }

        function validateForm() {
            const email = document.getElementById('email').value;
            const resetBtn = document.getElementById('resetBtn');

            const emailValid = validateEmail(email);
            updateFieldValidation('email', emailValid, 'Adresse email invalide');

            resetBtn.disabled = !emailValid || isSubmitting;
            return emailValid;
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function updateFieldValidation(fieldName, isValid, errorMessage) {
            const input = document.getElementById(fieldName);
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
            const resetBtn = document.getElementById('resetBtn');
            resetBtn.classList.add('loading');
            resetBtn.disabled = true;
        }

        window.toggleTheme = toggleTheme;
    </script>
</body>

</html>