<?php

namespace Inet\Proxy\Mail;

/**
 * Интерфейс почтовой службы
 *
 * @package Inet\Proxy\Vaults
 */
interface iMail
{
    /**
     * @param array $emails    Почтовый адрес / список адресов в виде массива
     * @param string $subject  Тема письма
     * @param string $content  Содержимое письма (html5)
     * @param string $filePath Путь к файлу, прикрепляемому к письму
     * @param array $tags      Теги для внутренних систем почтовых сервисов
     * @return mixed
     */
    public function sendMail($emails = [], $subject = '', $content = 'default', $filePath = '', $tags = []);
}