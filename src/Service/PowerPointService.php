<?php

class PowerPointService
{
    private $uploadDir;
    private $publicDir;

    public function __construct()
    {
        $this->publicDir = __DIR__ . '/../../public';
        $this->uploadDir = $this->publicDir . '/uploads/contents';
    }

    /**
     * Convertit un fichier PowerPoint en une série d'images
     * @param string $filePath Chemin relatif du fichier PPT uploadé
     * @return array Résultat ['success' => bool, 'images' => string[], 'error' => string]
     */
    public function convertToImages($filePath)
    {
        $fullPath = $this->publicDir . $filePath;

        if (!file_exists($fullPath)) {
            return ['success' => false, 'error' => 'Fichier source introuvable'];
        }

        // Dossier de sortie basé sur le ID/Timestamp ou Hash
        $folderName = 'ppt_' . pathinfo($filePath, PATHINFO_FILENAME);
        $outputDir = $this->uploadDir . '/' . $folderName;
        $relativeOutputDir = '/uploads/contents/' . $folderName;

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // 1. Essai avec COM (Microsoft Office)
        if (class_exists('COM')) {
            try {
                return $this->convertWithCOM($fullPath, $outputDir, $relativeOutputDir);
            } catch (Exception $e) {
                // Fallback catch, continue to LibreOffice
            }
        }

        // 2. Essai avec LibreOffice
        // Vérification basique de la commande
        $soPath = $this->findLibreOffice();
        if ($soPath) {
            return $this->convertWithLibreOffice($soPath, $fullPath, $outputDir, $relativeOutputDir);
        }

        return ['success' => false, 'error' => 'Aucun convertisseur disponible (Install MS Office + enable COM, or Install LibreOffice)'];
    }

    private function convertWithCOM($inputPath, $outputDir, $relativeOutputDir)
    {
        // 0. Vérification des prérequis système critiques pour COM
        $systemProfileDesktop = 'C:\Windows\System32\config\systemprofile\Desktop';
        $sysWow64Desktop = 'C:\Windows\SysWOW64\config\systemprofile\Desktop';
        
        // On ne bloque pas si on ne peut pas lire (droits), mais c'est une info utile pour le debug
        // if (!file_exists($systemProfileDesktop) && !is_dir($systemProfileDesktop)) { ... } 

        $inputPath = realpath($inputPath);
        
        if (!$inputPath || !file_exists($inputPath)) {
             return ['success' => false, 'error' => 'Fichier source introuvable (chemin invalide)'];
        }

        $ppt = null;
        $presentation = null;

        try {
            $ppt = new COM("PowerPoint.Application");
            
            // Tentative d'ouverture
            try {
                $presentation = $ppt->Presentations->Open($inputPath, 0, 0, 0); // ReadOnly, Untitled, WithWindow=0
            } catch (Exception $e) {
                // Analyse de l'erreur "Unknown"
                $msg = $e->getMessage();
                if (strpos($msg, 'Unknown') !== false || strpos($msg, 'Source: Unknown') !== false) {
                     throw new Exception("Erreur de configuration serveur: Les dossiers 'Desktop' sont manquants dans systemprofile. Voir REQUIREMENTS_PPT.md.");
                }
                throw $e;
            }

            if (!$presentation) {
                throw new Exception("Impossible d'ouvrir la présentation (Fichier corrompu ou verrouillé).");
            }
            
            $images = [];
            foreach ($presentation->Slides as $index => $slide) {
                // Index starts at 1 in PowerPoint
                $filename = "slide_{$index}.jpg";
                $fullOut = $outputDir . DIRECTORY_SEPARATOR . $filename;
                
                // Export
                $slide->Export($fullOut, "JPG", 1920, 1080); // HD Resolution
                
                if (file_exists($fullOut)) {
                    $images[] = $relativeOutputDir . '/' . $filename;
                }
            }
            
            $presentation->Close();
            $presentation = null;
            
            $ppt->Quit();
            $ppt = null;

            if (empty($images)) {
                 // Fallback si Export échoue silencieusement
                 return ['success' => false, 'error' => 'La conversion a fonctionné mais aucune image n\'a été générée.'];
            }

            return ['success' => true, 'images' => $images];

        } catch (Exception $e) {
            // Nettoyage agressif
            if ($presentation) {
                try { $presentation->Close(); } catch (Exception $ex) {}
            }
            if ($ppt) {
                try { $ppt->Quit(); } catch (Exception $ex) {}
            }
            $ppt = null;
            
            return ['success' => false, 'error' => 'Erreur Conversion Office: ' . $e->getMessage()];
        }
    }

    private function convertWithLibreOffice($soPath, $inputPath, $outputDir, $relativeOutputDir)
    {
        // LibreOffice conversion to PDF first, then ImageMagick? 
        // Or direct to images? LibreOffice doesn't easily export *all* slides to images in one command line without a macro or PDF interm.
        // Easier path: PPT -> PDF -> Images (requires ImageMagick).
        // If ImageMagick (magick) is not present, we are stuck.

        // Let's try simple PDF export first, maybe the Player can display PDF? 
        // Browser displays PDF. But user asked for "Images or Video".
        // Let's assume PDF conversion is a safe "fallback" if we can't do images easy, 
        // BUT the user asked for compatibility.

        // Let's simplify: Try to convert to PDF. Then extract images?
        // Actually, for this environment, without knowing if ImageMagick is there, it's risky.

        // Alternative: LibreOffice --convert-to jpg ... this usually converts ONLY the first page or fails for multipage.

        return ['success' => false, 'error' => 'Conversion LibreOffice non implémentée complètement (nécessite ImageMagick)'];
    }

    private function findLibreOffice()
    {
        // Common paths
        $paths = [
            'C:\Program Files\LibreOffice\program\soffice.exe',
            'C:\Program Files (x86)\LibreOffice\program\soffice.exe'
        ];

        foreach ($paths as $p) {
            if (file_exists($p))
                return $p;
        }
        return null;
    }
}
