<?php
abstract class baseTables {
    /** @var array $tableColumns Поля текущей таблицы */
    protected array $tableColumns;

    /* @var string Префикс имени таблицы для хранения данных */
    protected string $tableNamePrefix = 'cms3_base_table';

    /** @var bool Использовать разделитель в названии таблиц (добавляет год и квартал в название) */
    protected bool $useDivider = true;

    /* @var int Относительная частота разделения таблицы за год (кратна 3-м, чем выше - тем реже)  */
    protected int $tableDivideFrequency = 3;

    /** @var selectorWhereSysProp[] Свойства, по которым идет выборка */
    protected array $whereProps = [];

    /** @var int Ограничение на количество записей */
    protected int $limit;

    /** @var int Отступ в выборке записей */
    protected int $offset;

    /** @var string $tableName Имя таблицы в бд, которая будет хранить данные */
    private string $tableName;

    /** @var string $frequencyPostfix Постфикс имени таблицы, содержащий период её создания */
    private string $frequencyPostfix;

    /* @const драйвер MySQL */
    const MYSQL_TABLE_ENGINE = 'InnoDB';


    /**
     * Конструктор
     * @throws publicException
     */
    public function __construct() {
        // prepare table for current entity
        $this->computeTableName();
    }

    /**
     * Возвращает имя таблицы в бд
     * @return string
     */
    public function getTableName(): string {
        return $this->tableName;
    }

    /**
     * Вычисляет имя таблицы, на основании переданной временной отметки
     * и разделителя, для сегментирования таблиц по-квартально
     * @param int|null $divider
     * @param null     $timePoint
     * @throws publicException
     */
    public function computeTableName(int $divider = null, $timePoint = null) {
        // set divide frequency for year
        $this->setFrequencyPostfix($divider, $timePoint);
        // set table name based on previous fields
        $this->setTableName();
    }

    /**
     * Добавляет запись в таблицу
     * @param array $record
     * @throws databaseException
     * @throws publicException
     */
    public function addTableRecord(array $record) {
        if ($this->checkIfTableExists() == false) {
            $this->createTable();
        }

        $this->saveRecordData(0, $record);
    }

    /**
     * Обновляет запись в таблице по переданному id
     * @param int|null $recordId
     * @param array    $record
     * @throws databaseException
     * @throws publicException
     */
    public function updateTableRecord(int $recordId = null, array $record = []) {
        if ($this->checkIfTableExists() == false) {
            $this->createTable();
        }

        $this->saveRecordData($recordId, $record);
    }

    /**
     * Указать фильтр по полю
     * @param string $fieldName Название поля
     * @return selectorWhereSysProp|null
     * @throws publicException Если поле выбрано неверно или не существует
     */
    public function where(string $fieldName): selectorWhereSysProp {
        $columns = array_keys($this->getTableColumns());
        if (!in_array($fieldName, $columns)) {
            throw new publicException(__METHOD__ . ": table columns `tableColumns` must contain field `$fieldName`");
        }

        return $this->whereProps[] = new selectorWhereSysProp($fieldName);
    }

