<?php
$EM_CONF[$_EXTKEY] = array(
    'title' => 'Apache Solr for TYPO3 - Result Grouping',
    'description' => 'Result Grouping',
    'version' => '1.1.0-dev',
    'category' => 'plugin',
    'author' => 'Ingo Renner',
    'author_email' => 'ingo@typo3.org',
    'author_company' => 'dkd Internet Service GmbH',
    'shy' => '',
    'priority' => '',
    'module' => '',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 0,
    'lockType' => '',
    'constraints' => array(
        'depends' => array(
            'solr' => '3.1.0-',
            'php' => '5.3.0-0.0.0',
            'typo3' => '6.2.0-7.99.99',
        ),
        'conflicts' => array(),
        'suggests' => array(),
    ),
    'autoload' => array(
        'psr-4' => array(
            'ApacheSolrForTypo3\\Solrgrouping\\' => 'Classes/'
        )
    ),
    '_md5_values_when_last_written' => ''
);
