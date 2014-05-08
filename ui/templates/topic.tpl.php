<?php

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
    <link rel="shortcut icon" href="<?=$tpl[ 'xddb_static_base_url' ]?>bootstrap/assets/ico/favicon.ico" />

    <title>
      <?=htmlspecialchars($tpl[ 'topic' ][ 'display_name' ][ 'value' ])?> | 
      <?=htmlspecialchars($tpl[ 'topicmap' ][ 'display_name' ])?>
    </title>

    <!-- Bootstrap core CSS -->
    <link href="<?=$tpl[ 'xddb_static_base_url' ]?>bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="<?=$tpl[ 'xddb_static_base_url' ]?>xddb.css" rel="stylesheet" />

  </head>

  <body>

    <div class="container">
      <div class="header">
        <ul class="nav nav-pills pull-right">
          <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
              <span class="glyphicon glyphicon-cog"></span>
              <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li><a href="#">Logged in as …</a></li>
              <li><a href="#">Log out</a></li>
            </ul>
          </li>
        </ul>
      <form class="form-inline pull-right" role="search">
        <div class="form-group">
          <input type="text" class="form-control" placeholder="Search" />
        </div>
        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
      </form>
        <h3 class="text-muted"><?=htmlspecialchars($tpl[ 'topicmap' ][ 'display_name' ])?></h3>
      </div>

      <div class="well well-lg">
        <h1>
          <?=htmlspecialchars($tpl[ 'topic' ][ 'display_name' ][ 'value' ])?>
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

        <!-- Additional names -->
        
        <?php
        
        if (count($tpl[ 'topic' ][ 'additional_names' ]) > 0)
        {
            echo '<p class="small"><i>';
            
            foreach ($tpl[ 'topic' ][ 'additional_names' ] as $i => $name)
            {
                if ($i > 0)
                    echo '. ';
                
                if ($name[ 'type' ] !== 'basename')
                    echo htmlspecialchars($tpl[ 'topic_names' ][ $name[ 'type' ] ]) . ': ';
                
                echo htmlspecialchars($name[ 'value' ]);
                
                if (count($name[ 'scope' ]) > 0)
                {
                    echo ' (';
                    
                    foreach ($name[ 'scope' ] as $j => $scope)
                    {
                        if ($j > 0)
                            echo ', ';
                            
                        echo htmlspecialchars($tpl[ 'topic_names' ][ $scope ]);
                    }
                    
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

        <!-- Description -->

        <?php        

        if (! isset($tpl[ 'occurrence_type_index' ][ 'description' ]))
            $tpl[ 'occurrence_type_index' ][ 'description' ] = [ ];

        foreach ($tpl[ 'occurrence_type_index' ][ 'description' ] as $key)
        {
            $occurrence = $tpl[ 'topic' ][ 'occurrences' ][ $key ];
            
            echo '<div>';
            
            if ($occurrence[ 'datatype' ] === 'datatype-xhtml')
            {
                echo $occurrence[ 'value' ];
            }
            else
            {
                echo '<p>' . htmlspecialchars($occurrence[ 'value' ]) . '</p>';
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
            if ($occurrence_type === 'description')
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
                    
                echo htmlspecialchars($occurrence[ 'value' ]);
                
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
                
                $first = false;
            }
            ?>
              </td>
            </tr>
            <?php
        }

        ?>
        </table>
      </div>

      <div class="row marketing">
        <div class="col-lg-6">

        <!-- Associations -->

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
                        if ($role[ 'player' ] === $tpl[ 'topic' ][ 'id' ])
                            continue;
                            
                        printf
                        (
                            '%s: <a href="%s">%s</a>. ',
                            htmlspecialchars($tpl[ 'topic_names' ][ $role[ 'type' ] ]),
                            htmlspecialchars($tpl[ 'xddb_base_url' ] . 'topic/' . urlencode($role[ 'player' ])),
                            htmlspecialchars($tpl[ 'topic_names' ][ $role[ 'player' ] ])
                        );
                    }
                    
                    echo '</p>';
                }
            }
        }
        
        ?>

        </div>

        <div class="col-lg-6">
          <h4>Subheading</h4>
          <p>Donec id elit non mi porta gravida at eget metus. Maecenas faucibus mollis interdum.</p>

          <h4>Subheading</h4>
          <p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras mattis consectetur purus sit amet fermentum.</p>

          <h4>Subheading</h4>
          <p>Maecenas sed diam eget risus varius blandit sit amet non magna.</p>
        </div>
      </div>

      <div class="footer">
        <p>XDDB 0.1 by Tim Strehle</p>
      </div>

    </div> <!-- /container -->

    <script src="<?=$tpl[ 'xddb_static_base_url' ]?>jquery.min.js"></script>
    <script src="<?=$tpl[ 'xddb_static_base_url' ]?>bootstrap/js/bootstrap.min.js"></script>

  </body>
</html>
