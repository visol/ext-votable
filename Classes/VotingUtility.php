<?php
namespace Visol\Votable;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * VotingUtility
 */
class VotingUtility
{

    /**
     * Makes a table categorizable by adding value into the category registry.
     * FOR USE IN ext_localconf.php FILES or files in Configuration/TCA/Overrides/*.php Use the latter to benefit from TCA caching!
     *
     * @param string $extensionKey Extension key to be used
     * @param string $tableName Name of the table to be categorized
     * @param string $relationFieldName Name of the field to be used to store categories
     * @param string $rankFieldName
     * @param array $options Additional configuration options
     * @see addTCAcolumns
     * @see addToAllTCAtypes
     */
    static public function makeVotable($extensionKey, $tableName, $relationFieldName = 'votes', $rankFieldName = 'rank', array $options = array()) {
        // Update the category registry
        $result = VotingRegistry::getInstance()->add($extensionKey, $tableName, $relationFieldName, $rankFieldName, $options);
        if ($result === FALSE) {
            $message = 'VotingRegistry: no voting registered for table "%s". Key was already registered.';
            /** @var $logger \TYPO3\CMS\Core\Log\Logger */
            $logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
            $logger->warning(
                sprintf($message, $tableName)
            );
        }
    }

}