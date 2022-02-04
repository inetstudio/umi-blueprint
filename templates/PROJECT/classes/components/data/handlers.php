<?php
	use UmiCms\Service;

	/** Класс обработчиков событий */
	class DataCustomHandlers {
		/** @var data|DataCustom $module */
		public $module;

        /** @var iUmiObjectTypesCollection $objectTypes */
        private iUmiObjectTypesCollection $objectTypes;

        /**
         * DataCustomHandlers constructor.
         */
        public function __construct() {
            $this->objectTypes = umiObjectTypesCollection::getInstance();
        }

        /**
         * Обработчик события добавления записей, связанных с начислением баллов.
         * На основе записей сделанных в справочниках, создаёт запросы
         * в сервис по работе с баллами пользователя.
         *
         * @param iUmiEventPoint $event Событие успешного завершения добавления записи
         * @throws coreException
         */
        public function onEntryCreateEvent(iUmiEventPoint $event) {
            if ($event->getMode() == "after") {
                /** @var umiObject $object */
                $object = $event->getRef('object');
                $hierarchyTypeId = $this->objectTypes->getTypeIdByHierarchyTypeName("content", "page");
                $guidTypeId = $this->objectTypes->getTypeIdByGUID('content-page');

                switch ($object->getTypeId()) {
                    // prizes entry, which lead to points subtract
                    case $hierarchyTypeId:
                        $user = static::checkTypeOfValue($object, 'user');
                        if ($user) {
                            // do something...
                        }
                        break;
                    // action entry, which lead to points entry
                    case $guidTypeId:
                        $user = self::checkTypeOfValue($object, 'user');
                        if ($user) {
                            // do something...
                        }
                        break;
                    default:
                        // do something...
                }
            }
        }

        /**
         * Обработчик события удаления записей из справочников.
         *
         * @param iUmiEventPoint $event Событие успешного завершения удаления записи
         * @throws coreException
         * @throws selectorException
         */
        public function onEntryDeleteEvent(iUmiEventPoint $event) {
            /** @var umiObject $object */
            $object = $event->getRef('object');
            $usersTypeId = $this->objectTypes->getTypeIdByHierarchyTypeName("users", "user");
            $hierarchyTypeId = $this->objectTypes->getTypeIdByHierarchyTypeName("content", "page");

            if ($event->getMode() == "before") {
                switch ($object->getTypeId()) {
                    case $usersTypeId:
                        $userId = $object->getId();
                        self::cleanUserGuideDependencies($userId, [$hierarchyTypeId]);

                        break;
                    default:
                        // do something...
                }
            }
        }

        /**
         * Обработчик события изменения записи в справочнике.
         *
         * @param iUmiEventPoint $event Событие успешного завершения изменения записи
         * @throws coreException
         */
        public function onEntryModifyEvent(iUmiEventPoint $event) {
            /** @var umiObject $object */
            $object = $event->getRef('object');
            $hierarchyTypeId = $this->objectTypes->getTypeIdByHierarchyTypeName("content", "page");

            // сверяем тип данных измененного объекта
            switch ($object->getTypeId()) {
                case $hierarchyTypeId:
                    if ($event->getMode() == "after") {
                        $user = self::checkTypeOfValue($object, 'user');
                        if ($user && $object->getValue('status') == $this->module->getEntryStatus('approved')) {
                            // do something...
                        }
                    }
                    break;
                default:
                    // do something...
            }
        }

        /**
         * Обработчик события изменения записи через табличный контроллер админки.*
         *
         * @param iUmiEventPoint $event Событие успешного завершения изменения записи
         * @throws coreException
         */
        public function onEntryModifyPropertyEvent(iUmiEventPoint $event) {
            /** @var umiObject $object */
            $object = $event->getRef('entity');
            $hierarchyTypeId = $this->objectTypes->getTypeIdByHierarchyTypeName("content", "page");

            switch ($object->getTypeId()) {
                case $hierarchyTypeId:
                    $user = self::checkTypeOfValue($object, 'user');
                    if ($user && $event->getMode() == "after" && $event->getParam('property') == 'status'
                        && $event->getParam('newValue') == $this->module->getEntryStatus('approved')) {
                        // do something...
                    }
                    break;
                default:
                    // do something...
            }
        }

        /**
         * If we get field value from a default auth
         * we will get the umiObject instance reference,
         * but if we get it under the root (admin) auth - it will return a numeric,
         * therefore we need to check type of the value
         * @fallback for older UMI versions
         *
         * @param iUmiObject $object
         * @param string     $fieldName
         * @return bool|umiObject
         */
        protected static function checkTypeOfValue(iUmiObject $object, string $fieldName = '') {
            $property = $object->getValue($fieldName);
            if (is_numeric($property)) {
                $objects = umiObjectsCollection::getInstance();
                $property = $objects->getObject($property);
            }

            return $property;
        }

        /**
         * @return void
         */
        public function dummyMethodOnCron(iUmiEventPoint $event) {
            if ($event->getMode() == "before") {
                //
            }

            if ($event->getMode() == "after") {
                //
            }
        }

        /**
         * @param int|null $userId
         * @param array    $guideIds
         * @return void
         * @throws coreException
         * @throws selectorException
         */
        private static function cleanUserGuideDependencies(int $userId = null, array $guideIds = []): void {
            if (!$userId || empty($guideId)) {
                return;
            }
            $sel = new selector('objects');
            $sel->types('object-type')->id($guideIds);
            $sel->where('user')->equals($userId);

            foreach ($sel->result() as $item) {
                /** @var umiObject $item */
                $item->delete();
            }
        }
    }
