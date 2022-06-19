<?php
	/** Класс обработчиков событий */
	class ContentCustomHandlers {
		/** @var content|ContentCustom $module */
		public $module;

        /** @var iUmiObjectTypesCollection $objectTypes */
        private iUmiObjectTypesCollection $objectTypes;

        /** @var iUmiHierarchyTypesCollection $hierarchyTypes */
        private iUmiHierarchyTypesCollection $hierarchyTypes;

        /**
         * DataCustomHandlers constructor.
         */
        public function __construct() {
            $this->objectTypes = umiObjectTypesCollection::getInstance();
            $this->hierarchyTypes = umiHierarchyTypesCollection::getInstance();
        }

        /**
         * @param iUmiEventPoint $event
         * @return void
         */
        public function onPageCreatedEvent(iUmiEventPoint $event) {
            if ($event->getMode() == "after") {
                /** @var umiHierarchyElement $element */
                $element = $event->getRef('element');
                $hierarchyTypeId = $this->objectTypes->getTypeIdByHierarchyTypeName("content", "page");

                switch ($element->getTypeId()) {
                    // default content page
                    case $hierarchyTypeId:
                        // do something...
                        break;
                    default:
                        // do something...
                }
            }
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
    }
