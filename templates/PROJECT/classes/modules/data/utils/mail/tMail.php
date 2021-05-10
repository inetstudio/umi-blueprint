<?php
    use Inet\Proxy\Mail\iMail;

    /**
     * Trait tMail
     * Трейт выбора способа отправки почты
     */
    trait tMail
    {
        /** @var array $availableMailTypes способы отправки почты */
        private static array $availableMailTypes = [
            'uMail' => uMail::class,
            'gunMail' => gunMail::class
        ];

        /** @var iMail $mailType строитель сущностей */
        private iMail $mailType;

        /**
         * @throws publicException
         */
        public function setMailTypeFromConfig() {
            /** @var data_custom $dataModule */
            $dataModule = cmsController::getInstance()->getModule('data');
            $mailType = $dataModule->loadCustomConfig()->get('mail-trait', 'engine');
    
            if (!array_key_exists($mailType, self::$availableMailTypes)) {
                throw new publicException("Mail type `{$mailType}` doesn't exist!");
            }

            $this->setMailType(new self::$availableMailTypes[$mailType]);
        }

        /**
         * Возвращает экземпляр класса для отправки почты
         * @return iMail
         */
        protected function getMailType(): iMail {
            return $this->mailType;
        }

        /**
         * Устанавливает тип класс для отправки почты
         * @param iMail $mailType хранилище ключей
         * @return $this
         */
        protected function setMailType(iMail $mailType) {
            $this->mailType = $mailType;
            return $this;
        }
    }