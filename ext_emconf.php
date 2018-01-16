<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Custom Error Pages',
    'description' => 'Shows custom 404/503 pages depending on domain/language/current tree...',
    'category' => 'system',
    'author' => 'Bitmotion GmbH',
    'author_email' => 'typo3-ext@bitmotion.de',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.3-dev',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Bitmotion\\CustomErrorPage\\' => 'Classes'
        ],
    ],
];
