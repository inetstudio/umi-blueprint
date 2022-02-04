<?php
/**
 * Created by PhpStorm.
 * User: support@inetstudio.ru
 * Date: 15.11.2018
 * Time: 18:33
 */

use UmiCms\Service;

$eventHandlerFactory = Service::EventHandlerFactory();

$eventHandlerFactory->createForModuleByConfig([
    /** Обработчики событий, которые отвечают за работу с записями в справочниках */
    [
        'event' => 'systemCreateObject',
        'method' => 'onEntryCreateEvent'
    ],
    [
        'event' => 'systemDeleteObject',
        'method' => 'onEntryDeleteEvent'
    ],
    [
        'event' => 'systemModifyObject',
        'method' => 'onEntryModifyEvent'
    ],
    [
        'event' => 'systemModifyPropertyValue',
        'method' => 'onEntryModifyPropertyEvent'
    ],
    /** Обработчики событий "cron" */
    [
        'event' => 'cron',
        'method' => 'dummyMethodOnCron'
    ],
], [
    'module' => 'data',
]);
