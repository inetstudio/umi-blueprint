<?php
/**
 * Created by PhpStorm.
 * User: support@inetstudio.ru
 * Date: 20.06.2022
 * Time: 01:33
 */

use UmiCms\Service;

$eventHandlerFactory = Service::EventHandlerFactory();

$eventHandlerFactory->createForModuleByConfig([
    /** Обработчики событий, которые отвечают за работу со страницами контента */
    [
        'event' => 'systemCreateElement ',
        'method' => 'onPageCreatedEvent'
    ],
    /** Обработчики событий "cron" */
    [
        'event' => 'cron',
        'method' => 'dummyMethodOnCron'
    ],
], [
    'module' => 'content',
]);
