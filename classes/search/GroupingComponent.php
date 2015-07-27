<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Ingo Renner <ingo@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


/**
 * Grouping search component
 *
 * @author Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage solr
 */
class tx_solr_search_GroupingComponent
	extends tx_solr_search_AbstractComponent
	implements tx_solr_QueryAware, tx_solr_PluginAware, tx_solr_CommandPostProcessor {

	/**
	 * Solr query
	 *
	 * @var tx_solr_Query
	 */
	protected $query;

	/**
	 * Parent plugin
	 *
	 * @var tx_solr_pi_results
	 */
	protected $parentPlugin;


	/**
	 * Initializes the search component.
	 *
	 */
	public function initializeSearchComponent() {
		$groupingEnabled   = TRUE;
		$solrGetParameters = t3lib_div::_GET('tx_solr');

		if ($this->searchConfiguration['grouping.']['allowGetParameterSwitch']
		&& isset($solrGetParameters['grouping'])
		&& $solrGetParameters['grouping'] === 'off') {
			$groupingEnabled = FALSE;
		}

		if ($this->searchConfiguration['grouping'] && $groupingEnabled) {
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['modifySearchQuery']['grouping']    = 'tx_solr_query_modifier_Grouping';
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['modifySearchResponse']['grouping'] = 'tx_solr_response_modifier_Grouping';

				// turn off pagination and results per page switch as grouping
				// uses the start and rows parameters, too and thus pagination
				// not working as expected
			$this->parentPlugin->conf['search.']['results.']['pagebrowser.']['enabled'] = 0;
			unset($this->parentPlugin->conf['search.']['results.']['resultsPerPageSwitchOptions']);
		}
	}

	/**
	 * Allows to manipulate command template variables.
	 *
	 * @param string $commandName Command name
	 * @param array|NULL $commandVariables Command variables or NULL
	 */
	public function postProcessCommandVariables($commandName, $commandVariables) {
		if ($commandName == 'results') {
			$groupingActive = 1;

			$solrGetParameters = t3lib_div::_GET('tx_solr');
			if (isset($solrGetParameters['grouping']) && $solrGetParameters['grouping'] === 'off') {
				$groupingActive = 0;
			}

			$commandVariables['groupingActive'] = $groupingActive;
		}

		return $commandVariables;
	}

	/**
	 * Provides the extension component with an instance of the current query.
	 *
	 * @param tx_solr_Query $query Current query
	 */
	public function setQuery(tx_solr_Query $query) {
		$this->query = $query;
	}

	/**
	 * Provides the extension component with an instance of the currently active
	 * plugin.
	 *
	 * @param tslib_pibase Currently active plugin
	 */
	public function setParentPlugin(tslib_pibase $parentPlugin) {
		$this->parentPlugin = $parentPlugin;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/search/class.tx_solr_search_groupingcomponent.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/search/class.tx_solr_search_groupingcomponent.php']);
}

?>