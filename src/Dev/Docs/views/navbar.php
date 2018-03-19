
<!-- Sticky navbar -->
<!--    <nav class="navbar navbar-default sticky-top">-->
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
          <? if(isset($brand)): ?>
          <? $link = isset($brand['link']) ? $brand['link'] : '/'; ?>
          <a class="navbar-brand" href="<?=BASEDIR.$link?>">
              <? if(isset($brand['pict'])): ?>
              <img src="<?=BASEDIR.$brand['pict']?>"/>
              <? endif; ?>
              <? if(isset($brand['text'])): ?>
              <?=$brand['text']?>
              <? endif; ?>
          </a>
          <? endif; ?>  
            
        </div>
          
        <div id="navbar" class="collapse navbar-collapse">            
            
          <? if(isset($left)): ?>           
          <? if(isset($params['action'])) $action = '/'.mb_strtolower($params['action']); ?>          
          <ul class="nav navbar-nav">              
            <? foreach($left AS $item): ?>            
            <? if($action == $item['link']) $active = ' class="active"';
              else $active = NULL; ?>    
            <li<?=$active?>><a href="<?=$prefix.$item['link']?>"><?=$item['text']?></a></li>                        
            <? endforeach; ?>
          </ul>
          <? endif; ?>  
            
          <? if(isset($right)): ?>            
          <? if(isset($params['controller'])) $controller = '/'.mb_strtolower($params['controller']); 
             else $controller = '/index'; ?>   
            
          <!--<p id="info" class="navbar-text">Signed in as Mark Otto</p>-->
            
          <ul class="nav navbar-nav navbar-right">              
            <? foreach($right AS $item): ?>              
            <? if($item['link'] == '/') $link = '/index'; 
               else $link = $item['link']; ?>
            <? if($controller == $link) $active = ' class="active"';
              else $active = NULL; ?>  
              
              
            <? if($link == '/index') $link = '/'; ?>  
            <li<?=$active?>><a href="<?=$prefix.$link?>"><?=$item['text']?></a></li>            
            <? endforeach; ?>
          </ul>
          <? endif; ?>
            
        </div><!--/.nav-collapse -->
      </div>
    <!--</nav>-->
