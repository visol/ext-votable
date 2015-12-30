<?php
return array(
    'ctrl' => [
        'title' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_voting.xlf:tx_votable_domain_model_voting',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',

        'delete' => 'deleted',
        'searchFields' => 'name',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('votable') . 'Resources/Public/Images/tx_votable_domain_model_voting.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'name, allowed_frequency, closing_date',
    ],
    'types' => [
        '1' => ['showitem' => 'name, allowed_frequency, closing_date'],
    ],
    'columns' => [

        'name' => [
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_voting.xlf:name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'allowed_frequency' => [
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_voting.xlf:allowed_frequency',
            'config' => [
                'items' => [
                    [
                        'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_voting.xlf:allowed_frequency.1',
                        1
                    ],
                    [
                        'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_voting.xlf:allowed_frequency.2',
                        2
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
            ],
        ],
        'closing_date' => [
            'label' => 'LLL:EXT:votable/Resources/Private/Language/tx_votable_domain_model_voting.xlf:closing_date',
            'config' => [
                'dbType' => 'datetime',
                'type' => 'input',
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => '0000-00-00 00:00:00'
            ],
        ],
    ],
);