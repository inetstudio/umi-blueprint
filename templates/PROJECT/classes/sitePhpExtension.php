<?php
    use UmiCms\Service;

    /**
     * Расширение php шаблонизатора для шаблона
     */
    class sitePhpExtension extends ViewPhpExtension
    {
        /** @var string Project alias in umiSettings module */
        const SITE_SETTINGS_ALIAS = 'PROJECT';

        /** @var array $_popups an array of additional popups */
        protected array $_popups = [];

        /**
         * Инициализирует общие переменные для шаблонов.
         *
         * @param array $variables Глобальные переменные запроса
         */
        public function initializeCommonVariables(array $variables) {
            $templateEngine = $this->getTemplateEngine();
            $templateEngine->setCommonVar('domain', $variables['domain']);
            $templateEngine->setCommonVar('lang', $variables['lang']);
            $templateEngine->setCommonVar('pre_lang', $variables['pre-lang']);
            $templateEngine->setCommonVar('header', $variables['header'] ?? '');
            $templateEngine->setCommonVar('request_uri', $variables['request-uri']);
            $templateEngine->setCommonVar('seo', new SeoPhpExtension($templateEngine));
        }

        /**
         * Убирает кэш браузера с обновленных асетов (js / css / img).
         *
         * @param $location
         * @return string
         */
        public function asset($location): string {
            if (file_exists($filename = $_SERVER['DOCUMENT_ROOT'] . $location)) {
                return sprintf('%s?v.%s', $location, filemtime($filename));
            }

            return $location;
        }

        /**
         * @param int $number
         * @param string $word
         * @param bool $strToUpperCase
         * @return mixed
         */
        public function getWordsEnd(int $number, string $word = '', bool $strToUpperCase = false) {
            $number = $number ?: 0;
            $words = [
                $word,
                $word . 'a',
                $word . 'ов',
            ];

            $words = $strToUpperCase ? array_map('mb_strtoupper', $words) : $words;
            return $this->getCorrectString($number, $words[0], $words[1], $words[2]);
        }

        /**
         * @param $number
         * @param $str1
         * @param $str2
         * @param $str3
         * @return mixed
         */
        private function getCorrectString($number, $str1, $str2, $str3) {
            $val = $number % 100;

            if ($val > 10 && $val < 20) return $str3;
            else {
                $val = $number % 10;
                if ($val == 1) return $str1;
                elseif ($val > 1 && $val < 5) return $str2;
                else return $str3;
            }
        }

        /**
         * @param string $popup
         */
        public function addPopup(string $popup = '') {
            $this->_popups[] = $popup;
        }

        /**
         * @return string
         */
        public function getPopups(): string {
            return implode(PHP_EOL, $this->_popups);
        }

        /**
         * Возвращает адрес изображения элемента
         *
         * @param iUmiEntinty $entity (страница/объект)
         * @param string      $field название поля
         * @param bool        $webMode
         * @return string
         */
        public function getImagePath(iUmiEntinty $entity, string $field = 'photo', bool $webMode = false): string {
            /** @var iUmiImageFile $image */
            $image = $entity->getValue($field);

            if ($image instanceof iUmiImageFile) {
                return $image->getFilePath($webMode);
            }

            return $this->getNoImageHolderPath();
        }

        /**
         * Возвращает имя объекта по его id
         *
         * @param int|null $id
         * @return string
         */
        public function getObjectNameById(int $id = null) {
            if (!$id) return false;

            $object = $this->getObjectById($id);
            return $object instanceof iUmiObject ? $object->getName() : '';
        }

        /**
         * Выводит текущий год
         *
         * @return string
         */
        public function getCurrentYear(): string {
            return date('Y');
        }

        /**
         * Возвращает родителя страницы, саму страницу (если это и есть родитель) или false
         *
         * @param array $variables Глобальные переменные запроса
         * @return bool|umiHierarchyElement
         */
        public function getImmediateParent(array $variables) {
            if (!empty($variables['parents'])) {
                $r = array_reverse($variables['parents']);

                return array_pop($r);
            }

            return $variables['page'] ?? false;
        }

        /**
         * Возвращает родителей страницы, саму страницу (если это и есть родитель) или false
         *
         * @param array $variables Глобальные переменные запроса
         * @return array|bool
         */
        public function getParents(array $variables) {
            if (!empty($variables['parents'])) {
                return array_reverse($variables['parents']);
            }

            return isset($variables['page']) ? [$variables['page']] : false;
        }

        /**
         * @param int $parentId
         * @return string
         * @throws coreException
         */
        public function getParentPathById(int $parentId = 0): string {
            $parent = umiHierarchy::getInstance()->getElement($parentId);

            if ($parent instanceof iUmiHierarchyElement) {
                return $this->getPath($parent);
            }

            return '';
        }


        /**
         * Возвращает текущую строку запроса без get-параметров
         * или только с первым
         *
         * @param bool $firstParam
         * @return string
         */
        public function getCurrentPath(bool $firstParam = false): string {
            $currentUrl = $this->getTemplateEngine()->getCommonVar('request_uri');
            $token = $firstParam ? '&' : '?';

            return strtok($currentUrl, $token);
        }


        /**
         * Возвращает путь до именного шаблона.
         * Возможна перегрузка шаблона для конкретного бренда.
         *
         * @param array  $variables
         * @param string $basePath
         * @return string
         */
        public function getNamedTemplate(array $variables, string $basePath = 'modules/content/content'): string {
            $page = $variables['page'];
            $parent = $this->getImmediateParent($variables);
            if ($parent instanceof umiHierarchyElement) {
                $root = $parent->getAltName();
            } else {
                $root = '';
            }

            // получаем директорию шаблонов
            $templatesDirectory = $this->getTemplateEngine()->getTemplatesDirectory();
            // перегружаемый шаблон
            $template = $templatesDirectory . $basePath . '/' . $root . '/' . $page->getAltName() . '.phtml';
            // проверяем доступность перегрузки
            $root = $root && is_readable($template) ? $root : 'global';

            return $basePath . '/' . $root . '/' . $page->getAltName();
        }

        /**
         * @param array  $variables
         * @param string $path
         * @return bool|string
         */
        public function getAssetsTemplate(array $variables, string $path = '') {
            $parent = $this->getImmediateParent($variables);
            if ($parent instanceof umiHierarchyElement) {
                // get templates directory
                $templatesDirectory = $this->getTemplateEngine()->getTemplatesDirectory();
                // get file path info
                $pathInfo = pathinfo($path);
                // get override template route
                $template = $templatesDirectory . '/assets/' . $parent->getAltName() . '/' . $pathInfo['basename'];
                if (is_readable($template)) {
                    return 'assets/' . $parent->getAltName() . '/' . $pathInfo['filename'];
                }
            }

            return false;
        }

        /**
         * Проверяем, является ли тип данных, переданной страницы, - "специальным" типом данных,
         * который требует персонального шаблона для вывода этих данных.
         *
         * @param iUmiHierarchyElement $page
         * @return array
         * @throws coreException
         */
        public function isSpecialPageDataType(iUmiHierarchyElement $page): array {
            $objectTypes = umiObjectTypesCollection::getInstance();
            $type = $objectTypes->getType($page->getObjectTypeId());
            $status = true;

            switch ($type->getGUID()) {
                case 'content-about-page':
                    $template = 'modules/content/content/global/about-one';
                    $wrapper = 'about-brand-full-page';
                    break;
                default:
                    $status = false;
                    $template = $wrapper = '';
            }

            return ['status' => $status, 'template' => $template, 'wrapper' => $wrapper];
        }

        /**
         * @return bool
         */
        public function isUserAuthorized(): bool {
            return Service::Auth()->isAuthorized();
        }

        /**
         * @return bool
         */
        public function isHomePage(): bool {
            return $this->getCurrentPageId() == $this->getDefaultPage()->getId();
        }

        /**
         * Возвращает ссылку на страницу
         *
         * @param iUmiHierarchyElement $page
         * @return string
         * @throws coreException
         */
        public function getLinkPage(iUmiHierarchyElement $page): string {
            return umiLinksHelper::getInstance()->getLink($page);
        }

        /**
         * Возвращает ссылку на страницу регистрации
         * @return string
         */
        public function getRegistrationLink(): string {
            return $this->getTemplateEngine()->getCommonVar('pre_lang') . '/users/registrate/';
        }

        /**
         * Возвращает ссылку на страницу восстановления пароля
         * @return string
         */
        public function getPasswordRestoreLink(): string {
            return $this->getTemplateEngine()->getCommonVar('pre_lang') . '/users/forget/';
        }

        /**
         * Возвращает путь до главной страницы сайта
         * @return string
         */
        public function getHomePageUrl(): string {
            return $this->getTemplateEngine()->getCommonVar('pre_lang') . '/';
        }

        /**
         * Возвращает ссылку на деавторизацию
         * @return string
         */
        public function getLogoutLink(): string {
            return $this->getTemplateEngine()->getCommonVar('pre_lang') . '/users/logout/';
        }

        /**
         * Возвращает ссылку на личный кабинет пользователя
         * @param string $prefix
         * @return string
         */
        public function getCabinetLink(string $prefix = ''): string {
            return $this->getTemplateEngine()->getCommonVar('pre_lang') . '/cabinet/';
        }
    
        /**
         * Возвращает ссылку на общую форму отзыва
         * @param string $prefix
         * @return string
         */
        public function getCommonFeedbackLink(string $prefix = ''): string {
            return $this->getTemplateEngine()->getCommonVar('pre_lang') . '/feedback/';
        }

        /**
         * Возвращает ссылку для голосования за страницу
         *
         * @param int $pageId
         * @return string
         */
        public function getVotePageLink(int $pageId): string {
            return $this->getTemplateEngine()->getCommonVar('pre_lang') . '/udata//vote/setElementRating//' . $pageId;
        }


        /**
         * Возвращает объект настроек сайта
         * @return iUmiObject|bool
         * @throws publicException
         */
        public function getSettingsContainer() {
            /** @var umiSettings|UmiSettingsMacros $settings */
            $settings = cmsController::getInstance()->getModule('umiSettings');

            $settingsContainerId = $settings->getIdByCustomId(self::SITE_SETTINGS_ALIAS);

            return umiObjectsCollection::getInstance()->getObject($settingsContainerId);
        }
    }
