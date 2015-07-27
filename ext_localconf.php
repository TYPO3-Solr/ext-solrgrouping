<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\Tx_Solr_Search_SearchComponentManager::registerSearchComponent(
	'grouping',
	'ApacheSolrForTypo3\Solrgrouping\Search\GroupingComponent'
);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['pi_results']['results']['postProcessCommandVariables'][] = 'ApacheSolrForTypo3\Solrgrouping\Search\GroupingComponent';
