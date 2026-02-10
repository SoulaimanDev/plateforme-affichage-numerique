<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Plume Vision CMS</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= url('/css/global.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/dashboard.css') ?>">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>

<body>
    <!-- Layout principal -->
    <div class="dashboard-layout">

        <!-- Sidebar Navigation -->
        <aside class="sidebar" id="sidebar">
            <!-- Header Sidebar -->
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="sidebar-logo-icon">
                        <i class="fas fa-tv"></i>
                    </div>
                    <div class="sidebar-logo-text">Plume Vision</div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Principal</div>
                    <div class="nav-item">
                        <a href="<?= url('/') ?>" class="nav-link active">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Gestion</div>

                    <?php if (in_array($user['role'], ['admin', 'editor'])): ?>
                        <div class="nav-item">
                            <a href="<?= url('/contents') ?>" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <span>Contenus</span>
                            </a>
                        </div>

                        <div class="nav-item">
                            <a href="<?= url('/screens') ?>" class="nav-link">
                                <i class="nav-icon fas fa-desktop"></i>
                                <span>Écrans</span>
                            </a>
                        </div>

                        <div class="nav-item">
                            <a href="<?= url('/zones') ?>" class="nav-link">
                                <i class="nav-icon fas fa-map-marker-alt"></i>
                                <span>Zones</span>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="nav-item">
                        <a href="<?= url('/playlists') ?>" class="nav-link">
                            <i class="nav-icon fas fa-list"></i>
                            <span>Playlists</span>
                        </a>
                    </div>

                    <?php if (in_array($user['role'], ['admin', 'editor'])): ?>
                        <div class="nav-item">
                            <a href="<?= url('/schedules') ?>" class="nav-link">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <span>Programmations</span>
                            </a>
                        </div>

                        <div class="nav-item">
                            <a href="<?= url('/audiences') ?>" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <span>Audiences</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($user['role'] === 'admin'): ?>
                    <div class="nav-section">
                        <div class="nav-section-title">Administration</div>
                        <div class="nav-item">
                            <a href="<?= url('/users') ?>" class="nav-link">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <span>Utilisateurs</span>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Contenu principal -->
        <main class="main-content">
            <!-- Header -->
            <header class="main-header">
                <div class="header-left">
                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="header-title">Dashboard</h1>
                </div>

                <div class="header-right">
                    <!-- Indicateur de connexion -->
                    <div class="connection-status">
                        <div class="connection-dot" id="connectionDot"></div>
                        <span id="connectionStatus">Connecté</span>
                    </div>

                    <!-- Toggle thème -->
                    <button class="theme-toggle" onclick="toggleTheme()" title="Changer de thème">
                        <i class="fas fa-moon" id="themeIcon"></i>
                    </button>

                    <!-- Notifications -->
                    <div class="notification-bell" onclick="toggleNotifications()">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>

                        <!-- Dropdown notifications -->
                        <div class="notification-dropdown" id="notificationDropdown">
                            <div class="notification-header">
                                <h4>Notifications</h4>
                                <button onclick="markAllNotificationsRead()"
                                    style="background: none; border: none; color: var(--primary-color); cursor: pointer;">Tout
                                    marquer comme lu</button>
                            </div>
                            <div id="notificationList">
                                <!-- Contenu dynamique -->
                            </div>
                        </div>
                    </div>
                    <div class="user-menu">
                        <button class="user-menu-btn" onclick="toggleUserMenu()">
                            <div class="user-avatar">
                                <?= strtoupper(substr($user['email'], 0, 2)) ?>
                            </div>
                            <div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                    <?= htmlspecialchars(ucfirst($user['role'])) ?>
                                </div>
                            </div>
                            <i class="fas fa-chevron-down"
                                style="font-size: 0.75rem; color: var(--text-secondary);"></i>
                        </button>

                        <!-- Menu déroulant utilisateur -->
                        <div class="user-dropdown" id="userDropdown"
                            style="display: none; position: absolute; top: 100%; right: 0; background: white; border: 1px solid var(--border-color); border-radius: 8px; box-shadow: var(--shadow-lg); min-width: 200px; z-index: 1000;">
                            <a href="<?= url('/logout') ?>"
                                style="display: block; padding: 0.75rem 1rem; color: var(--danger-color); text-decoration: none; border-top: 1px solid var(--border-color);">
                                <i class="fas fa-sign-out-alt" style="margin-right: 0.5rem;"></i>
                                Déconnexion
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Contenu Dashboard -->
            <div class="dashboard-content">
                <!-- Message de bienvenue -->
                <div class="dashboard-welcome">
                    <?php
                    $displayName = $user['email'];
                    if (!empty($user['firstname']) && !empty($user['lastname'])) {
                        $displayName = $user['firstname'] . ' ' . $user['lastname'];
                    } elseif (!empty($user['firstname'])) {
                        $displayName = $user['firstname'];
                    }
                    ?>
                    <h2 class="welcome-title">Bienvenue, <?= htmlspecialchars($displayName) ?></h2>
                    <p class="welcome-subtitle">Voici un aperçu de votre plateforme d'affichage numérique</p>
                </div>

                <!-- Cartes de statistiques -->
                <div class="stats-grid">
                    <!-- Contenus -->
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Contenus</div>
                            <div class="stat-card-icon primary">
                                <i class="fas fa-file-alt"></i>
                            </div>
                        </div>
                        <div class="stat-card-value" id="totalContents">0</div>
                        <div class="stat-card-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+12% ce mois</span>
                        </div>
                    </div>

                    <!-- Écrans actifs -->
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Écrans actifs</div>
                            <div class="stat-card-icon success">
                                <i class="fas fa-desktop"></i>
                            </div>
                        </div>
                        <div class="stat-card-value" id="activeScreens">0</div>
                        <div class="stat-card-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+3 cette semaine</span>
                        </div>
                    </div>

                    <!-- Utilisateurs -->
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Utilisateurs</div>
                            <div class="stat-card-icon info">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="stat-card-value" id="totalUsers">0</div>
                        <div class="stat-card-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+2 nouveaux</span>
                        </div>
                    </div>

                    <!-- Zones -->
                    <div class="stat-card">
                        <div class="stat-card-header">
                            <div class="stat-card-title">Zones</div>
                            <div class="stat-card-icon warning">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                        </div>
                        <div class="stat-card-value" id="totalZones">0</div>
                        <div class="stat-card-change positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>Stable</span>
                        </div>
                    </div>
                </div>

                <!-- Graphiques -->
                <div class="charts-grid">
                    <!-- Graphique des diffusions -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Diffusions par jour</h3>
                            <select id="chartPeriod"
                                style="padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;">
                                <option value="7">7 derniers jours</option>
                                <option value="30">30 derniers jours</option>
                            </select>
                        </div>
                        <div class="chart-container">
                            <canvas id="diffusionsChart"></canvas>
                        </div>
                    </div>

                    <!-- Répartition par zones -->
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3 class="chart-title">Répartition par zones</h3>
                        </div>
                        <div class="chart-container">
                            <canvas id="zonesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Activité temps réel -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3 class="chart-title">Activité temps réel</h3>
                        <div class="export-buttons">
                            <button class="export-btn csv" onclick="exportStats('csv')">
                                <i class="fas fa-file-csv"></i> CSV
                            </button>
                            <button class="export-btn pdf" onclick="exportStats('pdf')">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                        </div>
                    </div>
                    <div style="padding: 1rem 0; max-height: 400px; overflow-y: auto;">
                        <div id="recentActivity">
                            <!-- Contenu dynamique injecté par JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Overlay mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Scripts -->
    <script>
        // ===================================
        // DASHBOARD JAVASCRIPT
        // ===================================

        // URL de base pour l'API (générée par PHP)
        const BASE_URL = "<?= url('/') ?>";

        // Variables globales
        let diffusionsChart, zonesChart;
        let notificationInterval, activityInterval;
        let currentTheme = localStorage.getItem('theme') || 'light';

        // Initialisation au chargement
        document.addEventListener('DOMContentLoaded', function () {
            initializeTheme();
            initializeDashboard();
            initializeCharts();
            setupEventListeners();
            startRealTimeUpdates();
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
        }

        function updateThemeIcon() {
            const icon = document.getElementById('themeIcon');
            icon.className = currentTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
        }

        // ===================================
        // SYSTÈME DE NOTIFICATIONS
        // ===================================

        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';

            if (dropdown.style.display === 'block') {
                loadNotifications();
            }
        }

        async function loadNotifications() {
            try {
                const response = await fetch(`${BASE_URL}api/notifications`);
                const data = await response.json();

                updateNotificationBadge(data.count);
                renderNotifications(data.notifications);
            } catch (error) {
                console.error('Erreur lors du chargement des notifications:', error);
            }
        }

        function updateNotificationBadge(count) {
            const badge = document.getElementById('notificationBadge');
            const bell = document.querySelector('.notification-bell');

            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
                bell.classList.add('has-new');
            } else {
                badge.style.display = 'none';
                bell.classList.remove('has-new');
            }
        }

        function renderNotifications(notifications) {
            const list = document.getElementById('notificationList');

            if (notifications.length === 0) {
                list.innerHTML = '<div style="padding: 2rem; text-align: center; color: var(--text-secondary);">Aucune notification</div>';
                return;
            }

            list.innerHTML = notifications.map(notification => `
                <div class="notification-item ${notification.is_read ? '' : 'unread'}" onclick="markNotificationRead(${notification.id})">
                    <div style="font-weight: 500; margin-bottom: 0.25rem;">${notification.title}</div>
                    <div style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 0.5rem;">${notification.message}</div>
                    <div style="font-size: 0.75rem; color: var(--text-light);">${formatTime(notification.created_at)}</div>
                </div>
            `).join('');
        }

        async function markNotificationRead(id) {
            try {
                await fetch(`${BASE_URL}api/notifications/read`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                loadNotifications();
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        async function markAllNotificationsRead() {
            try {
                await fetch(`${BASE_URL}api/notifications/read-all`, {
                    method: 'POST'
                });
                loadNotifications();
            } catch (error) {
                console.error('Erreur:', error);
            }
        }

        // ===================================
        // ACTIVITÉ TEMPS RÉEL
        // ===================================

        function startRealTimeUpdates() {
            // Charger l'activité initiale
            loadActivity();
            loadNotifications();

            // Polling toutes les 30 secondes
            activityInterval = setInterval(loadActivity, 30000);
            notificationInterval = setInterval(loadNotifications, 60000);

            // Indicateur de connexion
            updateConnectionStatus(true);
        }

        async function loadActivity() {
            try {
                const response = await fetch(`${BASE_URL}api/activity`);
                const data = await response.json();
                renderActivity(data.activities);
                updateConnectionStatus(true);
            } catch (error) {
                console.error('Erreur lors du chargement de l\'activité:', error);
                updateConnectionStatus(false);
            }
        }

        function renderActivity(activities) {
            const container = document.getElementById('recentActivity');

            if (activities.length === 0) {
                container.innerHTML = '<div style="padding: 2rem; text-align: center; color: var(--text-secondary);">Aucune activité récente</div>';
                return;
            }

            container.innerHTML = activities.map(activity => `
                <div class="activity-item">
                    <div class="activity-icon ${activity.color}">
                        <i class="${activity.icon}"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-message">${activity.message}</div>
                        <div class="activity-meta">${activity.user} • ${activity.time}</div>
                    </div>
                </div>
            `).join('');
        }

        function updateConnectionStatus(connected) {
            const dot = document.getElementById('connectionDot');
            const status = document.getElementById('connectionStatus');

            if (connected) {
                dot.classList.remove('disconnected');
                status.textContent = 'Connecté';
            } else {
                dot.classList.add('disconnected');
                status.textContent = 'Déconnecté';
            }
        }

        // ===================================
        // EXPORT DES DONNÉES
        // ===================================

        function exportStats(format) {
            const url = `${BASE_URL}api/export?format=${format}`;

            if (format === 'csv') {
                // Téléchargement direct pour CSV
                window.location.href = url;
            } else if (format === 'pdf') {
                // Pour PDF, on pourrait utiliser jsPDF
                generatePDF();
            }
        }

        function generatePDF() {
            // Simulation de génération PDF
            alert('Fonctionnalité PDF en cours de développement');
        }

        // ===================================
        // UTILITAIRES
        // ===================================

        function formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;

            if (diff < 60000) return 'À l\'instant';
            if (diff < 3600000) return `${Math.floor(diff / 60000)}m`;
            if (diff < 86400000) return `${Math.floor(diff / 3600000)}h`;
            return date.toLocaleDateString();
        }

        // Fermer les dropdowns en cliquant ailleurs
        document.addEventListener('click', function (event) {
            const notificationBell = document.querySelector('.notification-bell');
            const notificationDropdown = document.getElementById('notificationDropdown');
            const userMenu = document.querySelector('.user-menu');
            const userDropdown = document.getElementById('userDropdown');

            if (!notificationBell.contains(event.target)) {
                notificationDropdown.style.display = 'none';
            }

            if (!userMenu.contains(event.target)) {
                userDropdown.style.display = 'none';
            }
        });

        // Nettoyage lors de la fermeture
        window.addEventListener('beforeunload', function () {
            if (activityInterval) clearInterval(activityInterval);
            if (notificationInterval) clearInterval(notificationInterval);
        });

        // Données dynamiques injectées par PHP
        const dashboardData = {
            stats: {
                totalContents: <?= $stats['contents'] ?? 0 ?>,
                activeScreens: <?= $stats['screens'] ?? 0 ?>,
                totalUsers: <?= $stats['users'] ?? 0 ?>,
                totalZones: <?= $stats['zones'] ?? 0 ?>
            },
            charts: {
                diffusions: <?= json_encode($chartData['diffusions'] ?? []) ?>,
                zones: <?= json_encode($chartData['zones'] ?? []) ?>
            }
        };

        console.log('Dashboard Data:', dashboardData);



        // Initialisation du dashboard
        function initializeDashboard() {
            // Mise à jour des statistiques
            updateStats();

            // Animation des compteurs
            animateCounters();
        }

        // Mise à jour des statistiques
        function updateStats() {
            document.getElementById('totalContents').textContent = dashboardData.stats.totalContents;
            document.getElementById('activeScreens').textContent = dashboardData.stats.activeScreens;
            document.getElementById('totalUsers').textContent = dashboardData.stats.totalUsers;
            document.getElementById('totalZones').textContent = dashboardData.stats.totalZones;
        }

        // Animation des compteurs
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-card-value');

            counters.forEach(counter => {
                const target = parseInt(counter.textContent);
                let current = 0;
                const increment = target / 50;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target;
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 30);
            });
        }

        // Initialisation des graphiques
        function initializeCharts() {
            // Graphique des diffusions
            const diffusionsCtx = document.getElementById('diffusionsChart').getContext('2d');
            diffusionsChart = new Chart(diffusionsCtx, {
                type: 'line',
                data: {
                    labels: dashboardData.charts.diffusionLabels || ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                    datasets: [{
                        label: 'Diffusions',
                        data: dashboardData.charts.diffusions.length ? dashboardData.charts.diffusions : [12, 19, 8, 15, 22, 18, 25],
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Graphique des zones
            const zonesCtx = document.getElementById('zonesChart').getContext('2d');
            zonesChart = new Chart(zonesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Entrée', 'Couloir', 'Cafeteria', 'Cour'],
                    datasets: [{
                        data: dashboardData.charts.zones.length ? dashboardData.charts.zones : [30, 25, 25, 20],
                        backgroundColor: [
                            '#2563eb',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        // Configuration des événements
        function setupEventListeners() {
            // Menu mobile
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            mobileMenuBtn?.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            });

            overlay?.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });

            // Période du graphique
            document.getElementById('chartPeriod')?.addEventListener('change', async function () {
                const period = this.value;
                try {
                    const response = await fetch(`${BASE_URL}api/stats/diffusions?period=${period}`);
                    const data = await response.json();

                    if (diffusionsChart) {
                        diffusionsChart.data.labels = data.labels;
                        diffusionsChart.data.datasets[0].data = data.data;
                        diffusionsChart.update();
                    }
                } catch (error) {
                    console.error('Erreur lors de la mise à jour du graphique:', error);
                }
            });
        }

        // Menu utilisateur
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }

        // Fermer le menu utilisateur en cliquant ailleurs
        document.addEventListener('click', function (event) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');

            if (!userMenu.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });

        // Fonction pour mettre à jour les données (appelable depuis PHP)
        function updateDashboardData(newData) {
            dashboardData.stats = { ...dashboardData.stats, ...newData.stats };
            updateStats();

            if (newData.charts) {
                // Mise à jour des graphiques
                if (newData.charts.diffusions && diffusionsChart) {
                    diffusionsChart.data.datasets[0].data = newData.charts.diffusions;
                    diffusionsChart.update();
                }

                if (newData.charts.zones && zonesChart) {
                    zonesChart.data.datasets[0].data = newData.charts.zones;
                    zonesChart.update();
                }
            }
        }
    </script>
</body>

</html>