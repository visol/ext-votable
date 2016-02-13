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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

/**
 * Class to register category configurations.
 */
class VotingRegistry implements SingletonInterface
{

    /**
     * @var array
     */
    protected $registry = [];

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * @var array
     */
    protected $addedVoteTabs = [];

    /**
     * @var string
     */
    protected $template = '';

    /**
     * Returns a class instance
     *
     * @return VotingRegistry
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    /**
     * Creates this object.
     */
    public function __construct()
    {
        // first field corresponds to "votes", second to "rank"
        $this->template = '


CREATE TABLE %s (
 	%s int(11) DEFAULT \'%s\' NOT NULL
);


';
    }

    /**
     * Adds a new category configuration to this registry.
     * TCA changes are directly applied
     *
     * @param string $extensionKey Extension key to be used
     * @param string $tableName Name of the table to be registered
     * @param string $relationFieldName Name of the field to be registered
     * @param string $rankFieldName
     * @param array $options Additional configuration options
     *              + fieldList: field configuration to be added to showitems
     *              + typesList: list of types that shall visualize the votes field
     *              + position: insert position of the votes field
     *              + label: backend label of the votes field
     *              + fieldConfiguration: TCA field config array to override defaults
     * @return bool
     */
    public function add($extensionKey, $tableName, $relationFieldName = 'votes', $rankFieldName = 'rank', array $options = [])
    {
        $didRegister = FALSE;
        if (empty($tableName) || !is_string($tableName)) {
            throw new \InvalidArgumentException('No or invalid table name "' . $tableName . '" given.', 1369122038);
        }

        if (!$this->isRegistered($tableName, $relationFieldName)) {
            $this->registry[$tableName][$relationFieldName] = $options;
            $this->extensions[$extensionKey][$tableName]['relationField'][$relationFieldName] = $relationFieldName;
            $this->extensions[$extensionKey][$tableName]['rankField'][$rankFieldName] = $rankFieldName;

            if (!isset($GLOBALS['TCA'][$tableName]['columns']) && isset($GLOBALS['TCA'][$tableName]['ctrl']['dynamicConfigFile'])) {
                // Handle deprecated old style dynamic TCA column loading.
                ExtensionManagementUtility::loadNewTcaColumnsConfigFiles();
            }

            if (isset($GLOBALS['TCA'][$tableName]['columns'])) {
                $this->applyTcaForTableAndField($tableName, $relationFieldName, $rankFieldName);
                $didRegister = TRUE;
            }
        }

        return $didRegister;
    }

    /**
     * Gets the registered category configurations.
     *
     * @deprecated since 6.2 will be removed two versions later - Use ->isRegistered to get information about registered category fields.
     * @return array
     */
    public function get()
    {
        \TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();
        return $this->registry;
    }

    /**
     * Gets all extension keys that registered a category configuration.
     *
     * @return array
     */
    public function getExtensionKeys()
    {
        return array_keys($this->extensions);
    }

    /**
     * Gets all categorized tables
     *
     * @return array
     */
    public function getCategorizedTables()
    {
        return array_keys($this->registry);
    }

    /**
     * Returns a list of category fields for a given table for populating selector "category_field"
     * in tt_content table (called as itemsProcFunc).
     *
     * @param array $configuration Current field configuration
     * @throws \UnexpectedValueException
     * @return void
     */
    public function getCategoryFieldsForTable(array &$configuration)
    {
        $table = '';
        // Define the table being looked up from the type of menu
        if ($configuration['row']['menu_type'] == 'categorized_pages') {
            $table = 'pages';
        } elseif ($configuration['row']['menu_type'] == 'categorized_content') {
            $table = 'tt_content';
        }
        // Return early if no table is defined
        if (empty($table)) {
            throw new \UnexpectedValueException('The given menu_type is not supported.', 1381823570);
        }
        // Loop on all registries and find entries for the correct table
        foreach ($this->registry as $tableName => $fields) {
            if ($tableName === $table) {
                foreach ($fields as $fieldName => $options) {
                    $fieldLabel = $this->getLanguageService()->sL($GLOBALS['TCA'][$tableName]['columns'][$fieldName]['label']);
                    $configuration['items'][] = [$fieldLabel, $fieldName];
                }
            }
        }
    }

    /**
     * Tells whether a table has a category configuration in the registry.
     *
     * @param string $tableName Name of the table to be looked up
     * @param string $fieldName Name of the field to be looked up
     * @return boolean
     */
    public function isRegistered($tableName, $fieldName = 'votes')
    {
        return isset($this->registry[$tableName][$fieldName]);
    }

