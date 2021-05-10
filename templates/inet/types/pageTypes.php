<?php
/**
 * Created Maxim Rakhmankin
 * @author Maxim Rakhmankin <support@inetstudio.ru>
 * @copyright Copyright (c) 2021, Maxim Rakhmankin
 */

final class PageExtension extends TypeExtendingInstaller implements ITypeExtension
{
    /**
     * Updates content page type
     */
    public function updateContentPageFields() {
        $type = $this->objectTypesCollection->getTypeByGUID('content-page');

        $dataStructure = [
            [
                'alias' => 'more_params',
                'name' => 'i18n::fields-group-more_params',
                'inheritName' => false,
                'fields' => [
                    ['wrapper_class', 'Класс обёртки', 'string', 'Класс, накладываемый на html тег верхнего уровня данной страницы'],
                    ['use_named_template', 'Использовать именной шаблон', 'boolean', 'В качестве имени шаблона используется «псевдостатический адрес» страницы.'],
                    ['additional_info', 'Дополнительный инфо-блок', 'wysiwyg', 'Блок с дополнительной информацией для вывода на страницу.'],
                ]
            ],
            [
                'alias' => 'page_redirects',
                'name' => 'i18n::fields-group-redirect_props',
                'inheritName' => false,
                'fields' => [
                    ['redirect', 'Выберите страницу', ['symlink', true]],
                ]
            ],
        ];

        $this->handleTypeStructureChanges($dataStructure, $type, $this->buffer);
    }

    public function execute() {
        $this->updateContentPageFields();
    }
}