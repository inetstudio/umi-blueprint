<?php

    /**
     * Interface ITypeExtension
     * Интерфейс конфигурации класса
     */
    interface ITypeExtension
    {
        /**
         * Запускает выполнение методов, расширяющих типы данных
         */
        public function execute();

        /**
         * @return int
         */
        public function getPriority(): int;
    }