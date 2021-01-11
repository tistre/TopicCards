<?php

function button_go_to_page(array $params)
{
    ?>
    
    <button class="btn btn-default" data-topicbank_event="choose_topic_go_to_page" data-topicbank_page_num="<?=htmlspecialchars($params[ 'page_num' ])?>" type="button">
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

?>
<ul class="nav nav-pills nav-stacked">
  <?php foreach ($tpl[ 'results' ] as $topic_arr) { ?>
  <li>
    <button data-topicbank_element="topic" class="btn btn-link" type="button">
      <span data-topicbank_element="name"><?=htmlspecialchars($topic_arr[ 'label' ])?></span>
      <span data-topicbank_element="id" class="hidden"><?=htmlspecialchars($topic_arr[ 'id' ])?></span>
    </button>
    <small><?=htmlspecialchars($topic_arr[ 'type' ])?></small>
  </li>
  <?php } ?>
</ul>
<?php pagination($tpl); ?>
