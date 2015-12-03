<?php
namespace ApacheSolrForTypo3\Solrgrouping\Migrations\Database;

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

use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Updates old sys_template entries to use the new location
 *
 * @package ApacheSolrForTypo3\Solrgrouping\Migrations\Database
 */
class SysTemplate extends AbstractUpdate
{

    protected $title = 'EXT:solrgrouping - Migrate static template includes';


    /**
     * Checks whether updates are required.
     *
     * @param string &$description The description for the update
     * @return boolean Whether an update is required (TRUE) or not (FALSE)
     */
    public function checkForUpdate(&$description)
    {
        $database = $this->getDatabaseConnection();
        $oldTemplatesCount = $database->exec_SELECTcountRows('uid', 'sys_template',
            'include_static_file LIKE "%EXT:solrgrouping/static/solrgrouping/%"');

        return ($oldTemplatesCount > 0);
    }

    /**
     * Performs the accordant updates.
     *
     * @param array &$dbQueries Queries done in this update
     * @param mixed &$customMessages Custom messages
     * @return boolean Whether everything went smoothly or not
     */
    public function performUpdate(array &$dbQueries, &$customMessages)
    {
        $database = $this->getDatabaseConnection();
        $oldTemplates = $database->exec_SELECTgetRows(
            'uid, include_static_file',
            'sys_template',
            'include_static_file LIKE "%EXT:solrgrouping/static/solrgrouping/%"'
        );

        foreach ($oldTemplates as $oldTemplate) {
            $newTemplate = str_replace(
                'EXT:solrgrouping/static/solrgrouping/',
                'EXT:solrgrouping/Configuration/TypoScript/',
                $oldTemplate['include_static_file']
            );

            $database->exec_UPDATEquery('sys_template', 'uid = ' . $oldTemplate['uid'], array(
                'include_static_file' => $newTemplate
            ));
        }

        $dummyDescription = '';
        return !$this->checkForUpdate($dummyDescription);
    }

    /**
     * @return DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
