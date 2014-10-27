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
  <form class="form-inline pull-right" role="search" method="GET" action="<?=$tpl[ 'topicbank_base_url' ]?>topics">
    <div class="form-group">
      <input name="q" type="text" class="form-control" placeholder="Search" />
    </div>
    <button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search"></span></button>
  </form>
  <ul class="nav nav-pills pull-right">
    <li>
      <!-- XXX this should be a form POST, not idempotent -->
      <a href="<?=$tpl[ 'topicbank_base_url' ]?>edit_new_topic">
        <span class="glyphicon glyphicon-plus"></span> Add topic
      </a>
    </li>
  </ul>
  <h3 class="text-muted"><?=htmlspecialchars($tpl[ 'topicmap' ][ 'display_name' ])?></h3>
</div>
