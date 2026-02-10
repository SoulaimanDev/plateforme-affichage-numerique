/**
 * Player PWA Logic
 * Gère le Service Worker, la synchro de playlist et le cache
 */

class PlayerPWA {
    constructor(screenKey, initialPlaylist) {
        this.screenKey = screenKey;
        this.playlist = initialPlaylist;
        this.currentIndex = 0;
        this.container = document.getElementById('player-container');
        this.debugName = document.getElementById('debug-item-name');
        this.timeoutId = null;
        this.typingTimeout = null;

        // Configuration
        this.SYNC_INTERVAL = 60000; // Vérifier les mises à jour toutes les 60s
        this.RETRY_DELAY = 10000;   // Réessayer après 10s si erreur réseau

        this.init();
    }

    async init() {
        console.log('Player PWA Initializing...');

        // 1. Enregistrer le Service Worker
        if ('serviceWorker' in navigator) {
            try {
                const reg = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker enregistré:', reg.scope);
            } catch (error) {
                console.error('Erreur SW:', error);
            }
        }

        // 2. Charger la dernière playlist locale si dispo (Offline First)
        const localPlaylist = localStorage.getItem('player_playlist_' + this.screenKey);
        if (localPlaylist) {
            try {
                this.playlist = JSON.parse(localPlaylist);
                console.log('Playlist chargée depuis LocalStorage');
            } catch (e) {
                console.error('Erreur parsing LocalStorage', e);
            }
        }

        // 3. Lancer la lecture
        if (this.playlist && this.playlist.items && this.playlist.items.length > 0) {
            this.playItem(0);
        } else {
            this.showError('En attente de contenu...');
        }

        // 4. Démarrer la boucle de synchro
        this.startSyncLoop();

        // 5. Gestion Online/Offline
        window.addEventListener('online', () => {
            console.log('Connexion rétablie');
            this.syncPlaylist();
        });
        window.addEventListener('offline', () => {
            console.log('Mode Hors Ligne activé');
        });
    }

    /**
     * Boucle de lecture
     */
    playItem(index) {
        if (this.timeoutId) clearTimeout(this.timeoutId);
        if (this.typingTimeout) clearTimeout(this.typingTimeout);
        // this.container.innerHTML = ''; // NE PLUS VIDER LE CONTENEUR POUR LA TRANSITION FLUIDE

        if (!this.playlist || !this.playlist.items || this.playlist.items.length === 0) {
            this.container.innerHTML = ''; // Si vide, on vide tout
            setTimeout(() => this.playItem(0), 5000);
            return;
        }

        if (index >= this.playlist.items.length) index = 0;
        this.currentIndex = index;

        const item = this.playlist.items[this.currentIndex];
        this.updateDebugInfo(item, index);

        // Rendu selon le type
        if (item.type === 'image') {
            this.renderImage(item);
        } else if (item.type === 'video') {
            this.renderVideo(item);
        } else if (item.type === 'text') {
            this.renderText(item);
        } else if (item.type === 'text_image') {
            this.renderTextImage(item);
        } else if (item.type === 'pdf') {
            this.renderPdf(item);
        } else {
            console.warn('Type de contenu inconnu ou non supporté:', item.type);
            this.showError(`Type non supporté: ${item.type}`);
            setTimeout(() => this.nextItem(), 5000);
        }

        // DEBUG VISUEL TEMPORAIRE (Désactivé pour prod)
        // const debugDiv = document.getElementById('debug-item-name');
        // if (debugDiv) {
        //    debugDiv.innerHTML = `<strong>${item.name}</strong> (${index + 1}/${this.playlist.items.length})<br>
        //     <span style="font-size:10px; opacity:0.7">Type: ${item.type} | URL: ...${item.url ? item.url.slice(-30) : 'null'}</span>`;
        // }
    }

