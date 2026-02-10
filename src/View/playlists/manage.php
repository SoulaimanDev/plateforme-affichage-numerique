<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer le contenu -
        <?= htmlspecialchars($playlist['name']) ?>
    </title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="<?= url('/css/global.css') ?>">
    <link rel="stylesheet" href="<?= url('/css/dashboard.css') ?>">
    <style>
        /* Styles inline simplifiés pour gagner du temps */
        .page-header {
            background: white;
            padding: 2rem;
            border-bottom: 1px solid #e2e8f0;
            margin-bottom: 2rem;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            padding: 0 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        .card-header {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
        }

        .list-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            gap: 1rem;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .item-order {
            background: #e2e8f0;
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-weight: bold;
        }

        .item-info {
            flex: 1;
        }

        .item-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-add {
            background: #10b981;
            color: white;
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="page-header">
        <div
            style="max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <a href="<?= url('/playlists') ?>"
                    style="color: #64748b; text-decoration: none; margin-bottom: 0.5rem; display: block;"><i
                        class="fas fa-arrow-left"></i> Retour aux playlists</a>
                <h1 style="margin: 0; font-size: 1.8rem;">Gérer :
                    <?= htmlspecialchars($playlist['name']) ?>
                </h1>
            </div>
            <div style="text-align: right;">
                <span
                    style="background: #e0f2fe; color: #0284c7; padding: 4px 8px; border-radius: 4px; font-size: 0.9rem;">
                    <i class="fas fa-map-marker-alt"></i> Zone ID:
                    <?= $playlist['zone_id'] ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Feedback -->
    <?php if (isset($success) && $success): ?>
        <div
            style="background: #dcfce7; color: #166534; padding: 1rem; margin: 0 2rem 1rem; border-radius: 8px; max-width: 1400px; margin-left: auto; margin-right: auto;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="content-grid">
        <!-- Liste des contenus actuels -->
        <div class="card">
            <div class="card-header">
                <span>Contenus de la playlist (
                    <?= count($items) ?>)
                </span>
            </div>
            <?php if (empty($items)): ?>
                <div style="padding: 3rem; text-align: center; color: #94a3b8;">
                    <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <p>Cette playlist est vide.</p>
                </div>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                    <div class="list-item">
                        <div class="item-order">
                            <?= $item['display_order'] ?>
                        </div>
                        <div
                            style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 4px; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                            <?php if ($item['content_type'] === 'image'): ?>
                                <i class="fas fa-image" style="color: #64748b;"></i>
                            <?php elseif ($item['content_type'] === 'video'): ?>
                                <i class="fas fa-video" style="color: #64748b;"></i>
                            <?php else: ?>
                                <i class="fas fa-font" style="color: #64748b;"></i>
                            <?php endif; ?>
                        </div>
                        <div class="item-info">
                            <div style="font-weight: 600;">
                                <?= htmlspecialchars($item['title']) ?>
                            </div>
                            <div style="font-size: 0.85rem; color: #64748b;">
                                Durée:
                                <?= $item['override_duration'] ?>s | Type:
                                <?= ucfirst($item['content_type']) ?>
                            </div>
                        </div>
                        <div class="item-actions">
                            <form method="POST" action="<?= url('/playlists/remove-content') ?>"
                                onsubmit="return confirm('Retirer ce contenu ?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                                <input type="hidden" name="playlist_id" value="<?= $playlist['id'] ?>">
                                <input type="hidden" name="content_id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn-sm btn-danger" title="Retirer"><i
                                        class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Formulaire d'ajout -->
        <div class="card" style="height: fit-content;">
            <div class="card-header">
                <span>Ajouter un contenu</span>
            </div>
            <div style="padding: 1.5rem;">
                <form method="POST" action="<?= url('/playlists/add-content') ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <input type="hidden" name="playlist_id" value="<?= $playlist['id'] ?>">
                    <input type="hidden" name="order" value="<?= count($items) + 1 ?>">

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Sélectionner le
                            contenu</label>
                        <select name="content_id" required
                            style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px;">
                            <option value="">-- Choisir --</option>
                            <?php foreach ($allContents as $content): ?>
                                <option value="<?= $content['id'] ?>">
                                    <?= htmlspecialchars($content['title']) ?> (
                                    <?= $content['content_type'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Durée (secondes)</label>
                        <input type="number" name="duration" value="10" min="1" required
                            style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px;">
                    </div>

                    <button type="submit" class="btn-add">Ajouter à la playlist</button>
                </form>
            </div>

            <div
                style="padding: 1.5rem; border-top: 1px solid #e2e8f0; background: #f8fafc; font-size: 0.9rem; color: #64748b;">
                <i class="fas fa-info-circle"></i> Les contenus s'afficheront dans l'ordre de la liste.
            </div>
        </div>
    </div>
</body>

</html>