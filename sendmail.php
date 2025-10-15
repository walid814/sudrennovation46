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

    // Adresse de réception (toi)
    $to = "walid.c69@outlook.fr";
    $subject = "Nouvelle demande de devis - Sud Rénovation";

    // Corps du mail envoyé à toi
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

    // Entêtes du mail principal
    $headers = "From: noreply@sud-renovation.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "MIME-Version: 1.0\r\n";

    // Log de base
    $log_message  = "\n==============================\n";
    $log_message .= "[" . date('Y-m-d H:i:s') . "] Tentative d'envoi à : $to\n";
    $log_message .= "De : $name <$email>\n";
    $log_message .= "Service : $service\n";
    $log_message .= "Message : $message\n";

    // Envoi du mail principal (à toi)
    $mainMail = mail($to, $subject, $body, $headers);

    // Envoi du mail de confirmation (au client)
    $confirm_subject = "Confirmation de votre demande de devis - Sud Rénovation";
    $confirm_body = "Bonjour $name,\n\n";
    $confirm_body .= "Merci pour votre demande de devis concernant : \"$service\".\n";
    $confirm_body .= "Nous avons bien reçu votre message et nous vous répondrons dans les plus brefs délais.\n\n";
    $confirm_body .= "Rappel de votre message :\n";
    $confirm_body .= "--------------------------------------\n";
    $confirm_body .= "$message\n";
    $confirm_body .= "--------------------------------------\n\n";
    $confirm_body .= "Cordialement,\nL’équipe Sud Rénovation\n";
    $confirm_body .= "📞 09 70 35 41 39 | 🌐 sud-renovation.com\n";
    $confirm_body .= "Date : " . date('d/m/Y à H:i:s') . "\n";

    $confirm_headers = "From: Sud Rénovation <noreply@sud-renovation.com>\r\n";
    $confirm_headers .= "Reply-To: noreply@sud-renovation.com\r\n";
    $confirm_headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $confirm_headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $confirmMail = mail($email, $confirm_subject, $confirm_body, $confirm_headers);

    // 🔹 Nouveau : création d'un fichier log spécifique pour l'email automatique envoyé au client
    $auto_log  = "\n==============================\n";
    $auto_log .= "[" . date('Y-m-d H:i:s') . "] Email automatique envoyé à : $email\n";
    $auto_log .= "Sujet : $confirm_subject\n\n";
    $auto_log .= "Contenu du message envoyé :\n";
    $auto_log .= "--------------------------------------\n";
    $auto_log .= $confirm_body . "\n";
    $auto_log .= "--------------------------------------\n";
    $auto_log .= "Résultat de l'envoi : " . ($confirmMail ? "✅ Succès" : "❌ Échec") . "\n";
    $auto_log .= "==============================\n";
    file_put_contents('email_auto_log.txt', $auto_log, FILE_APPEND);

    // Gestion des logs et messages utilisateur
    if ($mainMail) {
        $success_log  = "✅ SUCCÈS : Email envoyé à l’entreprise.\n";
        if ($confirmMail) {
            $success_log .= "✅ Confirmation envoyée à $email.\n";
        } else {
            $success_log .= "⚠️ Échec de l'envoi de confirmation à $email.\n";
        }
        $success_log .= "Date : " . date('d/m/Y H:i:s') . "\n";
        $success_log .= "==============================\n";
        file_put_contents('email_log.txt', $log_message . $success_log, FILE_APPEND);
        echo "✅ Merci $name, votre demande de devis a bien été envoyée. Un email de confirmation vous a été transmis.";
    } else {
        $error = error_get_last();
        $error_log  = "❌ ERREUR : Email non envoyé.\n";
        $error_log .= "Détail : " . ($error['message'] ?? 'Aucun message d\'erreur PHP') . "\n";
        $error_log .= "==============================\n";
        file_put_contents('email_log.txt', $log_message . $error_log, FILE_APPEND);
        echo "❌ Désolé, une erreur est survenue lors de l'envoi. Veuillez nous contacter directement au 09 70 35 41 39.";
    }
} else {
    echo "Accès non autorisé.";
}
?>
