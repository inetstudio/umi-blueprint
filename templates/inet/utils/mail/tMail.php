<?php
    use Inet\Proxy\Mail\iMail;

    /**
     * Trait tMail
     * Трейт выбора способа отправки почты
     */
    trait tMail
    {
        /** @var array $availableMailTypes Способы отправки почты */
        private static array $availableMailTypes = [
            'uMail' => uMail::class,
            'gunMail' => gunMail::class,
            'responseMail' => getResponseMail::class,
        ];

        /** @var iMail $mailType Строитель сущностей */
        private iMail $mailType;

        /**
         * @param string|null $proxy
         * @throws publicException
         */
        public function setMailTypeFromConfig(string $proxy = null) {
            // load template config
            $config = cmsController::getInstance()->getModule('data')->loadCustomConfig();
            $mailType = $proxy ?: $config->get('mail-proxy', 'type');

            $this->setMailTypeProxy($mailType);
        }

        /**
         * @param string $mailType
         * @throws publicException
         */
        public function setMailTypeProxy(string $mailType): void {
            if (!array_key_exists($mailType, self::$availableMailTypes)) {
                throw new publicException("Mail type `{$mailType}` doesn't exist!");
            }

            $this->setMailType(new self::$availableMailTypes[$mailType]);
        }

        /**
         * Возвращает тип почты
         * @return iMail
         */
        protected function getMailType(): iMail {
            if (! isset($this->mailType)) $this->setMailTypeFromConfig();
            return $this->mailType;
        }

        /**
         * Устанавливает тип почты
         * @param iMail $mailType
         * @return $this
         */
        protected function setMailType(iMail $mailType) {
            $this->mailType = $mailType;
            return $this;
        }
    }