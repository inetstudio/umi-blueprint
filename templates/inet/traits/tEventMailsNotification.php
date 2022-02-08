<?php

/**
 * Trait tEventMailsNotification
 * Трейт управления отправкой уведомлений на различные события.
 * Использует трейт для отправки писем.
 */
trait tEventMailsNotification
{
    use tMail;

    private ?string $subject = null;
    private ?string $content = null;

    /** @var array $variables Параметры, используемые внутри шаблона письма (по-умолчанию) */
    private static array $variables = [
        'page_header', 'page_link'
    ];

    /**
     * @return array
     */
    public static function getVariables(): array {
        return self::$variables;
    }

    /**
     * @param array $variables
     */
    public static function setVariables(array $variables): void {
        self::$variables = $variables;
    }

    /**
     * @param iUmiEntinty $entity
     * @param string $methodName
     * @return mixed
     * @throws coreException
     * @throws publicException
     */
    public function sendNotificationMailFor(iUmiEntinty $entity, string $methodName = 'default') {
        /** @var ClassConfig $classConfig */
        $classConfig = self::getConfig();
        $map = $classConfig->get($methodName);

        if (!is_array($map)) {
            throw new Exception(sprintf('Невозможно загрузить конфигурацию из метода %s', $methodName));
        }

        switch (true) {
            case $entity instanceof iUmiObject:
                static::prepareObjectVariables($entity);
                $objectList = [$entity];
                break;
            case $entity instanceof iUmiHierarchyElement:
            default:
                static::prepareHierarchyElementVariables($entity);
                $objectList = [$entity->getObject()];
        }

        // FIXME!!! do we need to check this from the trait?
        if ($this->module->isUsingUmiNotifications()) {
            $this->setMailFieldsUsingUmiNotifications($map, $objectList);
        } else {
            $this->setMailFieldsUsingTemplateParser($map['fallback-template'] ?? []);
        }

        if ($this->subject && $this->content) {
            $variables = self::getVariables();
            $mailSettings = $this->module->getMailSettings();
            $emailFrom = $mailSettings->getSenderEmail();
            $emails = $variables['recipients'] ?? $variables['email'] ?? $emailFrom;

            return $this->getMailType()->sendMail(
                $emails,
                $this->subject,
                $this->content,
                $variables['file_append'] ?? ''
            );
        }

        return false;
    }

    /**
     * @param iUmiHierarchyElement $entity
     * @return void
     */
    private static function prepareHierarchyElementVariables(iUmiHierarchyElement $entity) {
        $umiHierarchy = umiHierarchy::getInstance();
        $umiDomains = \UmiCms\Service::DomainCollection();

        $domain = $umiDomains->getDomain($entity->getDomainId());
        $pageId = $entity->getId();
        $pageLink = $domain->getUrl() . $umiHierarchy->getPathById($pageId);

        $variables = array_merge(self::getVariables(), [
            'page_header' => $entity->getName(),
            'page_link'   => $pageLink,
        ]);

        self::setVariables($variables);
    }

    /**
     * @param iUmiObject $entity
     * @return void
     */
    private static function prepareObjectVariables(iUmiObject $entity) {
        // additional transformations
    }

    /**
     * @param array $variables
     * @param string $templateName
     * @param $subject
     * @param $content
     * @return void
     * @throws Exception
     */
    private function setMailFieldsUsingUmiNotifications(array $map, array $objectList): void {
        $mailNotifications = \UmiCms\Service::MailNotifications();
        $notification = $mailNotifications->getCurrentByName($map['notification']);

        if ($notification instanceof MailNotification) {
            $subjectTemplate = $notification->getTemplateByName($map['subject-mail-template']);
            $contentTemplate = $notification->getTemplateByName($map['content-mail-template']);

            $variables = self::getVariables();

            if ($subjectTemplate instanceof MailTemplate) {
                $this->subject = $subjectTemplate->parse($variables, $objectList);
            }

            if ($contentTemplate instanceof MailTemplate) {
                $this->content = $contentTemplate->parse($variables, $objectList);
            }
        }
    }

    /**
     * @param array $template
     * @return void
     */
    private function setMailFieldsUsingTemplateParser(array $template): void {
        $variables = self::getVariables();
        $userId = $variables['userId'] ?? 0;

        $template['path'] = $template['path'] ?? 'users/global/default';
        $template['name'] = $template['name'] ?? 'placeholder_mail';

        try {
            list($subjectTemplate, $contentTemplate) = def_module::loadTemplatesForMail(
                $template['path'],
                "{$template['name']}_subject",
                $template['name']
            );
            $this->subject = def_module::parseTemplateForMail($subjectTemplate, $variables, false, $userId);
            $this->content = def_module::parseTemplateForMail($contentTemplate, $variables, false, $userId);
        } catch (Exception $e) {
            // nothing
        }
    }
}
