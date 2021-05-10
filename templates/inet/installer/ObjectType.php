<?php

/**
 * Created by Evgenii Ioffe
 * Updated by Max Rakhmankin
 * @author Evgenii Ioffe <ioffe@umispec.ru>
 * @author Max Rakhmankin <support@inetstudio.ru>
 * @copyright Copyright (c) 2021, Evgenii Ioffe, Max Rakhmankin
 */
class ObjectTypeInstallerObjectType {
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $guid = '';
    /**
     * @var int
     */
    protected $parentId = 0;
    /**
     * @var bool
     */
    protected $hasGuide = false;
    /**
     * @var bool
     */
    protected $isPublic = false;


    /**
     * ObjectTypeInstallerObjectType constructor.
     *
     * @param        $name
     * @param string $guid
     * @param int    $parentId
     * @param bool   $hasGuide
     * @param bool   $isPublic
     */
    function __construct($name, $guid = '', $parentId = 0, $hasGuide = false, $isPublic = false) {
        $this->name = $name;
        $this->guid = $guid;
        $this->parentId = $parentId;
        $this->hasGuide = $hasGuide;
        $this->isPublic = $isPublic;
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
    public function setName(string $name): void {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getGuid(): string {
        return $this->guid;
    }

    /**
     * @param string $guid
     */
    public function setGuid(string $guid): void {
        $this->guid = $guid;
    }

    /**
     * @return int
     */
    public function getParentId(): int {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId(int $parentId): void {
        $this->parentId = $parentId;
    }

    /**
     * @return boolean
     */
    public function hasGuide(): bool {
        return $this->hasGuide;
    }

    /**
     * @param boolean $hasGuide
     */
    public function setHasGuide(bool $hasGuide): void {
        $this->hasGuide = $hasGuide;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic(bool $isPublic): void {
        $this->isPublic = $isPublic;
    }
}