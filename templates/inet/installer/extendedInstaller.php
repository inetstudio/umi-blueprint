<?php
require_once dirname(__FILE__) . '/Installer.php';

/**
 * Created by Max Rakhmankin
 * @author Max Rakhmankin <support@inetstudio.ru>
 * @copyright Copyright (c) 2021, Max Rakhmankin
 */
class ExtendedInstaller extends ObjectTypeInstaller {
    /** @var int $rootGuidesTypeId Id типа корневого справочника */
    protected int $rootGuidesTypeId = 0;
    /** @var int $rootPagesTypeId Id типа корневого раздела */
    protected int $rootPagesTypeId = 0;
    /** @var array $extensions Массив названий для классов расширений */
    protected array $extensions = [];

    /**
     * ExtendedInstaller constructor.
     * @throws coreException
     * @throws publicException
     */
    public function __construct() {
        parent::__construct();

        $rootGuidesTypeId = $this->objectTypesCollection->getTypeIdByGUID('root-guides-type');
        if (!$rootGuidesTypeId) {
            throw new publicException('Не найден базовый тип данных "Справочники"');
        }

        $rootPagesTypeId = $this->objectTypesCollection->getTypeIdByGUID('root-pages-type');
        if (!$rootPagesTypeId) {
            throw new publicException('Не найден базовый тип данных "Раздел сайта"');
        }

        $this->rootGuidesTypeId = $rootGuidesTypeId;
        $this->rootPagesTypeId = $rootPagesTypeId;

        cmsController::$IGNORE_MICROCACHE = true;
    }

    /**
     * @param string $extensionsFolder
     */
    protected function scanExtensionsFolder(string $extensionsFolder) {
        umiDirectory::requireFolder($extensionsFolder, CURRENT_WORKING_DIR);

        $directory = new umiDirectory($extensionsFolder);
        foreach ($directory->getFiles('(.*).php') as $filePath) {
            require_once $filePath;
        }
    }

    //region Type creating helpers

    /**
     * @param $arGroups
     * @param umiObjectType $type
     * @param $guideTypeGUID
     * @param $guideTypeId
     * @return void
     * @throws coreException
     * @throws databaseException
     * @throws publicException
     * @throws selectorException
     * @throws wrongParamException
     */
    protected function createTypeFields($arGroups, umiObjectType $type, $guideTypeGUID = null, $guideTypeId = null) {
        $groups = [];

        foreach ($arGroups as $arGroup) {
            $group = new ObjectTypeInstallerGroup($arGroup['name'], $arGroup['title']);
            $group->setFieldInheritName($arGroup['inheritName'] ?? $group->isFieldInheritName());
            $group->setActive($arGroup['isActive'] ?? $group->isActive());
            $group->setVisible($arGroup['isVisible'] ?? $group->isVisible());
            $group->setTip($arGroup['tip'] ?? $group->getTip());

            if (isset($arGroup['fields'])) {
                foreach ($arGroup['fields'] as $arField) {
                    $field = new ObjectTypeInstallerField($arField['name'], $arField['title'], $arField['type_id']);
                    $field->setTip($arField['tip']);
                    $field->setRequired($arField['required'] ?? false);

                    // check if it's a relation field type
                    if (isset($arField['guide'])) {
                        // get potential guide GUID
                        $guid = $arField['generate_guid'] ? $guideTypeGUID . '-' . $arField['guide'] : $arField['guide'];

                        // do we need to create a new guide?
                        if ($arField['create']) {
                            $guide = $this->createObjectType(new ObjectTypeInstallerObjectType($field->getTitle(), $guid, $guideTypeId, true));
                        } else {
                            $guide = $this->getObjectTypeByGUID($guid);
                        }

                        // do we have a values to fill the guide?
                        if (is_array($arField['values'])) {
                            $values = [];

                            foreach ($arField['values'] as $guid => $name) {
                                $values[] = $this->createGuideValueArray($arField['guide'] . '-' . $guid, $name);
                            }

                            $this->createGuideValues($guide->getId(), $values);
                        }

                        // call local guide manipulations methods from extended classes
                        $this->performGuideManipulations($arField, $guide);

                        // attach guide to this field
                        $field->setGuideId($guide->getId());
                    }

                    $group->addField($field);
                }
            }

            $groups[] = $group;
        }

        $this->createObjectTypeGroups($groups, $type);
    }

