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
        <form method="post" action="">

          <div style="padding-bottom: 15px; border-bottom: 1px solid #CCCCCC;">

            <div class="pull-right">
              <table>
          
            <?php
          
            if (count($tpl[ 'topic' ][ 'types' ]) === 0)
                $tpl[ 'topic' ][ 'types' ][ ] = '';
          
            foreach ($tpl[ 'topic' ][ 'types' ] as $type_id)
            {
                ?>
                <tr>
                  <td>
                    <input type="text" name="types[]" value="<?=htmlspecialchars($type_id)?>" />
                  </td>
                </tr>
                <?php
            }
            
            ?>
            
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
              </tr>
          
            <?php } $i++; ?>

              <tr>
                <td><input type="text" name="other_names[<?=$i?>][type]" value="" />:</td>
                <td><input type="text" name="other_names[<?=$i?>][value]" value="" /></td>
                <td><input type="text" name="other_names[<?=$i?>][scope][]" value="" /></td>
              </tr>
          
            </table>

          </div>
          
          <!-- Subject identifiers -->
        
          <div>
          
            <h4>Subject identifiers</h4>
            
            <table>
          
            <?php
          
            if (count($tpl[ 'topic' ][ 'subject_identifiers' ]) === 0)
                $tpl[ 'topic' ][ 'subject_identifiers' ][ ] = '';
          
            foreach ($tpl[ 'topic' ][ 'subject_identifiers' ] as $url)
            {
                ?>
                <tr>
                  <td>
                    <input type="text" name="subject_identifiers[]" value="<?=htmlspecialchars($url)?>" />
                  </td>
                </tr>
                <?php
            }
            
            ?>
            
            </table>

          </div>
          
          <!-- Subject locators -->
        
          <div>
          
            <h4>Subject locators</h4>
            
            <table>
          
            <?php
          
            if (count($tpl[ 'topic' ][ 'subject_locators' ]) === 0)
                $tpl[ 'topic' ][ 'subject_locators' ][ ] = '';
          
            foreach ($tpl[ 'topic' ][ 'subject_locators' ] as $url)
            {
                ?>
                <tr>
                  <td>
                    <input type="text" name="subject_locators[]" value="<?=htmlspecialchars($url)?>" />
                  </td>
                </tr>
                <?php
            }
            
            ?>
            
            </table>

          </div>

          <!-- Occurrences -->
        
          <div>
          
            <h4>Occurrences</h4>
            
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
              </tr>
          
            <?php } $i++; ?>

              <tr>
                <td><input type="text" name="occurrences[<?=$i?>][type]" value="" />:</td>
                <td>
                  <input type="text" name="occurrences[<?=$i?>][value]" value="" />
                  <br />
                  <input type="text" name="occurrences[<?=$i?>][datatype]" value="" />
                </td>
                <td><input type="text" name="occurrences[<?=$i?>][scope][]" value="" /></td>
              </tr>
          
            </table>

          </div>
                    
          <!-- Save button -->
          
          <div>
            <button type="submit" class="btn btn-primary pull-right">Save</button>
          </div>
          
        </form>

      </div>

      <div class="footer">
        <p>XDDB 0.1 by Tim Strehle</p>
      </div>

    </div> <!-- /container -->

    <script src="<?=$tpl[ 'xddb_static_base_url' ]?>jquery.min.js"></script>
    <script src="<?=$tpl[ 'xddb_static_base_url' ]?>bootstrap/js/bootstrap.min.js"></script>

  </body>
</html>
