<?php

class ContentCustom extends def_module
{
    /**
     * @var content|ContentCustomMacros $module
     */
    public $module;

    /**
     * @var array|string[]
     */
    private static array $extensions = [
        'ContentCustomHandlers' => '/handlers.php',
    ];

    /**
     * @noinspection PhpMissingParentConstructorInspection
     * DataCustom constructor.
     * @param data $self
     */
    public function __construct(data $self) {
        foreach (self::$extensions as $class => $path) {
            $self->__loadLib($path, (dirname(__FILE__)));
            $self->__implement($class);
        }
    }

    /**
     * Получает данные страниц различных типов
     *
     * @param int|null $parentId
     * @param string|null $method
     * @param string|null $view
     * @param int|null $limit
     * @return array
     * @throws coreException
     */
    public function getPagesData(
        int     $parentId = null,
        ?string $method = null,
        ?string $view = null,
        ?int    $limit = null): array
    {
        $umiHierarchy = umiHierarchy::getInstance();
        $parent = $umiHierarchy->getElement($parentId);
        $limit = $limit ?: $this->module->perPage;

        $dataList = [
            'parent_id' => $parentId,
            'items'     => [],
            'total'     => 0,
            'per_page'  => $limit
        ];

        if (!$parent instanceof iUmiHierarchyElement ||
            !$builder = $this->tryGetPagesBuilder($parent, $method, $view)) {
            return $dataList;
        }

        $builder->getSelectorRequest();
        $builder->applySelectorHierarchy($parentId, $builder::LEVEL);
        $builder->applySelectorLimits($limit);
        $builder->applySelectorFilters();
        $builder->applySelectorOrder($parent);

        $result = $builder->getSelectorResult();
        $total = $builder->getSelectorLength();

        $items = [];
        /** @var iUmiHierarchyElement $page */
        foreach ($result as $page) {
            if (!$page instanceof iUmiHierarchyElement)
                continue;

            $items[] = $builder->handleWithView($page)
                ->getView()
                ->getStructure();

            $umiHierarchy->unloadElement($page->getId());
        }

        $dataList['items'] = $items;
        $dataList['total'] = $total;
        $dataList['limit'] = (int)ceil($total / $limit);
        $dataList['curr_page'] = (int)getRequest('p');

        return $dataList;
    }

    /**
     * Возвращает класс-строитель структуры страниц
     *
     * @param iUmiHierarchyElement $parent
     * @param string|null $method
     * @param string|null $view
     * @return null|IPageEntitiesViewBuilder
     * @throws coreException
     */
    public function tryGetPagesBuilder(
        iUmiHierarchyElement $parent,
        ?string $method = null,
        ?string $view = null): ?IPageEntitiesViewBuilder
    {
        $method = $method ?: $this->getPagesBuilderByParentGUID($parent);

        try {
            /** @var IPageEntitiesViewBuilder $builder */
            $builder = new $method($view);
        } catch (Exception $exception) {
            umiExceptionHandler::report($exception);

            return null;
        }

        return $builder;
    }

    /**
     * Get builder by parent guid or return default content page builder
     * @param iUmiHierarchyElement $parent
     * @return string
     * @throws coreException
     */
    private function getPagesBuilderByParentGUID(iUmiHierarchyElement $parent): string {
        $objectTypes = umiObjectTypesCollection::getInstance();
        $type = $objectTypes->getType($parent->getObjectTypeId());

        switch ($type->getGUID()) {
            case 'news-rubric':
                $method = ArticlePageBuilder::class;
                break;
            case 'catalog-object':
                $method = ProductPageBuilder::class;
                break;
            default:
                $method = ContentPageBuilder::class;
                break;
        }

        return $method;
    }
}