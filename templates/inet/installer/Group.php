<?php

/**
 * Created by Evgenii Ioffe
 * Updated by Max Rakhmankin
 * @author Evgenii Ioffe <ioffe@umispec.ru>
 * @author Max Rakhmankin <support@inetstudio.ru>
 * @copyright Copyright (c) 2021, Evgenii Ioffe, Max Rakhmankin
 */
class ObjectTypeInstallerGroup {
    /** @var string $name */
    protected $name = '';

    /** @var string $title */
    protected $title = '';

    /** @var bool $active */
    protected $active = true;

    /** @var bool $visible */
    protected $visible = true;

    /** @var string $tip */
    protected $tip = '';

    /** @var array $fields */
    protected $fields = [];

    /** @var bool $fieldInheritName */
    protected $fieldInheritName = true;

    /**
     * ObjectTypeInstallerGroup constructor.
     * @param $name
     * @param $title
     */
    public function __construct($name, $title) {
        $this->name = $name;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTitle(): string {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title) {
        $this->title = $title;
    }

    /**
     * @return bool
     */
    public function isActive(): bool {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active) {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isVisible(): bool {
        return $this->visible;
    }

    /**
     * @param bool $visible
     */
    public function setVisible(bool $visible) {
        $this->visible = $visible;
    }

    /**
     * @return string
     */
    public function getTip(): string {
        return $this->tip;
    }

    /**
     * @param string $tip
     */
    public function setTip(string $tip) {
        $this->tip = $tip;
    }

    /**
     * @return array
     */
    public function getFields(): array {
        return $this->fields;
    }

    public function addField(ObjectTypeInstallerField $field) {
        if ($this->fieldInheritName) {
            if (strpos($field->getName(), $this->getName()) === false) {
                $field->setName($this->getName() . '_' . $field->getName());
            }
        }

        $this->fields[] = $field;
    }

    /**
     * @param bool $fieldInheritName
     */
    public function setFieldInheritName(bool $fieldInheritName) {
        $this->fieldInheritName = $fieldInheritName;
    }

    /**
     * @return bool
     */
    public function isFieldInheritName(): bool {
        return $this->fieldInheritName;
    }
}