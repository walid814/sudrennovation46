<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sécurisation des champs
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $service = htmlspecialchars(trim($_POST['service']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validation
    if (empty($name) || empty($email) || empty($phone) || empty($service) || empty($message)) {
        echo "❌ Tous les champs obligatoires doivent être remplis.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "❌ Adresse email invalide.";
        exit;
    }

    // Sauvegarde dans un fichier (au lieu d'envoyer un email)
    $devis_data = "=== NOUVELLE DEMANDE DE DEVIS ===\n";
    $devis_data .= "Date: " . date('d/m/Y H:i:s') . "\n";
    $devis_data .= "Nom: $name\n";
    $devis_data .= "Email: $email\n";
    $devis_data .= "Téléphone: $phone\n";
    $devis_data .= "Service: $service\n";
    $devis_data .= "Message: $message\n";
    $devis_data .= "==============================\n\n";

    // Écriture dans le fichier
    $file_saved = file_put_contents('devis_recus.txt', $devis_data, FILE_APPEND | LOCK_EX);

    if ($file_saved !== false) {
        // Afficher les données pour debug
        echo "✅ DEMANDE ENREGISTRÉE (TEST LOCAL)\n\n";
        echo "Nom: $name\n";
        echo "Email: $email\n";
        echo "Téléphone: $phone\n";
        echo "Service: $service\n";
        echo "Message: $message\n\n";
        echo "📁 Les données sont sauvegardées dans 'devis_recus.txt'\n";
        echo "🌐 En production, l'email sera envoyé à walid.c69@outlook.fr";
        
        // Afficher aussi dans la console navigateur
        echo "<script>console.log('Devis enregistré:', " . json_encode($_POST) . ");</script>";
    } else {
        echo "❌ Erreur lors de la sauvegarde. Contactez-nous au 09 70 35 41 39";
    }
} else {
    echo "Accès non autorisé.";
}
?>