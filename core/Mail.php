<?php
namespace App\core;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use App\Exceptions\MailException;

/**
 * Class Mail
 * @package App\core
 */
class Mail extends PHPMailer
{
    const DOMAIN = _DOMAIN_NAME_;

    /**
     * Mail constructor.
     */
    public function __construct()
    {
        parent::__construct(true);
        $this->hydrate();
    }

    /**
     *
     */
    private function hydrate()
    {
        $lines = file('core/mail.txt', FILE_IGNORE_NEW_LINES);
        try {
            //$this->SMTPDebug  = SMTP::DEBUG_SERVER;             // Enable verbose debug output
            $this->isSMTP();                                    // Send using SMTP
            $this->Host       = $lines[0];                      // Set the SMTP server to send through
            $this->SMTPAuth   = true;                           // Enable SMTP authentication
            $this->Username   = $lines[1];                      // SMTP username
            $this->Password   = $lines[2];                      // SMTP password
            $this->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;    // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $this->Port       = intval($lines[3]);              // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $this->CharSet = 'UTF-8';
            $this->DKIM_domain = 'blog.engrev.fr';
            $this->DKIM_private = _PATH_.'/dkim.private.key';
            $this->DKIM_selector = '1588895848.engrev';
            $this->DKIM_passphrase = '';
            $this->Encoding = 'base64';
            $this->setLanguage('fr', _PATH_.'/vendor/phpmailer/phpmailer/language/phpmailer.lang-fr.php');
        } catch (MailException $MailException) {
            $MailException->display(500, true, $this->ErrorInfo);
        }
    }

    /**
     * Send an email to reset the password.
     *
     * @param string $to
     * @param int    $id_user
     * @param string $token
     *
     * @throws MailException
     */
    public function resetToken(string $to, int $id_user, string $token)
    {
        $body = "<p>Bonjour,</p>
                 <p>Afin de réinitialiser votre mot de passe, merci de cliquer sur ce lien : <a href='http://".self::DOMAIN."/reinitialisation-mot-de-passe/{$id_user}-{$token}'>http://".self::DOMAIN."/reinitialisation-mot-de-passe/{$id_user}-{$token}</a>.</p>
                 <p>Pour votre sécurité, ce lien restera actif pour une durée de <b>30 min</b>. Après ce délai, il vous faudra renouveler votre demande.</p>
                 <br>
                 <p>Ceci est un mail automatique, merci de ne pas y répondre.</p>";
        $alt_body = "Bonjour,
                     Afin de réinitialiser votre mot de passe, merci de cliquer sur ce lien : http://".self::DOMAIN."/reinitialisation-mot-de-passe/{$id_user}-{$token}.
                     Pour votre sécurité, ce lien restera actif pour une durée de 30 min. Après ce délai, il vous faudra renouveler votre demande.
                     Ceci est un mail automatique, merci de ne pas y répondre.";
        try {
            $this->setFrom('no-reply@'.self::DOMAIN, 'Blog Engrev');
            //$this->addAddress('tv-LMFT@srv1.mail-tester.com');
            $this->addAddress($to);
            $this->isHTML(true);
            $this->Subject = 'Réinitialisation de votre mot de passe';
            $this->Body    = $body;
            $this->AltBody = $alt_body;
            $this->DKIM_identity = $this->From;
            $this->send();
        } catch (MailException $MailException) {
            $MailException->display(500, true, $this->ErrorInfo);
        }
    }

    /**
     * Sends an email to confirm the password reset.
     *
     * @param string $to
     *
     * @throws MailException
     */
    public function resetPassword(string $to)
    {
        $body = "<p>Bonjour,</p>
                 <p>Votre mot de passe a été modifié avec succès.</p>
                 <br>
                 <p>Ceci est un mail automatique, merci de ne pas y répondre.</p>";
        $alt_body = "Bonjour,
                     Votre mot de passe a été modifié avec succès.
                     Ceci est un mail automatique, merci de ne pas y répondre.";
        try {
            $this->setFrom('no-reply@'.self::DOMAIN, 'Blog Engrev');
            //$this->addAddress('tv-LMFT@srv1.mail-tester.com');
            $this->addAddress($to);
            $this->isHTML(true);
            $this->Subject = 'Réinitialisation de votre mot de passe';
            $this->Body    = $body;
            $this->AltBody = $alt_body;
            $this->DKIM_identity = $this->From;
            $this->send();
        } catch (MailException $MailException) {
            $MailException->display(500, true, $this->ErrorInfo);
        }
    }

    /**
     * Sends an email to confirm the deletion of the account.
     *
     * @param string $to
     *
     * @throws MailException
     */
    public function deleteAccount(string $to)
    {
        $body = "<p>Bonjour,</p>
                 <p>Votre compte a été supprimé avec succès.</p>
                 <br>
                 <p>Ceci est un mail automatique, merci de ne pas y répondre.</p>";
        $alt_body = "Bonjour,
                     Votre compte a été supprimé avec succès.
                     Ceci est un mail automatique, merci de ne pas y répondre.";
        try {
            $this->setFrom('no-reply@'.self::DOMAIN, 'Blog Engrev');
            //$this->addAddress('tv-LMFT@srv1.mail-tester.com');
            $this->addAddress($to);
            $this->isHTML(true);
            $this->Subject = 'Suppression de votre compte';
            $this->Body    = $body;
            $this->AltBody = $alt_body;
            $this->DKIM_identity = $this->From;
            $this->send();
        } catch (MailException $MailException) {
            $MailException->display(500, true, $this->ErrorInfo);
        }
    }
}