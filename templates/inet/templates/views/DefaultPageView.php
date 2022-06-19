<?php

class DefaultPageView implements IPageView
{
    /** @const $BASE_IMAGE_WIDTH */
    protected const BASE_IMAGE_WIDTH = 120;
    /** @const $BASE_IMAGE_HEIGHT */
    protected const BASE_IMAGE_HEIGHT = 120;
    /** @const $IMAGE_COMPRESSION_LEVEL */
    protected const IMAGE_COMPRESSION_LEVEL = 100;

    protected iUmiHierarchyElement $element;

    protected ?int $user = null;

    /** @var array $imagesFields Images fields */
    protected array $imagesFields = ['anons_pic', 'header_pic'];

    public function __construct(iUmiHierarchyElement $element) {
        $this->element = $element;
    }

    public function getId(): int {
        return $this->element->getId();
    }

    public function getName(): string {
        return $this->element->getName();
    }

    public function getTitle(): string {
        return $this->element->getValue('h1');
    }

    public function getParentId(): int {
        return $this->element->getParentId();
    }

    public function getParentAltName(): string {
        $parentId = $this->getParentId();

        return umiHierarchy::getInstance()->getElement($parentId)->getAltName();
    }

    public function getParentGUID(): string {
        $parent = umiHierarchy::getInstance()->getElement($this->getParentId());
        $objectTypes = umiObjectTypesCollection::getInstance();
        $type = $objectTypes->getType($parent->getObjectTypeId());

        return $type->getGUID();
    }

    public function getLink(): ?string {
        $linksHelper = umiLinksHelper::getInstance();

        return $linksHelper->getLinkByParts($this->element);
    }

    public function isVisibleInMenu(): bool {
        return $this->element->getIsVisible();
    }

    public function getCoverImage(int $width = self::BASE_IMAGE_WIDTH, int $height = self::BASE_IMAGE_HEIGHT): ?string {
        $elementImage = $this->getElementImage();
        if ($elementImage instanceof iUmiImageFile) {
            $imageThumbnailPath = $this->getImageThumbnail($elementImage, $width, $height);
        }

        return $imageThumbnailPath ?? null;
    }

    private function getElementImage(): ?umiImageFile {
        foreach ($this->imagesFields as $imageField) {
            $elementImage = $this->element->getValue($imageField);
            if ($elementImage instanceof iUmiImageFile) {
                return $elementImage;
            }
        }

        return null;
    }

    private function getImageThumbnail(umiImageFile $elementImage, int $width, int $height): ?string {
        /** @var system $system */
        $system = system_buildin_load('system');

        $imagePath = $elementImage->getFilePath();
        $imageThumbnail = $system->makeThumbnailFull($imagePath,
            $width, $height, null, false, true, 5, false, self::IMAGE_COMPRESSION_LEVEL);

        return $imageThumbnail['src'] ?? null;
    }

    public function getStructure(): array {
        return [
            'id'              => $this->getId(),
            'name'            => $this->getName(),
            'title'           => $this->getName(),
            'parent'          => $this->getParentId(),
            'link'            => $this->getLink(),
            'image'           => $this->getCoverImage(),
            'visible_in_menu' => $this->isVisibleInMenu(),
        ];
    }
}