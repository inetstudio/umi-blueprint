<?php

class ProductPageBuilder extends AbstractEntitiesViewBuilder
{
    public function getSelectorRequest(): selector {
        $selector = new selector('pages');
        $selector->types('hierarchy-type')->name('catalog', 'object');

        return $this->selector = $selector;
    }
}