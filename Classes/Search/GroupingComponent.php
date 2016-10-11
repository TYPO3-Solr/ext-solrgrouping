<?php
namespace ApacheSolrForTypo3\Solrgrouping\Search;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2015 Ingo Renner <ingo@typo3.org>
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

use ApacheSolrForTypo3\Solr\Plugin\CommandPostProcessor;
use ApacheSolrForTypo3\Solr\Plugin\PluginAware;
use ApacheSolrForTypo3\Solr\Query;
use ApacheSolrForTypo3\Solr\Search\AbstractComponent;
use ApacheSolrForTypo3\Solr\Search\QueryAware;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Plugin\AbstractPlugin;

/**
 * Grouping search component
 *
 * @author Ingo Renner <ingo@typo3.org>
 * @package TYPO3
 * @subpackage solr
 */
class GroupingComponent extends AbstractComponent implements QueryAware, PluginAware, CommandPostProcessor
{

    /**
     * Solr query
     *
     * @var Query
     */
    protected $query;

    /**
     * Parent plugin
     *
     * @var \ApacheSolrForTypo3\Solr\Plugin\Results\Results
     */
    protected $parentPlugin;


    /**
     * Initializes the search component.
     *
     */
    public function initializeSearchComponent()
    {
        $groupingEnabled = true;
        $solrGetParameters = GeneralUtility::_GET('tx_solr');

        if ($this->searchConfiguration['grouping.']['allowGetParameterSwitch']
            && isset($solrGetParameters['grouping'])
            && $solrGetParameters['grouping'] === 'off'
        ) {
            $groupingEnabled = false;
        }

        if ($this->searchConfiguration['grouping'] && $groupingEnabled) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['modifySearchQuery']['grouping'] = 'ApacheSolrForTypo3\Solrgrouping\Query\Modifier\Grouping';
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['solr']['modifySearchResponse']['grouping'] = 'ApacheSolrForTypo3\Solrgrouping\Response\Modifier\Grouping';

            // turn off pagination and results per page switch as grouping
            // uses the start and rows parameters, too and thus pagination
            // not working as expected
            $overwriteConfiguration = array();
            $overwriteConfiguration['search.']['results.']['pagebrowser.']['enabled'] = 0;
            $overwriteConfiguration['search.']['results.']['resultsPerPageSwitchOptions'] = '__UNSET';

            /** @var $configurationManager \ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager */
            $configurationManager = GeneralUtility::makeInstance('ApacheSolrForTypo3\Solr\System\Configuration\ConfigurationManager');
            $configurationManager->getTypoScriptConfiguration()->mergeSolrConfiguration($overwriteConfiguration);
        }
    }

    /**
     * Allows to manipulate command template variables.
     *
     * @param string $commandName Command name
     * @param array|NULL $commandVariables Command variables or NULL
     * @return array
     */
    public function postProcessCommandVariables($commandName, $commandVariables)
    {
        if ($commandName == 'results') {
            $groupingActive = 1;

            $solrGetParameters = GeneralUtility::_GET('tx_solr');
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
     * @param Query $query Current query
     */
    public function setQuery(Query $query)
    {
        $this->query = $query;
    }

    /**
     * Provides the extension component with an instance of the currently active
     * plugin.
     *
     * @param AbstractPlugin $parentPlugin Currently active plugin
     */
    public function setParentPlugin(AbstractPlugin $parentPlugin)
    {
        $this->parentPlugin = $parentPlugin;
    }
}
