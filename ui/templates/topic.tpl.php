<?php


function printReifierSummary(array $reifier, array $tpl)
{    
    $first_type = true;

    foreach ($reifier[ 'occurrence_type_index' ] as $occurrence_type => $keys)
    {
        if (! $first_type)
            echo ' ';

        $first_type = false;
        
        if ($occurrence_type !== $tpl[ 'id_text' ])
            echo htmlspecialchars($tpl[ 'topic_names' ][ $occurrence_type ]) . ': ';
    
        $first_value = true;
        
        foreach ($keys as $key)
        {
            $occurrence = $reifier[ 'topic' ][ 'occurrences' ][ $key ];
            
            if (! $first_value)
                echo ' / ';

            $first_value = false;

            if ($occurrence[ 'datatype' ] === $tpl[ 'id_xhtml' ])
            {
                echo $occurrence[ 'value' ];
            }
            else
            {
                echo htmlspecialchars($occurrence[ 'value' ]);
            }
        }
    }
}


header('Content-Type: application/xhtml+xml; charset=UTF-8');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-type" content="application/xhtml+xml; charset=UTF-8" />
    <meta charset="UTF-8" />

    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="<?=$tpl[ 'topicbank_base_url' ]?>bootstrap/assets/ico/favicon.ico" />

    <title>
      <?=htmlspecialchars($tpl[ 'topic' ][ 'label' ])?> | 
      <?=htmlspecialchars($tpl[ 'topicmap' ][ 'label' ])?>
    </title>

    <!-- Bootstrap core CSS -->
    <link href="<?=$tpl[ 'topicbank_base_url' ]?>bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="<?=$tpl[ 'topicbank_base_url' ]?>static/topicbank.css" rel="stylesheet" />

  </head>

  <body>

    <div class="container">

      <?php include('header.tpl.php'); ?>

      <div class="well well-lg">
        <h1>

          <!-- Topic label -->
          
          <?=htmlspecialchars($tpl[ 'topic' ][ 'label' ])?>
          
          <!-- Topic reifies ... -->
          
          <?php if (strlen($tpl[ 'topic' ][ 'reifies_summary_html' ]) > 0) { ?>
          <small><?=$tpl[ 'topic' ][ 'reifies_summary_html' ]?></small>
          <?php } ?>
          
          <!-- Types -->
          
          <small class="pull-right">
            <?php
          
            $first = true;
          
            foreach ($tpl[ 'topic' ][ 'types' ] as $type_id)
            {
                if (! $first)
                    echo ', ';
                
                echo htmlspecialchars($tpl[ 'topic_names' ][ $type_id ]);
                
                $first = false;   
            }

            ?>
          </small>
        </h1>

        <!-- Names -->
        
        <?php
        
        if (count($tpl[ 'topic' ][ 'names' ]) > 0)
        {
            echo '<p class="small"><i>';
            
            foreach ($tpl[ 'topic' ][ 'names' ] as $i => $name)
            {
                if ($i > 0)
                    echo '. ';
                
                if ($name[ 'type' ] !== 'basename')
                    echo htmlspecialchars($tpl[ 'topic_names' ][ $name[ 'type' ] ]) . ': ';
                
                echo htmlspecialchars($name[ 'value' ]);
                
                if (count($name[ 'scope' ]) > 0)
                {
                    echo ' <i>(';
                    
                    foreach ($name[ 'scope' ] as $j => $scope)
                    {
                        if ($j > 0)
                            echo ', ';
                            
                        echo htmlspecialchars($tpl[ 'topic_names' ][ $scope ]);
                    }
                    
                    echo ')</i>';
                }
                
                if (is_array($name[ 'reifier' ]))
                {
                    printf
                    (
                        '  (<a href="%stopic/%s"><span class="glyphicon glyphicon-paperclip"></span></a> ', 
                        $tpl[ 'topicbank_base_url' ], 
                        htmlspecialchars(urlencode($name[ 'reifier' ][ 'topic' ][ 'id' ]))
                    );
                    
                    printReifierSummary($name[ 'reifier' ], $tpl);

                    echo ')';
                }
            }
        
            echo '</i></p>';
        }
        
        ?>

        <!-- Subject identifiers -->
        
        <?php
        
        if (count($tpl[ 'topic' ][ 'subject_identifiers' ]) > 0)
        {
            echo '<p class="small"><i>';
            
            foreach ($tpl[ 'topic' ][ 'subject_identifiers' ] as $i => $subject_identifier)
            {
                if ($i > 0)
                    echo ' | ';
                
                printf('<a href="%s">%s</a>', htmlspecialchars($subject_identifier), htmlspecialchars($subject_identifier));
            }
        
            echo '</i></p>';
        }
        
        ?>

        <!-- Subject locators -->
        
        <?php
        
        if (count($tpl[ 'topic' ][ 'subject_locators' ]) > 0)
        {
            echo '<p class="small"><i>Locator: ';
            
            foreach ($tpl[ 'topic' ][ 'subject_locators' ] as $i => $subject_locator)
            {
                if ($i > 0)
                    echo ' | ';
                
                printf('<a href="%s">%s</a>', htmlspecialchars($subject_locator), htmlspecialchars($subject_locator));
            }
        
            echo '</i></p>';
        }
        
        ?>

        <!-- Text -->

        <?php        

        if (! isset($tpl[ 'occurrence_type_index' ][ $tpl[ 'id_text' ] ]))
            $tpl[ 'occurrence_type_index' ][ $tpl[ 'id_text' ] ] = [ ];

        foreach ($tpl[ 'occurrence_type_index' ][ $tpl[ 'id_text' ] ] as $key)
        {
            $occurrence = $tpl[ 'topic' ][ 'occurrences' ][ $key ];
            
            echo '<div>';
            
            if ($occurrence[ 'datatype' ] === $tpl[ 'id_xhtml' ])
            {
                echo $occurrence[ 'value' ];
            }
            else
            {
                echo '<p>' . nl2br(htmlspecialchars($occurrence[ 'value' ])) . '</p>';
            }

            if (count($occurrence[ 'scope' ]) > 0)
            {
                echo '<p><i>(';
                
                foreach ($occurrence[ 'scope' ] as $j => $scope)
                {
                    if ($j > 0)
                        echo ', ';
                        
                    echo htmlspecialchars($tpl[ 'topic_names' ][ $scope ]);
                }
                
                echo ')</i></p>';
            }
            
            echo '</div>';
        }
        
        ?>

        <!-- Occurrences -->
        
        <table>
        <?php

        foreach ($tpl[ 'occurrence_type_index' ] as $occurrence_type => $keys)
        {
            if ($occurrence_type === $tpl[ 'id_text' ])
                continue;
                        
            ?>
            <tr>
              <td style="padding-right: 15px;" valign="top"><?=htmlspecialchars($tpl[ 'topic_names' ][ $occurrence_type ])?>:</td>
              <td valign="top">
            <?php
        
            $first = true;
            
            foreach ($keys as $key)
            {
                $occurrence = $tpl[ 'topic' ][ 'occurrences' ][ $key ];
                
                if (! $first)
                    echo '<br />';

                if ($occurrence[ 'datatype' ] === $tpl[ 'id_xhtml' ])
                {
                    echo $occurrence[ 'value' ];
                }
                else
                {                    
                    echo nl2br(htmlspecialchars($occurrence[ 'value' ]));
                }
                
                if (count($occurrence[ 'scope' ]) > 0)
                {
                    echo ' <i>(';
                
                    foreach ($occurrence[ 'scope' ] as $j => $scope)
                    {
                        if ($j > 0)
                            echo ', ';
                        
                        echo htmlspecialchars($tpl[ 'topic_names' ][ $scope ]);
                    }
                
                    echo ')</i>';
                }

                if (is_array($occurrence[ 'reifier' ]))
                {
                    printf
                    (
                        '  (<a href="%stopic/%s"><span class="glyphicon glyphicon-paperclip"></span></a> ', 
                        $tpl[ 'topicbank_base_url' ], 
                        htmlspecialchars(urlencode($occurrence[ 'reifier' ][ 'topic' ][ 'id' ]))
                    );
                    
                    printReifierSummary($occurrence[ 'reifier' ], $tpl);

                    echo ')';
                }
                
                $first = false;
            }
            ?>
              </td>
            </tr>
            <?php
        }

        ?>
        </table>

        <div>
          
          <br />
          
          <small class="text-muted">Last modified: <?=htmlspecialchars($tpl[ 'topic' ][ 'updated' ])?></small>
                
          <a href="<?=htmlspecialchars($tpl[ 'edit_url' ])?>" class="btn btn-default pull-right">Edit</a>
                  
          <form method="POST" action="<?=htmlspecialchars($tpl[ 'delete_url' ])?>" class="pull-right">
            <button type="submit" class="btn btn-link pull-right" onclick="return confirm('Delete this topic?');">Delete</button>
            <input type="hidden" name="delete" value="1" />
          </form>
                    
        </div>
        
      </div>

        <!-- Associations -->
        
      <div>
      
        <?php
        
        foreach ($tpl[ 'association_type_index' ] as $association_type => $role_types)
        {
            foreach ($role_types as $role_type => $association_keys)
            {
                ?>
                
          <h4>
            <?=htmlspecialchars($tpl[ 'topic_names' ][ $association_type ])?>
            (as <?=htmlspecialchars($tpl[ 'topic_names' ][ $role_type ])?>)
          </h4>

                <?php
                          
                foreach ($association_keys as $association_key)
                {
                    echo '<p>';
                    
                    $association = $tpl[ 'associations' ][ $association_key ];
                    
                    foreach ($association[ 'roles' ] as $role)
                    {
                        printf
                        (
                            '%s: <a href="%s">%s</a>. ',
                            htmlspecialchars($tpl[ 'topic_names' ][ $role[ 'type' ] ]),
                            htmlspecialchars($tpl[ 'topicbank_base_url' ] . 'topic/' . urlencode($role[ 'player' ])),
                            htmlspecialchars($tpl[ 'topic_names' ][ $role[ 'player' ] ])
                        );

                        if (is_array($role[ 'reifier' ]))
                        {
                            printf
                            (
                                '  (<a href="%stopic/%s"><span class="glyphicon glyphicon-paperclip"></span></a> ', 
                                $tpl[ 'topicbank_base_url' ], 
                                htmlspecialchars(urlencode($role[ 'reifier' ][ 'topic' ][ 'id' ]))
                            );
                    
                            printReifierSummary($role[ 'reifier' ], $tpl);

                            echo ') ';
                        }
                    }

                    $br = false;

                    if (count($association[ 'scope' ]) > 0)
                    {
                        echo '<br /><i>(';
                        
                        $br = true;
                
                        foreach ($association[ 'scope' ] as $j => $scope)
                        {
                            if ($j > 0)
                                echo ', ';
                        
                            echo htmlspecialchars($tpl[ 'topic_names' ][ $scope ]);
                        }
                
                        echo ')</i>';
                    }

                    if (is_array($association[ 'reifier' ]))
                    {
                        if (! $br)
                            echo '<br />';
                            
                        printf
                        (
                            '  (<a href="%stopic/%s"><span class="glyphicon glyphicon-paperclip"></span></a> ', 
                            $tpl[ 'topicbank_base_url' ], 
                            htmlspecialchars(urlencode($association[ 'reifier' ][ 'topic' ][ 'id' ]))
                        );
                    
                        printReifierSummary($association[ 'reifier' ], $tpl);

                        echo ')';
                    }
                    
                    echo '</p>';
                }
            }
        }
        
        ?>
        
      </div>

      <div class="footer">
        <p>TopicBank 0.1 by Tim Strehle</p>
      </div>

    </div> <!-- /container -->

    <script src="<?=$tpl[ 'topicbank_base_url' ]?>jquery/jquery.min.js"></script>
    <script src="<?=$tpl[ 'topicbank_base_url' ]?>bootstrap/js/bootstrap.min.js"></script>

  </body>
</html>
