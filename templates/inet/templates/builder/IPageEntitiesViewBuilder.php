<?php

/**
 * Interface IPageEntitiesViewBuilder
 * Интерфейс конфигурации класса
 */
interface IPageEntitiesViewBuilder
{
    /** @var int $LEVEL Depth of selector search scope */
    const LEVEL = 2;

    public function getSelectorRequest(): selector;

    public function getSelectorResult(): array;

    public function getSelectorLength(): int;

    public function applySelectorHierarchy(int $parent = null, int $level = self::LEVEL): void;

    public function applySelectorLimits(int $limit, bool $ignorePaging = true): void;

    public function applySelectorFilters(): void;

    public function applySelectorOrder(iUmiHierarchyElement $element): void;

    public function handleWithView(iUmiHierarchyElement $element): IPageEntitiesViewBuilder;

    public function getView(): IPageView;
}