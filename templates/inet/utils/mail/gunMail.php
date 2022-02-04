<?php

use UmiCms\Service;
use Mailgun\Mailgun;
use Inet\Proxy\Mail\iMail;
use Mailgun\Hydrator\ArrayHydrator;
use Mailgun\HttpClient\HttpClientConfigurator;
use Mailgun\Model\Message\SendResponse;

class gunMail implements iMail
{
    /** @const bool VALIDATE_EMAIL нужно ли проверять валидность email (сервис может быть не доступен!) */
    const VALIDATE_EMAIL = false;

    /**
     * Отправляет письмо через MailGun
     *
     * @inheritdoc
     * @return bool|SendResponse
     * @throws coreException
     */
    public function sendMail($emails = [], $subject = '', $content = 'default', $filePath = '', $tags = []) {
        // loading custom config file
        // TODO: replace with load from .dotenv config
        $dataModule = cmsController::getInstance()->getModule('data');
        $config = $dataModule->loadCustomConfig();

        $umiRegistry = Service::Registry();
        $apiKey = $config->get('mailgun', 'API-Key');
        $domain = Service::DomainDetector()->detectHost();
        $emailFrom = "$domain <{$umiRegistry->get("//settings/email_from")}>";
        $attachment = $filePath ? [['filePath' => $filePath]] : [];

        if (empty($emails))
            throw new Exception('Empty Email list');

        // remove `www.` from domain to qualify it for mailGun api
        $domain = preg_match('~www~', $domain) ? substr($domain, 4) : $domain;

        // set mailgun configuration
        $configurator = new HttpClientConfigurator();
        $configurator->setEndpoint("https://api.eu.mailgun.net/");
        $configurator->setApiKey($apiKey);
        $configurator->setDebug(true);

        $mg = new Mailgun($configurator, new ArrayHydrator());

        $isValid = true;
        $result = false;

        $emails = is_array($emails) ? $emails : [$emails];
        foreach ($emails as $emailTo) {
            // email validation on Mailgun
            if (self::VALIDATE_EMAIL) {
                $isValid = $this->validateEmail($mg, $emailTo);
            }

            if ($isValid) {
                try {
                    $result = $mg->messages()->send($domain, [
                        'from'    => $emailFrom,
                        'to'      => $emailTo,
                        'subject' => $subject,
                        'text'    => '',
                        'html'    => $content,
                        'attachment' => $attachment,
                        'o:tag'   => $tags
                    ]);
                } catch (\Mailgun\Exception\HydrationException $e) {
                    var_dump($e);
                }
            }
        }

        return $result;
    }

    /**
     * Email validation on Mailgun
     *
     * @depracated
     * @param Mailgun $mg
     * @param string $email
     * @return false
     * @throws Exception
     */
    private function validateEmail(Mailgun $mg, string $email = ''): bool {
        $valResult = false;

        try {
            $validation = $mg->emailValidation();
            $valResult = $validation->validate($email, true);
        } catch (\Mailgun\Exception $e) {
            var_dump($e);
        }

        return $valResult;
    }
}