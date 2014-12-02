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
    <link rel="shortcut icon" href="<?=$tpl[ 'topicbank_base_url' ]?>bootstrap/assets/ico/favicon.ico" />

    <title>
      Upload a file | 
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

      <form id="topicbank_form_edit" method="POST" action="" enctype="multipart/form-data">

        <input type="file" name="file" />
        
        <button type="submit" class="btn btn-primary">Upload</button>

      </form>
          
      <!-- Footer -->

      <div class="footer">
        <p>TopicBank 0.1 by Tim Strehle</p>
      </div>
    
    </div> <!-- /container -->
    
    <script src="<?=$tpl[ 'topicbank_base_url' ]?>jquery/jquery.min.js"></script>
    <script src="<?=$tpl[ 'topicbank_base_url' ]?>bootstrap/js/bootstrap.min.js"></script>
    <script src="<?=$tpl[ 'topicbank_base_url' ]?>tinymce/tinymce.jquery.min.js"></script>
    
  </body>
</html>
