<?php
    use Inet\Proxy\Mail\iMail;
    use UmiCms\Service;

    class uMail implements iMail
    {
        /**
         * Отправляет письмо через UmiMail
         *
         * @param string $emails
         * @param string $subject
         * @param string $content
         * @param string $filePath
         * @param array $tags
         * @return mixed
         * @throws Exception
         */
        public function sendMail($emails = 'ms@inetstudio.ru', $subject = 'Test', $content = 'Test', $filePath = '', $tags = []) {
            $regedit = Service::Registry();
            $emailFrom = $regedit->get("//settings/email_from");
            $fioFrom = $regedit->get("//settings/fio_from");
        
            $uMail = new umiMail();
            $uMail->setFrom($emailFrom, $fioFrom);
            $uMail->setSubject($subject);
            $uMail->setContent($content);
    
            foreach (explode(',', $emails) as $address) {
                $address = trim($address);
                if ($address)
                    $uMail->addRecipient($address);
            }
            
            if ($filePath) {
                $file = new UmiFile($filePath);
                $uMail->attachFile($file);
            }
            
            $uMail->commit();
        
            return $uMail->send();
        }
    }