<?php
    namespace Inet\Proxy\Mail;

    /**
     * Интерфейс хранилища
     * @package Inet\Proxy\Vaults
     */
    interface iMail
    {
        /**
         * @param string|null $key
         * @return mixed
         */
        public function sendMail($emails = null, $subject = '', $content = 'default', $filePath ='', $tags = []);
        
    }