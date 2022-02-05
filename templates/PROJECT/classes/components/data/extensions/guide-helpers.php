<?php

use UmiCms\Classes\System\Entities\City\City;
use UmiCms\Classes\System\Entities\Country\Country;

/** Класс обработчиков событий */
class DataGuideHelpers
{
    /** @var data $module */
    public data $module;

    /**
     * @return array
     * @throws coreException
     * @throws selectorException
     */
    public function getCitiesList(): array {
        $cities = new selector('objects');
        $cities->types('object-type')->guid(City::CITY_TYPE_GUID);
        $cities->option('no-length', true);

        $items = $this->createItemsArray($cities);

        return ["items" => $items];
    }

    /**
     * @return array[]
     * @throws coreException
     * @throws selectorException
     */
    public function getCountriesList(): array {
        $countries = new selector('objects');
        $countries->types('object-type')->guid(Country::COUNTRY_TYPE_GUID);
        $countries->option('no-length', true);
        $countries->limit(0, 10);

        $options = ['I18n_field' => 'translation'];
        $items = $this->createItemsArray($countries, $options);

        return ["items" => $items];
    }

    /**
     * @param selector $selector
     * @param array $options
     * @return array
     * @throws coreException
     */
    private function createItemsArray(selector $selector, array $options = []): array {
        $items = [];
        /** @var umiObject $item */
        foreach ($selector->result() as $item) {
            $merge = [];
            if (!empty($options)) {
                array_walk($options, function ($o, $k) use ($item, &$merge) {
                    $merge[$k] = $item->getValue($o);
                });
            }
            $items[] = array_merge([
                'id'   => $item->getId(),
                'name' => $item->getName(),
            ], $merge);
        }

        return $items;
    }
}
