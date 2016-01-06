<?php
namespace Visol\Votable\Backend;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/**
 * A class to interact with TCEForms.
 */
class TceForms
{

    /**
     * This method modifies the list of items for FlexForm "dataType".
     *
     * @param array $parameters
     */
    public function getContentTypes(&$parameters)
    {

        /** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
        $configurationUtility = $this->getObjectManager()->get('TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility');
        $configuration = $configurationUtility->getCurrentConfiguration('votable');
        $availableContentTypes = GeneralUtility::trimExplode(',', $configuration['content_types']['value'], TRUE);

        foreach ($GLOBALS['TCA'] as $contentType => $tca) {
            if (isset($GLOBALS['TCA'][$contentType]['grid']) && (empty($availableContentTypes) || in_array($contentType, $availableContentTypes))) {
                $label = sprintf(
                    '%s (%s)',
                    $this->getLanguageService()->sL($GLOBALS['TCA'][$contentType]['ctrl']['title']),
                    $contentType
                );
                $values = array($label, $contentType, NULL);

                $parameters['items'][] = $values;
            }
        }
    }

    /**
     * This method modifies the list of items for FlexForm "template".
     *
     * @param array $parameters
     */
    public function getTemplates(&$parameters)
    {
        $configuration = $this->getPluginConfiguration();

        if (empty($configuration) || empty($configuration['settings']['templates'])) {
            $parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
        } else {

            if (version_compare(TYPO3_branch, '7.0', '<')) {
                $configuredDataType = $this->getDataTypeFromFlexformLegacy($parameters);
            } else {
                $configuredDataType = $this->getDataTypeFromFlexform($parameters['flexParentDatabaseRow']['pi_flexform']);
            }

            $parameters['items'][] = ''; // Empty value
            foreach ($configuration['settings']['templates'] as $template) {
                $values = array($template['title'], $template['path'], NULL);
                if (empty($template['dataType']) || $template['dataType'] === $configuredDataType) {
                    $parameters['items'][] = $values;
                }
            }
        }
    }

    /**
     * @param $parameters
     * @return string
     */
    protected function getDataTypeFromFlexformLegacy($parameters)
    {
        $configuredDataType = '';
        if (!empty($parameters['row']['pi_flexform'])) {
            $flexform = @GeneralUtility::xml2array($parameters['row']['pi_flexform']);
            if (!empty($flexform['data']['general']['lDEF']['settings.dataType'])) {
                $configuredDataType = $flexform['data']['general']['lDEF']['settings.dataType']['vDEF'];
            }
        }
        return $configuredDataType;
    }

    /**
     * @param array $flexform
     * @return string
     */
    protected function getDataTypeFromFlexform(array $flexform = array())
    {

        $configuredDataType = '';

        if (!empty($flexform)) {

            $normalizedFlexform = $this->normalizeFlexForm($flexform);
            if (!empty($normalizedFlexform['settings']['dataType'])) {
                $configuredDataType = $normalizedFlexform['settings']['dataType'];
                if (is_array($configuredDataType)) {
                    $configuredDataType = $configuredDataType[0];
                }
            }
        }
        return $configuredDataType;
    }

    /**
     * Returns the TypoScript configuration for this extension.
     *
     * @return array
     */
    protected function getPluginConfiguration()
    {
        $setup = $this->getConfigurationManager()->getTypoScriptSetup();

        $pluginConfiguration = array();
        if (is_array($setup['plugin.']['tx_Votable.'])) {
            /** @var TypoScriptService $typoScriptService */
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $pluginConfiguration = $typoScriptService->convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_Votable.']);
        }
        return $pluginConfiguration;
    }

    /**
     * @return BackendConfigurationManager
     */
    protected function getConfigurationManager()
    {
        return $this->getObjectManager()->get(BackendConfigurationManager::class);
    }

    /**
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        /** @var ObjectManager $objectManager */
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * Parses the flexForm content and converts it to an array
     * The resulting array will be multi-dimensional, as a value "bla.blubb"
     * results in two levels, and a value "bla.blubb.bla" results in three levels.
     *
     * Note: multi-language flexForms are not supported yet
     *
     * @param array $flexForm flexForm xml string
     * @param string $languagePointer language pointer used in the flexForm
     * @param string $valuePointer value pointer used in the flexForm
     * @return array the processed array
     */
    protected function normalizeFlexForm(array $flexForm, $languagePointer = 'lDEF', $valuePointer = 'vDEF')
    {
        $settings = array();
        $flexForm = isset($flexForm['data']) ? $flexForm['data'] : array();
        foreach (array_values($flexForm) as $languages) {
            if (!is_array($languages[$languagePointer])) {
                continue;
            }
            foreach ($languages[$languagePointer] as $valueKey => $valueDefinition) {
                if (strpos($valueKey, '.') === false) {
                    $settings[$valueKey] = $this->walkFlexFormNode($valueDefinition, $valuePointer);
                } else {
                    $valueKeyParts = explode('.', $valueKey);
                    $currentNode = &$settings;
                    foreach ($valueKeyParts as $valueKeyPart) {
                        $currentNode = &$currentNode[$valueKeyPart];
                    }
                    if (is_array($valueDefinition)) {
                        if (array_key_exists($valuePointer, $valueDefinition)) {
                            $currentNode = $valueDefinition[$valuePointer];
                        } else {
                            $currentNode = $this->walkFlexFormNode($valueDefinition, $valuePointer);
                        }
                    } else {
                        $currentNode = $valueDefinition;
                    }
                }
            }
        }
        return $settings;
    }

    /**
     * Parses a flexForm node recursively and takes care of sections etc
     *
     * @param array $nodeArray The flexForm node to parse
     * @param string $valuePointer The valuePointer to use for value retrieval
     * @return array
     */
    protected function walkFlexFormNode($nodeArray, $valuePointer = 'vDEF')
    {
        if (is_array($nodeArray)) {
            $return = array();
            foreach ($nodeArray as $nodeKey => $nodeValue) {
                if ($nodeKey === $valuePointer) {
                    return $nodeValue;
                }
                if (in_array($nodeKey, array('el', '_arrayContainer'))) {
                    return $this->walkFlexFormNode($nodeValue, $valuePointer);
                }
                if ($nodeKey[0] === '_') {
                    continue;
                }
                if (strpos($nodeKey, '.')) {
                    $nodeKeyParts = explode('.', $nodeKey);
                    $currentNode = &$return;
                    $nodeKeyPartsCount = count($nodeKeyParts);
                    for ($i = 0; $i < $nodeKeyPartsCount - 1; $i++) {
                        $currentNode = &$currentNode[$nodeKeyParts[$i]];
                    }
                    $newNode = array(next($nodeKeyParts) => $nodeValue);
                    $currentNode = $this->walkFlexFormNode($newNode, $valuePointer);
                } elseif (is_array($nodeValue)) {
                    if (array_key_exists($valuePointer, $nodeValue)) {
                        $return[$nodeKey] = $nodeValue[$valuePointer];
                    } else {
                        $return[$nodeKey] = $this->walkFlexFormNode($nodeValue, $valuePointer);
                    }
                } else {
                    $return[$nodeKey] = $nodeValue;
                }
            }
            return $return;
        }
        return $nodeArray;
    }

    /**
     * Will be removed when dropping 6.2 compatibility.
     *
     * @param array $parameters
     * @return array
     */
    public function getLegacyFlexform(array $parameters)
    {
        $flexform = array();
        if ($parameters['row']['pi_flexform']) {
            $flexform = GeneralUtility::xml2array($parameters['row']['pi_flexform']);
        }

        return is_array($flexform) ? $flexform : [];
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

}