<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $service = htmlspecialchars(trim($_POST['service']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Détection environnement
    $isLocalhost = ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['SERVER_ADDR'] === '127.0.0.1');
    
    if ($isLocalhost) {
        // MODE LOCAL : Sauvegarde fichier
        $data = "=== DEVIS LOCAL ===\n";
        $data .= "Nom: $name\nEmail: $email\nTéléphone: $phone\nService: $service\nMessage: $message\n";
        $data .= "Date: " . date('d/m/Y H:i:s') . "\n\n";
        
        file_put_contents('devis_local.txt', $data, FILE_APPEND);
        
        echo "✅ MODE TEST - Demande sauvegardée (fichier devis_local.txt)";
        echo "<script>console.log('Données:', " . json_encode($_POST) . ");</script>";
        
    } else {
        // MODE PRODUCTION : Vrai envoi email
        $to = "walid.c69@outlook.fr";
        $subject = "Nouvelle demande de devis - Sud Rénovation";
        $body = "Nom: $name\nEmail: $email\nTéléphone: $phone\nService: $service\n\nMessage:\n$message";
        $headers = "From: noreply@sudrenovation46.fr\r\nReply-To: $email\r\n";
        
        if (mail($to, $subject, $body, $headers)) {
            echo "✅ Merci $name, votre demande a bien été envoyée.";
        } else {
            echo "❌ Erreur d'envoi. Contactez-nous au 09 70 35 41 39";
        }
    }
}
?>