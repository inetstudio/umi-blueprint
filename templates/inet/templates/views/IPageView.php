<?php

/**
 * Interface IPointsView
 * Интерфейс конфигурации класса
 */
interface IPageView
{
    public function getId(): int;

    public function getName(): string;

    public function getTitle(): string;

    public function getParentId(): int;

    public function getParentAltName(): string;

    public function getParentGUID(): string;

    public function getLink(): ?string;

    public function isVisibleInMenu(): bool;

    public function getStructure(): array;
}