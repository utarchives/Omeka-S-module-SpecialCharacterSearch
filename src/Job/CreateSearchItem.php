<?php
namespace SpecialCharacterSearch\Job;

use Omeka\Job\AbstractJob;
use Omeka\Module\Manager;

class CreateSearchItem extends AbstractJob
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
        $this->createSearchItems();
    }
    protected function createSearchItems() {
        $connection = $this->getServiceLocator()->get('Omeka\Connection');
        $sql = "truncate table search_value_item;
INSERT
INTO search_value_item(
  id
  , owner_id
  , resource_class_id
  , resource_template_id
  , is_public
  , created
  , modified
)
SELECT
  b.id
  , owner_id
  , resource_class_id
  , resource_template_id
  , is_public
  , created
  , modified
FROM
  resource a
  inner join
  item b
  on
  a.id = b.id;
";
        $connection->exec($sql);
        $omekaModules = $this->getServiceLocator()->get('Omeka\ModuleManager');
        $resourceTree = $omekaModules->getModule('ResourceTree');
        if ((!$resourceTree ||
            (Manager::STATE_NOT_INSTALLED == $resourceTree->getState() ||
                Manager::STATE_NOT_ACTIVE == $resourceTree->getState()))) {
                    return;
                }
                $sql = "truncate table search_item;
INSERT
INTO search_item(
  id
  , owner_id
  , resource_class_id
  , resource_template_id
  , parent_item_id
  , is_public
  , created
  , modified
  , `depth`
  , is_parent
  , is_here
  , target_resource_class_id
  , sort
)
SELECT
distinct
  b.id
  , owner_id
  , resource_class_id
  , resource_template_id
  , parent_item_id
  , is_public
  , created
  , modified
  , depth
  , c.is_parent
  , c.is_here
  , resource_class_id as target_resource_class_id
  , c.id as sort

FROM
  resource a
  inner join
  item b
  on
  a.id = b.id
  inner join
  item_tree c
  on
  b.id = c.child_item_id;
truncate table search_group_item;
INSERT
INTO search_group_item(
  id
  , owner_id
  , resource_class_id
  , resource_template_id
  , is_public
  , created
  , modified
  , `depth`
  , search_item_id
  , parent_item_id
  , is_parent
)
SELECT
distinct
  b.id
  , owner_id
  , resource_class_id
  , resource_template_id
  , is_public
  , created
  , modified
  , d.depth
  , c.child_item_id as search_item_id
  , d.parent_item_id
  , c.is_parent
FROM
  resource a
  inner join
  item b
  on
  a.id = b.id
  inner join item_tree c
  on a.id = c.parent_item_id
  and c.is_parent
  inner join item_tree d
  on a.id = d.child_item_id;
truncate table search_item_public;
INSERT
INTO search_item_public(
  id
  , owner_id
  , resource_class_id
  , resource_template_id
  , parent_item_id
  , is_public
  , created
  , modified
  , `depth`
  , is_parent
  , is_here
  , target_resource_class_id
  , sort
)
SELECT
distinct
  b.id
  , owner_id
  , resource_class_id
  , resource_template_id
  , parent_item_id
  , is_public
  , created
  , modified
  , depth
  , c.is_parent
  , c.is_here
  , resource_class_id as target_resource_class_id
  , c.id as sort

FROM
  resource a
  inner join
  item b
  on
  a.id = b.id
  inner join
  item_tree c
  on
  b.id = c.child_item_id
  where a.id not in (select child_item_id from parent_item where is_public = 0) ;
truncate table search_group_item_public;
INSERT
INTO search_group_item_public(
  id
  , owner_id
  , resource_class_id
  , resource_template_id
  , is_public
  , created
  , modified
  , `depth`
  , search_item_public_id
  , parent_item_id
  , is_parent
)
SELECT
distinct
  b.id
  , owner_id
  , resource_class_id
  , resource_template_id
  , is_public
  , created
  , modified
  , d.depth
  , c.child_item_id as search_item_public_id
  , d.parent_item_id
  , c.is_parent
FROM
  resource a
  inner join
  item b
  on
  a.id = b.id
  inner join item_tree c
  on a.id = c.parent_item_id
  and c.is_parent
  inner join item_tree d
  on a.id = d.child_item_id
  where a.id not in (select child_item_id from parent_item where is_public = 0) ;";
                $connection->exec($sql);
    }

}