    /**
     * Affiche la nouvelle frame et supprime les anciennes après transition
     */
    showFrame(newFrame) {
        // Force le reflow
        void newFrame.offsetWidth;
        newFrame.style.opacity = '1';

        // Nettoyage des vieux éléments (ceux qui ne sont pas la newFrame)
        const oldFrames = Array.from(this.container.children).filter(el => el !== newFrame);

        // On attend la fin de la transition (1s définie en CSS) + marge
        setTimeout(() => {
            oldFrames.forEach(el => {
                if (el.parentNode === this.container) {
                    this.container.removeChild(el);
                }
            });
        }, 1200);
    }

    renderImage(item) {
        // Wrapper Frame
        const frame = document.createElement('div');
        frame.className = 'media-frame';
        this.container.appendChild(frame);

        if (!item.url) {
            console.error('URL manquante pour l\'image');
            frame.innerHTML = '<h2 style="color:red; text-align:center;">Erreur: URL image manquante</h2>';
            frame.style.opacity = '1'; // Force show error
            setTimeout(() => this.nextItem(), 3000);
            return;
        }

        const img = document.createElement('img');
        img.src = item.url;
        img.className = 'media-content';

        img.onload = () => {
            this.showFrame(frame); // Déclenche la transition
            this.timeoutId = setTimeout(() => this.nextItem(), (item.duration || 10) * 1000);
        };
        img.onerror = () => {
            console.error('Erreur chargement image:', item.url);
            frame.innerHTML = `<h2 style="color:red; text-align:center;">Erreur chargement image<br><small>${item.url}</small></h2>`;
            frame.style.opacity = '1'; // Force show error
            setTimeout(() => this.nextItem(), 3000);
        };

        frame.appendChild(img);
    }

    renderVideo(item) {
        const frame = document.createElement('div');
        frame.className = 'media-frame';
        this.container.appendChild(frame);

        if (!item.url) {
            console.error('URL manquante pour vidéo');
            frame.innerHTML = '<h2 style="color:red; text-align:center;">Erreur: URL vidéo manquante</h2>';
            frame.style.opacity = '1';
            setTimeout(() => this.nextItem(), 3000);
            return;
        }

        const video = document.createElement('video');
        video.src = item.url;
        video.className = 'media-content';
        video.autoplay = true;
        video.muted = false; // Tenter avec son
        video.playsInline = true;

        // Quand la vidéo est prête à jouer (buffer suffisant), on affiche la frame
        video.oncanplay = () => {
            this.showFrame(frame);
        };

        // Fallback si autoplay bloqué
        video.onplay = () => {
            // Started successfully
        };

        if (item.duration) {
            this.timeoutId = setTimeout(() => {
                video.pause();
                this.nextItem();
            }, item.duration * 1000);
        } else {
            video.onended = () => {
                this.nextItem();
            };
        }

        video.onerror = () => {
            console.error('Erreur chargement vidéo:', item.url);
            frame.innerHTML = `<h2 style="color:red; text-align:center;">Erreur chargement vidéo<br><small>${item.url}</small></h2>`;
            frame.style.opacity = '1';
            setTimeout(() => this.nextItem(), 3000);
        };

        // Gestion Autoplay Bloqué
        video.play().catch(e => {
            console.warn("Autoplay bloqué ou erreur:", e);
            if (e.name === 'NotAllowedError') {
                // On mute et on relance
                video.muted = true;
                video.play().then(() => {
                    // Si succès en mute, on affiche le bouton UNMUTE Customisé
                    const btn = document.createElement('button');
                    btn.innerHTML = "🔇 Activer le son";
                    btn.style.position = 'absolute';
                    btn.style.bottom = '20px';
                    btn.style.right = '20px';
                    btn.style.zIndex = '100';
                    btn.style.padding = '12px 24px';
                    btn.style.fontSize = '16px';
                    btn.style.background = 'rgba(255, 255, 255, 0.9)';
                    btn.style.color = '#333';
                    btn.style.border = 'none';
                    btn.style.borderRadius = '50px';
                    btn.style.cursor = 'pointer';
                    btn.style.boxShadow = '0 4px 15px rgba(0,0,0,0.3)';
                    btn.style.fontWeight = 'bold';
                    btn.style.fontFamily = 'Segoe UI, sans-serif';

                    btn.onclick = (e) => {
                        e.stopPropagation(); // Évite de cliquer sur la frame
                        video.muted = false;
                        btn.remove();
                    };
                    frame.appendChild(btn);
                    this.showFrame(frame); // On affiche la frame maintenant que ça joue (même en muet)
                }).catch(e2 => {
                    console.error("Autoplay définitivement bloqué:", e2);
                    frame.innerHTML = '<h2 style="color:orange; text-align:center;">Lecture auto bloquée par le navigateur.</h2>';
                    this.showFrame(frame); // On affiche l'erreur
                    this.timeoutId = setTimeout(() => this.nextItem(), 5000);
                });
            } else {
                this.nextItem();
            }
        });

        frame.appendChild(video);
    }

