@echo off
REM ====================================================
REM  DIGITAL SIGNAGE LAUNCHER
REM  Mode Kiosque pour Chrome avec Autoplay Audio/Video
REM ====================================================

REM ----------------------------------------------------
REM CONFIGURATION
REM ----------------------------------------------------
SET "TARGET_URL=http://plateforme-affichage-numerique/player/SCREEN_001"

echo Lancement du Player affichage dynamique...
echo URL Cible: %TARGET_URL%

REM Fermeture des instances existantes de Chrome pour garantir la prise en compte des flags
taskkill /F /IM chrome.exe /T >nul 2>&1

REM ----------------------------------------------------
REM FLAGS CHROME
REM ----------------------------------------------------
REM --kiosk : Mode plein écran sans barre d'adresse
REM --autoplay-policy=no-user-gesture-required : Autorise l'autoplay AVEC le son
REM --unsafely-treat-insecure-origin-as-secure : Force Chrome a traiter notre URL http comme securisee (Requis pour l'autoplay)
REM --user-data-dir : Profil separe pour eviter les conflits
REM ----------------------------------------------------
SET "CHROME_FLAGS=--kiosk --autoplay-policy=no-user-gesture-required --unsafely-treat-insecure-origin-as-secure=%TARGET_URL% --disable-web-security --user-data-dir="%LOCALAPPDATA%\KioskBrowserData" --disable-infobars --disable-session-crashed-bubble --no-first-run --check-for-update-interval=31536000 --disable-translate"

REM ----------------------------------------------------
REM Recherche de l'executable Chrome
REM ----------------------------------------------------
SET "CHROME_EXE="
if exist "C:\Program Files\Google\Chrome\Application\chrome.exe" (
    SET "CHROME_EXE=C:\Program Files\Google\Chrome\Application\chrome.exe"
)
if not defined CHROME_EXE (
    if exist "C:\Program Files (x86)\Google\Chrome\Application\chrome.exe" (
        SET "CHROME_EXE=C:\Program Files (x86)\Google\Chrome\Application\chrome.exe"
    )
)
if not defined CHROME_EXE (
    if exist "%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe" (
        SET "CHROME_EXE=%LOCALAPPDATA%\Google\Chrome\Application\chrome.exe"
    )
)

if defined CHROME_EXE (
    echo Chrome trouve : "%CHROME_EXE%"
    echo Flags : %CHROME_FLAGS%
    echo.
    echo Lancement en cours...
    start "" "%CHROME_EXE%" %CHROME_FLAGS% "%TARGET_URL%"
    
    REM Pause pour voir les erreurs eventuelles avant de fermer
    echo.
    echo Lancement effectue. La fenetre va se fermer.
    timeout /t 10
    exit
)

echo.
echo [ERREUR] Google Chrome n'a pas ete trouve.
echo Veuillez installer Google Chrome ou modifier le script pour pointer vers le bon executable.
echo.
pause
