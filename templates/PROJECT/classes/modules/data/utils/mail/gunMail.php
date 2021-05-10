<?php
    use Inet\Proxy\Mail\iMail;
    use UmiCms\Service;
    use Mailgun\Mailgun;
    use Mailgun\HttpClientConfigurator;

    class gunMail implements iMail
    {
        /**
         * Отправляет письмо через MailGun
         *
         * @param mixed $emails
         * @param string $subject
         * @param string $content
         * @param string $filePath
         * @param array $tags
         * @return bool|\Mailgun\Model\Message\SendResponse
         * @throws coreException
         */
        public function sendMail($emails = null, $subject = '', $content = 'default', $filePath = '', $tags = []) {
            // loading custom config file
            $dataModule = cmsController::getInstance()->getModule('data');
            $config = $dataModule->loadCustomConfig();
        
            $umiRegistry = Service::Registry();
            $apiKey = $config->get('mailgun', 'API-Key');
            $apiPubKey = $config->get('mailgun', 'API-Pub-Key');
            $domain = Service::DomainDetector()->detectHost();
            $emailFrom = "{$umiRegistry->get('//settings/fio_from')} <{$umiRegistry->get("//settings/email_from")}>";
            $attachment = $filePath ? [['filePath' => $filePath]] : [];
    
            $emails = explode(',', $emails);
            if (empty($emails))
                throw new Exception('Empty Email list');
            
            foreach ($emails as $email) {
                $configurator = new HttpClientConfigurator();
                $configurator->setEndpoint("https://api.eu.mailgun.net/v3/$domain/messages");
                $configurator->setApiKey($apiKey);
                $configurator->setDebug(true);
    
                $mg = Mailgun::configure($configurator);
                
                //email validation on Mailgun
                $mgValClient = new Mailgun($apiPubKey);
                $valResult = $mgValClient->get("address/validate", ['address' => $email]);
                $isValid = $valResult->http_response_body->is_valid;
    
                $result = false;
                if ($isValid){
                    try {
                        $result = $mg->messages()->send($domain, [
                            'from'    => $emailFrom,
                            'to'      => $email,
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
    }