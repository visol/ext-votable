Votable
=======

Versatile and pluggable Voting system. Declare a content type to be votable in your `ext_tables`, create a content element votable on a page, fine tune the CSS file and your Frontend Users can vote...  

User can vote for something:

![alt text](https://raw.githubusercontent.com/visol/ext-votable/master/Documentation/Frontend-01.png)

Once voted:

![alt text](https://raw.githubusercontent.com/visol/ext-votable/master/Documentation/Frontend-02.png)


Data Model
==========


```

    ----------------------------------            ----------------------------------            ----------------------------------
    | fe_users                       |            | tx_votable_domain_model_vote   |            | * tx_votable_vote_record_mm    |
    ----------------------------------            ---------------------------------- (limit 1)  ----------------------------------
    |                                | ---------- | * voting (int)                 | ---------  | * uid_local                    |
    |                                |  1      n  | * item (int)                   |  1     1   | * uid_foreign                  |
    |                                |            | * user (int)                   |            | * tablenames                   |
    |                                |            |                                |        --  | * fieldname                    |
    ----------------------------------            ----------------------------------        | n ---------------------------------
                                                                                            |
                                                                                            |
                                                                                            |    ----------------------------------
                                                                                            |    | * (any content)                |
                                                                                            | 1  ----------------------------------
                                                                                            |--  | * votes                        |
                                                                                                 | * rank                         |
                                                                                                 |                                |
                                                                                                 ----------------------------------

```

Installation
============

Install the extension as normal in the Extension Manager. Make sure to declare what tables you wish to be "votable" there.

Next step is to create a content element of type votable that will configure the jQuery plugin (automatically loaded)

![alt text](https://raw.githubusercontent.com/visol/ext-votable/master/Documentation/Backend-01.png)

Then on the Frontend, everywhere you want the votable widget displayed, you must generate this kind of HTML where x is the current id of the votable object as declared in the plugin settings:  

```

    <div class="widget-votable" data-object="x"/>
```


Important: load and adjust the CSS in your plugin. (todo: the loading could be made more automatic but also configurable)


```

    page.includeCSS {
    
        # Sourced version
        votableCss = EXT:votable/Resources/Public/StyleSheets/votable.css
    }
```


Make Votable API
================
    
This code is automatically generated for you according the the settings in the Extension Manager. However, you can fiddle around and ajust it for your needs. It will add extra fields to the table "tx_domain_domain_model_foo", by default `votes` and `rank`. 

```

    \Visol\Votable\VotingUtility::makeVotable(
    
            # Mandatory extension name to have it seamlessly integrated when activating the extension in the EM. 
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
                    'exclude' => false,
                    
                    // Override generic configuration, e.g. sort by title rather than by sorting
                    'fieldConfiguration' => array(
                            ...
                    ),
            ),
    );
```


Build assets
============

Source is located at `Resources/Public/JavaScript/*.js`. Grunt will watch the files and generate a min file as editing. To start watching:

```
	npm install
	grunt watch
```

