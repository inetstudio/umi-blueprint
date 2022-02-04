<?php
use UmiCms\Service;
use UmiCms\System\Auth\PasswordHash\WrongAlgorithmException;

class UsersCustom extends def_module {
    use tMail;

    /**
     * @var users|UsersMacros|UsersCustomMacros $module
     */
    public $module;
    
    /**
     * Авторизует пользователя.
     * В случае успешной авторизации производит редирект на referrer.
     *
     * @return mixed
     * @throws baseException
     * @throws coreException
     * @throws publicAdminException Если авторизация не удалась через административную панель
     */
    public function loginDoJson(): array {
        $login = htmlspecialchars(trim(getRequest('login')));
        $password = htmlspecialchars(trim(getRequest('password')));

        $user = $this->module->getUserByCredentials($login, $password);
 
        if ($user instanceof iUmiObject) {
            $event = new umiEventPoint("usersLoginDo");
            $event->setMode("before");
            users::setEventPoint($event);
    
            return $this->handleLoginSuccess($user);
        }

        return $this->handleLoginFailure($login, $password);
    }

    /**
     * Отправляет письмо с кодом активации для восстановления пароля
     * @param string $template Имя шаблона для tpl шаблонизатора
     * @return mixed
     * @throws baseException
     * @throws coreException
     * @throws selectorException
     */
    public function forgetDo(string $template = 'default'): array {
        static $macrosResult;

        $module = $this->module;

        if ($macrosResult) {
            return $macrosResult;
        }

        $forgetLogin = (string) getRequest('login');
        $hasLogin = $forgetLogin !== '';
        $userId = false;

        if ($hasLogin) {
            $sel = new selector('objects');
            $sel->types('object-type')->name('users', 'user');
            $sel->where('login')->equals($forgetLogin);
            $sel->limit(0, 1);

            if ($sel->first()) {
                $userId = $sel->first()->getId();
            }
        }

        $result = ['success' => true, 'message' => getLabel('error-new-password-sent-if-user-exist')];
        if (!$userId) {
            return $result;
        }
        
        $restoreCode = md5($module->getRandomPassword());
        $user = umiObjectsCollection::getInstance()->getObject($userId);

        $withoutActivation = (bool) Service::Registry()->get('//modules/users/without_act');

        if ($withoutActivation || (int) $user->getValue('is_activated')) {
            $user->setValue('activate_code', $restoreCode);
            $user->commit();

            $email = $user->getValue('email');
            $domain = Service::DomainDetector()->detect();
            $restoreLink = $domain->getCurrentUrl() . "/?restore_code=" . $restoreCode;

            $variables = [
                'domain'       => $domain->getCurrentHostName(),
                'restore_link' => $restoreLink,
                'login'        => $user->getValue('login'),
                'email'        => $email,
            ];
            $objectList = [$user];

            $event = new umiEventPoint("usersSendMail");
            $event->addRef("variables", $variables);
            users::setEventPoint($event);

            $subject = null;
            $content = null;

            // TODO: extract emails logic to events handlers
            if ($this->module->isUsingUmiNotifications()) {
                $mailNotifications = Service::MailNotifications();
                $notification = $mailNotifications->getCurrentByName('notification-users-restore-password');

                if ($notification instanceof MailNotification) {
                    $subjectTemplate = $notification->getTemplateByName('users-restore-password-subject');
                    $contentTemplate = $notification->getTemplateByName('users-restore-password-content');

                    if ($subjectTemplate instanceof MailTemplate) {
                        $subject = $subjectTemplate->parse($variables, $objectList);
                    }

                    if ($contentTemplate instanceof MailTemplate) {
                        $content = $contentTemplate->parse($variables, $objectList);
                    }
                }
            } else {
                try {
                    list($subjectTemplate, $contentTemplate) = users::loadTemplatesForMail(
                        'users/forget/' . $template,
                        'mail_verification_subject',
                        'mail_verification'
                    );
                    $subject = users::parseTemplateForMail($subjectTemplate, $variables, false, $userId);
                    $content = users::parseTemplateForMail($contentTemplate, $variables, false, $userId);
                } catch (Exception $e) {
                    // nothing
                }
            }

            if ($subject && $content)
                $this->getMailType()->sendMail($email, $subject, $content);

            $eventPoint = new umiEventPoint('users_restore_password');
            $eventPoint->setParam('user_id', $userId);
            users::setEventPoint($eventPoint);
            return $result;
        }

        return $result;
    }

