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
 * Modifies a query to add grouping parameters
 *
 * @author Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage solr
 */
class tx_solr_query_modifier_Grouping implements tx_solr_QueryModifier {

	/**
	 * Solr configuration
	 *
	 * @var array
	 */
	protected $configuration;

	/**
	 * Grouping realted configuration
	 *
	 * plugin.tx.solr.search.grouping
	 *
	 * @var array
	 */
	protected $groupingConfiguration;

	protected $groupingParameters = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		$this->configuration = tx_solr_Util::getSolrConfiguration();
		$this->groupingConfiguration = $this->configuration['search.']['grouping.'];
	}

	/**
	 * Modifies the given query and adds the parameters necessary
	 * for result grouping.
	 *
	 * @param tx_solr_Query The query to modify
	 * @return tx_solr_Query The modified query with grouping parameters
	 */
	public function modifyQuery(tx_solr_Query $query) {
		$query->setGrouping();

		$query->setNumberOfResultsPerGroup($this->findHighestGroupResultsLimit());

		if (!empty($this->groupingConfiguration['numberOfGroups'])) {
			$query->setNumberOfGroups($this->groupingConfiguration['numberOfGroups']);
		}

		$configuredGroups = $this->groupingConfiguration['groups.'];
		foreach ($configuredGroups as $groupName => $groupConfiguration) {

			if (isset($groupConfiguration['field'])) {
				$query->addGroupField($groupConfiguration['field']);
			} elseif (isset($groupConfiguration['query'])) {
				$query->addGroupQuery($groupConfiguration['query']);
			}

            // Added Group Sorting / Daniel Hirth <hirth@stimme.net> 18.06.2014 //
            if (isset($groupConfiguration['sortBy'])){
                $query->addGroupSorting($groupConfiguration['sortBy']);
            }
		}

		return $query;
	}

	/**
	 * Finds the highest number of results per group.
	 *
	 * Checks the global setting, as well as each group configuration's
	 * individual results limit.
	 *
	 * The lowest limit returned will be 1, as this is the default for Solr's
	 * group.limit parameter. See http://wiki.apache.org/solr/FieldCollapsing
	 *
	 * @return integer Highest number of results per group configured.
	 */
	protected function findHighestGroupResultsLimit() {
		$highestLimit = 1;

		if (!empty($this->groupingConfiguration['numberOfResultsPerGroup'])) {
			$highestLimit = $this->groupingConfiguration['numberOfResultsPerGroup'];
		}

		$configuredGroups = $this->groupingConfiguration['groups.'];
		foreach ($configuredGroups as $groupName => $groupConfiguration) {
			if (!empty($groupConfiguration['numberOfResultsPerGroup'])
			&& $groupConfiguration['numberOfResultsPerGroup'] > $highestLimit) {
				$highestLimit = $groupConfiguration['numberOfResultsPerGroup'];
			}
		}

		return $highestLimit;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/query/modifier/class.tx_solr_query_modifier_grouping.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/solr/classes/query/modifier/class.tx_solr_query_modifier_grouping.php']);
}

?>