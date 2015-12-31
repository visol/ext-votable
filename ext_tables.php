<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	'Fab.votable',
	'Pi1',
	'votable'
);

/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

/** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
$configurationUtility = $objectManager->get(\TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility::class);
$configuration = $configurationUtility->getCurrentConfiguration('votable');

// Possible Static TS loading
if (TRUE === isset($configuration['autoload_typoscript']['value']) && FALSE === (bool)$configuration['autoload_typoscript']['value']) {
	\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('votable', 'Configuration/TypoScript', 'Variety of forms - effortless!');
}

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('vidi')) {

	/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
	$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Fab\Vidi\Module\ModuleLoader::class, 'tx_votable_domain_model_voting');

	/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
	$moduleLoader->setIcon('EXT:votable/Resources/Public/Images/tx_votable_domain_model_voting.png')
		->setModuleLanguageFile('LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_voting.xlf')
		->setDefaultPid($configuration['default_pid']['value']) // configurable
		->register();

	$moduleLoader = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Fab\Vidi\Module\ModuleLoader::class, 'tx_votable_domain_model_vote');

	/** @var \Fab\Vidi\Module\ModuleLoader $moduleLoader */
	$moduleLoader->setIcon('EXT:votable/Resources/Public/Images/tx_votable_domain_model_vote.png')
		->setModuleLanguageFile('LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_vote.xlf')
		->addJavaScriptFile('EXT:votable/Resources/Public/JavaScript/tx_votable_domain_model_vote.js')
		->register();
}

# Allowed on every page.
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_votable_domain_model_vote');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_votable_domain_model_voting');


\Visol\Votable\VotingUtility::makeVotable('votable', 'tx_easyvotesmartvote_domain_model_candidate');