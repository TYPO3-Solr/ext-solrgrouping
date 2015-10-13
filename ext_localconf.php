<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\ApacheSolrForTypo3\Solr\Search\SearchComponentManager::registerSearchComponent(
	'grouping',
	'ApacheSolrForTypo3\Solrgrouping\Search\GroupingComponent'
);

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['pi_results']['results']['postProcessCommandVariables'][] = 'ApacheSolrForTypo3\Solrgrouping\Search\GroupingComponent';

	# ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- #

// updates
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['solrgrouping_templates'] = 'ApacheSolrForTypo3\Solrgrouping\Migrations\Database\SysTemplate';

