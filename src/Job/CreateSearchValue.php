<?php
namespace SpecialCharacterSearch\Job;

use Omeka\Job\AbstractJob;
use Omeka\Module\Manager;

class CreateSearchValue extends AbstractJob
{
    protected $api;

    protected $addedCount;

    protected $logger;

    protected $hasErr = false;
    public function perform()
    {
        ini_set("auto_detect_line_endings", true);
        $this->logger = $this->getServiceLocator()->get('Omeka\Logger');
        $this->api = $this->getServiceLocator()->get('Omeka\ApiManager');
        $this->createSearchValue();
    }
    protected function createSearchValue() {
        $this->api->delete('search_values', []);
        $this->getCharacterMaps();
        $connection = $this->getServiceLocator()->get('Omeka\Connection');
        while (true) {
            $countSql = 'select count(*) cnt from value a
left join
search_value b
on
a.id = b.value_id
where
b.id is null';
            $count = $connection->fetchAll($countSql, [], [])[0]['cnt'];
            if ($count == 0) {
                break;
            }
            $selectSql = "select a.id
, a.resource_id
, a.property_id
, a.value
, a.lang
, a.type
, a.uri from value a
left join
search_value b
on
a.id = b.value_id
where
b.id is null order by a.id limit 10000";
            $values = $connection->fetchAll($selectSql, [], []);
            $this->createSearchValuesRow($values);
        }
    }
    protected function createSearchValuesRow($values){
        foreach ($values as $value) {
            $param = ['value_id' => $value['id'],
                'resource_id' => $value['resource_id'],
                'property_id' => $value['property_id'],
                'search_value' => $this->convertSearchValue($value['value']),
                'lang' => $value['lang'],
                'type' => $value['type'],
                'uri' => $value['uri']];
            $this->api->create('search_values', $param);
        }
    }
    protected function convertSearchValue($value) {
        $searchValue = '';
        for ($i = 0; $i < mb_strlen($value); $i++) {
            if (isset($this->map[mb_substr($value, $i, 1)])) {
                $searchValue .= $this->map[mb_substr($value, $i, 1)];
            } else {
                $searchValue .= mb_substr($value, $i, 1);
            }
        }
        return $searchValue;
    }
    protected $map = [];
    protected function getCharacterMaps() {
        $sql = 'select original_character, search_character from character_map';
        $connection = $this->getServiceLocator()->get('Omeka\Connection');
//         $response = $this->api->search('character_maps', ['limit' => 100000]);
        $characterMaps = $connection->fetchAll($sql, [], []);
        $map = [];
        foreach ($characterMaps as $characterMap) {
            $chars = $characterMap['original_character'];
            for ($i = 0; $i < mb_strlen($chars); $i++) {
                $map[mb_substr($chars, $i, 1)] = $characterMap['search_character'];
            }
        }
        $this->map =  $map;
    }
}

