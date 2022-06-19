<?php

class ExtendedPageView extends DefaultPageView
{
    /**
     * @return mixed
     */
    public function getBrandId() {
        return $this->element->getValue('mark');
    }

    public function getBrand(): string {
        $brandId = $this->getBrandId();
        $object = umiObjectsCollection::getInstance()->getObject($brandId);

        return $object instanceof iUmiObject ? mb_strtoupper($object->getName()) : '';
    }

    public function getRatingPageInfo(): array {
        /** @var VoteMacros $voteModule */
        $voteModule = cmsController::getInstance()->getModule('vote');
        $rating = $voteModule->getElementRating(null, $this->getId());

        return [
            'count'      => $rating['rate_sum'] ?? 0,
            'page_rated' => $rating['is_rated'] ?? false
        ];
    }

    public function getStructure(): array {
        return array_merge(parent::getStructure(), [
            'brand'  => $this->getBrand(),
            'rating' => $this->getRatingPageInfo(),
        ]);
    }
}