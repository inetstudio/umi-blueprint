<?php
class userEventsStatistics extends baseTables {
    /** @inheritdoc  */
    protected array $tableColumns = [
        "obj_id" => "obj_id",
        "user" => "int",
        "name" => "string",
        "uuid" => "string",
        "type" => "string",
        "progress" => "text",
        "points" => "float",
        "begin_date" => "date",
        "end_date" => "date",
    ];

    /** @inheritdoc */
    protected string $tableNamePrefix = 'user_events_stats';

    /** @inheritdoc */
    protected int $tableDivideFrequency = 6;
}