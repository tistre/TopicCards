<div class="row">
  <div class="col-md-4">
    <h4>Recent</h4>
    <ul class="nav nav-pills nav-stacked">
      <?php foreach ($tpl[ 'recent' ] as $topic_arr) { ?>
      <li>
        <button data-topicbank_element="topic" class="btn btn-link" type="button">
          <span data-topicbank_element="name"><?=htmlspecialchars($topic_arr[ 'label' ])?></span>
          <span data-topicbank_element="id" class="hidden"><?=htmlspecialchars($topic_arr[ 'id' ])?></span>
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
    <form>
      <select size="1" name="name" data-topicbank_element="type">
        <option value="">(No type)</option>
        <?php foreach ($tpl[ 'topic_types' ] as $topic_arr) { ?>
        <option value="<?=htmlspecialchars($topic_arr[ 'id' ])?>"><?=htmlspecialchars($topic_arr[ 'label' ])?></option>
        <?php } ?>
      </select>
      <input type="text" name="name" data-topicbank_element="name" />
      <button data-topicbank_element="create_topic" class="btn btn-default" type="button">Create</button>
    </form>
  </div>
</div>