    /**
     * @param bool $activateCode
     * @return mixed
     */
    public function checkRestore(bool $activateCode = false): array {
        if (!$activateCode) {
            $activateCode = (string) getRequest('param0');
            $activateCode = trim($activateCode);
        }

        $userId = Service::Auth()->checkCode($activateCode);
        $user = selector::get('object')->id($userId);

        if ($user instanceof iUmiObject) {
            $userId = $user->getId();
        } else {
            $userId = false;
        }

        if (!($userId && $activateCode)) {
            return ['success' => false];
        }

        return ['success' => true, 'user_id' => $userId];
    }

    /**
     * Восстанавливает доступ пользователя и отправляет ему
     * письмо с данными для доступа
     * @param null   $userId
     * @param string $template Имя шаблона для tpl шаблонизатора
     * @return mixed
     * @throws ErrorException
     * @throws WrongAlgorithmException
     * @throws baseException
     * @throws coreException
     */
    public function restore($userId = null, string $template = 'default'): array {
        $module = $this->module;
        static $result = [];

        if (isset($result[$template])) {
            return $result[$template];
        }

        if (!$userId) {
            return $module->getRestoreResult($template, false);
        }

        $user = selector::get('object')->id($userId);

        $password = $module->getRandomPassword();
        $encodedPassword = Service::PasswordHashAlgorithm()->hash($password);

        $login = $user->getValue('login');
        $email = $user->getValue('email');
        $user->setValue('password', $encodedPassword);
        $user->setValue('activate_code', '');
        $user->commit();

        $variables = [
            'domain' => getServer('HTTP_HOST'),
            'password' => $password,
            'login' => $login
        ];
        $objectList = [$user];

        $event = new umiEventPoint("usersSendMail");
        $event->addRef("variables", $variables);
        users::setEventPoint($event);

        $subject = null;
        $content = null;

        if ($this->module->isUsingUmiNotifications()) {
            $mailNotifications = Service::MailNotifications();
            $notification = $mailNotifications->getCurrentByName('notification-users-new-password');

            if ($notification instanceof MailNotification) {
                $subjectTemplate = $notification->getTemplateByName('users-new-password-subject');
                $contentTemplate = $notification->getTemplateByName('users-new-password-content');

                if ($subjectTemplate instanceof MailTemplate) {
                    $subject = $subjectTemplate->parse($variables, $objectList);
                }

                if ($contentTemplate instanceof MailTemplate) {
                    $content = $contentTemplate->parse($variables, $objectList);
                }
            }
        } else {
            try {
                list($subjectTemplate, $contentTemplate) = users::loadTemplatesForMail(
                    'users/forget/' . $template,
                    'mail_password_subject',
                    'mail_password'
                );
                $subject = users::parseTemplateForMail($subjectTemplate, $variables, false, $userId);
                $content = users::parseTemplateForMail($contentTemplate, $variables, false, $userId);
            } catch (Exception $e) {
                // nothing
            }
        }

        if ($subject && $content)
            $this->getMailType()->sendMail($email, $subject, $content);

        $eventPoint = new umiEventPoint('successfulPasswordRestoring');
        $eventPoint->setMode('after');
        $eventPoint->setParam('userId', $userId);
        $eventPoint->setParam('password', $password);
        $eventPoint->call();

        return ['status' => true];
    }

    /**
     * Возвращает список доступных групп пользователей.
     * @return array
     * @throws selectorException
     */
    public function groupsList(): array {
        $sel = new selector('objects');
        $sel->types('object-type')->name('users', 'users');
        
        if (!permissionsCollection::getInstance()->isSv()) {
            $sel->where('guid')->notequals('users-users-15');
        }
        
        $result = [];
        /** @var umiObject $item */
        foreach ($sel->result() as $item) {
            $result[$item->getId()] = $item->getName() ;
        }
        
        return $result;
    }

    /**
     * Обрабатывает результат успешной авторизации
     * в зависимости от режима работы системы.
     *
     * @param iUmiObject $user пользователь
     * @return mixed
     * @throws coreException
     * @throws Exception
     */
    private function handleLoginSuccess(iUmiObject $user): array {
        $result = [
            'success' => true,
        ];

        if (Service::Session()->get('fake-user')) {
            $this->module->restoreUser(true);
            return $result;
        }

        Service::Auth()->loginUsingId($user->getId());
        $this->module->triggerLoginSuccessEvent($user);

        return $result;
    }

    /**
     * Обрабатывает результат неудачной авторизации
     * в зависимости от режима работы системы.
     * @param string $login Введенный логин
     * @param string $password введенный пароль
     * @return mixed
     * @throws publicAdminException
     * @throws Exception
     */
    private function handleLoginFailure(string $login, string $password): array {
        $this->module->triggerLoginFailureEvent($login, $password);
        if (Service::Request()->isAdmin()) {
            throw new publicAdminException(getLabel('label-text-error'));
        }
        return ['success' => false, 'error' => getLabel('login_do_try_again')];
    }

}