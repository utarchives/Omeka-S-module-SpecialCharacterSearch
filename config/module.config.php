<?php
namespace SpecialCharacterSearch;

return [
    'api_adapters' => [
        'invokables' => [
            'character_maps' => Api\Adapter\CharacterMapAdapter::class,
            'search_items' => Api\Adapter\SearchItemAdapter::class,
            'search_public_items' => Api\Adapter\SearchItemPublicAdapter::class,
            'search_group_items' => Api\Adapter\SearchGroupItemAdapter::class,
            'search_group_public_items' => Api\Adapter\SearchGroupItemPublicAdapter::class,
            'search_child_items' => Api\Adapter\SearchChildItemAdapter::class,
            'search_child_public_items' => Api\Adapter\SearchChildItemPublicAdapter::class,
            'search_value_items' => Api\Adapter\SearchValueItemAdapter::class,
            'search_values' => Api\Adapter\SearchValueAdapter::class,
            'value_datas' => Api\Adapter\ValueDataAdapter::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            OMEKA_PATH.'/modules/SpecialCharacterSearch/view',
        ],
    ],
    'entity_manager' => [
        'mapping_classes_paths' => [
            dirname(__DIR__) . '/src/Entity',
        ],
        'proxy_paths' => [
            dirname(__DIR__) . '/data/doctrine-proxies',
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => OMEKA_PATH . '/modules/SpecialCharacterSearch/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
        ],
        'factories' => [
            'SpecialCharacterSearch\Controller\Admin\Index' => Service\Controller\Admin\IndexControllerFactory::class,
        ],
    ],
    'block_layouts' => [
        'invokables' => [
        ],
        'factories' => [

        ],
    ],
    'form_elements' => [
        'factories' => [
            Form\ConfigForm::class => Service\Form\ConfigFormFactory::class,
        ],
    ],
    'navigation_links' => [
        'invokables' => [
        ],
    ],
    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Special Character Search',
                'route' => 'admin/special-character-search',
                'resource' => 'SpecialCharacterSearch\Controller\Admin\Index',
                'controller' => 'Index',
                'action' => 'index',
                'pages' => [
                    [
                        'label' => 'Import Character Map',
                        'route' => 'admin/special-character-search/map-import',
                        'resource' => 'SpecialCharacterSearch\Controller\Admin\Index',
                        'controller' => 'Index',
                        'action' => 'map-import',
                    ],
                    [
                        'label'      => 'Create Search Value', // @translate
                        'route'      => 'admin/special-character-search/create-search-value',
                        'controller' => 'Index',
                        'action' => 'create-search-value',
                        'resource' => 'SpecialCharacterSearch\Controller\Admin\Index',
                    ],
                    [
                        'label'      => 'Create Search Item', // @translate
                        'route'      => 'admin/special-character-search/create-search-item',
                        'controller' => 'Index',
                        'action' => 'create-search-item',
                        'resource' => 'SpecialCharacterSearch\Controller\Admin\Index',
                    ],
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'site' => [
            ],
            'admin' => [
                'child_routes' => [
                    'special-character-search' => [
                        'type' => 'Literal',
                        'options' => [
                            'route' => '/special-character-search',
                            'defaults' => [
                                '__NAMESPACE__' => 'SpecialCharacterSearch\Controller\Admin',
                                'controller' => 'Index',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'map-import' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/map-import',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'SpecialCharacterSearch\Controller\Admin',
                                        'controller' => 'Index',
                                        'action' => 'map-import',
                                    ],
                                ],
                            ],
                            'create-search-value' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/create-search-value',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'SpecialCharacterSearch\Controller\Admin',
                                        'controller' => 'Index',
                                        'action' => 'create-search-value',
                                    ],
                                ],
                            ],
                            'create-search-item' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/create-search-item',
                                    'defaults' => [
                                        '__NAMESPACE__' => 'SpecialCharacterSearch\Controller\Admin',
                                        'controller' => 'Index',
                                        'action' => 'create-search-item',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'specialcharactersearch' => [
        'settings' => [
            'special_character_search_folder' => false,
        ],
    ],
];
