<?php

require_once dirname(__FILE__) . '/ObjectType.php';
require_once dirname(__FILE__) . '/Field.php';
require_once dirname(__FILE__) . '/Group.php';

/**
 * Created by Evgenii Ioffe
 * Updated by Max Rakhmankin
 * @author Evgenii Ioffe <ioffe@umispec.ru>
 * @author Max Rakhmankin <support@inetstudio.ru>
 * @copyright Copyright (c) 2020, Evgenii Ioffe, Max Rakhmankin
 */
class ObjectTypeInstaller {
    const guide_value_guid = 'guid';
    const guide_value_name = 'name';

    /** @var umiHierarchyTypesCollection */
    protected $hierarchyTypesCollection;

    /** @var umiObjectTypesCollection */
    protected $objectTypesCollection;

    /** @var umiFieldsCollection */
    protected $fieldsCollection;

    /** @var umiFieldTypesCollection */
    protected $fieldTypesCollection;

    /** @var umiObjectsCollection */
    protected $objectsCollection;

    /**
     * ObjectTypeInstaller constructor.
     */
    public function __construct() {
        $this->hierarchyTypesCollection = umiHierarchyTypesCollection::getInstance();
        $this->objectTypesCollection = umiObjectTypesCollection::getInstance();
        $this->fieldsCollection = umiFieldsCollection::getInstance();
        $this->fieldTypesCollection = umiFieldTypesCollection::getInstance();
        $this->objectsCollection = umiObjectsCollection::getInstance();
    }

    /**
     * @param string $name
     * @param string $method
     * @return bool|umiObjectType
     * @throws coreException
     */
    protected function getObjectTypeByHierarchyTypeName(string $name, string $method = '') {
        $typeId = $this->objectTypesCollection->getTypeIdByHierarchyTypeName($name, $method);

        if (!$typeId) {
            return false;
        }

        $type = $this->objectTypesCollection->getType($typeId);

        return $type instanceof umiObjectType ? $type : false;
    }

    /**
     * @param string $guid
     * @return bool|iUmiObjectType|umiObjectType|null
     */
    protected function getObjectTypeByGUID(string $guid) {
        $type = $this->objectTypesCollection->getTypeByGUID($guid);

        return $type instanceof umiObjectType ? $type : false;
    }

    /**
     * @param ObjectTypeInstallerObjectType $objectType
     * @param bool                          $bUpdate
     * @return bool|iUmiObjectType|umiObjectType|null
     * @throws coreException
     * @throws databaseException
     * @throws publicException
     */
    protected function createObjectType(ObjectTypeInstallerObjectType $objectType, bool $bUpdate = true) {
        $guid = $objectType->getGuid();

        if (!$guid) {
            throw new publicException('У класса ' . $objectType->getName() . ' не указан guid');
        }

        $type = $this->getObjectTypeByGUID($guid);

        if (!$type instanceof umiObjectType) {
            $typeId = $this->objectTypesCollection->addType($objectType->getParentId(), $objectType->getName());
            if (!$typeId) {
                throw new publicException('Ошибка при создании класса ' . $objectType->getName());
            }

            $type = $this->objectTypesCollection->getType($typeId);

            if (!$type instanceof umiObjectType) {
                throw new publicException('Ошибка при получении класса ' . $typeId);
            }

            $type->setGUID($objectType->getGuid());
            $type->setIsGuidable($objectType->hasGuide());
            $type->setIsPublic($objectType->isPublic());
            $type->commit();

            $this->objectTypesCollection->clearCache();
        } else {
            if ($bUpdate) {
                $type->setName($objectType->getName());
                $type->setIsGuidable($objectType->hasGuide());
                $type->setIsPublic($objectType->isPublic());
                $type->commit();
            }
        }

        return $type instanceof umiObjectType ? $type : false;
    }

