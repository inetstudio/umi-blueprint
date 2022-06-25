<?php

class ArticlePageBuilder extends AbstractEntitiesViewBuilder
{
    public function getSelectorRequest(): selector {
        $selector = new selector('pages');
        $selector->types('hierarchy-type')->name('news', 'item');

        return $this->selector = $selector;
    }

    public function handleWithView(iUmiHierarchyElement $element): IPageEntitiesViewBuilder {
        $this->view = new ArticlePageView($element);

        return $this;
    }
}