    /**
     * Generates tables definitions for all registered tables.
     *
     * @return string
     */
    protected function getDatabaseTableDefinitions()
    {
        $sql = '';
        foreach ($this->getExtensionKeys() as $extensionKey) {
            $sql .= $this->getDatabaseTableDefinition($extensionKey);
        }
        return $sql;
    }

    /**
     * Generates table definitions for registered tables by an extension.
     *
     * @param string $extensionKey Extension key to have the database definitions created for
     * @return string
     */
    protected function getDatabaseTableDefinition($extensionKey)
    {
        if (!isset($this->extensions[$extensionKey]) || !is_array($this->extensions[$extensionKey])) {
            return '';
        }
        $sql = '';

        foreach ($this->extensions[$extensionKey] as $tableName => $fields) {
            foreach ($fields['relationField'] as $fieldName) {
                $sql .= sprintf($this->template, $tableName, $fieldName, 0);
            }
            foreach ($fields['rankField'] as $fieldName) {
                $sql .= sprintf($this->template, $tableName, $fieldName, 1);
            }
        }

        return $sql;
    }

    /**
     * Applies the additions directly to the TCA
     *
     * @param string $tableName
     * @param string $relationFieldName
     * @param string $rankFieldName
     */
    protected function applyTcaForTableAndField($tableName, $relationFieldName, $rankFieldName)
    {
        $this->addRelationFieldTcaColumn($tableName, $relationFieldName, $this->registry[$tableName][$relationFieldName]);
        $this->addRankFieldTcaColumn($tableName, $rankFieldName);
        $this->addToAllTCAtypes($tableName, $relationFieldName, $this->registry[$tableName][$relationFieldName]);
    }

    /**
     * Add a new field into the TCA types -> showitem
     *
     * @param string $tableName Name of the table to be categorized
     * @param string $fieldName Name of the field to be used to store votes
     * @param array $options Additional configuration options
     *              + fieldList: field configuration to be added to showitems
     *              + typesList: list of types that shall visualize the votes field
     *              + position: insert position of the votes field
     * @return void
     */
    protected function addToAllTCAtypes($tableName, $fieldName, array $options)
    {

        // Makes sure to add more TCA to an existing structure
        if (isset($GLOBALS['TCA'][$tableName]['columns'])) {

            if (empty($options['fieldList'])) {
                $fieldList = $this->addVoteTab($tableName, $fieldName);
            } else {
                $fieldList = $options['fieldList'];
            }

            $typesList = '';
            if (!empty($options['typesList'])) {
                $typesList = $options['typesList'];
            }

            $position = '';
            if (!empty($options['position'])) {
                $position = $options['position'];
            }

            // Makes the new "votes" field to be visible in TSFE.
            ExtensionManagementUtility::addToAllTCAtypes($tableName, $fieldList, $typesList, $position);

        }
    }

    /**
     * Creates the 'fieldList' string for $fieldName which includes a votes tab.
     * But only one votes tab is added per table.
     *
     * @param string $tableName
     * @param string $fieldName
     * @return string
     */
    protected function addVoteTab($tableName, $fieldName)
    {
        $fieldList = '';
        if (!in_array($tableName, $this->addedVoteTabs)) {
            $fieldList .= '--div--;LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:tabs.votes, ';
            $this->addedVoteTabs[] = $tableName;
        }
        $fieldList .= $fieldName;
        return $fieldList;
    }

