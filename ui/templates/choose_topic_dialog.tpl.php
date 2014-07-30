<div class="row">
  <div class="col-md-4">
    <h4>Recent</h4>
    <ul class="nav nav-pills nav-stacked">
      <?php foreach ($tpl[ 'recent' ] as $topic_id) { ?>
      <li>
        <button data-topicbank_element="topic" class="btn btn-link" type="button">
          <span data-topicbank_element="name"><?=htmlspecialchars($topic_id)?></span>
          <span data-topicbank_element="id" class="hidden"><?=htmlspecialchars($topic_id)?></span>
        </button>
      </li>
      <?php } ?>
    </ul>
  </div>
  <div class="col-md-4">
    <h4>Search</h4>
  </div>
  <div class="col-md-4">
    <h4>Create new</h4>
  </div>
</div>
