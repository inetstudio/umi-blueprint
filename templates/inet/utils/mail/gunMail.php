<?php
    use Inet\Proxy\Mail\iMail;
    use UmiCms\Service;
    use Mailgun\Mailgun;
    use Mailgun\HttpClientConfigurator;

    class gunMail implements iMail
    {
        /** @const bool VALIDATE_EMAIL нужно ли проверять валидность email (сервис может быть не доступен!) */
        const VALIDATE_EMAIL = false;

        /**
         * Отправляет письмо через MailGun
         *
         * @inheritdoc
         * @return bool|\Mailgun\Model\Message\SendResponse
         * @throws coreException
         */
        public function sendMail($emails = [], $subject = '', $content = 'default', $filePath = '', $tags = []) {
            // loading custom config file
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
            $configurator->setEndpoint("https://api.eu.mailgun.net/v3/$domain/messages");
            $configurator->setApiKey($apiKey);
            $configurator->setDebug(true);

            $mg = Mailgun::configure($configurator);
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
         * @param Mailgun $mg
         * @param string  $email
         * @return false
         */
        private function validateEmail(Mailgun $mg, $email = ''): bool {
            $valResult = false;

            try {
                $validation = $mg->emailValidation();
                $valResult = $validation->validate($email, true);
            } catch (\Mailgun\Exception $e) {
                var_dump($e);
            }

            return $valResult;
        }

        /**
         * Deprecated email validation on Mailgun
         * @deprecated
         * @param iConfiguration $config
         * @param string         $email
         * @return mixed
         */
        private function validateEmailViaDeprecatedAPI(iConfiguration $config, $email = '') {
            $apiPubKey = $config->get('mailgun', 'API-Pub-Key');

            $mgValClient = new Mailgun($apiPubKey);
            $valResult = $mgValClient->get("address/validate", ['address' => $email]);
            return $valResult->http_response_body->is_valid;
        }
    }