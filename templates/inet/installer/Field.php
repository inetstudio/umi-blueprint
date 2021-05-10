<?php

/**
 * Created by Evgenii Ioffe
 * Updated by Max Rakhmankin
 * @author Evgenii Ioffe <ioffe@umispec.ru>
 * @author Max Rakhmankin <support@inetstudio.ru>
 * @copyright Copyright (c) 2021, Evgenii Ioffe, Max Rakhmankin
 */
class ObjectTypeInstallerField {
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $title = '';
    /**
     * @var int
     */
    protected $typeId = 0;
    /**
     * @var bool
     */
    protected $visible = true;
    /**
     * @var bool
     */
    protected $locked = false;
    /**
     * @var bool
     */
    protected $required = false;
    /**
     * @var string
     */
    protected $tip = '';
    /**
     * @var bool
     */
    protected $inFilter = false;
    /**
     * @var bool
     */
    protected $inSearch = false;
    /**
     * @var int
     */
    protected $guideId = 0;

    /**
     * @param $name
     * @param $title
     * @param $typeId
     */
    function __construct($name, $title, $typeId) {
        $this->name = $name;
        $this->title = $title;
        $this->typeId = $typeId;
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
     * @return int
     */
    public function getTypeId(): int {
        return $this->typeId;
    }

    /**
     * @param int $typeId
     */
    public function setTypeId(int $typeId) {
        $this->typeId = $typeId;
    }

    /**
     * @return boolean
     */
    public function isVisible(): bool {
        return $this->visible;
    }

    /**
     * @param boolean $visible
     */
    public function setVisible(bool $visible) {
        $this->visible = $visible;
    }

    /**
     * @return boolean
     */
    public function isLocked(): bool {
        return $this->locked;
    }

    /**
     * @param boolean $locked
     */
    public function setLocked(bool $locked) {
        $this->locked = $locked;
    }

    /**
     * @return boolean
     */
    public function isRequired(): bool {
        return $this->required;
    }

    /**
     * @param boolean $required
     */
    public function setRequired(bool $required) {
        $this->required = $required;
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
     * @return boolean
     */
    public function isInFilter(): bool {
        return $this->inFilter;
    }

    /**
     * @param boolean $inFilter
     */
    public function setInFilter(bool $inFilter) {
        $this->inFilter = $inFilter;
    }

    /**
     * @return boolean
     */
    public function isInSearch(): bool {
        return $this->inSearch;
    }

    /**
     * @param boolean $inSearch
     */
    public function setInSearch(bool $inSearch) {
        $this->inSearch = $inSearch;
    }

    /**
     * @return int
     */
    public function getGuideId(): int {
        return $this->guideId;
    }

    /**
     * @param int $guideId
     */
    public function setGuideId(int $guideId) {
        $this->guideId = $guideId;
    }
}