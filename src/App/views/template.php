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
      <img src="<?=BASEDIR?>/media/img/jukb1.png" width="150"/>
      <h1><?=$content?></h1>
      lalala
      <?=@$block?>
      
      <div id="result"></div>
      
      
      
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
      
    <script>
       $(document).ready(function () { 
      $("#result").load("/index/lala");
//      alert('qq');
  });
    </script>
      
  </body>
</html>