<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

   # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- #

	// trigger loading of ext_autoload.php
//\ApacheSolrForTypo3\Solrgrouping\ClassLoader::loadClasses();

\Tx_Solr_Search_SearchComponentManager::registerSearchComponent(
	'grouping',
	'ApacheSolrForTypo3\Solrgrouping\Search\GroupingComponent'
);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['pi_results']['results']['postProcessCommandVariables'][] = 'ApacheSolrForTypo3\Solrgrouping\Search\GroupingComponent';
