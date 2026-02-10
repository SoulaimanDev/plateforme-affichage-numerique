<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($screen['name']) ?> - Player</title>
    <!-- PWA -->
    <link rel="manifest" href="<?= url('/manifest.json') ?>">

    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Antic+Slab&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Palette Vibrante */
            --color-yellow: #EA9500;
            --color-pink: #B22F89;
            --color-green: #2B8736;
            --bg-dark: #121212;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: var(--bg-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Cadre Principal Animé */
        .media-frame {
            position: absolute;
            /* Pour superposition (transition fluide) */
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            height: 90%;
            border-radius: 20px;
            /* Création de la bordure animée via un pseudo-élément ou background complexe */
            background: padding-box linear-gradient(var(--bg-dark), var(--bg-dark)), border-box linear-gradient(45deg, var(--color-yellow), var(--color-pink), var(--color-green), var(--color-yellow));
            background-size: 300% 300%;
            border: 6px solid transparent;

            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            /* Départ invisible pour fade-in */
            transition: opacity 1s ease-in-out;
            /* Transition douce */
            z-index: 1;

            /* Animations */
            animation: borderFlow 8s ease infinite, float 6s ease-in-out infinite;
        }

        /* Effet Glow derrière le cadre */
        .media-frame::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            z-index: -1;
            border-radius: 24px;
            background: linear-gradient(45deg, var(--color-yellow), var(--color-pink), var(--color-green));
            background-size: 300%;
            filter: blur(20px);
            opacity: 0.4;
            animation: borderFlow 8s ease infinite;
        }

        /* Contenu Média global */
        .media-content {
            width: 100%;
            height: 100%;
            border-radius: 14px;
            z-index: 2;
        }

        /* Images : On adapte sans couper (demande précédente) */
        img.media-content {
            object-fit: contain;
            background: black;
        }

        /* Vidéos : On remplit tout le cadre (demande actuelle) */
        video.media-content {
            object-fit: cover;
            /* Remplir l'espace (peut rogner les bords) */
            background: black;
        }

        /* Contenu Texte */
        .text-overlay {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 50px;
            box-sizing: border-box;

            /* Style demandé : Fond Blanc, Texte Rose, Font Antic Slab */
            background: white;
            color: var(--color-pink);
            font-family: 'Antic Slab', Georgia, "Times New Roman", serif;
            z-index: 2;
            flex-direction: column;
        }

        .slide-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .slide-title {
            font-size: 4rem;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            line-height: 1.1;
            letter-spacing: 2px;
            color: var(--color-green);
            /* Titre Vert */
        }

        .slide-single-title {
            font-size: 5rem;
            font-weight: 700;
            margin: 0;
            text-transform: uppercase;
            line-height: 1.2;
            color: var(--color-pink);
            /* Contenu seul : Rose */
        }

        .slide-separator {
            width: 100px;
            height: 6px;
            background-color: var(--color-green);
            border-radius: 3px;
            margin: 10px 0;
        }

        .slide-body {
            font-size: 2.5rem;
            font-weight: 400;
            margin: 0;
            line-height: 1.4;
            opacity: 0.95;
            white-space: pre-wrap;
            /* Conserve les retours ligne */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            /* Plus lisible pour le corps */
            text-transform: none;
            /* Corps en minuscule/normal */
            color: var(--color-pink);
            /* Texte Rose */
        }

        /* Animations Keyframes */
        @keyframes borderFlow {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Mode Texte + Image (Split) */
        .media-split {
            display: flex;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: white;
            /* Fond blanc global */
            border-radius: 14px;
            z-index: 2;
        }

        .split-image-container {
            flex: 1;
            /* 50% width */
            display: flex;
            align-items: center;
            justify-content: center;
            background: black;
            /* Fond noir pour l'image box */
            overflow: hidden;
            position: relative;
        }

        .split-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            /* Garde les proportions sans couper */
        }

        .split-text-container {
            flex: 1;
            /* 50% width */
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            box-sizing: border-box;
            text-align: center;
            overflow: hidden;
            /* Empêche le débordement */
        }

        /* Ajustements typos pour le mode split */
        .split-title {
            font-size: 3rem;
            /* Plus petit pour tenir */
            margin-bottom: 20px;
            width: 100%;
            word-wrap: break-word;
        }

        .split-body {
            font-size: 1.8rem;
            width: 100%;
            word-wrap: break-word;
            max-height: 70%;
            overflow-y: auto;
            /* Scroll si trop long */
        }

        /* Responsive Portrait : Image en haut, Texte en bas */
        @media (orientation: portrait) {
            .media-split {
                flex-direction: column;
            }

            .split-image-container {
                height: 40%;
                flex: none;
            }

            .split-text-container {
                height: 60%;
                flex: none;
                padding: 20px;
            }

            .split-title {
                font-size: 2.5rem;
            }

            .split-body {
                font-size: 1.5rem;
            }
        }

        @keyframes float {
            0% {
                transform: translate(-50%, -50%) translateY(0px);
            }

            50% {
                transform: translate(-50%, -50%) translateY(-15px);
            }

            100% {
                transform: translate(-50%, -50%) translateY(0px);
            }
        }

        @keyframes frameIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        #player-container {
            width: 100%;
            height: 100%;
            position: relative;
            /* Contexte pour les frames absolues */
            display: flex;
            /* Toujours utile pour le loader initial */
            justify-content: center;
            align-items: center;
            perspective: 1000px;
            overflow: hidden;
        }

        #debug-info {
            position: absolute;
            bottom: 20px;
            right: 20px;
            color: rgba(255, 255, 255, 0.2);
            font-size: 14px;
            pointer-events: none;
            z-index: 9999;
            display: none;
            /* Correction: Masqué */
        }

        #start-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(0, 0, 0, 0.4);
            z-index: 10000;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .start-btn {
            background: rgba(255, 255, 255, 0.9);
            color: #333;
            padding: 20px 40px;
            border-radius: 50px;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 15px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
            }

            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 10px rgba(255, 255, 255, 0);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            }
        }
    </style>
