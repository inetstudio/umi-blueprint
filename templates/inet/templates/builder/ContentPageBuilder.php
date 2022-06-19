<?php

class ContentPageBuilder extends AbstractEntitiesViewBuilder
{
    public function getSelectorRequest(): selector {
        $selector = new selector('pages');
        $selector->types('hierarchy-type')->name('content', 'page');

        return $this->selector = $selector;
    }
}