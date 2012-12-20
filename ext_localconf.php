<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

   # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- #

	// trigger loading of ext_autoload.php
tx_solrgrouping_ClassLoader::loadClasses();

tx_solr_search_SearchComponentManager::registerSearchComponent(
	'grouping',
	'tx_solr_search_GroupingComponent'
);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['pi_results']['results']['postProcessCommandVariables'][] = 'tx_solr_search_GroupingComponent';

?>