    /**
     * Ограничить количество результатов выборки
     * @param int $offset отступ
     * @param int $limit Нужное число результатов или ключевое слово,
     * отключающее ограничение - config.ini [kernel] grab-all-keyword
     * @return $this
     */
    public function limit(int $offset, int $limit = 0): baseTables {
        if ($limit === KEYWORD_GRAB_ALL) {
            return $this;
        }

        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    /**
     * Выводит данные последних записей текущей таблицы
     * @return array
     * @throws databaseException
     * @throws publicException
     * @throws selectorException
     */
    public function getTableRecords(): array {
        $tableName = $this->getTableName();
        $conditions = $this->buildConditions();
        $limit = $this->buildLimit();

        $sql = <<<SQL
SELECT * FROM `$tableName` WHERE $conditions $limit
SQL;
        $result = self::tryQueryResult($sql);

        return $this->transformRecordsData($result);
    }

    /**
     * @param string $field
     * @param null   $value
     * @throws databaseException
     * @throws publicException
     */
    public function deleteTableRecordByFieldValue(string $field, $value = null) {
        $tableName = $this->getTableName();
        $value = $this->prepareValue($value);
        $sql = <<<SQL
DELETE FROM `$tableName` WHERE `$field` = $value
SQL;
        self::tryQueryResult($sql);
    }

    /**
     * Удаляет таблицу.
     * @throws databaseException
     */
    protected function dropTable() {
        $tableName = $this->getTableName();
        $sql = <<<SQL
DROP TABLE IF EXISTS `$tableName`
SQL;
        self::tryQueryResult($sql);
    }

    /**
     * Проверяет существует ли таблица для хранения значений полей
     * @param string|null $tableName
     * @return bool
     * @throws databaseException
     */
    protected function checkIfTableExists(string $tableName = null): bool {
        $secondaryTableName = $tableName ?: $this->getTableName();

        $sql = <<<SQL
SHOW TABLES LIKE '{$secondaryTableName}'
SQL;
        $result = self::tryQueryResult($sql);

        return $result->length() > 0;
    }

    /**
     * @param string $sql
     * @return IQueryResult
     * @throws databaseException
     * @throws publicException
     */
    protected static function tryQueryResult(string $sql): ?IQueryResult {
        $connection = ConnectionPool::getInstance()->getConnection();
        try {
            $result = $connection->queryResult($sql);
        } catch (databaseException $e) {
            throw new publicException(__METHOD__ . ': MySQL exception has occurred:' . $e->getCode() . ' ' . $e->getMessage());
        }

        return $result;
    }

    /**
     * @param       $mode
     * @param       $value
     * @param false $column
     * @return string
     * @throws selectorException
     */
    protected function parseValue($mode, $value, $column = false): string {
        switch ($mode) {
            case 'equals':
                switch (true) {
                    case (is_array($value) || is_object($value)): {
                        $value = $this->prepareValue($value);
                        if (umiCount($value)) {
                            return ' IN(' . implode(', ', $value) . ')';
                        }

                        return ' = 0 = 1';  // Impossible value to reset query result to zero
                    }
                    default: {
                        return ' = ' . $this->prepareValue($value);
                    }
                }

            case 'notequals':
                if (is_array($value) || is_object($value)) {
                    $value = $this->prepareValue($value);
                    if (umiCount($value)) {
                        return ' NOT IN(' . implode(', ', $value) . ')' . ($column ? " OR {$column} IS NULL" : '');
                    }

                    return ' = 0 = 1';  // Impossible value to reset query result to zero
                }

                return ' != ' . $this->prepareValue($value) . ($column ? " OR {$column} IS NULL" : '');

            case 'like':
                if (is_array($value)) {
                    $conditionList = [];

                    foreach ($value as $item) {
                        $conditionList[] = $column . ' ' . $this->parseValue($mode, $item, $column);
                    }

                    if (count($conditionList) > 0) {
                        return ltrim(implode(' AND ', $conditionList), $column);
                    }

                    return ' = 0  AND ' . $column . ' = 1'; // Impossible value to reset query result to zero
                }

                return ' LIKE ' . $this->prepareValue($value);

            case 'ilike':
                return $this->parseValue('like', $value, $column);

            case 'more':
                if (is_array($value)) {
                    throw new selectorException(__METHOD__ . ": method `$mode` can't accept array");
                }

                return ' > ' . $this->prepareValue($value);

            case 'eqmore':
                if (is_array($value)) {
                    throw new selectorException(__METHOD__ . ": method `$mode` can't accept array");
                }

                return ' >= ' . $this->prepareValue($value);

            case 'less':
                if (is_array($value)) {
                    throw new selectorException(__METHOD__ . ": method `$mode` can't accept array");
                }

                return ' < ' . $this->prepareValue($value);

            case 'eqless':
                if (is_array($value)) {
                    throw new selectorException(__METHOD__ . ": method `$mode` can't accept array");
                }

                return ' <= ' . $this->prepareValue($value);

            case 'between':
                return ' BETWEEN ' . $this->prepareValue($value[0]) . ' AND ' . $this->prepareValue($value[1]);

            case 'isnotnull':
                $value = ($value === null) ? true : $value;
                return !$value ? ' IS NULL' : ' IS NOT NULL';

            case 'isnull':
                $value = ($value === null) ? true : $value;
                return $value ? ' IS NULL' : ' IS NOT NULL';

            default:
                throw new selectorException(__METHOD__ . ": unsupported field mode `$mode`");
        }
    }

    /**
     * Создает таблицу
     * @return void
     * @throws Exception
     */
    private function createTable(): void {
        $columnsDefinitions = $this->getColumnsDefinitions();
        $columnsDefinitionsSql = implode('', $columnsDefinitions);

        $tableName = $this->getTableName();
        $engine = self::MYSQL_TABLE_ENGINE;
        $sql = <<<SQL
CREATE TABLE `$tableName` (
	`id` int(10) unsigned NOT NULL auto_increment,
	$columnsDefinitionsSql
	PRIMARY KEY (`id`)
)ENGINE=$engine DEFAULT CHARSET=utf8;
SQL;
        self::tryQueryResult($sql);
    }

    /**
     * Возвращает команды на создание строк таблицы
     * @return array
     * @throws publicException если не удалось получить команду для какого-либо поля
     */
    private function getColumnsDefinitions(): array {
        $tableColumns = $this->getTableColumns();

        foreach ($tableColumns as $key => $value) {
            $definition = $this->getCustomColumnDefinition($key, $value);

            if ($definition === null) {
                throw new publicException(__METHOD__ . ": cant get definition for column with name: $key");
            }

            $tableColumns[$key] = $definition;
        }

        return $tableColumns;
    }

    /**
     * Возвращает команду на создание колонки для обычного поля таблицы
     * @param string $columnName имя обычного поля таблицы
     * @param string $columnType тип данных для создания поля внутри таблицы
     * @return string|null
     * @throws publicException поле с именем $columnName неподдерживаемого типа данных
     */
    private function getCustomColumnDefinition(string $columnName, string $columnType): ?string {
        if (!is_string($columnName)) {
            throw new publicException(__METHOD__ . ": correct column name expected, $columnName given");
        }

        switch ($columnType) {
            case 'obj_id':
            case 'type_id':
            case 'user_id':
            case 'parent_id': {
                return "`$columnName` int(10) unsigned NOT NULL, ";
            }
            case 'date':{
                return "`$columnName` DATETIME DEFAULT NULL, ";
            }
            case 'int': {
                return "`$columnName` bigint(20) DEFAULT NULL, ";
            }
            case 'string': {
                return "`$columnName` varchar(255) DEFAULT NULL, ";
            }
            case 'float': {
                return "`$columnName` double DEFAULT NULL, ";
            }
            case 'boolean':
            case 'file': {
                return "`$columnName` tinyint(1) DEFAULT 0, ";
            }
            case 'text': {
                return "`$columnName` mediumtext DEFAULT NULL, ";
            }
            default: {
                throw new publicException(__METHOD__ . ": unsupported field type: $columnType");
            }
        }
    }
    
    /**
     * Получает все существующие таблицы, данного типа
     * (по кварталам)
     *
     * @return array
     */
    private function getAllExistingTables(): array {
        $quarter = $this->getDividerPostfix();
        $year = date('y');
        $nameTables = [];
        
        do {
            if (!$quarter) {
                $quarter = 4;
                $year--;
            }
            
            $tableName = $this->tableNamePrefix . $quarter . '_' . $year;
            $quarter--;
            if (!$this->checkIfTableExists($tableName)) continue;
            
            $nameTables[] = $tableName;
        } while ($year >= 20);
        
        return $nameTables;
    }

    /**
     * Обновляет или создает запись для сущности
     * @param int   $entityId идентификатор сущности
     * @param array $data данные, которые требуется сохранить
     * @return void
     * @throws publicException если запрос к бд завершился ошибкой
     * @throws Exception
     */
    private function saveRecordData(int $entityId, array $data): void {
        // TODO: notice
        $tableName = $this->getTableName();
        $data = self::prepareDataTypeForSQL($data);
        $data = array_map([__CLASS__, 'prepareValue'], $data);
        $columnNames = array_keys($data);
        $columns = '`id`, `' . implode('`, `', $columnNames) . '`';
        $values = "$entityId, " . implode(', ', $data);

        $sql = <<<SQL
REPLACE INTO `$tableName` ($columns) VALUES ($values)
SQL;

        self::tryQueryResult($sql);
    }

    /**
     * @param array $data
     * @return array
     */
    private function prepareDataTypeForSQL(array $data): array {
        $columns = $this->getTableColumns();
        array_walk($data, function (&$field, $key) use ($columns) {
            if ($columns[$key] == "date" && is_numeric($field)) {
                $field = $this->returnMYSQLDateTime($field);
            }
        });

        return array_filter($data);
    }

    /**
     * @param IQueryResult $result
     * @return array
     */
    private function transformRecordsData(IQueryResult $result): array {
        $result->setFetchAssoc();

        $recordsData = [];
        while($row = $result->fetch()) {
            $recordsData[] = $row;
        }

        return $recordsData;
    }
    
    /**
     * Возвращает данные по полям из таблицы
     *
     * @param string $table
     * @param string $condition
     * @param array  $fields
     * @param string $orderLimit
     * @return array
     */
    public function getDataFromTables(string $table, string $condition, array $fields = ['id_inner'], string $orderLimit = ''): array {
        $connectionPool = ConnectionPool::getInstance();
        $connection = $connectionPool->getConnection();
        $data = [];
        
        $tablesNames = $this->getAllNamesTables($table);
        if (empty($tablesNames))
            return $data;
        
        $fields = implode(', ', $fields);
        $fields = $connection->escape($fields);
        $orderLimit = $connection->escape($orderLimit);

        $sql = [];
        foreach ($tablesNames as $tableName)
            $sql[] = "SELECT $fields FROM `$tableName` WHERE $condition";
        $sql = implode (' UNION ', $sql) . ' ' . $orderLimit;

        $result = $connection->queryResult($sql);
        
        if (!$result->length())
            return $data;
        
        while ($row = $result->fetch())
            $data[] = $row;
        
        return $data;
    }

    /**
     * Возвращает дату в формате MySql
     *
     * @param int $date
     * @return false|string
     */
    public function returnMYSQLDateTime(int $date) {
        return date('Y-m-d H:i:s', $date);
    }

    /**
     * Устанавливает постфикс имени текущей таблицы, содержащий период её создания
     * @param int|null $divider
     * @param null     $timePoint Точка отсчёта во времени для определения привязки к таблице
     */
    protected function setFrequencyPostfix(int $divider = null, $timePoint = null) {
        $thisQuarter = $this->getDividerPostfix($divider, $timePoint);
        $thisYear = date('y', $timePoint ?: time());

        $this->frequencyPostfix = $this->useDivider ? $thisQuarter . '_' . $thisYear : '';
    }

    /**
     * Возвращает текущую частоту разбиения за год
     * @return string
     */
    private function getFrequencyPostfix(): string {
        return $this->frequencyPostfix;
    }

    /**
     * Устанавливает имена таблиц
     * @param string $postfix Постфикс имени таблицы
     * @throws publicException Если $postfix не является строкой
     */
    private function setTableName(string $postfix = '') {
        if (!is_string($postfix)) {
            throw new publicException(__METHOD__ . ': table name postfix should be a string');
        }

        $this->tableName =
            $this->tableNamePrefix . '_' . $this->getFrequencyPostfix() . $postfix;
    }

    /**
     * Подготавливает данные для записи в бд
     * @param mixed $value данные
     * @return mixed
     * @throws Exception
     */
    private function prepareValue($value) {
        if (is_array($value)) {
            foreach ($value as $i => $val) {
                $value[$i] = $this->prepareValue($val);
            }
            return $value;
        }
        
        if (is_string($value)) {
            $connection = ConnectionPool::getInstance()->getConnection();
            return "'" . $connection->escape($value) . "'";
        }

        return $value;
    }

    /**
     * @return array
     * @throws publicException
     */
    private function getTableColumns(): array {
        if (empty($this->tableColumns)) {
            throw new publicException(__METHOD__ . ': table columns `$tableColumns` should be overridden from inherited class');
        }

        return $this->tableColumns ?? [];
    }

    /**
     * @return string
     */
    private function buildLimit(): string {
        if ($this->offset || $this->limit) {
            return " LIMIT $this->offset, $this->limit";
        }

        return "";
    }

    /**
     * @return string
     * @throws selectorException
     */
    private function buildConditions(): string {
        $whereConditions = [];

        foreach ($this->whereProps as $whereProp) {
            $propName = $whereProp->name;
            $whereConditions[$propName] = $this->buildWhereProp($whereProp);
        }

        return $whereConditions ? implode(' AND ', $whereConditions) : "";
    }

    /**
     * Формирует часть SQL запроса с фильтром по значению поля
     * @param selectorWhereSysProp $prop фильтр по значению поля
     * @return string
     * @throws selectorException
     */
    private function buildWhereProp(selectorWhereSysProp $prop): string {
        $name = $prop->name;
        $propertyCondition = $this->parseValue($prop->__get('mode'), $prop->__get('value'), $name);

        $sql = $name . $propertyCondition;
        return ($prop->__get('mode') == 'notequals') ? "($sql)" : $sql;
    }

    /**
     * @param int|null $divider
     * @param int|null $timePoint
     * @return int
     */
    private function getDividerPostfix(?int $divider = null, int $timePoint = null): int {
        // TODO: make divider account frequency in the right way, now it's really just a divide
        $divider = $divider ?: $this->tableDivideFrequency;

        return (int) ceil(date('n', $timePoint ?: time()) / $divider);
    }
}