    /**
     * @param       $name
     * @param       $title
     * @param array $fields
     * @return array
     */
    protected function createGroupArray($name, $title, array $fields = []): array {
        return [
            'name'   => $name,
            'title'  => $title,
            'fields' => $fields
        ];
    }

    /**
     * @param        $name
     * @param        $title
     * @param        $typeId
     * @param string $tip
     * @return array
     */
    protected function createFieldArray($name, $title, $typeId, string $tip = ''): array {
        return [
            'name'    => $name,
            'title'   => $title,
            'type_id' => $typeId,
            'tip'     => $tip
        ];
    }

    /**
     * @param        $name
     * @param        $title
     * @param int    $typeId Id типа поля
     * @param string $guide GUID справочника данного поля
     * @param array  $values Значения, которыми необходимо заполнять справочник пол
     * @param string $tip Подсказки
     * @param bool   $generateGuid Использовать ли родительский GUID при формировании справочника данного поля
     * @param bool   $create Нужно ли создавать справочник для данного поля?
     * @return array
     */
    protected function createFieldArrayGuide($name, $title, $typeId = null, $guide = '', $values = [], $tip = '', $generateGuid = false, $create = false): array {
        return [
            'name'          => $name,
            'title'         => $title,
            'type_id'       => $typeId,
            'guide'         => $guide,
            'values'        => $values,
            'tip'           => $tip,
            'generate_guid' => $generateGuid,
            'create'        => $create
        ];
    }

    /**
     * @param array $structure
     * @return array
     * @throws databaseException
     * @throws publicException
     */
    protected function generateGroupsFieldsArray(array $structure = []): array {
        $arGroups = [];
        foreach ($structure as $dataGroup) {
            $fields = [];
            foreach ($dataGroup['fields'] as $dataField) {
                if (is_array($dataField[2])) {
                    list($fieldType, $isMultiple) = $dataField[2];
                    $fieldTypeId = $this->getFieldTypeId($fieldType, $isMultiple);
                } else {
                    $fieldTypeId = $this->getFieldTypeId($fieldType = $dataField[2]);
                }
                switch ($fieldType) {
                    case 'relation':
                    case 'optioned':
                        $field = $this->createFieldArrayGuide($dataField[0], $dataField[1], $fieldTypeId, $dataField[3] ?? null, $dataField[4] ?? null);
                        break;
                    default:
                        $field = $this->createFieldArray($dataField[0], $dataField[1], $fieldTypeId, $dataField[3] ?? '');
                }
                // [field in the structure with key => value pair]
                $field['required'] = $dataField['required'] ?? false;
                $fields[] = $field;
            }
            $group = $this->createGroupArray($dataGroup['alias'], $dataGroup['name'], $fields);
            if (isset($dataGroup['inheritName'])) {
                $group['inheritName'] = $dataGroup['inheritName'];
            }
            $arGroups[] = $group;
        }

        return $arGroups;
    }
    //endregion

    /**
     * @param array $dataStructure
     * @param       $type
     * @param iOutputBuffer $buffer
     * @param null $guideTypeGUID
     * @param null $guideTypeId
     * @throws coreException
     * @throws databaseException
     * @throws publicException
     * @throws selectorException
     * @throws wrongParamException
     */
    protected function handleTypeStructureChanges(
        array $dataStructure,
        $type,
        iOutputBuffer $buffer,
        $guideTypeGUID = null,
        $guideTypeId = null
    ): void {
        // returns only the last two backtrace stack entries without arguments
        $callStack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $callStack[1]['function'] ?? null;

        $arGroups = $this->generateGroupsFieldsArray($dataStructure);

        $this->createTypeFields($arGroups, $type, $guideTypeGUID, $guideTypeId);
        $buffer->push($caller . ": " . true . PHP_EOL);
    }

    /**
     * @param array $arField
     * @param umiObjectType|null $guide
     */
    protected function performGuideManipulations(array $arField, ?umiObjectType $guide) {}
}
