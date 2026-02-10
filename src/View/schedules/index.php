<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programmations - Plume Vision CMS</title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="<?= url('/css/global.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/dashboard.css') ?>">

    <style>
        /* Variables et Styles Modernes (Copie conforme Playlists/Contents) */
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

        /* Éléments spécifiques Schedules */
        .day-badge {
            display: inline-block;
            width: 20px;
            height: 20px;
            line-height: 20px;
            text-align: center;
            border-radius: 50%;
            font-size: 0.7em;
            margin-right: 2px;
            background: #e2e8f0;
            color: #64748b;
        }

        .day-badge.active {
            background: var(--primary-color);
            color: white;
            font-weight: bold;
        }

        .priority-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
            width: 100px;
            margin-top: 4px;
        }

        .priority-value {
            height: 100%;
            background: linear-gradient(90deg, var(--success-color), var(--warning-color), var(--danger-color));
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
            max-width: 700px;
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
        .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
        }

        .days-selector {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .day-check {
            display: none;
        }

        .day-label {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.875rem;
            color: var(--text-secondary);
            transition: all 0.2s;
            user-select: none;
        }

        .day-check:checked+.day-label {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
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
                        <h1 class="header-title">Programmations</h1>
                        <p class="header-subtitle">Planifiez la diffusion de vos contenus</p>
                    </div>
                    <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'editor'])): ?>
                        <div class="header-actions">
                            <a href="<?= url('/') ?>" class="btn btn-ghost"><i class="fas fa-arrow-left"></i> Retour
                                Dashboard</a>
                            <button onclick="openCreateModal()" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle
                                Programmation</button>
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
                    <?php if (!empty($schedules)): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Contenu / Zone</th>
                                    <th>Période</th>
                                    <th>Heures</th>
                                    <th>Jours</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $sched): ?>
                                    <?php
                                    $dayString = $sched['day_of_week'] ?? '';
                                    $days = [];
                                    // Handle legacy comma-separated keys and new numeric string
                                    if (!empty($dayString)) {
                                        if (preg_match('/^\d+$/', $dayString)) {
                                            $numMap = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
                                            foreach (str_split($dayString) as $d) {
                                                if (isset($numMap[$d]))
                                                    $days[] = $numMap[$d];
                                            }
                                        } else {
                                            $days = explode(',', $dayString);
                                        }
                                    }
                                    $isActive = (int) ($sched['is_active'] ?? 0);
                                    $allDays = ['Monday' => 'L', 'Tuesday' => 'M', 'Wednesday' => 'M', 'Thursday' => 'J', 'Friday' => 'V', 'Saturday' => 'S', 'Sunday' => 'D'];
                                    $playlistName = $sched['playlist_name'] ?? null;
                                    $contentTitle = $sched['content_title'] ?? null;

                                    $displayTitle = 'Inconnu';
                                    $icon = 'fa-question';

                                    if ($playlistName) {
                                        $displayTitle = 'Playlist: ' . $playlistName;
                                        $icon = 'fa-layer-group';
                                    } elseif ($contentTitle) {
                                        $displayTitle = $contentTitle;
                                        $icon = 'fa-file-video';
                                    }

                                    $zoneName = $sched['zone_name'] ?? 'Zone inconnue';
                                    $startTime = $sched['start_time'] ?? '00:00:00';
                                    $endTime = $sched['end_time'] ?? '00:00:00';
                                    $startDate = $sched['start_date'] ?? date('Y-m-d');
                                    $endDate = $sched['end_date'] ?? date('Y-m-d');
                                    ?>
                                    <tr style="opacity: <?= $isActive ? '1' : '0.6' ?>;">
                                        <td>
                                            <div
                                                style="font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 8px;">
                                                <i class="fas <?= $icon ?>" style="color: #64748b; font-size: 0.9em;"></i>
                                                <?= htmlspecialchars($displayTitle) ?>
                                            </div>
                                            <div
                                                style="font-size: 0.85em; color: var(--text-secondary); margin-top:2px; margin-left: 24px;">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <?= htmlspecialchars($zoneName) ?>
                                            </div>
                                        </td>
                                        <td style="font-size: 0.9em; white-space: nowrap;">
                                            Du
                                            <?= date('d/m', strtotime($startDate)) ?><br>
                                            Au
                                            <?= date('d/m/Y', strtotime($endDate)) ?>
                                        </td>
                                        <td style="font-size: 0.9em; font-family: monospace;">
                                            <?= substr($startTime, 0, 5) ?> -
                                            <?= substr($endTime, 0, 5) ?>
                                        </td>
                                        <td>
                                            <div style="display:flex;">
                                                <?php foreach ($allDays as $en => $fr): ?>
                                                    <span class="day-badge <?= in_array($en, $days) ? 'active' : '' ?>">
                                                        <?= $fr ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: bold; font-size: 0.9em;">
                                                <?= $sched['priority'] ?>%
                                            </div>
                                            <div class="priority-bar">
                                                <div class="priority-value" style="width: <?= $sched['priority'] ?>%"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($isActive): ?>
                                                <span
                                                    style="padding: 4px 8px; background: #dcfce7; color: #166534; border-radius: 6px; font-size: 0.8em; font-weight: 600;">Active</span>
                                            <?php else: ?>
                                                <span
                                                    style="padding: 4px 8px; background: #fee; color: #991b1b; border-radius: 6px; font-size: 0.8em; font-weight: 600;">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (in_array($_SESSION['user_role'] ?? '', ['admin', 'editor'])): ?>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-edit"
                                                        onclick='editSchedule(<?= htmlspecialchars(json_encode($sched), ENT_QUOTES, 'UTF-8') ?>)'><i
                                                            class="fas fa-edit"></i></button>
                                                    <button class="btn-action btn-delete"
                                                        onclick="confirmDelete(<?= $sched['id'] ?>)"><i
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
                            <div class="empty-icon"><i class="fas fa-calendar-alt"></i></div>
                            <h3>Aucune programmation</h3>
                            <p>Commencez à planifier vos contenus.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div id="scheduleModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Nouvelle Programmation</h3>
                <button onclick="closeModal()" class="modal-close"
                    style="background:none; border:none; font-size:1.5rem; cursor:pointer;">&times;</button>
            </div>
            <form id="scheduleForm" method="POST" action="<?= url('/schedules/store') ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <input type="hidden" name="schedule_id" id="scheduleId">

                <div class="form-group">
                    <label class="form-label">Type de programmation</label>
                    <div style="display:flex; gap: 1rem;">
                        <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="sched_type" value="content" checked onchange="toggleSchedType()">
                            Contenu unique
                        </label>
                        <label style="cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="sched_type" value="playlist" onchange="toggleSchedType()">
                            Playlist
                        </label>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group" id="groupContent">
                        <label class="form-label">Contenu *</label>
                        <select name="content_id" id="schedContent" class="form-select">
                            <option value="">Choisir un contenu...</option>
                            <?php foreach ($contents as $c): ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= htmlspecialchars($c['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" id="groupPlaylist" style="display:none;">
                        <label class="form-label">Playlist *</label>
                        <select name="playlist_id" id="schedPlaylist" class="form-select">
                            <option value="">Choisir une playlist...</option>
                            <?php foreach ($playlists as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['name']) ?> (Zone: <?= $p['zone_id'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Zone Cible *</label>
                        <select name="zone_id" id="schedZone" class="form-select" required>
                            <option value="">Choisir une zone...</option>
                            <?php foreach ($zones as $z): ?>
                                <option value="<?= $z['id'] ?>">
                                    <?= htmlspecialchars($z['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Date Début *</label>
                        <input type="date" name="start_date" id="schedStartDate" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date Fin *</label>
                        <input type="date" name="end_date" id="schedEndDate" class="form-input" required>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Heure Début *</label>
                        <input type="time" name="start_time" id="schedStartTime" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Heure Fin *</label>
                        <input type="time" name="end_time" id="schedEndTime" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Jours de diffusion *</label>
                    <div style="margin-bottom: 0.5rem;">
                        <button type="button" onclick="selectAllDays()" class="btn btn-ghost"
                            style="padding: 0.25rem 0.5rem; font-size: 0.8em;">
                            Tout sélectionner
                        </button>
                    </div>
                    <div class="days-selector">
                        <?php
                        $daysMap = ['Monday' => 'Lun', 'Tuesday' => 'Mar', 'Wednesday' => 'Mer', 'Thursday' => 'Jeu', 'Friday' => 'Ven', 'Saturday' => 'Sam', 'Sunday' => 'Dim'];
                        foreach ($daysMap as $en => $fr):
                            ?>
                            <div>
                                <input type="checkbox" name="days[]" value="<?= $en ?>" id="day_<?= $en ?>"
                                    class="day-check">
                                <label for="day_<?= $en ?>" class="day-label">
                                    <?= $fr ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Priorité (0-100)</label>
                    <input type="range" name="priority" id="schedPriority" min="0" max="100" value="50"
                        style="width:100%">
                    <div style="text-align:center; font-weight:bold; color:var(--primary-color);" id="priorityVal">50
                    </div>
                </div>

                <div class="form-group">
                    <label style="display:flex; align-items:center; gap:8px;">
                        <input type="checkbox" name="is_active" id="schedActive" checked>
                        <span>Programmation active</span>
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

        // Toggle Type
        function toggleSchedType() {
            const type = document.querySelector('input[name="sched_type"]:checked').value;
            const groupContent = document.getElementById('groupContent');
            const groupPlaylist = document.getElementById('groupPlaylist');
            const selectContent = document.getElementById('schedContent');
            const selectPlaylist = document.getElementById('schedPlaylist');

            if (type === 'content') {
                groupContent.style.display = 'block';
                groupPlaylist.style.display = 'none';
                selectContent.required = true;
                selectPlaylist.required = false;
                selectPlaylist.value = '';
            } else {
                groupContent.style.display = 'none';
                groupPlaylist.style.display = 'block';
                selectContent.required = false;
                selectPlaylist.required = true;
                selectContent.value = '';
            }
        }

        // Update priority value display
        document.getElementById('schedPriority').addEventListener('input', function (e) {
            document.getElementById('priorityVal').textContent = e.target.value;
        });

        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Nouvelle Programmation';
            document.getElementById('scheduleForm').action = `${BASE_URL}schedules/store`;
            document.getElementById('scheduleId').value = '';

            // Default to content
            document.querySelector('input[name="sched_type"][value="content"]').checked = true;
            toggleSchedType();

            document.getElementById('schedContent').value = '';
            document.getElementById('schedPlaylist').value = '';

            document.getElementById('schedZone').value = '';
            document.getElementById('schedStartDate').value = '';
            document.getElementById('schedEndDate').value = '';
            document.getElementById('schedStartTime').value = '08:00';
            document.getElementById('schedEndTime').value = '18:00';
            document.getElementById('schedPriority').value = 50;
            document.getElementById('priorityVal').textContent = '50';
            document.getElementById('schedActive').checked = true;

            // Check all days by default
            document.querySelectorAll('.day-check').forEach(c => c.checked = true);

            document.getElementById('scheduleModal').classList.add('active');
        }

        function editSchedule(data) {
            document.getElementById('modalTitle').textContent = 'Modifier la Programmation';
            document.getElementById('scheduleForm').action = `${BASE_URL}schedules/update`;
            document.getElementById('scheduleId').value = data.id;

            if (data.playlist_id) {
                document.querySelector('input[name="sched_type"][value="playlist"]').checked = true;
                document.getElementById('schedPlaylist').value = data.playlist_id;
            } else {
                document.querySelector('input[name="sched_type"][value="content"]').checked = true;
                document.getElementById('schedContent').value = data.content_id;
            }
            toggleSchedType();

            document.getElementById('schedZone').value = data.zone_id;
            document.getElementById('schedStartDate').value = data.start_date;
            document.getElementById('schedEndDate').value = data.end_date;
            document.getElementById('schedStartTime').value = data.start_time;
            document.getElementById('schedEndTime').value = data.end_time;
            document.getElementById('schedPriority').value = data.priority;
            document.getElementById('priorityVal').textContent = data.priority;
            document.getElementById('schedActive').checked = data.is_active == 1;

            // Handle days
            let days = [];
            const rawDays = data.day_of_week || '';

            // Check if numeric string "12345" or legacy "Monday,Tuesday"
            if (/^\d+$/.test(rawDays)) {
                const numMap = { '1': 'Monday', '2': 'Tuesday', '3': 'Wednesday', '4': 'Thursday', '5': 'Friday', '6': 'Saturday', '7': 'Sunday' };
                days = rawDays.split('').map(d => numMap[d]);
            } else {
                days = rawDays.split(',');
            }

            document.querySelectorAll('.day-check').forEach(c => {
                c.checked = days.includes(c.value);
            });

            document.getElementById('scheduleModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('scheduleModal').classList.remove('active');
        }

        function confirmDelete(id) {
            if (confirm('Supprimer cette programmation ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `${BASE_URL}schedules/delete`;
                form.innerHTML = `
                    <input type="hidden" name="schedule_id" value="${id}">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Auto remove toasts
        setTimeout(() => document.querySelectorAll('.toast').forEach(t => t.remove()), 5000);

        function selectAllDays() {
            const checkboxes = document.querySelectorAll('.day-check');
            // Check if ALL are currently checked
            const allAreChecked = Array.from(checkboxes).every(c => c.checked);

            // Toggle: If all checked -> uncheck all. Else -> check all.
            const newState = !allAreChecked;

            checkboxes.forEach(c => {
                c.checked = newState;
            });
            console.log('Select All Days:', newState ? 'Checked All' : 'Unchecked All');
        }
    </script>
</body>

</html>