    /**
     * @param array $arGroups
     * @param umiObjectType $type
     * @param umiObjectType|null $baseType
     * @throws coreException
     * @throws databaseException
     * @throws publicException
     * @throws wrongParamException
     */
    protected function createObjectTypeGroups(array $arGroups, umiObjectType $type, umiObjectType $baseType = null) {
        foreach ($arGroups as $arGroup) {
            $this->createObjectTypeGroup($arGroup, $type, $baseType);
        }

        $subTypesList = $this->objectTypesCollection->getSubTypesList($type->getId());

        if ($subTypesList) {
            foreach ($subTypesList as $subTypeId) {
                $subType = $this->objectTypesCollection->getType($subTypeId);

                if (!$subType instanceof umiObjectType) {
                    continue;
                }

                $this->createObjectTypeGroups($arGroups, $subType, $type);
            }
        }
    }

    /**
     * @param ObjectTypeInstallerGroup $oGroup
     * @param umiObjectType            $type
     * @param umiObjectType|null       $baseType
     * @throws coreException
     * @throws publicException
     * @throws wrongParamException
     */
    protected function createObjectTypeGroup(ObjectTypeInstallerGroup $oGroup, umiObjectType $type, umiObjectType $baseType = null) {
        $name = $oGroup->getName();

        if (!$name) {
            throw new publicException('Не указан идентификатор группы');
        }

        $title = $oGroup->getTitle();
        $active = $oGroup->isActive();
        $visible = $oGroup->isVisible();
        $tip = $oGroup->getTip();

        $group = $type->getFieldsGroupByName($name, true);

        if (!$group instanceof umiFieldsGroup) {
            $groupId = $type->addFieldsGroup($name, $title, $active, $visible, $tip);

            if (!$groupId) {
                throw new publicException("Не удалось создать группу полей $title ($name) в типе данных " . $type->getId());
            }

            $group = $type->getFieldsGroup($groupId);

            if (!$group instanceof umiFieldsGroup) {
                throw new publicException("Не удалось создать группу полей $title ($name) в типе данных " . $type->getId());
            }
        }

        if ($group->getTitle() != $title) {
            $group->setTitle($title);
        }

        if ($group->getIsActive() != $active) {
            $group->setIsActive($active);
        }

        if ($group->getIsVisible() != $visible) {
            $group->setIsVisible($visible);
        }

        if ($group->getTip() != $tip && $tip) {
            $group->setTip($tip);
        }

        if ($group->getIsUpdated()) {
            $group->commit();
        }

        $fields = $oGroup->getFields();

        if ($fields) {
            foreach ($fields as $arField) {
                $this->createObjectTypeField($arField, $type, $group, $baseType);
            }
        }
    }

