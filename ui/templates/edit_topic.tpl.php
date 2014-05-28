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
      <?=htmlspecialchars($tpl[ 'topic' ][ 'id' ])?> | 
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

      <?php if (! empty($tpl[ 'error_html' ])) { ?>
      <div class="alert alert-danger"><?=$tpl[ 'error_html' ]?></div>
      <?php } ?>

      <div class="well well-lg">
        <form id="xddb_form_edit" method="post" action="">

          <div style="padding-bottom: 15px; border-bottom: 1px solid #CCCCCC;">

            <!-- Types -->

            <div class="pull-right">
              <table>
          
            <?php
          
            foreach ($tpl[ 'topic' ][ 'types' ] as $type_id)
            {
                ?>
                <tr>
                  <td>
                    <input type="text" name="types[]" value="<?=htmlspecialchars($type_id)?>" />
                  </td>
                  <td>
                    <button class="btn btn-link" type="button" data-xddb_event="remove">
                      <span class="glyphicon glyphicon-remove"></span>
                    </button>
                  </td>
                </tr>
                <?php
            }
            
            ?>

                <tr id="xddb_new_type" class="hidden">
                  <td>
                    <input type="text" name="types[]" value="" />
                  </td>
                  <td>
                    <button class="btn btn-link" type="button" data-xddb_event="remove">
                      <span class="glyphicon glyphicon-remove"></span>
                    </button>
                  </td>
                </tr>
                
                <tr>
                  <td>
                    <button id="xddb_button_add_type" class="btn btn-link" type="button">
                      <span class="glyphicon glyphicon-plus"></span>
                      Add a type
                    </button>
                  </td>
                </tr>
            
              </table>
            </div>
          
            <!-- Unscoped base names -->

            <div>
            
            <?php 
          
            if (count($tpl[ 'topic' ][ 'unscoped_basenames' ]) === 0) 
                $tpl[ 'topic' ][ 'unscoped_basenames' ][ ] = ''; 
            
            foreach ($tpl[ 'topic' ][ 'unscoped_basenames' ] as $name) 
            { 
                ?>
          
              <input type="text" name="unscoped_basenames[]" value="<?=htmlspecialchars($name[ 'value' ])?>" style="font-weight: 500; font-size: 36px;" />
              <br />
          
                <?php 
            } 
        
            ?>
            
            </div>

          </div>
          
          <!-- Additional names -->
        
          <div>
          
            <h4>Additional names</h4>
            
            <table>
          
            <?php $i = -1; foreach ($tpl[ 'topic' ][ 'other_names' ] as $i => $name) { ?>

              <tr>
                <td><input type="text" name="other_names[<?=$i?>][type]" value="<?=htmlspecialchars($name[ 'type' ])?>" />:</td>
                <td><input type="text" name="other_names[<?=$i?>][value]" value="<?=htmlspecialchars($name[ 'value' ])?>" /></td>
                <td>
                  <?php if (count($name[ 'scope' ]) === 0) $name[ 'scope' ][ ] = ''; foreach ($name[ 'scope' ] as $scope) { ?>
                  <input type="text" name="other_names[<?=$i?>][scope][]" value="<?=htmlspecialchars($scope)?>" />
                  <?php } ?>
                </td>
                <td>
                  <button class="btn btn-link" type="button" data-xddb_event="remove">
                    <span class="glyphicon glyphicon-remove"></span>
                  </button>
                </td>
              </tr>
          
            <?php } $i++; ?>

              <tr id="xddb_new_name" class="hidden" data-xddb_counter="<?=$i?>">
                <td><input type="text" name="other_names[XDDB_COUNTER][type]" value="" />:</td>
                <td><input type="text" name="other_names[XDDB_COUNTER][value]" value="" /></td>
                <td><input type="text" name="other_names[XDDB_COUNTER][scope][]" value="" /></td>
                <td>
                  <button class="btn btn-link" type="button" data-xddb_event="remove">
                    <span class="glyphicon glyphicon-remove"></span>
                  </button>
                </td>
              </tr>

              <tr>
                <td>
                  <button id="xddb_button_add_name" class="btn btn-link" type="button">
                    <span class="glyphicon glyphicon-plus"></span>
                    Add a name
                  </button>
                </td>
              </tr>
          
            </table>

          </div>
          
          <!-- Subject identifiers -->
        
          <div>
          
            <h4>Identifier URLs</h4>
            
            <table>
          
            <?php
          
            foreach ($tpl[ 'topic' ][ 'subject_identifiers' ] as $url)
            {
                ?>
                <tr>
                  <td>
                    <input type="text" name="subject_identifiers[]" value="<?=htmlspecialchars($url)?>" style="width: 400px;" />
                  </td>
                  <td>
                    <button class="btn btn-link" type="button" data-xddb_event="remove">
                      <span class="glyphicon glyphicon-remove"></span>
                    </button>
                  </td>
                </tr>
                <?php
            }
            
            ?>
            
              <tr id="xddb_new_subject_identifier" class="hidden">
                <td>
                  <input type="text" name="subject_identifiers[]" value="" style="width: 400px;" />
                </td>
                <td>
                  <button class="btn btn-link" type="button" data-xddb_event="remove">
                    <span class="glyphicon glyphicon-remove"></span>
                  </button>
                </td>
              </tr>
              
              <tr>
                <td>
                  <button id="xddb_button_add_subject_identifier" class="btn btn-link" type="button">
                    <span class="glyphicon glyphicon-plus"></span>
                    Add an identifier URL
                  </button>
                </td>
              </tr>
              
            </table>

          </div>
          
          <!-- Subject locators -->
        
          <div>
          
            <h4>Resource URLs</h4>
            
            <table>
          
            <?php
          
            foreach ($tpl[ 'topic' ][ 'subject_locators' ] as $url)
            {
                ?>
                <tr>
                  <td>
                    <input type="text" name="subject_locators[]" value="<?=htmlspecialchars($url)?>" style="width: 400px;" />
                  </td>
                  <td>
                    <button class="btn btn-link" type="button" data-xddb_event="remove">
                      <span class="glyphicon glyphicon-remove"></span>
                    </button>
                  </td>
                </tr>
                <?php
            }
            
            ?>
            
              <tr id="xddb_new_subject_locator" class="hidden">
                <td>
                  <input type="text" name="subject_locators[]" value="" style="width: 400px;" />
                </td>
                <td>
                  <button class="btn btn-link" type="button" data-xddb_event="remove">
                    <span class="glyphicon glyphicon-remove"></span>
                  </button>
                </td>
              </tr>
                
              <tr>
                <td>
                  <button id="xddb_button_add_subject_locator" class="btn btn-link" type="button">
                    <span class="glyphicon glyphicon-plus"></span>
                    Add a resource URL
                  </button>
                </td>
              </tr>

            </table>

          </div>

          <!-- Occurrences -->
        
          <div>
          
            <h4>Properties</h4>
            
            <table>
          
            <?php $i = -1; foreach ($tpl[ 'topic' ][ 'occurrences' ] as $i => $occurrence) { ?>

              <tr>
                <td><input type="text" name="occurrences[<?=$i?>][type]" value="<?=htmlspecialchars($occurrence[ 'type' ])?>" />:</td>
                <td>
                  <input type="text" name="occurrences[<?=$i?>][value]" value="<?=htmlspecialchars($occurrence[ 'value' ])?>" />
                  <br />
                  <input type="text" name="occurrences[<?=$i?>][datatype]" value="<?=htmlspecialchars($occurrence[ 'datatype' ])?>" />
                </td>
                <td>
                  <?php if (count($occurrence[ 'scope' ]) === 0) $occurrence[ 'scope' ][ ] = ''; foreach ($occurrence[ 'scope' ] as $scope) { ?>
                  <input type="text" name="occurrences[<?=$i?>][scope][]" value="<?=htmlspecialchars($scope)?>" />
                  <?php } ?>
                </td>
                <td>
                  <button class="btn btn-link" type="button" data-xddb_event="remove">
                    <span class="glyphicon glyphicon-remove"></span>
                  </button>
                </td>
              </tr>
          
            <?php } $i++; ?>

              <tr id="xddb_new_occurrence" class="hidden" data-xddb_counter="<?=$i?>">
                <td><input type="text" name="occurrences[XDDB_COUNTER][type]" value="" />:</td>
                <td>
                  <input type="text" name="occurrences[XDDB_COUNTER][value]" value="" />
                  <br />
                  <input type="text" name="occurrences[XDDB_COUNTER][datatype]" value="" />
                </td>
                <td><input type="text" name="occurrences[XDDB_COUNTER][scope][]" value="" /></td>
                <td>
                  <button class="btn btn-link" type="button" data-xddb_event="remove">
                    <span class="glyphicon glyphicon-remove"></span>
                  </button>
                </td>
              </tr>

              <tr>
                <td>
                  <button id="xddb_button_add_occurrence" class="btn btn-link" type="button">
                    <span class="glyphicon glyphicon-plus"></span>
                    Add a property
                  </button>
                </td>
              </tr>
          
            </table>

          </div>
                    
          <!-- Save button -->
          
          <div>
            <p class="pull-right">
              <a href="<?=htmlspecialchars($tpl[ 'cancel_url' ])?>" class="btn btn-link">Cancel</a>
              <button type="submit" class="btn btn-primary">Save</button>
            </p>
          </div>
          
        </form>

      </div>

      <div class="footer">
        <p>XDDB 0.1 by Tim Strehle</p>
      </div>

    </div> <!-- /container -->

    <script src="<?=$tpl[ 'xddb_static_base_url' ]?>jquery.min.js"></script>
    <script src="<?=$tpl[ 'xddb_static_base_url' ]?>bootstrap/js/bootstrap.min.js"></script>
    
    <script>
    // <![CDATA[
    
    $(document).ready(function() 
    {
        var _private = { };
        
        _private.addFormSection = function(selector_to_clone)
        {
            var $elem_to_clone, $clone, counter;
            
            $elem_to_clone = $(selector_to_clone);            
            $clone = $elem_to_clone.clone();
            
            $clone
                .attr('id', '')
                .insertBefore(selector_to_clone)
                .removeClass('hidden')
                .find('input').first().focus();
                
            counter = $elem_to_clone.data('xddb_counter');
            
            if (counter !== undefined)
            {
                counter = parseInt(counter, 10);
                
                $clone.find('input').each(function(i, item)
                {
                    var $item = $(item);
                    var elem_name = $item.attr('name');
                    
                    if ((elem_name === undefined) || (elem_name.length < 1))
                    {
                        return;
                    }
                    
                    $item.attr('name', elem_name.replace(/XDDB_COUNTER/, counter));
                });
                
                $elem_to_clone.data('xddb_counter', (counter + 1));
            }
        };

        $([ 'type', 'name', 'subject_locator', 'subject_identifier', 'occurrence' ]).each(function(i, item)
        {
            var button_selector = '#xddb_button_add_' + item;
            var selector_to_clone = '#xddb_new_' + item;
            
            $(button_selector).on('click', function()
            {
                _private.addFormSection(selector_to_clone);
            });
        });
        
        $('#xddb_form_edit').on('click', 'button[data-xddb_event="remove"]', function(e)
        {
            $(e.target).closest('tr').remove();
        });

    });
    
    // ]]>
    </script>

  </body>
</html>
