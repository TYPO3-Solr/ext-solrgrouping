<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
	'solrgrouping',
	'Configuration/TypoScript/',
	'Apache Solr - Result Grouping'
);