    renderPdf(item) {
        const frame = document.createElement('div');
        frame.className = 'media-frame';
        frame.style.background = 'white';
        this.container.appendChild(frame); // Append immediately

        if (!item.url) {
            console.error('URL manquante pour PDF');
            frame.innerHTML = '<h2 style="color:red; text-align:center;">Erreur: URL PDF manquante</h2>';
            frame.style.opacity = '1';
            setTimeout(() => this.nextItem(), 3000);
            return;
        }

        // Container pour le canvas
        const canvasContainer = document.createElement('div');
        canvasContainer.style.width = '100%';
        canvasContainer.style.height = '100%';
        canvasContainer.style.display = 'flex';
        canvasContainer.style.justifyContent = 'center';
        canvasContainer.style.alignItems = 'center';

        const canvas = document.createElement('canvas');
        canvasContainer.appendChild(canvas);
        frame.appendChild(canvasContainer);

        // Chargement PDF
        const loadingTask = pdfjsLib.getDocument(item.url);
        loadingTask.promise.then(pdf => {
            const totalPages = pdf.numPages;
            const totalDuration = (item.duration || 30) * 1000;
            const pageDuration = totalDuration / totalPages;
            let currentPage = 1;

            const renderPage = (pageParam) => {
                if (!document.body.contains(frame)) return; // Stop si on a changé d'item

                pdf.getPage(pageParam).then(page => {
                    const viewport = page.getViewport({ scale: 1.5 }); // Scale initial

                    // Calcul du scale pour fit screen
                    const containerWidth = frame.clientWidth;
                    const containerHeight = frame.clientHeight;

                    const scaleX = containerWidth / viewport.width;
                    const scaleY = containerHeight / viewport.height;
                    const scale = Math.min(scaleX, scaleY) * 0.95; // Marge 5%

                    const scaledViewport = page.getViewport({ scale: scale });

                    canvas.height = scaledViewport.height;
                    canvas.width = scaledViewport.width;

                    const renderContext = {
                        canvasContext: canvas.getContext('2d'),
                        viewport: scaledViewport
                    };

                    page.render(renderContext).promise.then(() => {
                        this.showFrame(frame); // Afficher dès que la 1ère page est prête

                        // Page rendue, on attend avant la suivante
                        if (currentPage < totalPages) {
                            this.pptTimeout = setTimeout(() => {
                                currentPage++;
                                renderPage(currentPage);
                            }, pageDuration);
                        } else {
                            // Fin du document
                            this.pptTimeout = setTimeout(() => {
                                this.nextItem();
                            }, pageDuration);
                        }
                    });
                });
            };

            renderPage(currentPage);

        }, reason => {
            console.error('Error loading PDF:', reason);
            frame.innerHTML = `<h2 style="color:red; text-align:center;">Erreur chargement PDF<br><small>${reason.message}</small></h2>`;
            frame.style.opacity = '1';
            setTimeout(() => this.nextItem(), 5000);
        });
    }

