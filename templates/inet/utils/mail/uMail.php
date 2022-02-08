<?php

use Inet\Proxy\Mail\iMail;
use UmiCms\Service;

class uMail implements iMail
{
    /**
     * Отправляет письмо через UmiMail
     *
     * @inheritdoc
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

        $emails = is_array($emails) ? $emails : [$emails];
        foreach ($emails as $address) {
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