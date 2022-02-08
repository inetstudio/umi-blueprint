<?php

use UmiCms\Service;
use UmiCms\System\iMailNotification;

/**
 * Created by Maxim Rakhmankin
 *
 * @author    Maxim Rakhmankin <support@inetstudio.ru>
 * @copyright Copyright (c) 2021, Maxim Rakhmankin
 */

/**
 * Class for events email notifications
 */
final class MailNotificationsExtension extends TypeExtendingInstaller implements ITypeExtension
{
    private int $defaultLangId;
    private int $defaultDomainId;

    /**
     * INITIALISATION
     */
    private function init() {
        $this->defaultLangId = Service::LanguageCollection()->getDefaultLang()->getId();
        $this->defaultDomainId = Service::DomainCollection()->getDefaultDomain()->getId();
    }

    /**
     * @param iMailNotification|null $notification
     */
    private function deleteNotificationTemplatesAndSelf(?iMailNotification $notification): void {
        $templates = $notification->getTemplates();
        foreach ($templates as $template) {
            $this->mailTemplates->deleteById($template->getId());
        }
        $this->mailNotifications->deleteById($notification->getId());
    }

    /**
     * @param string $name
     * @param string $contentBody
     * @param bool $cleanUp
     * @throws Exception
     */
    protected function createNewNotification(string $name = 'default', string $contentBody = '', bool $cleanUp = false) {
        self::init();

        $mailNotificationsMap = $this->mailNotifications->getMap();
        $mailTemplatesMap = $this->mailTemplates->getMap();

        $notificationName = "notification-$name";

        $notification = $this->mailNotifications->getCurrentByName($notificationName);
        if ($notification instanceof MailNotification) {
            if ($cleanUp) $this->deleteNotificationTemplatesAndSelf($notification);

            return;
        }

        // create notification mail structure
        $newNotification = $this->mailNotifications->create([
            $mailNotificationsMap->get('LANG_ID_FIELD_NAME')   => $this->defaultLangId,
            $mailNotificationsMap->get('DOMAIN_ID_FIELD_NAME') => $this->defaultDomainId,
            $mailNotificationsMap->get('NAME_FIELD_NAME')      => $notificationName,
            $mailNotificationsMap->get('MODULE_FIELD_NAME')    => 'content',
        ]);

        // create notification mail header
        $this->mailTemplates->create([
            $mailTemplatesMap->get('NOTIFICATION_ID_FIELD_NAME') => $newNotification->getId(),
            $mailTemplatesMap->get('NAME_FIELD_NAME')            => "$name-subject",
            $mailTemplatesMap->get('TYPE_FIELD_NAME')            => 'subject',
            $mailTemplatesMap->get('CONTENT_FIELD_NAME')         => '%header%',
        ]);

        // create notification mail body
        $this->mailTemplates->create([
            $mailTemplatesMap->get('NOTIFICATION_ID_FIELD_NAME') => $newNotification->getId(),
            $mailTemplatesMap->get('NAME_FIELD_NAME')            => "$name-content",
            $mailTemplatesMap->get('TYPE_FIELD_NAME')            => 'content',
            $mailTemplatesMap->get('CONTENT_FIELD_NAME')         => $contentBody,
        ]);
    }

    public function createCallbackMailNotification() {
        $notificationName = 'callback';
        $contentBody = <<<CONTENT
<p>Поступила новая форма обратной связи.</p>
<div>
    <h1>Данные отправителя:</h1>
    <p>ФИО: %person_name%</p>
    <p>Телефон: %person_phone%</p>
</div>
CONTENT;

        $this->createNewNotification($notificationName, $contentBody);
        $this->buffer->push(__FUNCTION__ . ": " . true . PHP_EOL);
    }

    public function createContactsMailNotification() {
        $notificationName = 'contacts';
        $contentBody = <<<CONTENT
<p>Поступила новая форма со страницы контактов.</p>
<div>
    <h1>Данные отправителя:</h1>
    <p>ФИО: %person_name%</p>
    <p>E-mail: %person_email%</p>
    <p>Текст обращения: %text_message%</p>
</div>
CONTENT;

        $this->createNewNotification($notificationName, $contentBody);
        $this->buffer->push(__FUNCTION__ . ": " . true . PHP_EOL);
    }


    public function execute() {
        $this->createCallbackMailNotification();
        $this->createContactsMailNotification();
    }
}