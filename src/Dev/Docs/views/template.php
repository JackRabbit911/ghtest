<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
<!--    <link rel="icon" href="../../favicon.ico">-->

    <title><?=$title?></title>

    <!-- Bootstrap core CSS -->
    <link href="<?=BASEDIR?>/media/css/vendor/bootstrap-3.3.7/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--<link href="/wn/public/assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">-->
    
    
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.10.0/styles/default.min.css">
   

    <link href="<?=BASEDIR?>/media/css/vendor/highlight/github.css" rel="stylesheet"/>

    <!-- Custom styles for this template -->
    <link href="<?=BASEDIR?>/media/css/sticky-footer-navbar.css" rel="stylesheet">
    
    <link href="<?=BASEDIR?>/media/css/navbar.css" rel="stylesheet">
    <link href="<?=BASEDIR?>/media/css/custom.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
      <header class="header">
          <?=$header?>
      </header>
      <nav class="navbar navbar-default sticky-top">
          <?=$navbar?>
      </nav>
    

    <!-- Begin page content -->
    <div role="main">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                        <?=$content?>
                </div>
                <div class="col-md-4">
                        <?=$sidebar?>                        
                </div>
            </div>            
        </div>
    </div>
    
    

    <footer class="footer">
      <div class="container">
        <p class="text-muted">Place sticky footer content here!</p>
      </div>
    </footer>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="<?=BASEDIR?>/media/js/vendor/bootstrap-3.3.7/bootstrap.min.js"></script>
    
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.10.0/highlight.min.js"></script>
    <script type='text/javascript'>hljs.initHighlightingOnLoad();</script>
    
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <!--<script src="/wn/public/assets/js/ie10-viewport-bug-workaround.js"></script>-->
    
    <script>
        $(document).ready(function() {
          $('pre code').each(function(i, block) {
            hljs.highlightBlock(block);
          });
});
    </script>
    
    <?=_js();?>
    
  </body>
</html>