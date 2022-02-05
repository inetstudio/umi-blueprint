<?php

use UmiCms\Service;

/** Класс обработчиков событий */
class UsersCustomHandlers
{
    /** @var users|UsersCustom $module */
    public $module;

    public function onSendMailEvent(iUmiEventPoint $eventPoint) {
        /** @var array[] $variables */
        $variables = $eventPoint->getRef('variables');
        $requestMethod = $eventPoint->getParam('requestMethod');

        if (is_null($requestMethod)) {
            // FIXME!!! throw an error or just report it?
            umiExceptionHandler::report(
                new Exception(__CLASS__ . "::" . __FUNCTION__ . "() request method wasn't found!")
            );

            return;
        }

        // TODO: move mails data preparation to its own structure
        switch ($requestMethod) {
            case "forgetDoJson":
                $templateData = [
                    "path" => "users/forget/default",
                    "name" => "mail_verification",
                ];
                $templateName = "users-restore-password";
                break;
            case "restore":
                $templateData = [
                    "path" => "users/restore/default",
                    "name" => "mail_password",
                ];
                $templateName = "users-new-password";
                break;
            default:
                $templateData = [];
                $templateName = "users-placeholder-mail";
        }

        $subject = null;
        $content = null;

        if ($this->module->isUsingUmiNotifications()) {
            $this->getMailFieldsUsingUmiNotifications($variables, $templateName, $subject, $content);
        } else {
            $this->getMailFieldsUsingTemplateParser($variables, $templateData, $subject, $content);
        }

        if ($subject && $content)
            $this->module->getMailType()->sendMail($variables['email'] ?? [], $subject, $content);
    }

    /**
     * @return void
     */
    public function dummyMethodOnCron(iUmiEventPoint $eventPoint) {
        if ($eventPoint->getMode() == "before") {
            //
        }

        if ($eventPoint->getMode() == "after") {
            //
        }
    }

    /**
     * @param array $variables
     * @param string $templateName
     * @param $subject
     * @param $content
     * @return void
     * @throws Exception
     */
    private function getMailFieldsUsingUmiNotifications(array $variables, string $templateName, &$subject, &$content): void {
        $mailNotifications = Service::MailNotifications();
        $notification = $mailNotifications->getCurrentByName("notification-$templateName");

        $user = umiObjectsCollection::getInstance()->getById($variables['userId'] ?? 0);
        $objectList = [$user];
        if ($notification instanceof MailNotification) {
            $subjectTemplate = $notification->getTemplateByName("$templateName-subject");
            $contentTemplate = $notification->getTemplateByName("$templateName-content");

            if ($subjectTemplate instanceof MailTemplate) {
                $subject = $subjectTemplate->parse($variables, $objectList);
            }

            if ($contentTemplate instanceof MailTemplate) {
                $content = $contentTemplate->parse($variables, $objectList);
            }
        }
    }

    /**
     * @param array $variables
     * @param array $templateData
     * @param $subject
     * @param $content
     * @return void
     */
    private function getMailFieldsUsingTemplateParser(array $variables, array $templateData, &$subject, &$content): void {
        $userId = $variables['userId'] ?? 0;
        $templateData['path'] = $templateData['path'] ?? 'users/global/default';
        $templateData['name'] = $templateData['name'] ?? 'placeholder_mail';

        try {
            list($subjectTemplate, $contentTemplate) = users::loadTemplatesForMail(
                $templateData['path'],
                "{$templateData['name']}_subject",
                $templateData['name']
            );
            $subject = users::parseTemplateForMail($subjectTemplate, $variables, false, $userId);
            $content = users::parseTemplateForMail($contentTemplate, $variables, false, $userId);
        } catch (Exception $e) {
            // nothing
        }
    }
}
