<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


   # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- # ----- #

	// TypoScript
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/solrgrouping/', 'Apache Solr - Result Grouping');


?>