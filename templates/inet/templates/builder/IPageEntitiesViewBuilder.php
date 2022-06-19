<?php

/**
 * Interface IPageEntitiesViewBuilder
 * Интерфейс конфигурации класса
 */
interface IPageEntitiesViewBuilder
{
    public function getSelectorRequest(): selector;

    public function getSelectorResult(): array;

    public function getSelectorLength(): int;

    public function applySelectorHierarchy(int $parent = null, int $level = 1): void;

    public function applySelectorLimits(int $limit, bool $ignorePaging = true): void;

    public function applySelectorFilters(): void;

    public function applySelectorOrder(iUmiHierarchyElement $element): void;

    public function generateViewStructure(iUmiHierarchyElement $element): IPageEntitiesViewBuilder;

    public function getView(): IPageView;
}