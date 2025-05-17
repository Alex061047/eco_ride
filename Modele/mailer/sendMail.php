<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php'; 
use Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->safeLoad();

function sendMail($destinataire, $sujet, $message) {
    $mail = new PHPMailer(true);
    
    try {
        // Configuration SMTP
        $mail->isSMTP();
        $mail->Host       = $_ENV['SMTP_HOST'] ?? getenv('SMTP_HOST'); 
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['SMTP_USER'] ?? getenv('SMTP_USER');
        $mail->Password   = $_ENV['SMTP_PASS'] ?? getenv('SMTP_PASS');
        $mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? getenv('SMTP_SECURE');
        $mail->Port       = $_ENV['SMTP_PORT'] ?? getenv('SMTP_PORT');

        // Désactiver la vérification SSL (optionnel)
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Encodage UTF-8
        $mail->CharSet = 'UTF-8';
        
        // Paramètres email
        $mail->setFrom($_ENV['SMTP_FROM'] ?? getenv('SMTP_FROM'), $_ENV['SMTP_FROM_NAME'] ?? getenv('SMTP_FROM_NAME'));
        $mail->addAddress($destinataire);
        $mail->isHTML(true);
        $mail->Subject = $sujet;
        $mail->Body    = $message;

        $mail->send();
        return ["success" => true, "message" => "E-mail envoyé !"];
    } catch (Exception $e) {
        return ["success" => false, "message" => "Erreur : {$mail->ErrorInfo}"];
    }
}
?>
