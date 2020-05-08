<?php
namespace App\Core;

/**
 * Class ReCaptcha
 * @package App\Core
 */
class ReCaptcha
{
    use Libs;

    const URL_VERIFY = 'https://www.google.com/recaptcha/api/siteverify';
    const SECRET_KEY = '6LdhVPQUAAAAANDIJPNiPyUVycE0apOu1cSPd5nR';
    public $response;
    public $success;          // Whether this request was a valid reCAPTCHA token for your site
    public $score;            // The score for this request (0.0 - 1.0)
    public $action;           // The action name for this request (important to verify)
    public $challenge_ts;     // Timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
    public $hostname;         // The hostname of the site where the reCAPTCHA was solved
    public $error_codes = []; // Optional

    /**
     * ReCaptcha constructor.
     *
     * @param string $response
     */
    public function __construct($response)
    {
        $this->response = $response;
        $this->verify();
        $this->addLog();
    }

    /**
     * Check a captcha response.
     */
    private function verify()
    {
        $recaptcha = file_get_contents(self::URL_VERIFY.'?secret='.self::SECRET_KEY.'&response='.$this->response);
        $recaptcha = json_decode($recaptcha);
        $this->hydrate($recaptcha);
    }

    /**
     * Fill in the properties.
     *
     * @param $recaptcha
     */
    private function hydrate($recaptcha)
    {
        foreach ($recaptcha as $key => $value) {
            $key = str_replace('-', '_', $key);
            $this->$key = $value;
        }
    }

    public function addLog()
    {
        $params = [
            'response' => $this->response,
            'success' => $this->success === true ? 1 : 0,
            'score' => $this->score,
            'action' => $this->action,
            'challenge' => $this->challenge_ts,
            'hostname' => $this->hostname,
            'ip_address' => $this->getRemoteAddr(),
            'error_codes' => !empty($this->error_codes) ? json_encode($this->error_codes) : null
        ];
        Database::getInstance()->query('INSERT INTO b_recaptcha_logs (response, success, score, action, challenge, hostname, ip_address, error_codes)
                                              VALUES (:response, :success, :score, :action, :challenge, :hostname, :ip_address, :error_codes)', $params);
    }
}