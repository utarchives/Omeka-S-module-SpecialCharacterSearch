<?php

namespace SpecialCharacterSearch;

use Omeka\Module\AbstractModule;
use Omeka\Module\Manager;
use Omeka\Module\Exception\ModuleCannotInstallException;
use SpecialCharacterSearch\Form\ConfigForm;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\EventManager\SharedEventManagerInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\Mvc\Controller\AbstractController;
use Exception;

class Module extends AbstractModule
{

    public function onBootstrap(MvcEvent $event)
    {
        parent::onBootstrap($event);
        $acl = $this->getServiceLocator()->get('Omeka\Acl');

        $acl->allow(
            null,
            ['SpecialCharacterSearch\Api\Adapter\SearchItemAdapter',
                'SpecialCharacterSearch\Entity\SearchItem',
                'SpecialCharacterSearch\Api\Adapter\SearchItemPublicAdapter',
                'SpecialCharacterSearch\Entity\SearchItemPublic',
                'SpecialCharacterSearch\Api\Adapter\SearchGroupItemAdapter',
                'SpecialCharacterSearch\Entity\SearchGroupItem',
                'SpecialCharacterSearch\Api\Adapter\SearchGroupItemPublicAdapter',
                'SpecialCharacterSearch\Entity\SearchGroupItemPublic',
                'SpecialCharacterSearch\Api\Adapter\SearchChildItemAdapter',
                'SpecialCharacterSearch\Api\Adapter\SearchChildItemPublicAdapter',
                'SpecialCharacterSearch\Api\Adapter\SearchValueItemAdapter',
                'SpecialCharacterSearch\Entity\SearchValueItem',
            ]
            );
    }
    public function install(ServiceLocatorInterface $serviceLocator)
    {
        $omekaModules = $serviceLocator->get('Omeka\ModuleManager');
//         $module = $omekaModules->getModule('CSVImport');
//         if (!$module) {
//             throw new ModuleCannotInstallException('Require CSVImport');
//         }
//         if (Manager::STATE_NOT_INSTALLED == $module->getState()) {
//             throw new ModuleCannotInstallException('Require CSVImport');
//         }
        $module = $omekaModules->getModule('ResourceTree');
        $hasResourceTree = true;
        if (!$module) {
            $hasResourceTree = false;
        }
        // TODO Omeka3 
        // $module がfalseの場合はオブジェクトを返すことができないので条件追加
        if ($module != false && Manager::STATE_NOT_INSTALLED == $module->getState()) {
            $hasResourceTree = false;
        }
        $connection = $serviceLocator->get('Omeka\Connection');
        $sql = <<<'SQL'
CREATE TABLE character_map(
  id INT AUTO_INCREMENT NOT NULL
  , original_character VARCHAR (255) DEFAULT NULL
  , search_character VARCHAR (255) DEFAULT NULL
  , INDEX character_search_idx(search_character)
  , INDEX character_convert_idx(original_character)
  , PRIMARY KEY (id)
) DEFAULT CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;


CREATE TABLE search_group_item(
  id INT NOT NULL
  , owner_id INT DEFAULT NULL
  , resource_class_id INT DEFAULT NULL
  , resource_template_id INT DEFAULT NULL
  , search_item_id INT NOT NULL
  , parent_item_id INT NOT NULL
  , is_public TINYINT(1) NOT NULL
  , created DATETIME NOT NULL
  , modified DATETIME DEFAULT NULL
  , depth INT NOT NULL
  , is_parent TINYINT(1) NOT NULL
  , INDEX idx_search_group_item_id(id)
  , INDEX IDX_EEF74EA47E3C61F9(owner_id)
  , INDEX IDX_EEF74EA4448CC1BD(resource_class_id)
  , INDEX IDX_EEF74EA416131EA(resource_template_id)
  , INDEX UNIQ_EEF74EA49FBEBDC4(search_item_id)
  , INDEX UNIQ_EEF74EA460272618(parent_item_id)
) DEFAULT CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE search_item(
  id INT NOT NULL
  , owner_id INT DEFAULT NULL
  , resource_class_id INT DEFAULT NULL
  , resource_template_id INT DEFAULT NULL
  , parent_item_id INT NOT NULL
  , is_public TINYINT(1) NOT NULL
  , created DATETIME NOT NULL
  , modified DATETIME DEFAULT NULL
  , depth INT NOT NULL
  , is_parent TINYINT(1) NOT NULL
  , is_here TINYINT(1) NOT NULL
  , target_resource_class_id INT NOT NULL
  , sort INT NOT NULL
  , INDEX idx_search_item_id(id)
  , INDEX IDX_440130777E3C61F9(owner_id)
  , INDEX IDX_44013077448CC1BD(resource_class_id)
  , INDEX IDX_4401307716131EA(resource_template_id)
  , INDEX UNIQ_4401307760272618(parent_item_id)
) DEFAULT CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE search_value(
  id INT AUTO_INCREMENT NOT NULL
  , value_id INT NOT NULL
  , resource_id INT NOT NULL
  , property_id INT NOT NULL
  , value_resource_id INT DEFAULT NULL
  , search_value LONGTEXT DEFAULT NULL
  , lang VARCHAR (255) DEFAULT NULL
  , type VARCHAR (255) NOT NULL
  , uri LONGTEXT DEFAULT NULL
  , UNIQUE INDEX UNIQ_29429BDDF920BBA2(value_id)
  , INDEX IDX_29429BDD89329D25(resource_id)
  , INDEX IDX_29429BDD549213EC(property_id)
  , INDEX IDX_29429BDD4BC72506(value_resource_id)
  , PRIMARY KEY (id)
) DEFAULT CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
ALTER TABLE search_value ADD CONSTRAINT FK_29429BDDF920BBA2 FOREIGN KEY (value_id) REFERENCES value (id) ON DELETE CASCADE;
ALTER TABLE search_value ADD CONSTRAINT FK_29429BDD89329D25 FOREIGN KEY (resource_id) REFERENCES resource (id);
ALTER TABLE search_value ADD CONSTRAINT FK_29429BDD549213EC FOREIGN KEY (property_id) REFERENCES property (id) ON DELETE CASCADE;
ALTER TABLE search_value ADD CONSTRAINT FK_29429BDD4BC72506 FOREIGN KEY (value_resource_id) REFERENCES resource (id) ON DELETE CASCADE;

CREATE TABLE search_value_item(
  id INT NOT NULL
  , owner_id INT DEFAULT NULL
  , resource_class_id INT DEFAULT NULL
  , resource_template_id INT DEFAULT NULL
  , is_public TINYINT(1) NOT NULL
  , created DATETIME NOT NULL
  , modified DATETIME DEFAULT NULL
  , INDEX idx_search_value_item_id(id)
  , INDEX IDX_6E424E0D7E3C61F9(owner_id)
  , INDEX IDX_6E424E0D448CC1BD(resource_class_id)
  , INDEX IDX_6E424E0D16131EA(resource_template_id)
) DEFAULT CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE search_group_item_public(
  id INT NOT NULL
  , owner_id INT DEFAULT NULL
  , resource_class_id INT DEFAULT NULL
  , resource_template_id INT DEFAULT NULL
  , search_item_public_id INT NOT NULL
  , parent_item_id INT NOT NULL
  , is_public TINYINT(1) NOT NULL
  , created DATETIME NOT NULL
  , modified DATETIME DEFAULT NULL
  , depth INT NOT NULL
  , is_parent TINYINT(1) NOT NULL
  , INDEX IDX_8885EFB97E3C61F9(owner_id)
  , INDEX IDX_8885EFB9448CC1BD(resource_class_id)
  , INDEX IDX_8885EFB916131EA(resource_template_id)
  , INDEX UNIQ_8885EFB99FBEBDC4(search_item_public_id)
  , INDEX UNIQ_8885EFB960272618(parent_item_id)
) DEFAULT CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

CREATE TABLE search_item_public(
  id INT NOT NULL
  , owner_id INT DEFAULT NULL
  , resource_class_id INT DEFAULT NULL
  , resource_template_id INT DEFAULT NULL
  , parent_item_id INT NOT NULL
  , is_public TINYINT(1) NOT NULL
  , created DATETIME NOT NULL
  , modified DATETIME DEFAULT NULL
  , depth INT NOT NULL
  , is_parent TINYINT(1) NOT NULL
  , is_here TINYINT(1) NOT NULL
  , target_resource_class_id INT NOT NULL
  , sort INT NOT NULL
  , INDEX IDX_C624C64C7E3C61F9(owner_id)
  , INDEX IDX_C624C64C448CC1BD(resource_class_id)
  , INDEX IDX_C624C64C16131EA(resource_template_id)
  , INDEX UNIQ_C624C64C60272618(parent_item_id)
) DEFAULT CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

SQL;
        $connection->exec($sql);
        $sql = <<<'SQL'
create view search_item_item_set as
select
  item_set_id
  , item_id as search_item_id
  , item_id as search_item_public_id
  , item_id as search_value_item_id
from
  item_item_set;
SQL;
        $connection->exec($sql);
    }
    public function uninstall(ServiceLocatorInterface $serviceLocator)
    {
        $omekaModules = $serviceLocator->get('Omeka\ModuleManager');
        $controllerPlugins = $serviceLocator->get('ControllerPluginManager');
        $viewHelperManager = $serviceLocator->get('ViewHelperManager');
        $url = $viewHelperManager->get('Url');
        $messenger = $controllerPlugins->get('messenger');
        $module = $omekaModules->getModule('DocArchive');
        if (Manager::STATE_ACTIVE == $module->getState() || Manager::STATE_NOT_ACTIVE == $module->getState()) {
            throw new Exception('Should uninstall DocArchive');
        }
        $connection = $serviceLocator->get('Omeka\Connection');
        $connection->exec('DROP TABLE IF EXISTS search_value;');
        $connection->exec('DROP TABLE IF EXISTS character_map;');
        $connection->exec('DROP table IF EXISTS search_value_item;');
        $connection->exec('DROP VIEW IF EXISTS search_item_item_set;');
        $connection->exec('DROP table IF EXISTS search_group_item;');
        $connection->exec('DROP table IF EXISTS search_item;');
        $connection->exec('DROP table IF EXISTS search_group_item_public;');
        $connection->exec('DROP table IF EXISTS search_item_public;');
        $this->manageSettings($serviceLocator->get('Omeka\Settings'), 'uninstall');
        $this->manageSiteSettings($serviceLocator, 'install');
    }
    /**
     *
     * @param $settings
     * @param $process
     * @param string $key
     */
    protected function manageSettings($settings, $process, $key = 'settings')
    {
        $config = require __DIR__ . '/config/module.config.php';
        $defaultSettings = $config[strtolower(__NAMESPACE__)][$key];
        foreach ($defaultSettings as $name => $value) {
            switch ($process) {
                case 'install':
                    $settings->set($name, $value);
                    break;
                case 'uninstall':
                    $settings->delete($name);
                    break;
            }
        }
    }
    /**
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $process
     */
    protected function manageSiteSettings(ServiceLocatorInterface $serviceLocator, $process)
    {
        $siteSettings = $serviceLocator->get('Omeka\Settings\Site');
        $api = $serviceLocator->get('Omeka\ApiManager');
        $sites = $api->search('sites')->getContent();
        foreach ($sites as $site) {
            $siteSettings->setTargetId($site->id());
            $this->manageSettings($siteSettings, $process, 'site_settings');
        }
    }
    public function upgrade($oldVersion, $newVersion, ServiceLocatorInterface $serviceLocator)
    {

    }
    public function getConfigForm(PhpRenderer $renderer)
    {
        $services = $this->getServiceLocator();
        $config = $services->get('Config');
        $settings = $services->get('Omeka\Settings');
        $formElementManager = $services->get('FormElementManager');
        $data = [];
        $defaultSettings = $config[strtolower(__NAMESPACE__)]['settings'];
        foreach ($defaultSettings as $name => $value) {
            $data['special_character_search_config'][$name] = $settings->get($name);
        }
        $renderer->ckEditor();

        $form = $formElementManager->get(ConfigForm::class);
        $form->init();
        $form->setData($data);
        $html = $renderer->formCollection($form);
        return $html;
    }

    public function handleConfigForm(AbstractController $controller)
    {
        $services = $this->getServiceLocator();
        $config = $services->get('Config');
        $settings = $services->get('Omeka\Settings');

        $params = $controller->getRequest()->getPost();

        $form = $this->getServiceLocator()->get('FormElementManager')
        ->get(ConfigForm::class);
        $form->init();
        $form->setData($params);
        if (!$form->isValid()) {
            $controller->messenger()->addErrors($form->getMessages());
            return false;
        }
        $defaultSettings = $config[strtolower(__NAMESPACE__)]['settings'];
        foreach ($params as $name => $value) {
            if (isset($defaultSettings[$name])) {
                $settings->set($name, $value);
            }
        }
    }
    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {

    }

}
