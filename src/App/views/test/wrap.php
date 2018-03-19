<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    
    <link href="<?=BASEDIR?>/media/css/vendor/bootstrap-3.3.7/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.10.0/styles/default.min.css">
    
    <!--<link rel="import" href="/index/lala">-->
    
    <title><?=@$title?></title>
    
    
    
    
  </head>
  <body>
      <div class="container">
          <h1>Простая форма</h1>
          <section role="main">
             <?//= \Core\Request::internal('/user/auth')->execute();?>
             <?=$form_login?> 
          </section>
<!--          <section role="form">
             
          </section>-->
      </div>
      
      
      
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
      
      <script src="/media/js/vendor/bootstrap-3.3.7/bootstrap.min.js"></script>
      
      <!--<script src="/media/js/form.js"></script>-->
      
      <?=_js()?>
      
      
    <script>
      $(document).ready(function () {
          
          
          
//          $('body').wnFormSubmit('.wn-form');
          
//          
//          $('body').on('click', '#logOut', function(){
//              $.get('/user/log_out', function(){
//                  location.reload();
////                    location.replace('/');
//              });
//              return false;
//          });
//          
          
  });
    </script>
    
   
      
  </body>
</html>

