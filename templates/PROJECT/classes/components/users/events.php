<?php
/**
 * Created by PhpStorm.
 * User: support@inetstudio.ru
 * Date: 05.02.2022
 * Time: 02:11
 */

use UmiCms\Service;

$eventHandlerFactory = Service::EventHandlerFactory();

$eventHandlerFactory->createForModuleByConfig([
    /** Обработчики событий, которые отвечают за работу с отправкой писем */
    [
        'event' => 'usersSendMail',
        'method' => 'onSendMailEvent'
    ],
    /** Обработчики событий "cron" */
    [
        'event' => 'cron',
        'method' => 'dummyMethodOnCron'
    ],
], [
    'module' => 'users',
]);
