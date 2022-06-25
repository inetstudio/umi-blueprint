<?php

abstract class AbstractEntitiesViewBuilder implements IPageEntitiesViewBuilder
{
    protected selector $selector;

    protected IPageView $view;

    protected ?closure $lazyView = null;

    public function __construct(?string $view = null) {
        if (!is_null($view) && class_exists($view)) {
            $this->lazyView = static fn($element) => new $view($element);
        }
    }

    abstract public function getSelectorRequest(): selector;

    public function applySelectorHierarchy(int $parent = null, int $level = 1): void {
        $this->selector->where('hierarchy')->page($parent)->level($level);
    }

    public function applySelectorLimits(int $limit = 0, bool $ignorePaging = true): void {
        if (!$ignorePaging && $limit) {
            $currentPage = (int)getRequest('p');
            $this->selector->limit($currentPage * $limit, $limit);
        }
    }

    public function applySelectorOrder(iUmiHierarchyElement $element, string $sortOrder = 'asc'): void {
        $field = $element->getValue('sort_field');
        $order = in_array($sortOrder, ['asc', 'desc', 'rand']) ? $sortOrder : 'asc';

        $field = $this->selector->searchField($field) ? $field : 'ord';
        $this->selector->order($field)->$order();
    }

    public function applySelectorFilters(): void {
        selectorHelper::detectFilters($this->selector);
    }

    public function getSelectorResult(): array {
        return $this->selector->result();
    }

    public function getSelectorLength(): int {
        return $this->selector->length();
    }

    /**
     * @return IPageView
     */
    public function getView(): IPageView {
        return $this->view;
    }

    public function handleWithView(iUmiHierarchyElement $element): IPageEntitiesViewBuilder {
        $lazyView = $this->getLazyView();
        $this->view = is_callable($lazyView) ? $lazyView($element) : new DefaultPageView($element);

        return $this;
    }

    /**
     * @return Closure|null
     */
    private function getLazyView(): ?Closure {
        return $this->lazyView;
    }
}