    /**
     * Add a new TCA Column
     *
     * @param string $tableName Name of the table to be categorized
     * @param string $fieldName Name of the field to be used to store votes
     * @param array $options Additional configuration options
     *              + fieldConfiguration: TCA field config array to override defaults
     *              + label: backend label of the votes field
     *              + interface: boolean if the category should be included in the "interface" section of the TCA table
     *              + l10n_mode
     *              + l10n_display
     * @return void
     */
    protected function addRelationFieldTcaColumn($tableName, $fieldName, array $options)
    {

        // Makes sure to add more TCA to an existing structure
        if (isset($GLOBALS['TCA'][$tableName]['columns'])) {
            // Take specific label into account
            $label = 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf:votes';
            if (!empty($options['label'])) {
                $label = $options['label'];
            }

            // Take specific value of exclude flag into account
            $exclude = true;
            if (isset($options['exclude'])) {
                $exclude = (bool)$options['exclude'];
            }

            $fieldConfiguration = empty($options['fieldConfiguration']) ? [] : $options['fieldConfiguration'];

            $columns = [
                $fieldName => [
                    'exclude' => $exclude,
                    'label' => $label,
                    'config' => static::getRelationFieldConfiguration($tableName, $fieldName, $fieldConfiguration),
                ],
            ];

            // Register opposite references for the foreign side of a relation
//			if (empty($GLOBALS['TCA']['tx_votable_domain_model_voting']['columns']['items']['config']['MM_oppositeUsage'][$tableName])) {
//				$GLOBALS['TCA']['tx_votable_domain_model_voting']['columns']['items']['config']['MM_oppositeUsage'][$tableName] = [];
//			}
//			if (!in_array($fieldName, $GLOBALS['TCA']['tx_votable_domain_model_voting']['columns']['items']['config']['MM_oppositeUsage'][$tableName])) {
//				$GLOBALS['TCA']['tx_votable_domain_model_voting']['columns']['items']['config']['MM_oppositeUsage'][$tableName][] = $fieldName;
//			}

            // Add field to interface list per default (unless the 'interface' property is FALSE)
            if (
                (!isset($options['interface']) || $options['interface'])
                && !empty($GLOBALS['TCA'][$tableName]['interface']['showRecordFieldList'])
                && !GeneralUtility::inList($GLOBALS['TCA'][$tableName]['interface']['showRecordFieldList'], $fieldName)
            ) {
                $GLOBALS['TCA'][$tableName]['interface']['showRecordFieldList'] .= ',' . $fieldName;
            }

            // Adding fields to an existing table definition
            ExtensionManagementUtility::addTCAcolumns($tableName, $columns);
        }
    }

    /**
     * Add a new TCA Column
     *
     * @param string $tableName Name of the table to be categorized
     * @param string $fieldName Name of the field to be used to store votes
     * @return void
     */
    protected function addRankFieldTcaColumn($tableName, $fieldName)
    {

        // Makes sure to add more TCA to an existing structure
        if (isset($GLOBALS['TCA'][$tableName]['columns'])) {
            // Take specific label into account

            $columns = [
                $fieldName => [
                    'label' => 'Rank',
                    'config' => [
                        'type' => 'passthrough',
                    ]
                ],
            ];

            // Adding fields to an existing table definition
            ExtensionManagementUtility::addTCAcolumns($tableName, $columns);
        }
    }

    /**
     * Get the config array for given table and field.
     * This method does NOT take care of adding sql fields, adding the field to TCA types
     * nor does it set the MM_oppositeUsage in the tx_votable_domain_model_voting TCA. This has to be taken care of manually!
     *
     * @param string $tableName The table name
     * @param string $fieldName The field name (default votes)
     * @param array $fieldConfigurationOverride Changes to the default configuration
     * @return array
     * @api
     */
    static public function getRelationFieldConfiguration($tableName, $fieldName = 'votes', array $fieldConfigurationOverride = [])
    {
        // Forges a new field, default name is "votes"
        $fieldConfiguration = [
            'foreign_table' => 'tx_votable_domain_model_vote',
            #'foreign_table_where' => ' AND sys_category.sys_language_uid IN (-1, 0) ORDER BY sys_category.sorting ASC',
            'maxitems' => '999999',
            'MM' => 'tx_votable_vote_record_mm',
            'MM_match_fields' => [
                'fieldname' => 'votes',
                'tablenames' => 'tx_easyvotesmartvote_domain_model_candidate',
            ],
            'MM_opposite_field' => 'item',
            'type' => 'inline',
        ];

        // Merge changes to TCA configuration
        if (!empty($fieldConfigurationOverride)) {
            ArrayUtility::mergeRecursiveWithOverrule(
                $fieldConfiguration,
                $fieldConfigurationOverride
            );
        }

        return $fieldConfiguration;
    }

    /**
     * A slot method to inject the required category database fields to the
     * tables definition string
     *
     * @param array $sqlString
     * @return array
     */
    public function addVoteDatabaseSchemaToTablesDefinition(array $sqlString)
    {
        $sqlString[] = $this->getDatabaseTableDefinitions();
        return ['sqlString' => $sqlString];
    }

    /**
     * A slot method to inject the required category database fields of an
     * extension to the tables definition string
     *
     * @param array $sqlString
     * @param string $extensionKey
     * @return array
     */
    public function addExtensionVoteDatabaseSchemaToTablesDefinition(array $sqlString, $extensionKey)
    {
        $sqlString[] = $this->getDatabaseTableDefinition($extensionKey);
        return ['sqlString' => $sqlString, 'extensionKey' => $extensionKey];
    }

    /**
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

}