    renderText(item) {
        const frame = document.createElement('div');
        frame.className = 'media-frame';
        this.container.appendChild(frame); // Append immediately

        const textDiv = document.createElement('div');
        textDiv.className = 'text-overlay';

        // Parsing intelligent comme pour TextImage
        const textContent = item.text || '';
        const parts = textContent.split(/\n(.+)/s);

        let htmlContent = '';
        let elementToType = null;
        let textToType = '';

        if (parts.length >= 2) {
            const title = parts[0].trim();
            const rawBody = parts[1].trim();
            htmlContent = `
                <div class="slide-container">
                    <h2 class="slide-title">${title}</h2>
                    <div class="slide-separator"></div>
                    <div class="slide-body">${rawBody}</div> 
                </div>
            `;
            elementToType = 'slide-body';
            textToType = rawBody.replace(/<[^>]*>/g, ''); // Typing sur texte brut seulement si on veut garder l'effet
        } else {
            htmlContent = `<h2 class="slide-single-title">${textContent}</h2>`;
            elementToType = 'slide-single-title';
            textToType = textContent.replace(/<[^>]*>/g, '');
        }

        textDiv.innerHTML = htmlContent;
        frame.appendChild(textDiv);

        requestAnimationFrame(() => this.showFrame(frame)); // Affichage immédiat (le texte est synchrone)

        // Typing Effect
        if (elementToType && textToType) {
            const targetElement = textDiv.querySelector('.' + elementToType);
            if (targetElement) {
                this.typingTimeout = setTimeout(() => {
                    this.typeText(targetElement, textToType);
                }, 500);
            }
        }

        this.timeoutId = setTimeout(() => this.nextItem(), (item.duration || 10) * 1000);
    }

    renderTextImage(item) {
        // Wrapper Frame
        const frame = document.createElement('div');
        frame.className = 'media-frame';
        this.container.appendChild(frame); // Append immediately

        // Split Layout Container
        const splitContainer = document.createElement('div');
        splitContainer.className = 'media-split';

        // 1. Image Part (Left)
        const imgDiv = document.createElement('div');
        imgDiv.className = 'split-image-container';
        const img = document.createElement('img');

        // 2. Text Part (Right)
        const textDiv = document.createElement('div');
        textDiv.className = 'split-text-container';

        // Logique de parsing intelligente : Titre vs Corps
        const textContent = item.text || '';
        const parts = textContent.split(/\n(.+)/s);

        let htmlContent = '';
        let elementToType = null;
        let textToType = '';

        if (parts.length >= 2) {
            const title = parts[0].trim();
            const rawBody = parts[1].trim();
            htmlContent = `
                <div class="slide-container">
                    <h2 class="slide-title split-title">${title}</h2>
                    <div class="slide-separator"></div>
                    <div class="slide-body split-body">${rawBody}</div>
                </div>
            `;
            elementToType = 'slide-body';
            textToType = rawBody;
        } else {
            htmlContent = `<h2 class="slide-single-title split-title"></h2>`;
            elementToType = 'slide-single-title';
            textToType = textContent;
        }

        textDiv.innerHTML = htmlContent;

        // Assemble
        splitContainer.appendChild(imgDiv);
        splitContainer.appendChild(textDiv);
        frame.appendChild(splitContainer);

        // Gestion asynchrone pour l'image
        img.className = 'split-image';
        img.src = item.file_path || item.url;
        imgDiv.appendChild(img);

        img.onload = () => {
            this.showFrame(frame);
        };
        img.onerror = () => {
            this.showFrame(frame); // On affiche quand même le texte même si l'image plante
        };

        // Typing Effect
        if (elementToType && textToType) {
            const targetElement = textDiv.querySelector('.' + elementToType);
            if (targetElement) {
                this.typingTimeout = setTimeout(() => {
                    this.typeText(targetElement, textToType);
                }, 500);
            }
        }

        this.timeoutId = setTimeout(() => this.nextItem(), (item.duration || 10) * 1000);
    }

