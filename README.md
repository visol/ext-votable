Votable
=======

Voting system and its API...

Make Votable
============
    
```
    // Add an extra votable field to the table "foo"
    \Visol\Votable\VotingUtility::makeVotable(
    
            # 
            'extension_key',
            
            # The table you want votable
            'tx_domain_domain_model_foo',
            
            # Default field name
            'votes',
            
            # Default sorting field name automatically computed
            'rank',
            
            # Configuration option
            array(
                    // Set a custom label
                    'label' => 'LLL:EXT:examples/Resources/Private/Language/locallang.xlf:additional_categories',
                    // This field should not be an exclude-field
                    'exclude' => FALSE,
                    // Override generic configuration, e.g. sort by title rather than by sorting
                    'fieldConfiguration' => array(
                            'foreign_table_where' => ' AND sys_category.sys_language_uid IN (-1, 0) ORDER BY sys_category.title ASC',
                    ),
                    // string (keyword), see TCA reference for details
                    'l10n_mode' => 'exclude',
                    // list of keywords, see TCA reference for details
                    'l10n_display' => 'hideDiff',
            ),
    );
```
