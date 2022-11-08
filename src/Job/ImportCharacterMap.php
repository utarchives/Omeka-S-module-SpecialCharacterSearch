<?php
namespace SpecialCharacterSearch\Job;

use SpecialCharacterSearch\CsvFile;
use Omeka\Job\AbstractJob;

class ImportCharacterMap extends AbstractJob
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
        $config = $this->getServiceLocator()->get('Config');
        $csvFile = new CsvFile($this->getServiceLocator());
        $csvFile->setTempPath($this->getArg('csvpath'));
        $csvFile->loadFromTempPath();
        $this->api->delete('character_maps', []);
        foreach ($csvFile->fileObject as $index => $row) {
            //skip the first (header) row, and any blank ones
            if ($index == 0 || empty($row)) {
                continue;
            }
            $characterMap = [
                'original_character' => $row[0],
                'search_character' => $row[1],
            ];
            $this->api->create('character_maps', $characterMap);
        }
    }

}

