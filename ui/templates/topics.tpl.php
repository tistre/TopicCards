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
      <?=htmlspecialchars($tpl[ 'topicmap' ][ 'display_name' ])?>
    </title>

    <!-- Bootstrap core CSS -->
    <link href="<?=$tpl[ 'topicbank_static_base_url' ]?>bootstrap/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="<?=$tpl[ 'topicbank_static_base_url' ]?>topicbank.css" rel="stylesheet" />

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
              <li><a href="<?=$tpl[ 'topicbank_base_url' ]?>edit_new_topic"><span class="glyphicon glyphicon-plus"></span> Create a new topic</a></li>
              <li><a href="#">Logged in as …</a></li>
              <li><a href="#">Log out</a></li>
            </ul>
          </li>
        </ul>
      <form class="form-inline pull-right" role="search" method="GET" action="<?=$tpl[ 'topicbank_base_url' ]?>topics">
        <div class="form-group">
          <input name="q" type="text" class="form-control" placeholder="Search" />
        </div>
        <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
      </form>
        <h3 class="text-muted"><?=htmlspecialchars($tpl[ 'topicmap' ][ 'display_name' ])?></h3>
      </div>

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