    /**
     * @param ObjectTypeInstallerField $oField
     * @param umiObjectType            $type
     * @param umiFieldsGroup           $group
     * @param umiObjectType|null       $baseType
     * @throws coreException
     * @throws publicException
     * @throws wrongParamException
     */
    protected function createObjectTypeField(ObjectTypeInstallerField $oField, umiObjectType $type, umiFieldsGroup $group, umiObjectType $baseType = null) {
        $name = $oField->getName();

        if (!$name) {
            throw new publicException('Не указан идентификатор поля');
        }

        $title = $oField->getTitle();
        $typeId = $oField->getTypeId();
        $visible = $oField->isVisible();
        $locked = $oField->isLocked();

        $bAttach = true;

        $fieldId = $type->getFieldId($name);

        if (!$fieldId) {
            if (!$baseType) {
                $fieldId = $this->fieldsCollection->addField($name, $title, $typeId, $visible, $locked);

                if (!$fieldId) {
                    throw new publicException('Ошибка при создании поля ' . $title);
                }
            } else {
                $fieldId = $baseType->getFieldId($name);
            }
        } else {
            $bAttach = false;
        }

        if (!$fieldId) {
            throw new publicException('Не найдено поле ' . $name);
        }

        $field = $this->fieldsCollection->getField($fieldId);

        if (!$field instanceof umiField) {
            throw new publicException('Не найдено поле ' . $fieldId);
        }

        if ($field->getTitle() != $title) {
            $field->setTitle($title);
        }

        if ($field->getFieldTypeId() != $typeId) {
            $field->setFieldTypeId($typeId);
        }

        if ($field->getIsVisible() != $visible) {
            $field->setIsVisible($visible);
        }

        if ($visible && !$field->isImportant()) {
            $field->setImportanceStatus(true);
        }

        if ($field->getIsLocked() != $locked) {
            $field->setIsLocked($locked);
        }

        $required = $oField->isRequired();

        if ($field->getIsRequired() != $required) {
            $field->setIsRequired($required);
        }

        $tip = $oField->getTip();

        if ($field->getTip() != $tip) {
            $field->setTip($tip);
        }

        $guideId = $oField->getGuideId();

        if ($field->getGuideId() != $guideId) {
            $field->setGuideId($guideId);
        }

        $isInFilter = $oField->isInFilter();

        if ($field->getIsInFilter() != $isInFilter) {
            $field->setIsInFilter($isInFilter);
        }

        if ($field->getIsInSearch()) {
            $field->setIsInSearch(false);
        }

        if ($field->getIsInFilter()) {
            $field->setIsInFilter(false);
        }

        if (!$field->getIsInheritable()) {
            $field->setIsInheritable(true);
        }

        if ($field->getIsUpdated()) {
            $field->commit();
        }

        if ($bAttach) {
            $group->attachField($fieldId);
        }
    }

    /**
     * @param      $type
     * @param bool $isMultiple
     * @return mixed
     * @throws databaseException
     * @throws publicException
     */
    protected function getFieldTypeId($type, bool $isMultiple = false) {
        static $types = array();

        $hash = $type;

        if ($isMultiple) {
            $hash .= '_multiple';
        }

        if (!isset($types[$hash])) {
            $fieldType = $this->fieldTypesCollection->getFieldTypeByDataType($type, $isMultiple);

            if (!$fieldType instanceof umiFieldType) {
                throw new publicException('Не найдено поле с типом ' . $type);
            }

            $types[$hash] = $fieldType->getId();
        }

        return $types[$hash];
    }

    /**
     * @param       $typeId
     * @param array $values
     * @return bool
     * @throws coreException
     * @throws selectorException
     */
    protected function createGuideValues($typeId, array $values = []): bool {
        if (!$values) {
            return false;
        }

        $type = $this->objectTypesCollection->getType($typeId);

        if (!$type instanceof umiObjectType) {
            return false;
        }

        $arValues = [];
        foreach ($values as $value) {
            $guid = getArrayKey($value, self::guide_value_guid);

            if (!$guid) {
                continue;
            }

            $arValues[$guid] = getArrayKey($value, self::guide_value_name);
        }

        $sel = new selector('objects');
        $sel->types('object-type')->id($typeId);
        $sel->where('guid')->equals(array_keys($arValues));

        $guideValues = [];

        foreach ($sel->result() as $object) {
            if (!$object instanceof umiObject) {
                continue;
            }

            $guid = $object->getGUID();

            if (!$guid) {
                continue;
            }

            $guideValues[$guid] = $object->getId();
        }

        foreach ($arValues as $guid => $name) {
            $objectId = $guideValues[$guid] ?? $this->objectsCollection->addObject($name, $typeId);

            $object = $this->objectsCollection->getObject($objectId);

            if (!$object instanceof umiObject) {
                continue;
            }

            $object->setGUID($guid);
            $object->setName($name);
            $object->commit();
        }

        return true;
    }

    /**
     * @param $guid
     * @param $name
     * @return array
     */
    protected function createGuideValueArray($guid, $name): array {
        return [
            self::guide_value_guid => $guid,
            self::guide_value_name => $name
        ];
    }
}