    typeText(element, text, callback) {
        let i = 0;
        const speed = 40; // Vitesse de frappe (ms)

        const type = () => {
            // Sécurité : stop si l'élément n'est plus dans le DOM (changement de slide)
            if (!document.body.contains(element)) return;

            if (i < text.length) {
                const char = text.charAt(i);

                if (char === '\n') {
                    element.appendChild(document.createElement('br'));
                } else {
                    element.append(char);
                }

                i++;
                this.typingTimeout = setTimeout(type, speed);
            } else {
                if (callback) callback();
            }
        };
        type();
    }

    nextItem() {
        this.playItem(this.currentIndex + 1);
    }

    /**
     * Synchronisation avec le serveur
     */
    startSyncLoop() {
        setInterval(() => this.syncPlaylist(), this.SYNC_INTERVAL);
        this.syncPlaylist(); // check immédiat
    }

    async syncPlaylist() {
        if (!navigator.onLine) return; // Pas la peine si hors ligne

        try {
            // Utilisation de l'URL absolue générée par PHP pour supporter les sous-dossiers
            const urlToFetch = (typeof API_URL !== 'undefined') ? API_URL : `/player/${this.screenKey}/json`;
            console.log("Syncing playlist from:", urlToFetch);

            const response = await fetch(urlToFetch);
            if (!response.ok) throw new Error('Erreur HTTP ' + response.status);

            const newPlaylist = await response.json();

            // Comparer avec la playlist actuelle (simple check JSON string)
            const currentHash = JSON.stringify(this.playlist);
            const newHash = JSON.stringify(newPlaylist);

            if (currentHash !== newHash) {
                console.log('Nouvelle playlist détectée !');
                this.updatePlaylist(newPlaylist);
            } else {
                console.log('Playlist à jour.');
            }

        } catch (error) {
            console.warn('Erreur synchro playlist:', error);
            // Retry géré par le prochain intervalle
        }
    }

    async updatePlaylist(newPlaylist) {
        // 1. Précharger/Cacher les nouveaux fichiers
        const filesToCache = [];
        if (newPlaylist.items) {
            newPlaylist.items.forEach(item => {
                if (item.url && (item.type === 'image' || item.type === 'video')) {
                    filesToCache.push(item.url);
                }
            });
        }

        if (filesToCache.length > 0 && navigator.serviceWorker && navigator.serviceWorker.controller) {
            console.log('Mise en cache des fichiers:', filesToCache.length);
            navigator.serviceWorker.controller.postMessage({
                type: 'CACHE_FILES',
                files: filesToCache
            });
        }

        // 2. Sauvegarder en local
        localStorage.setItem('player_playlist_' + this.screenKey, JSON.stringify(newPlaylist));

        // 3. Mettre à jour l'état (au prochain tour de boucle, ça changera)
        this.playlist = newPlaylist;
        console.log('Playlist mise à jour et sauvegardée.');

        // Note: On ne force pas le reload immédiat pour ne pas couper le média en cours.
        // La nouvelle playlist sera prise en compte au prochain appel de PlayItem(0) si l'index dépasse
        // ou si on implémente une logique de hot-swap.
        // Pour l'instant, c'est transparent.
    }

    updateDebugInfo(item, index) {
        if (this.debugName) {
            this.debugName.textContent = `${item.name} (${index + 1}/${this.playlist.items.length}) ${navigator.onLine ? 'ONLINE' : 'OFFLINE'}`;
        }
    }

    showError(msg) {
        this.container.innerHTML = `<div class="content-text" style="color:red">${msg}</div>`;
    }
}
