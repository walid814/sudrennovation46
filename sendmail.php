<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sécurisation des champs
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST['phone']));
    $service = htmlspecialchars(trim($_POST['service']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validation des champs requis
    if (empty($name) || empty($email) || empty($phone) || empty($service) || empty($message)) {
        echo "❌ Tous les champs obligatoires doivent être remplis.";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "❌ Adresse email invalide.";
        exit;
    }

    // Adresse de réception
    $to = "walid.c69@outlook.fr";
    $subject = "Nouvelle demande de devis - Sud Rénovation";

    // Contenu du mail enrichi
    $body = "=== NOUVELLE DEMANDE DE DEVIS ===\n\n";
    $body .= "INFORMATIONS CLIENT :\n";
    $body .= "Nom : $name\n";
    $body .= "Email : $email\n";
    $body .= "Téléphone : $phone\n";
    $body .= "Service demandé : $service\n\n";
    $body .= "MESSAGE :\n$message\n\n";
    $body .= "---\n";
    $body .= "Envoyé depuis le formulaire de contact Sud Rénovation\n";
    $body .= "Date : " . date('d/m/Y à H:i:s') . "\n";

    // Entêtes améliorées
    $headers = "From: noreply@sud-renovation.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "MIME-Version: 1.0\r\n";

    // Journalisation pour debug
    $log_message = "[" . date('Y-m-d H:i:s') . "] Tentative d'envoi à: $to\n";
    $log_message .= "De: $name ($email)\n";
    $log_message .= "Sujet: $service\n";
    file_put_contents('email_log.txt', $log_message, FILE_APPEND);

    // Tentative d'envoi
    if (mail($to, $subject, $body, $headers)) {
        // Log de succès
        file_put_contents('email_log.txt', "✅ Email envoyé avec succès\n", FILE_APPEND);
        echo "✅ Merci $name, votre demande de devis a bien été envoyée. Nous vous recontacterons rapidement.";
    } else {
        // Log d'erreur
        $error = error_get_last();
        file_put_contents('email_log.txt', "❌ Erreur d'envoi: " . $error['message'] . "\n", FILE_APPEND);
        echo "❌ Désolé, une erreur est survenue lors de l'envoi. Veuillez nous contacter directement au 09 70 35 41 39";
    }
} else {
    echo "Accès non autorisé.";
}
?>