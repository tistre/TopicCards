<?php

function button_remove()
{
    ?>
    
    <button class="btn btn-link" type="button" data-topicbank_event="remove">
      <span class="glyphicon glyphicon-remove"></span>
    </button>

    <?php
}


function button_add(array $params)
{
    ?>

    <button data-topicbank_event="<?=htmlspecialchars($params[ 'event' ])?>" class="btn btn-link" type="button">
      <span class="glyphicon glyphicon-plus"></span>
      <?=htmlspecialchars($params[ 'label' ])?>
    </button>
    
    <?php
}


function button_choose_topic(array $params)
{
    ?>
    
    <button class="btn btn-link" data-topicbank_event="show_choose_topic_dialog" data-topicbank_what="<?=htmlspecialchars($params[ 'what' ])?>" type="button" data-toggle="modal" data-target="#choose_topic_dialog">
      <span data-topicbank_element="name"><?=htmlspecialchars($params[ 'label' ])?></span>:
      <span class="caret"></span>
    </button>

    <?php
}


function button_reify(array $params)
{
    global $tpl;

    $data_str = '';
    
    if (! empty($params[ 'reifier' ]))
    {
        $url = sprintf('%sedit_topic/%s', TOPICBANK_BASE_URL, $params[ 'reifier' ]);
    }
    elseif (empty($params[ 'reifies_id' ]))
    {
        $url = "javascript:alert('Please save this topic first.');";
    }        
    else
    {
        $url = '#';
        
        $data =
        [
            'event' => 'submit_reify_form',
            'reifies_type' => $params[ 'reifies_type' ],
            'reifies_id' => $params[ 'reifies_id' ],
            'topic' => $tpl[ 'topic' ][ 'id' ],
            'association' => 
            (
                isset($params[ 'association' ])
                ? $params[ 'association' ]
                : ''
            )
        ];
                
        foreach ($data as $key => $value)
            $data_str .= sprintf(' data-topicbank_%s="%s"', $key, htmlspecialchars($value));
    }

    ?>
    
    <a class="btn btn-link" href="<?=htmlspecialchars($url)?>" <?=$data_str?>>
      <?php if (empty($params[ 'reifier' ])) { ?>
      <span class="glyphicon glyphicon-plus"></span>
      <?php } ?>
      <span class="glyphicon glyphicon-paperclip"></span>
    </a>

    <?php
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

      <?php if (! empty($tpl[ 'error_html' ])) { ?>
      <div class="alert alert-danger"><?=$tpl[ 'error_html' ]?></div>
      <?php } ?>

      <form id="topicbank_form_edit" method="post" action="">

      <div class="well well-lg">

          <div>

            <!-- Types -->

            <div class="pull-right">
              <table>
          
            <?php
          
            foreach ($tpl[ 'topic' ][ 'types' ] as $type_id)
            {
                ?>
                <tr>
                  <td>
                    <?php button_choose_topic([ 'what' => 'topic_type', 'label' => $tpl[ 'topic_names' ][ $type_id ] ]); ?>
                    <input type="hidden" name="types[]" value="<?=htmlspecialchars($type_id)?>" data-topicbank_element="id" />
                  </td>
                  <td><?php button_remove(); ?></td>
                </tr>
                <?php
            }
            
            ?>

                <tr data-topicbank_template="new_type" class="hidden">
                  <td>
                    <?php button_choose_topic([ 'what' => 'topic_type', 'label' => 'Type' ]); ?>
                    <input type="hidden" name="types[]" value="" data-topicbank_element="id" />
                  </td>
                  <td><?php button_remove(); ?></td>
                </tr>
                
                <tr>
                  <td>
                    <?php button_add([ 'event' => 'new_type', 'label' => 'Add Type' ]); ?>
                  </td>
                </tr>
            
              </table>
            </div>

            <h1>
          
              <!-- Topic label -->

              <?=htmlspecialchars($tpl[ 'topic' ][ 'label' ])?>        

              <!-- Topic reifies ... -->
          
              <?php if (strlen($tpl[ 'topic' ][ 'reifies_summary_html' ]) > 0) { ?>
              <small><?=$tpl[ 'topic' ][ 'reifies_summary_html' ]?></small>
              <?php } ?>
            
            </h1>

          </div>
          
          <!-- Names -->
        
          <div>
          
            <h4>Names</h4>
            
            <table width="100%">
          
            <?php $i = -1; foreach ($tpl[ 'topic' ][ 'names' ] as $i => $name) { ?>

              <tr>
                <td width="25%" valign="top">
                  <?php button_choose_topic([ 'what' => 'name_type', 'label' => $tpl[ 'topic_names' ][ $name[ 'type' ] ] ]); ?>
                  <input type="hidden" name="names[<?=$i?>][type]" value="<?=htmlspecialchars($name[ 'type' ])?>" data-topicbank_element="id" />
                </td>
                <td width="70%" valign="top">
                
                  <input type="text" name="names[<?=$i?>][value]" value="<?=htmlspecialchars($name[ 'value' ])?>" style="width:100%;" />
                  <input type="hidden" name="names[<?=$i?>][id]" value="<?=htmlspecialchars($name[ 'id' ])?>" />
                  
                  <table>
                    <tr>
                      <td>
                        <table>
              
                          <?php $j = -1; foreach ($name[ 'scope' ] as $j => $scope) { ?>
                          <tr>
                            <td>
                              <?php button_choose_topic([ 'what' => 'name_scope', 'label' => $tpl[ 'topic_names' ][ $scope ] ]); ?>
                              <input type="hidden" name="names[<?=$i?>][scope][<?=$j?>]" value="<?=htmlspecialchars($scope)?>" data-topicbank_element="id" />
                            </td>
                            <td><?php button_remove(); ?></td>
                          </tr>              
                          <?php } $j++; ?>
              
                          <tr data-topicbank_template="new_name_scope" class="hidden" data-topicbank_counter_value="<?=$j?>" data-topicbank_counter_name="TOPICBANK_COUNTER2">
                            <td>
                              <?php button_choose_topic([ 'what' => 'name_scope', 'label' => '[Scope]' ]); ?>
                              <input type="hidden" name="names[<?=$i?>][scope][TOPICBANK_COUNTER2]" value="" data-topicbank_element="id" />
                            </td>
                            <td><?php button_remove(); ?></td>
                          </tr>
                
                          <tr>
                            <td>
                              <?php button_add([ 'event' => 'new_name_scope', 'label' => 'Add Scope' ]); ?>
                            </td>
                          </tr>
              
                        </table>
                      </td>
                      <td>
                        <?php button_reify([ 'reifier' => $name[ 'reifier' ], 'reifies_type' => 'name', 'reifies_id' => $name[ 'id' ] ]); ?>
                        <input type="hidden" name="names[<?=$i?>][reifier]" value="<?=htmlspecialchars($name[ 'reifier' ])?>" />
                      </td>
                    </tr>
                  </table>
                </td>
                <td width="5%" valign="top"><?php button_remove(); ?></td>
              </tr>
          
            <?php } $i++; ?>

              <tr data-topicbank_template="new_name" class="hidden" data-topicbank_counter_value="<?=$i?>" data-topicbank_counter_name="TOPICBANK_COUNTER1">
                <td width="25%" valign="top">
                
                  <?php button_choose_topic([ 'what' => 'name_type', 'label' => '[Type]' ]); ?>
                  <input type="hidden" name="names[TOPICBANK_COUNTER1][type]" value="" data-topicbank_element="id" />
                </td>
                <td width="70%" valign="top">
                
                  <input type="text" name="names[TOPICBANK_COUNTER1][value]" value="" style="width:100%;" />
                  <input type="hidden" name="names[TOPICBANK_COUNTER1][id]" value="" />
                
                  <table>
                    <tr>
                      <td>
                        <table>
                  
                          <tr data-topicbank_template="new_name_scope" class="hidden" data-topicbank_counter_value="0" data-topicbank_counter_name="TOPICBANK_COUNTER2">
                            <td>
                              <?php button_choose_topic([ 'what' => 'name_scope', 'label' => '[Scope]' ]); ?>
                              <input type="hidden" name="names[TOPICBANK_COUNTER1][scope][TOPICBANK_COUNTER2]" value="" data-topicbank_element="id" />
                            </td>
                            <td><?php button_remove(); ?></td>
                          </tr>
                
                          <tr>
                            <td>
                              <?php button_add([ 'event' => 'new_name_scope', 'label' => 'Add Scope' ]); ?>
                            </td>
                          </tr>

                        </table>
                      </td>
                      <td>
                        <?php button_reify([ 'reifies_type' => 'name' ]); ?>
                        <input type="hidden" name="names[TOPICBANK_COUNTER1][reifier]" value="" />
                      </td>
                    </tr>
                  </table>

                </td>
                <td width="5%" valign="top"><?php button_remove(); ?></td>
              </tr>

              <tr>
                <td>
                  <?php button_add([ 'event' => 'new_name', 'label' => 'Add Name' ]); ?>
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
                  <td width="95%" valign="top">
                    <input type="text" name="subject_identifiers[]" value="<?=htmlspecialchars($url)?>" style="width: 100%;" />
                  </td>
                  <td width="5%" valign="top"><?php button_remove(); ?></td>
                </tr>
                <?php
            }
            
            ?>
            
              <tr data-topicbank_template="new_subject_identifier" class="hidden">
                <td width="95%" valign="top">
                  <input type="text" name="subject_identifiers[]" value="" style="width: 100%;" />
                </td>
                <td width="5%" valign="top"><?php button_remove(); ?></td>
              </tr>
              
              <tr>
                <td valign="top">
                  <?php button_add([ 'event' => 'new_subject_identifier', 'label' => 'Add Identifier URL' ]); ?>
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
                  <td width="95%" valign="top">
                    <input type="text" name="subject_locators[]" value="<?=htmlspecialchars($url)?>" style="width: 100%;" />
                  </td>
                  <td width="5%" valign="top"><?php button_remove(); ?></td>
                </tr>
                <?php
            }
            
            ?>
            
              <tr data-topicbank_template="new_subject_locator" class="hidden">
                <td width="95%" valign="top">
                  <input type="text" name="subject_locators[]" value="" style="width: 100%;" />
                </td>
                <td width="5%" valign="top"><?php button_remove(); ?></td>
              </tr>
                
              <tr>
                <td valign="top">
                  <?php button_add([ 'event' => 'new_subject_locator', 'label' => 'Add Resource URL' ]); ?>
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
              
                <!-- Occurrence type -->
                
                <?php if ($occurrence[ 'type' ] === $tpl[ 'id_text' ]) { ?>                
                <td colspan="2" width="95%" valign="top">
                <?php } else { ?>
                <td width="25%" valign="top">
                <?php } ?>
                
                  <?php button_choose_topic([ 'what' => 'occurrence_type', 'label' => $tpl[ 'topic_names' ][ $occurrence[ 'type' ] ] ]); ?>
                  <input type="hidden" name="occurrences[<?=$i?>][type]" value="<?=htmlspecialchars($occurrence[ 'type' ])?>" data-topicbank_element="id" />

                <?php if ($occurrence[ 'type' ] !== $tpl[ 'id_text' ]) { ?>                
                </td>
                <?php } ?>
                
                <!-- Occurrence value and datatype -->
                
                <?php if ($occurrence[ 'type' ] !== $tpl[ 'id_text' ]) { ?>
                <td width="70%" valign="top">
                <?php } ?>
                
                  <textarea name="occurrences[<?=$i?>][value]" style="width:100%; <?php if ($occurrence[ 'datatype' ] === $tpl[ 'id_xhtml' ]) { ?>height: 300px;<?php } ?>" <?php if ($occurrence[ 'datatype' ] === $tpl[ 'id_xhtml' ]) { ?>data-topicbank_html_editor="enabled"<?php } ?>><?=htmlspecialchars($occurrence[ 'value' ])?></textarea>
                  <table>
                    <tr>
                      <td valign="top">
                        <?php button_choose_topic([ 'what' => 'occurrence_datatype', 'label' => $tpl[ 'topic_names' ][ $occurrence[ 'datatype' ] ] ]); ?>
                        <input type="hidden" name="occurrences[<?=$i?>][datatype]" value="<?=htmlspecialchars($occurrence[ 'datatype' ])?>" data-topicbank_element="id" />
                      </td>
                                  
                      <!-- Occurrence scope -->
                
                      <td valign="top">
                
                        <table>
              
                        <?php $j = -1; foreach ($occurrence[ 'scope' ] as $j => $scope) { ?>
                          <tr>
                            <td>
                              <?php button_choose_topic([ 'what' => 'occurrence_scope', 'label' => $tpl[ 'topic_names' ][ $scope ] ]); ?>
                              <input type="hidden" name="occurrences[<?=$i?>][scope][<?=$j?>]" value="<?=htmlspecialchars($scope)?>" data-topicbank_element="id" />
                            </td>
                            <td><?php button_remove(); ?></td>
                          </tr>              
                        <?php } $j++; ?>
              
                          <tr data-topicbank_template="new_occurrence_scope" class="hidden" data-topicbank_counter_value="<?=$j?>" data-topicbank_counter_name="TOPICBANK_COUNTER2">
                            <td>
                              <?php button_choose_topic([ 'what' => 'occurrence_scope', 'label' => '[Scope]' ]); ?>
                              <input type="hidden" name="occurrences[<?=$i?>][scope][TOPICBANK_COUNTER2]" value="" data-topicbank_element="id" />
                            </td>
                            <td><?php button_remove(); ?></td>
                          </tr>
                
                          <tr>
                            <td>
                              <?php button_add([ 'event' => 'new_occurrence_scope', 'label' => 'Add Scope' ]); ?>
                            </td>
                          </tr>
              
                        </table>
                
                      </td>
                      
                      <td valign="top">
                        <?php button_reify([ 'reifier' => $occurrence[ 'reifier' ], 'reifies_type' => 'occurrence', 'reifies_id' => $occurrence[ 'id' ] ]); ?>
                        <input type="hidden" name="occurrences[<?=$i?>][reifier]" value="<?=htmlspecialchars($occurrence[ 'reifier' ])?>" />
                      </td>
                    </tr>
                  </table>
                </td>
                <td width="5%" valign="top"><?php button_remove(); ?></td>
              </tr>
          
            <?php } $i++; ?>

              <tr data-topicbank_template="new_occurrence" class="hidden" data-topicbank_counter_value="<?=$i?>" data-topicbank_counter_name="TOPICBANK_COUNTER1">

                <!-- New occurrence type -->
                
                <td width="25%" valign="top">
                  <?php button_choose_topic([ 'what' => 'occurrence_type', 'label' => '[Property]' ]); ?>
                  <input type="hidden" name="occurrences[TOPICBANK_COUNTER1][type]" value="" data-topicbank_element="id" />
                </td>

                <!-- New occurrence value and datatype -->
                
                <td width="70%" valign="top">
                  <textarea name="occurrences[TOPICBANK_COUNTER1][value]" style="width:100%;"></textarea>
                  
                  <table>
                    <tr>
                      <td valign="top">
                        <?php button_choose_topic([ 'what' => 'occurrence_datatype', 'label' => '[Datatype]' ]); ?>
                        <input type="hidden" name="occurrences[TOPICBANK_COUNTER1][datatype]" value="" data-topicbank_element="id" />
                      </td>
                
                      <!-- New occurrence scopes -->
                
                      <td valign="top">
                  
                        <table>
                  
                          <tr data-topicbank_template="new_occurrence_scope" class="hidden" data-topicbank_counter_value="0" data-topicbank_counter_name="TOPICBANK_COUNTER2">
                            <td>
                              <?php button_choose_topic([ 'what' => 'occurrence_scope', 'label' => '[Scope]' ]); ?>
                              <input type="hidden" name="occurrences[TOPICBANK_COUNTER1][scope][TOPICBANK_COUNTER2]" value="" data-topicbank_element="id" />
                            </td>
                            <td><?php button_remove(); ?></td>
                          </tr>
                
                          <tr>
                            <td>
                              <?php button_add([ 'event' => 'new_occurrence_scope', 'label' => 'Add Scope' ]); ?>
                            </td>
                          </tr>
              
                        </table>
                  
                      </td>
                      <td valign="top">
                        <?php button_reify([ 'reifies_type' => 'occurrence' ]); ?>
                        <input type="hidden" name="occurrences[TOPICBANK_COUNTER1][reifier]" value="" />
                      </td>
                    </tr>
                  </table>
                </td>
                <td width="5%" valign="top"><?php button_remove(); ?></td>
              </tr>

              <tr>
                <td valign="top">
                  <?php button_add([ 'event' => 'new_occurrence', 'label' => 'Add Property' ]); ?>
                </td>
              </tr>
          
            </table>

          </div>
                    
          <!-- Save button -->
          
          <div>
            
            <br />
            
            <small class="text-muted">Last modified: <?=htmlspecialchars($tpl[ 'topic' ][ 'updated' ])?></small>
            
            <p class="pull-right">
            
              <a href="<?=htmlspecialchars($tpl[ 'cancel_url' ])?>" class="btn btn-link">Cancel</a>
              
              <button type="submit" class="btn btn-default">Save</button>
              
              <button type="submit" class="btn btn-primary" onclick="$('#input_close_after_save').val('1');">Save and close</button>
              
              <input type="hidden" id="input_close_after_save" name="close_after_save" value="" />
              
            </p>
          </div>
          
      </div>

      <!-- Associations -->
    
      <div>
      
        <h4>Associations</h4>
        
        <table>
      
        <?php $i = -1; foreach ($tpl[ 'associations' ] as $i => $association) { ?>

          <tr>
          
            <!-- Association type -->
            
            <td>
              <?php button_choose_topic([ 'what' => 'association_type', 'label' => $tpl[ 'topic_names' ][ $association[ 'type' ] ] ]); ?>
              <input type="hidden" name="associations[<?=$i?>][type]" value="<?=htmlspecialchars($association[ 'type' ])?>" data-topicbank_element="id" />
              
              <input type="hidden" name="associations[<?=$i?>][id]" value="<?=htmlspecialchars($association[ 'id' ])?>" />
              <input type="hidden" name="associations[<?=$i?>][delete]" value="0" />
              
            </td>
            
            <!-- Association roles -->
            
            <td>
            
              <table>

                <?php foreach ($association[ 'roles' ] as $j => $role) { ?>
                <tr>
                  <td>
                    <?php button_choose_topic([ 'what' => 'role_type', 'label' => $tpl[ 'topic_names' ][ $role[ 'type' ] ] ]); ?>
                    <input type="hidden" name="associations[<?=$i?>][roles][<?=$j?>][type]" value="<?=htmlspecialchars($role[ 'type' ])?>" data-topicbank_element="id" />
                  </td>
                  <td>
                    <?php if ($role[ 'this_topic' ]) { ?>
                    (This topic)
                    <?php } else { ?>
                    <?php button_choose_topic([ 'what' => 'role_player', 'label' => $tpl[ 'topic_names' ][ $role[ 'player' ] ] ]); ?>
                    <?php } ?>
                    <input type="hidden" name="associations[<?=$i?>][roles][<?=$j?>][player]" value="<?=htmlspecialchars($role[ 'player' ])?>" data-topicbank_element="id" />
                  </td>
                  <td>
                    <?php button_reify([ 'reifier' => $role[ 'reifier' ], 'reifies_type' => 'role', 'reifies_id' => $role[ 'id' ], 'association' => $association[ 'id' ] ]); ?>
                    <input type="hidden" name="associations[<?=$i?>][roles][<?=$j?>][reifier]" value="<?=htmlspecialchars($role[ 'reifier' ])?>" />
                  </td>
                  <td>
                    <?php if (! $role[ 'this_topic' ]) button_remove(); ?>
                  </td>
                </tr>
                <?php } $j++; ?>

                <tr data-topicbank_template="new_role" class="hidden" data-topicbank_counter_value="<?=$j?>" data-topicbank_counter_name="TOPICBANK_COUNTER2">
                  <td>
                    <?php button_choose_topic([ 'what' => 'role_type', 'label' => '[Role type]' ]); ?>
                    <input type="hidden" name="associations[<?=$i?>][roles][TOPICBANK_COUNTER2][type]" value="" data-topicbank_element="id" />
                  </td>
                  <td>
                    <?php button_choose_topic([ 'what' => 'role_player', 'label' => '[Role player]' ]); ?>
                    <input type="hidden" name="associations[<?=$i?>][roles][TOPICBANK_COUNTER2][player]" value="" data-topicbank_element="id" />
                  </td>
                  <td>
                    <?php button_reify([ 'reifies_type' => 'role' ]); ?>
                    <input type="hidden" name="associations[<?=$i?>][roles][TOPICBANK_COUNTER2][reifier]" value="" />
                  </td>
                  <td><?php button_remove(); ?></td>
                </tr>

                <tr>
                  <td>
                    <?php button_add([ 'event' => 'new_role', 'label' => 'Add Role' ]); ?>
                  </td>
                </tr>
                
              </table>
              
            </td>
            
            <!-- Association scopes -->
            
            <td>
              <table>
              
              <?php foreach ($association[ 'scope' ] as $j => $scope) { ?>
                <tr>
                  <td>
                    <?php button_choose_topic([ 'what' => 'association_scope', 'label' => $tpl[ 'topic_names' ][ $scope ] ]); ?>
                    <input type="hidden" name="associations[<?=$i?>][scope][<?=$j?>]" value="<?=htmlspecialchars($scope)?>" data-topicbank_element="id" />
                  </td>
                  <td><?php button_remove(); ?></td>
                </tr>              
              <?php } $j++; ?>
              
                <tr data-topicbank_template="new_association_scope" class="hidden" data-topicbank_counter_value="<?=$j?>" data-topicbank_counter_name="TOPICBANK_COUNTER2">
                  <td>
                    <?php button_choose_topic([ 'what' => 'association_scope', 'label' => '[Scope]' ]); ?>
                    <input type="hidden" name="associations[<?=$i?>][scope][TOPICBANK_COUNTER2]" value="" data-topicbank_element="id" />
                  </td>
                  <td><?php button_remove(); ?></td>
                </tr>
                
                <tr>
                  <td>
                    <?php button_add([ 'event' => 'new_association_scope', 'label' => 'Add Scope' ]); ?>
                  </td>
                </tr>
              
              </table>
            </td>
            
            <!-- Association reifier -->
            
            <td>
              <?php button_reify([ 'reifier' => $association[ 'reifier' ], 'reifies_type' => 'association', 'reifies_id' => $association[ 'id' ], 'association' => $association[ 'id' ] ]); ?>
              <input type="hidden" name="associations[<?=$i?>][reifier]" value="<?=htmlspecialchars($association[ 'reifier' ])?>" />
            </td>

            <!-- Remove association -->
            
            <td>
              <button class="btn btn-link" type="button" data-topicbank_event="remove" data-topicbank_remove_hide="associations[<?=$i?>][delete]">
                <span class="glyphicon glyphicon-remove"></span>
              </button>
            </td>
          </tr>
      
        <?php } $i++; ?>

          <!-- New association -->

          <tr data-topicbank_template="new_association" class="hidden" data-topicbank_counter_value="<?=$i?>" data-topicbank_counter_name="TOPICBANK_COUNTER1">
          
            <!-- New association type -->
            
            <td>
              <?php button_choose_topic([ 'what' => 'association_type', 'label' => '[Association type]' ]); ?>
              <input type="hidden" name="associations[TOPICBANK_COUNTER1][type]" value="" data-topicbank_element="id" />
              
              <input type="hidden" name="associations[TOPICBANK_COUNTER1][id]" value="" />
              <input type="hidden" name="associations[TOPICBANK_COUNTER1][delete]" value="0" />
            </td>
            
            <!-- New association roles -->
            
            <td>
            
              <table>
              
                <tr>
                  <td>
                    <?php button_choose_topic([ 'what' => 'role_type', 'label' => '[Role type]' ]); ?>
                    <input type="hidden" name="associations[TOPICBANK_COUNTER1][roles][0][type]" value="" data-topicbank_element="id" />
                  </td>
                  <td>
                    (This topic)
                    <input type="hidden" name="associations[TOPICBANK_COUNTER1][roles][0][player]" value="{this_topic}" data-topicbank_element="id" />
                  </td>
                  <td>
                    <?php button_reify([ 'reifies_type' => 'role' ]); ?>
                    <input type="hidden" name="associations[TOPICBANK_COUNTER1][roles][0][reifier]" value="" />
                  </td>
                  <td>
                  </td>
                </tr>

                <tr data-topicbank_template="new_role" class="hidden" data-topicbank_counter_value="1" data-topicbank_counter_name="TOPICBANK_COUNTER2">
                  <td>
                    <?php button_choose_topic([ 'what' => 'role_type', 'label' => '[Role type]' ]); ?>
                    <input type="hidden" name="associations[TOPICBANK_COUNTER1][roles][TOPICBANK_COUNTER2][type]" value="" data-topicbank_element="id" />
                  </td>
                  <td>
                    <?php button_choose_topic([ 'what' => 'role_player', 'label' => '[Role player]' ]); ?>
                    <input type="hidden" name="associations[TOPICBANK_COUNTER1][roles][TOPICBANK_COUNTER2][player]" value="" data-topicbank_element="id" />
                  </td>
                  <td>
                    <?php button_reify([ 'reifies_type' => 'role' ]); ?>
                    <input type="hidden" name="associations[TOPICBANK_COUNTER1][roles][TOPICBANK_COUNTER2][reifier]" value="" />
                  </td>
                  <td><?php button_remove(); ?></td>
                </tr>

                <tr>
                  <td>
                    <?php button_add([ 'event' => 'new_role', 'label' => 'Add Role' ]); ?>
                  </td>
                </tr>
                
              </table>
              
            </td>
            
            <!-- New association scopes -->
            
            <td>
              <table>
              
                <tr data-topicbank_template="new_association_scope" class="hidden" data-topicbank_counter_value="0" data-topicbank_counter_name="TOPICBANK_COUNTER2">
                  <td>
                    <?php button_choose_topic([ 'what' => 'association_scope', 'label' => '[Scope]' ]); ?>
                    <input type="hidden" name="associations[TOPICBANK_COUNTER1][scope][TOPICBANK_COUNTER2]" value="" data-topicbank_element="id" />
                  </td>
                  <td><?php button_remove(); ?></td>
                </tr>
                
                <tr>
                  <td>
                    <?php button_add([ 'event' => 'new_association_scope', 'label' => 'Add Scope' ]); ?>
                  </td>
                </tr>
              
              </table>            
            </td>
            
            <!-- New association reifier -->

            <td>
              <?php button_reify([ 'reifies_type' => 'association' ]); ?>            
              <input type="hidden" name="associations[TOPICBANK_COUNTER1][reifier]" value="" />
            </td>

            <!-- Remove new association -->
            
            <td><?php button_remove(); ?></td>
            
          </tr>

          <tr>
            <td>
              <?php button_add([ 'event' => 'new_association', 'label' => 'Add Association' ]); ?>
            </td>
          </tr>

        </table>

      </div>

      </form>
          
      <!-- Footer -->

      <div class="footer">
        <p>TopicBank 0.1 by Tim Strehle</p>
      </div>

      <!-- "Choose topic" dialog, initially hidden -->

      <div class="modal" id="choose_topic_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="container modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h4 class="modal-title" id="myModalLabel">Choose a topic</h4>
            </div>
            <div class="modal-body">
            </div>
          </div>
        </div>
      </div>
    
      <!-- "Reify" form, hidden -->

      <form method="POST" action="<?=TOPICBANK_BASE_URL?>edit_new_reifier_topic" id="reify_form">
        <input type="hidden" name="reifies_type" value="" />
        <input type="hidden" name="reifies_id" value="" />
        <input type="hidden" name="association" value="" />
        <input type="hidden" name="topic" value="" />
      </form>
    
    </div> <!-- /container -->
    
    <script src="<?=$tpl[ 'topicbank_base_url' ]?>jquery/jquery.min.js"></script>
    <script src="<?=$tpl[ 'topicbank_base_url' ]?>bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=$tpl[ 'topicbank_base_url' ]?>tinymce/tinymce.jquery.min.js"></script>
    
    <script>
    // <![CDATA[
    
    var topicbank_base_url = '<?=$tpl[ 'topicbank_base_url' ]?>';
    
    $(document).ready(function() 
    {
        var _private = { };
        
        _private.topic_id = <?=json_encode($tpl[ 'topic' ][ 'id' ])?>;
        _private.form_changed = false;
        
        
        _private.addFormSection = function($elem_to_clone)
        {
            var $choose_topic_buttons, $clone, counter_value, counter_name, counter_pattern;
            
            $clone = $elem_to_clone.clone();
            
            $clone
                .attr('id', '')
                .insertBefore($elem_to_clone)
                .removeClass('hidden');
                
            // If there's a "Choose topic" button, click the first one to open the dialog.
            // If there is none (e.g. with subject identifiers), focus the first input field.
            
            $choose_topic_buttons = $clone.find('button[data-topicbank_event="show_choose_topic_dialog"]');
            
            if ($choose_topic_buttons.length > 0)
            {
                $choose_topic_buttons.first().trigger('click');
            }
            else
            {
                $clone.find('input').first().focus();
            }
                
            counter_value = $elem_to_clone.data('topicbank_counter_value');
            counter_name = $elem_to_clone.data('topicbank_counter_name');
            
            counter_pattern = new RegExp(counter_name);
            
            if (counter_value !== undefined)
            {
                counter_value = parseInt(counter_value, 10);
                
                $clone.find('input, textarea').each(function(i, item)
                {
                    var $item = $(item);
                    var elem_name = $item.attr('name');
                    
                    if ((elem_name === undefined) || (elem_name.length < 1))
                    {
                        return;
                    }
                    
                    $item.attr('name', elem_name.replace(counter_pattern, counter_value));
                });
                
                $elem_to_clone.data('topicbank_counter_value', (counter_value + 1));
            }
        };

        var edit_templates = 
        [ 
            'type', 'name', 'name_scope', 'subject_locator', 'subject_identifier', 
            'occurrence', 'occurrence_scope', 'association', 'role', 'association_scope' 
        ];
        
        $(edit_templates).each(function(i, item)
        {
            var button_selector = 'button[data-topicbank_event="new_' + item + '"]';
            var selector_to_clone = 'tr[data-topicbank_template="new_' + item + '"]';
            
            $('#topicbank_form_edit').on('click', button_selector, function(e)
            {
                var $elem_to_clone = $(e.target).closest('tr').siblings(selector_to_clone).last();
                _private.addFormSection($elem_to_clone);
            });
        });

        $('#topicbank_form_edit').on('click', 'button[data-topicbank_event="remove"]', function(e)
        {
            var $button = $(e.target);
            var $tr = $button.closest('tr');
            
            var hidden_field_name = $button.data('topicbank_remove_hide');
            
            if ((hidden_field_name !== undefined) && (hidden_field_name.length > 0))
            {
                $tr.find('[name="' + hidden_field_name + '"]').val('1');
                $tr.addClass('hidden');
            }
            else
            {
                $tr.remove();
            }
        });

        $('#topicbank_form_edit').on('click', 'button[data-topicbank_event="show_choose_topic_dialog"]', function(e)
        {
            var $choose_topic_dialog, $dialog_body, what;

            $choose_topic_dialog = $('#choose_topic_dialog');
            $dialog_body = $choose_topic_dialog.find('div.modal-body');
            
            _private.choose_topic_dialog_opener = e.target;
            
            what = $(e.target).data('topicbank_what');
            
            $choose_topic_dialog.data('what', what);
            
            $dialog_body.empty().load(topicbank_base_url + 'choose_topic_dialog?what=' + encodeURIComponent(what));
        });

        $('#choose_topic_dialog').on('click', 'button[data-topicbank_element="topic"]', function(e)
        {
            var $target, $target_id, $target_name, $source, $source_id, $source_name;
            
            if (_private.choose_topic_dialog_opener === undefined)
            {
                return;
            }
            
            $source = $(e.target);
            
            $source_id = $source.find('span[data-topicbank_element="id"]');
            $source_name = $source.find('span[data-topicbank_element="name"]');
            
            $target = $(_private.choose_topic_dialog_opener).closest('td');
            
            $target_id = $target.find('input[data-topicbank_element="id"]');
            $target_name = $target.find('span[data-topicbank_element="name"]');
            
            $target_id.val($source_id.text());
            $target_name.html($source_name.html());

            $.ajax(
            {
                url: topicbank_base_url + 'ajax_add_to_history',
                cache: false,
                data:
                {
                    what: $('#choose_topic_dialog').data('what'),
                    topic_id: $source_id.text()
                }
            });
                        
            $('#choose_topic_dialog').modal('hide');
        });

        $('#choose_topic_dialog').on('submit', '#topicbank_choose_topic_dialog_searchform', function(e)
        {
            var search_name, search_page, search_type;
            var $search_name_input, $search_page_input, $search_type_input, $search_results;
            
            e.preventDefault();
            
            if (_private.choose_topic_dialog_opener === undefined)
            {
                return false;
            }
            
            $search_name_input = $('#choose_topic_dialog').find('input[data-topicbank_element="search_name"]');
            search_name = $search_name_input.val();
            
            $search_type_input = $('#choose_topic_dialog').find('select[data-topicbank_element="search_type"]');            
            search_type = $search_type_input.val();
            
            $search_page_input = $('#choose_topic_dialog').find('input[data-topicbank_element="search_page"]');
            search_page = $search_page_input.val();
            
            $search_results = $('#choose_topic_dialog').find('div[data-topicbank_element="search_results"]');

            $search_results.empty().load(topicbank_base_url + 'search_topic?name=' + encodeURIComponent(search_name) + '&type=' + encodeURIComponent(search_type) + '&p=' + encodeURIComponent(search_page));
            
            return false;
        });
        
        $('#choose_topic_dialog').on('click', 'button[data-topicbank_event="choose_topic_go_to_page"]', function(e)
        {
            var page_num;
            var $button, $search_form;
            
            $button = $(e.currentTarget);
            
            page_num = $button.data('topicbank_page_num');
            
            $search_form = $('#topicbank_choose_topic_dialog_searchform');
            
            $search_form.find('input[data-topicbank_element="search_page"]').val(page_num);
            
            $search_form.submit();
        });

        $('#choose_topic_dialog').on('submit', '#topicbank_choose_topic_dialog_createform', function(e)
        {
            var new_name, new_type, new_subject;
            var $new_name_input, $new_type_input, $new_subject_input, $target, $target_id, $target_name;
            
            if (_private.choose_topic_dialog_opener === undefined)
            {
                return false;
            }
            
            $new_name_input = $('#choose_topic_dialog').find('input[data-topicbank_element="create_name"]');
            new_name = $new_name_input.val();
            
            $new_type_input = $('#choose_topic_dialog').find('select[data-topicbank_element="create_type"]');            
            new_type = $new_type_input.val();

            $new_subject_input = $('#choose_topic_dialog').find('input[data-topicbank_element="create_subject"]');
            new_subject = $new_subject_input.val();
            
            $.ajax(
            {
                url: topicbank_base_url + 'ajax_create_topic',
                cache: false,
                data:
                {
                    name: new_name,
                    type: new_type,
                    subject_identifier: new_subject,
                    what: 'XXX'
                },
                dataType: 'json'
            }).done(function(data)
            {
                $target = $(_private.choose_topic_dialog_opener).closest('td');
            
                $target_id = $target.find('input[data-topicbank_element="id"]');
                $target_name = $target.find('span[data-topicbank_element="name"]');
            
                $target_id.val(data.id);
                $target_name.html(data.name);
            
                $('#choose_topic_dialog').modal('hide');
            });
            
            return false;
        });

        $('#topicbank_form_edit').on('click', 'a[data-topicbank_event="submit_reify_form"]', function(e)
        {
            var $reify_form, $dialog_body, data;

            $reify_form = $('#reify_form');
                        
            data = $(e.currentTarget).data();
            
            $reify_form.find('input[name="reifies_type"]').val(data.topicbank_reifies_type);
            $reify_form.find('input[name="reifies_id"]').val(data.topicbank_reifies_id);
            $reify_form.find('input[name="topic"]').val(data.topicbank_topic);
            $reify_form.find('input[name="association"]').val(data.topicbank_association);
            
            $reify_form.submit();
        });
        
        
        $('#topicbank_form_edit :input').on('change', function()
        {
            _private.form_changed = true;
        });
        
        
        $('#topicbank_form_edit').on('submit', function()
        {
            // Disable the "unsaved changes" beforeunload prompt - 
            // we are about to save!
            
            _private.form_changed = false;            
        });
        
        
        window.addEventListener('beforeunload', function (e) 
        {
            if (! _private.form_changed)
                return;
                
            var confirmationMessage = 'Your changes have not been saved yet. They will be lost if you leave now.';

            //Gecko + IE
            (e || window.event).returnValue = confirmationMessage;
            
            //Webkit, Safari, Chrome etc.
            return confirmationMessage;                                
        });
        
        
        tinymce.init(
        {
            entity_encoding: 'raw',
            gecko_spellcheck: true,
            menubar: false,
            plugins: [ 'link', 'code' ],
            relative_urls: false,
            selector: 'textarea[data-topicbank_html_editor="enabled"]',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | code',
            toolbar_items_size: 'small',
            content_css: '<?=$tpl[ "topicbank_base_url" ]?>static/tinymce_content.css'
        });

    });
    
    // ]]>
    </script>

  </body>
</html>
