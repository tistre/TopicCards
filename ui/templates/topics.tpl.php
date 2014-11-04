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
    <link rel="shortcut icon" href="<?=$tpl[ 'topicbank_static_base_url' ]?>bootstrap/assets/ico/favicon.ico" />

    <title>
      Search topics | 
      <?=htmlspecialchars($tpl[ 'topicmap' ][ 'label' ])?>
    </title>

    <!-- Bootstrap core CSS -->
    <link href="<?=$tpl[ 'topicbank_static_base_url' ]?>bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="<?=$tpl[ 'topicbank_static_base_url' ]?>topicbank.css" rel="stylesheet" />

  </head>

  <body>

    <div class="container">

      <?php include('header.tpl.php'); ?>

      <h1>Search</h1>

      <form class="form-inline" role="search" action="" method="GET" name="main_searchform">
    
        <div class="form-group">
          <input name="q" type="text" value="<?=htmlspecialchars($tpl[ 'fulltext_query' ])?>" autofocus="autofocus" class="form-control" />
        </div>

        <div class="form-group">
          <select name="type" size="1" onchange="document.forms.main_searchform.submit()" class="form-control">
            <option value="">All types</option>
            <?php foreach ($tpl[ 'topic_types' ] as $topic_arr) { ?>
            <option value="<?=htmlspecialchars($topic_arr[ 'id' ])?>" <?=($topic_arr[ 'selected' ] ? 'selected="selected"' : '')?>><?=htmlspecialchars($topic_arr[ 'label' ])?></option>
            <?php } ?>
          </select>
        </div>
      
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search</button>

      </form>
      
      <div>
    
      <?php foreach ($tpl[ 'topics' ] as $topic) { ?>
      
        <div>
          <a href="<?=htmlspecialchars($topic[ 'url' ])?>">
            <?=htmlspecialchars($topic[ 'label' ])?>
          </a>
          <small><?=htmlspecialchars($topic[ 'type' ])?></small>
        </div>
      
      <?php } ?>
      
      </div>

      <div class="footer">
        <p>TopicBank 0.1 by Tim Strehle</p>
      </div>

    </div> <!-- /container -->

    <script src="<?=$tpl[ 'topicbank_static_base_url' ]?>jquery.min.js"></script>
    <script src="<?=$tpl[ 'topicbank_static_base_url' ]?>bootstrap/js/bootstrap.min.js"></script>

  </body>
</html>
