<?php foreach ($tpl[ 'results' ] as $topic_arr) { ?>
<li>
  <button data-topicbank_element="topic" class="btn btn-link" type="button">
    <span data-topicbank_element="name"><?=htmlspecialchars($topic_arr[ 'label' ])?></span>
    <span data-topicbank_element="id" class="hidden"><?=htmlspecialchars($topic_arr[ 'id' ])?></span>
  </button>
</li>
<?php } ?>
