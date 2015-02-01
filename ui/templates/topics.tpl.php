<?php

function button_go_to_page(array $params)
{
    ?>
    
    <button class="btn btn-default" data-topicbank_event="go_to_page" data-topicbank_page_num="<?=htmlspecialchars($params[ 'page_num' ])?>" type="button">
      <?=htmlspecialchars($params[ 'label' ])?>
    </button>

    <?php
}

function pagination(array $tpl)
{
    ?>
    
    <div>
      <?php button_go_to_page($tpl[ 'pages' ][ 'first' ]); ?>
      <?php button_go_to_page($tpl[ 'pages' ][ 'previous' ]); ?>
      <?=htmlspecialchars($tpl[ 'page_num' ])?>
      <?php button_go_to_page($tpl[ 'pages' ][ 'next' ]); ?>
      <?php button_go_to_page($tpl[ 'pages' ][ 'last' ]); ?>
    </div>
    
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
      Search topics | 
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

      <h1>Search</h1>

      <form class="form-inline" role="search" action="" method="GET" name="main_searchform" id="main_searchform">
    
        <div class="form-group">
          <input name="q" type="text" value="<?=htmlspecialchars($tpl[ 'fulltext_query' ])?>" autofocus="autofocus" class="form-control" />
        </div>

        <div class="form-group">
          <select name="type" size="1" class="form-control">
            <option value="">All types</option>
            <?php foreach ($tpl[ 'topic_types' ] as $topic_arr) { ?>
            <option value="<?=htmlspecialchars($topic_arr[ 'subject' ])?>" <?=($topic_arr[ 'selected' ] ? 'selected="selected"' : '')?>><?=htmlspecialchars($topic_arr[ 'label' ])?></option>
            <?php } ?>
          </select>
        </div>
      
        <input type="hidden" name="p" value="<?=htmlspecialchars($tpl[ 'page_num' ])?>" />
      
        <button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Search</button>
        
        <a href="<?=$tpl[ 'topicbank_base_url' ]?>topics">Reset</a>

      </form>
      
      <div>
        <?=$tpl[ 'total_hits' ]?> topics found.
      </div>
      
      <?php pagination($tpl); ?>
      
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

      <?php pagination($tpl); ?>

      <div class="footer">
        <p>TopicBank 0.1 by Tim Strehle</p>
      </div>

    </div> <!-- /container -->

    <script src="<?=$tpl[ 'topicbank_base_url' ]?>jquery/jquery.min.js"></script>
    <script src="<?=$tpl[ 'topicbank_base_url' ]?>bootstrap/js/bootstrap.min.js"></script>

    <script>
    // <![CDATA[
    
    var topicbank_base_url = '<?=$tpl[ 'topicbank_base_url' ]?>';
    
    $(document).ready(function() 
    {
        var _private = { };
        
        _private.$search_form = $('#main_searchform');
        _private.$page_num_input = _private.$search_form.find('input[name="p"]');
        

        $('button[data-topicbank_event="go_to_page"]').on('click', function(e)
        {
            _private.$page_num_input.val($(e.currentTarget).data('topicbank_page_num'));
            
            _private.$search_form.submit();
        });  
        
        
        _private.$search_form.on('change', ':input', function(e)
        {
            _private.$page_num_input.val('1');
            
            if ($(e.currentTarget).attr('name') === 'type')
            {
                _private.$search_form.submit();
            }
        });
    });
        
    // ]]>
    </script>

  </body>
</html>
