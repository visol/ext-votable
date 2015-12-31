<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['votable']);

if (FALSE === isset($configuration['autoload_typoscript']) || TRUE === (bool)$configuration['autoload_typoscript']) {

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'votable',
        'constants',
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:votable/Configuration/TypoScript/constants.ts">'
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
        'votable',
        'setup',
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:votable/Configuration/TypoScript/setup.ts">'
    );
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Visol.votable',
    'Pi1',
    array(
        'Vote' => 'list, update',

    ),
    // non-cacheable actions
    array(
        'Vote' => 'list, update',

    )
);

if (TYPO3_MODE === 'BE') {
    if (!(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
        $signalSlotDispatcher->connect(
            'TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility',
            'tablesDefinitionIsBeingBuilt',
            \Visol\Votable\VotingRegistry::class,
            'addExtensionVoteDatabaseSchemaToTablesDefinition'
        );
    }

    $signalSlotDispatcher->connect(
        'TYPO3\\CMS\\Install\\Service\\SqlExpectedSchemaService',
        'tablesDefinitionIsBeingBuilt',
        \Visol\Votable\VotingRegistry::class,
        'addVoteDatabaseSchemaToTablesDefinition'
    );

}