</head>

<body>
    <!-- Outil de Debug Global : Affiche les erreurs JS directement sur l'écran -->
    <div id="global-error-overlay"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:auto; background:rgba(200,0,0,0.9); color:white; padding:20px; z-index:99999; font-family:monospace; white-space:pre-wrap; overflow:auto;">
    </div>
    <script>
        window.onerror = function (msg, url, line, col, error) {
            const overlay = document.getElementById('global-error-overlay');
            overlay.style.display = 'block';
            overlay.innerHTML += `<strong>ERREUR CRITIQUE:</strong><br>${msg}<br><small>${url}:${line}:${col}</small><br><br>`;
            return false;
        };

        window.addEventListener('unhandledrejection', function (event) {
            const overlay = document.getElementById('global-error-overlay');
            overlay.style.display = 'block';
            overlay.innerHTML += `<strong>PROMESSE REJETÉE:</strong><br>${event.reason}<br><br>`;
        });
    </script>

    <div id="player-container">
        <!-- Correction: Loader vide -->
    </div>

    <div id="debug-info">
        Zone: <?= htmlspecialchars($screen['zone_id']) ?> |
        <span id="debug-item-name">Loading PWA...</span>
    </div>

    <!-- Logique PWA -->
    <script src="<?= url('/js/player-pwa.js') ?>?v=<?= time() ?>"></script>
    <script>
        // Sécurisation de l'injection JSON pour éviter les SyntaxError
        const initialPlaylist = <?= ($json = json_encode($content, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP)) ? $json : 'null' ?>;
        const SCREEN_KEY = "<?= htmlspecialchars($screen['screen_key']) ?>";
        const API_URL = "<?= url('/player/' . $screen['screen_key'] . '/json') ?>"; // URL absolue pour la synchro

        if (!initialPlaylist) {
            console.error("Erreur critique: Impossible de charger la playlist initiale (JSON invalid).");
            document.getElementById('debug-item-name').textContent = "ERREUR DATA";
            document.getElementById('player-container').innerHTML = '<h2 style="color:red;text-align:center">Erreur de données (JSON)<br>Vérifiez les logs serveur.</h2>';
        } else {
            // Initialisation du Player PWA
            try {
                const player = new PlayerPWA(SCREEN_KEY, initialPlaylist);
            } catch (e) {
                console.error("Erreur init player:", e);
                document.getElementById('player-container').innerHTML = '<h2 style="color:red;text-align:center">Erreur JS: ' + e.message + '</h2>';
            }
        }
    </script>
</body>

</html>