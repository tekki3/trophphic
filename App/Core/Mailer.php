<?php


namespace Trophphic\Core;

use Email;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Trophphic\Core\Logger;
use Trophphic\App\Models\EmailModel;

/**
 * Class Mailer
 * A robust email sender class using PHPMailer.
 */
class Mailer
{
    /** @var PHPMailer */
    private $mailer;

    /** @var array Errors encountered */
    private $errors = [];

    /**
     * Mailer constructor.
     * @param string $host SMTP host.
     * @param string $username SMTP username.
     * @param string $password SMTP password.
     * @param string $from Sender email address.
     * @param string $fromName Sender name.
     * @param int $port SMTP port.
     * @param bool $smtpAuth Enable SMTP authentication.
     * @param string $encryption Encryption type (ssl/tls).
     */
    public function __construct(
        string $host,
        string $username,
        string $password,
        string $from,
        string $fromName = '',
        int $port = 587,
        bool $smtpAuth = true,
        string $encryption = 'tls'
    ) {
        $this->mailer = new PHPMailer(true);

        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $host;
            $this->mailer->SMTPAuth = $smtpAuth;
            $this->mailer->Username = $username;
            $this->mailer->Password = $password;
            $this->mailer->SMTPSecure = $encryption;
            $this->mailer->Port = $port;

            // Sender info
            $this->mailer->setFrom($from, $fromName);
            $this->mailer->isHTML(true);
        } catch (Exception $e) {
            $this->errors[] = "Mailer Error: {$this->mailer->ErrorInfo}";
            Logger::error("Mailer Error: {$this->mailer->ErrorInfo}");
        }
    }

    /**
     * Send an email.
     *
     * @param string $to Recipient email address.
     * @param string $subject Email subject.
     * @param string $message Email body.
     * @param array $attachments Optional file paths for attachments.
     * @return bool True on success, false on failure.
     */
    public function send(string $to, string $subject, string $message, array $attachments = []): bool
    {
        try {
            // Recipient
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $message;

            // Attachments
            foreach ($attachments as $filePath) {
                if (file_exists($filePath)) {
                    $this->mailer->addAttachment($filePath);
                } else {
                    $this->errors[] = "Attachment file not found: $filePath";
                    Logger::error("Attachment file not found: $filePath");
                }
            }

            // Send email
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            $this->errors[] = "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
            Logger::error("Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }

     /**
     * Queue an email for later processing.
     */
    public function queueEmail($recipient, $subject, $body, $sender) {
        $emailModel = new EmailModel();
        try {
            $result = $emailModel->insert($recipient, $subject, $body, $sender);
            if ($result === false) {
                throw new Exception("Failed to queue email.");
            }
            return true;
        } catch (Exception $e) {
            Logger::error('Error queuing email: ' . $e->getMessage());
            return false;
        }
    }  
    /**
     * Get error messages.
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}

// Example usage:
// $mailer = new Trophphic\Core\Mailer(
//     'smtp.example.com',
//     'username',
//     'password',
//     'noreply@example.com',
//     'My App'
// );
// if (!$mailer->send('user@example.com', 'Test Email', '<h1>Hello World</h1>', ['/path/to/file.pdf'])) {
//     print_r($mailer->getErrors());
// } else {
//     echo "Email sent successfully!";
// }
