<!DOCTYPE html>
<html lang="fr" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Plume Vision CMS</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>css/login-pro.css">
</head>

<body>
    <!-- Container principal -->
    <div class="login-container">

        <!-- Section gauche - Formulaire -->
        <div class="login-form-section">
            <!-- Header avec logo et toggle thème -->
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

                <!-- Toggle thème -->
                <button class="theme-toggle" onclick="toggleTheme()" title="Changer de thème">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>
            </div>

            <!-- Formulaire de connexion -->
            <div class="login-form-container">
                <div class="form-header">
                    <h2 class="form-title">Connexion</h2>
                    <p class="form-subtitle">Accédez à votre tableau de bord</p>
                </div>

                <!-- Messages d'erreur/succès -->
                <?php if (isset($expired) && $expired): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-clock"></i>
                        <span>Votre session a expiré. Veuillez vous reconnecter.</span>
                    </div>
                <?php endif; ?>

                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($success) && $success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?= htmlspecialchars($success) ?></span>
                    </div>
                <?php endif; ?>

                <!-- Formulaire -->
                <form method="POST" action="<?= defined('BASE_URL') ? BASE_URL : '/' ?>authenticate" class="login-form"
                    id="loginForm">
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

                    <!-- Champ Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">Adresse email</label>
                        <div class="input-container">
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" id="email" name="email" class="form-input" placeholder="votre@email.com"
                                required autocomplete="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        </div>
                        <div class="field-error" id="emailError"></div>
                    </div>

                    <!-- Champ Mot de passe -->
                    <div class="form-group">
                        <label for="password" class="form-label">Mot de passe</label>
                        <div class="input-container">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" id="password" name="password" class="form-input"
                                placeholder="••••••••" required autocomplete="current-password">
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                        <div class="field-error" id="passwordError"></div>
                    </div>

                    <!-- Options -->
                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember" id="remember">
                            <span class="checkmark"></span>
                            <span class="checkbox-text">Rester connecté</span>
                        </label>

                        <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>forgot-password" class="forgot-link">
                            Mot de passe oublié ?
                        </a>
                    </div>

                    <!-- Bouton de connexion -->
                    <button type="submit" class="login-btn" id="loginBtn" disabled>
                        <span class="btn-text">Se connecter</span>
                        <div class="btn-loader">
                            <div class="loader-spinner"></div>
                        </div>
                        <i class="fas fa-arrow-right btn-icon"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Section droite - Illustration -->
        <div class="login-illustration-section">
            <div class="illustration-container">
                <div class="illustration-content">
                    <div class="illustration-icon">
                        <i class="fas fa-tv"></i>
                        <div class="icon-glow"></div>
                    </div>
                    <h3 class="illustration-title">Gestion d'Affichage Numérique</h3>
                    <p class="illustration-text">
                        Contrôlez vos écrans, gérez vos contenus et programmez vos diffusions
                        depuis une interface moderne et intuitive.
                    </p>
                </div>

                <!-- Éléments décoratifs animés -->
                <div class="floating-elements">
                    <div class="floating-element" style="--delay: 0s; --duration: 3s;"></div>
                    <div class="floating-element" style="--delay: 1s; --duration: 4s;"></div>
                    <div class="floating-element" style="--delay: 2s; --duration: 5s;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // ===================================
        // CONFIGURATION ET VARIABLES
        // ===================================

        let currentTheme = localStorage.getItem('theme') || 'light';
        let isSubmitting = false;

        // ===================================
        // INITIALISATION
        // ===================================

        document.addEventListener('DOMContentLoaded', function () {
            initializeTheme();
            initializeForm();
            initializeAnimations();

            console.log('🚀 Page de login initialisée');
        });

        // ===================================
        // SYSTÈME DE THÈME
        // ===================================

        function initializeTheme() {
            document.documentElement.setAttribute('data-theme', currentTheme);
            updateThemeIcon();
        }

        function toggleTheme() {
            currentTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', currentTheme);
            localStorage.setItem('theme', currentTheme);
            updateThemeIcon();

            // Animation de transition
            document.body.style.transition = 'background-color 0.3s ease';
            setTimeout(() => {
                document.body.style.transition = '';
            }, 300);
        }

        function updateThemeIcon() {
            const icon = document.getElementById('themeIcon');
            icon.className = currentTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }

        // ===================================
        // GESTION DU FORMULAIRE
        // ===================================

        function initializeForm() {
            const form = document.getElementById('loginForm');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const loginBtn = document.getElementById('loginBtn');

            // Validation en temps réel
            emailInput.addEventListener('input', validateForm);
            passwordInput.addEventListener('input', validateForm);

            // Focus automatique
            if (!emailInput.value) {
                emailInput.focus();
            } else {
                passwordInput.focus();
            }

            // Soumission du formulaire
            form.addEventListener('submit', handleFormSubmit);

            // Validation initiale
            validateForm();
        }

        function validateForm() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');

            // Validation email
            const emailValid = validateEmail(email);
            updateFieldValidation('email', emailValid, 'Adresse email invalide');

            // Validation mot de passe
            const passwordValid = password.length >= 6;
            updateFieldValidation('password', passwordValid, 'Mot de passe trop court (min. 6 caractères)');

            // État du bouton
            const formValid = emailValid && passwordValid;
            loginBtn.disabled = !formValid || isSubmitting;

            return formValid;
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

            // Animation du bouton
            isSubmitting = true;
            const loginBtn = document.getElementById('loginBtn');
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;

            // Le formulaire se soumet normalement
        }

        // ===================================
        // FONCTIONNALITÉS INTERACTIVES
        // ===================================

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }

        function fillTestAccount(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;

            // Déclencher la validation
            validateForm();

            // Animation de remplissage
            const inputs = [document.getElementById('email'), document.getElementById('password')];
            inputs.forEach((input, index) => {
                setTimeout(() => {
                    input.classList.add('filled');
                    setTimeout(() => input.classList.remove('filled'), 300);
                }, index * 100);
            });
        }

        // ===================================
        // ANIMATIONS
        // ===================================

        function initializeAnimations() {
            // Animation du logo au chargement
            const logoIcon = document.querySelector('.logo-icon');
            setTimeout(() => {
                logoIcon.classList.add('animate');
            }, 500);

            // Animation des statistiques
            animateStats();

            // Animation des éléments flottants
            startFloatingAnimation();
        }

        function animateStats() {
            const statNumbers = document.querySelectorAll('.stat-number');

            statNumbers.forEach((stat, index) => {
                setTimeout(() => {
                    const finalValue = stat.textContent;
                    const isNumber = !isNaN(parseInt(finalValue));

                    if (isNumber) {
                        animateNumber(stat, 0, parseInt(finalValue), 1000);
                    }
                }, index * 200);
            });
        }

        function animateNumber(element, start, end, duration) {
            const startTime = performance.now();

            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                const current = Math.floor(start + (end - start) * easeOutQuart(progress));
                element.textContent = current + (element.textContent.includes('+') ? '+' : '');

                if (progress < 1) {
                    requestAnimationFrame(update);
                }
            }

            requestAnimationFrame(update);
        }

        function easeOutQuart(t) {
            return 1 - Math.pow(1 - t, 4);
        }

        function startFloatingAnimation() {
            const elements = document.querySelectorAll('.floating-element');

            elements.forEach(element => {
                element.style.animationDelay = element.style.getPropertyValue('--delay');
                element.style.animationDuration = element.style.getPropertyValue('--duration');
            });
        }

        // ===================================
        // GESTION DES ERREURS
        // ===================================

        window.addEventListener('error', function (event) {
            console.error('Erreur JavaScript:', event.error);
        });

        // ===================================
        // RESPONSIVE
        // ===================================

        window.addEventListener('resize', function () {
            // Ajustements responsive si nécessaire
        });

        // Fonctions globales
        window.toggleTheme = toggleTheme;
        window.togglePassword = togglePassword;
        window.fillTestAccount = fillTestAccount;

        console.log('✨ Login Pro chargé avec succès!');
    </script>
</body>

</html>