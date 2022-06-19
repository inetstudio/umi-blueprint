<?php

class ProductPageView extends ExtendedPageView
{
    /** @inheritdoc */
    protected array $imagesFields = ['photo_in_catalog', 'header_pic'];

    public function getPrice(): int {
        return $this->element->getValue('price');
    }

    public function getNewPrice(): int {
        return $this->element->getValue('new_price');
    }

    public function getQuantityLimit(): int {
        return $this->element->getValue('quantity_limit');
    }

    public function isSpecialPrice(): bool {
        return $this->element->getValue('special_price');
    }

    public function isWrinkledPackaging(): bool {
        return $this->element->getValue('wrinkled_packaging');
    }

    public function getCommonQuantity(): int {
        return $this->element->getValue('common_quantity');
    }

    public function isNew(): bool {
        return $this->element->getValue('new');
    }

    public function getTitleInCatalog(): string {
        return $this->element->getValue('title_in_catalog');
    }

    public function getDescription(): string {
        return $this->element->getValue('short_description');
    }

    public function getSize(): string {
        return $this->element->getValue('size');
    }

    public function getStructure(): array {
        return array_merge(parent::getStructure(), [
            'image'              => $this->getCoverImage(30, 30),
            'price'              => $this->getPrice(),
            'new_price'          => $this->getNewPrice(),
            'quantity_limit'     => $this->getQuantityLimit(),
            'special_price'      => $this->isSpecialPrice(),
            'wrinkled_packaging' => $this->isWrinkledPackaging(),
            'common_quantity'    => $this->getCommonQuantity(),
            'text'               => $this->getName(),
            'product_is_new'     => $this->isNew(),
            'title'              => $this->getTitleInCatalog(),
            'short_description'  => $this->getDescription(),
            'size'               => $this->getSize(),
        ]);
    }
}