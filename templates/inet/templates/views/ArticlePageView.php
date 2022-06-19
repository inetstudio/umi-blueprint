<?php

class ArticlePageView extends ExtendedPageView
{
    public function getPublishDate(): string {
        return $this->element->getValue('publish_time');
    }

    public function getAnons(): ?string {
        return $this->element->getValue('anons');
    }

    public function getStructure(): array {
        return array_merge(parent::getStructure(), [
            'publish_time' => $this->getPublishDate(),
            'anons'        => $this->getAnons(),
        ]);
    }
}