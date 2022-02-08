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

        $user = umiObjectsCollection::getInstance()->getById($variables['userId'] ?? 0);
        try {
            UsersCustom::setVariables($variables);
            $response = $this->module->sendNotificationMailFor($user, $requestMethod);
        } catch (Exception $exception) {
            // TODO: make errors handling ?!
        }
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
}
