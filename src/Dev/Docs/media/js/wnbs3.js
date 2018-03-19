$.fn.wnStickyTop = function(fixedclass = 'navbar-fixed-top'){
    
    var element = $(this);
    var offsetTop = element.prevAll('header').outerHeight(true);
    var elementHeight = element.outerHeight(true);

    $(window).on("scroll", function() {
        if ($(window).scrollTop() > offsetTop) { 
            element.addClass(fixedclass);
            $('body').css('padding-top', elementHeight+'px');
        }
        else { 
            element.removeClass(fixedclass);
            $('body').css('padding-top', '0px');
        }
    });
    
    return elementHeight;
};

$.fn.wnAffix = function(Atop, Abottom){
    
    var top = Atop;
    
    var element = $(this);
    var elementHeight = element.height();
    var elementTop = element.offset().top;
    var elementLeft = element.offset().left;
    var elementBottom = elementTop+elementHeight;
    var windowHeight = $(window).height();
    var documentHeight = $(document).outerHeight(true);
    var newTop;
    var inside;
    var bottom = Abottom;
    if(Abottom === undefined) {
        var bottom = $('.footer').outerHeight(true);
    }
    
    $(window).on("resize", function() {            
        windowHeight = $(window).outerHeight(true);
        elementLeft = element.offset().left;
    });
    
    $(window).on("scroll", function(){
        
        if(Atop === undefined) {
            top = $('body').css('padding-top').slice(0, -2);
        }
        
        var scrollTop = $(window).scrollTop();
        var scrollBottom = scrollTop + windowHeight;
        
        if(top > 0) {      
            var y = +elementHeight + +top + +bottom;
            
            if(windowHeight > y){
                inside = true;
            } else {
                inside = false;
            }
            
            if((inside && scrollTop > elementTop-top))
            {
                newTop = +top + +scrollTop;    
                element.offset({top:newTop, left:elementLeft});
            } else if (!inside && scrollBottom > elementBottom && scrollBottom < documentHeight-bottom-top) {
                if(+elementHeight + +top > windowHeight) {
                    newTop = scrollBottom - elementHeight;                
                } else {
                    newTop = +top + +scrollTop;
                }
                element.offset({top:newTop, left:elementLeft});
            } else if (!inside && scrollBottom >= documentHeight-bottom-top) {
                newTop = documentHeight-elementHeight-top;
                element.offset({top:newTop, left:elementLeft});
            } else {
                element.offset({top:elementTop, left:elementLeft});
            }        
        }       
    });
    
    return this; //false;
};

$.fn.wnContentsList = function (source) {
    var item = '';    
    var h = $(":header", $(source));
    if (h.length > 0) { // Собрать оглавление
        h.each(function (index) {
            var hash = 'header-'+index+'-'+$.slugify($(this).text());
            $(this).before('<a name="'+hash+'"></a>');
            var level = $(this)[0].tagName.replace(/[H|h]/, "");
            item += '<a href="#'+hash+'" class="wn-sidebar-'+level+'">'+$(this).text()+'</a>';
        });
 
    }
            
    $(this).html(item);
        
    return this;    
};

//$.fn.wnContentsUl = function(source){
//    var item = '';
//    var anchor = '';
//
//    var h2 = $(source + ' h2');
//    if(h2) {
//        item += '<ul class="wn-nav">';
//        h2.each(function(i=0){
//            var h = 'header-';
//            var j = 0;
//            i++;
//            anchor = h+i+'-'+$.slugify($(this).text());
//            item += '<li><a href="#'+anchor+'">'+$(this).text()+'</a></li>';
//            anchor = '<a name="'+anchor+'"></a>';
//            $(this).before(anchor);
//
//            var h3 = $(this).nextUntil(source + ' h2').filter('h3');
//            if(h3){
//                item += '<ul class="wn-nav">';
//                h3.each(function(j=0){
//                    var h = 'header-';
//                    
//                    j++;
//                    anchor = h + i + j + '-'+$.slugify($(this).text());
//                    item += '<li><a href="#'+anchor+'">'+$(this).text()+'</a></li>';
//                    anchor = '<a name="'+anchor+'"></a>';
//                    $(this).before(anchor);
//
//                    var h4 = $(this).nextUntil(source + ' h3').filter('h4');
//                    if(h4){
//                        item += '<ul>';
//                        h4.each(function(j=0){
//                            var h = 'h4';
//
//                            j++;
//                            anchor = h+j+i+$(this).text();
//                            item += '<li><a href="#'+anchor+'">'+$(this).text()+'</a></li>';
//                            anchor = '<a name="'+anchor+'"></a>';
//                            $(this).before(anchor);
//
//                        });
//                        item += '</ul>';
//                    }
//                });
//                item += '</ul>';
//            }
//        });
//        item += '</ul>';
//    }
//
//    $(this).html(item);
//    
//    return false;
//};

$.fn.wnSlowScroll = function(offsetTop){
//    $(this).find('a')
        $(this).find('a[href*="#"]').on('click', function(){
//            alert(offsetTop);
        var anchor = $(this).attr('href').substring(1);
        var element = $('a[name="'+anchor+'"]').next();
        var marginTop = element.css('margin-top').slice(0, -2);
        var offset = $('a[name="'+anchor+'"]').next().offset().top - offsetTop + marginTop/2;        
        $("html, body").animate({scrollTop: offset}, "slow");
        var hash = '#'+anchor;
        setLocation(hash);
        return false;
    });
    return this;
};
    
$.fn.wnScrollSpy = function(offsetTop){
    var element = $(this);
  
    var oy = $('a[name]').map(function(indx, item){
        var top = $(item).offset().top;
        var name = $(item).attr('name');
        return {'top':top, 'name':name};
    });
    var y = oy.get();
    
    var hash = window.location.href;
    
    $(window).on("scroll", function(){
        var scrollTop = +$(window).scrollTop() + +offsetTop;
        
        var current = 0;
        var next = 0;
        
        var href = '';
                
        for (var i = 0; i < y.length; i++) {
            current = y[i].top;
            
            if(y[i+1] !== undefined)
                next = y[i+1].top;
            else next = 0;

            if(scrollTop < y[0].top) {
                  href = hash;
                  element.find('a[href]').removeClass('wn-active');
              }
              else if(scrollTop >= current && scrollTop < next) {                  
                  href = '#'+y[i].name;
                  element.find('a[href="'+href+'"]').addClass('wn-active');
                  element.find('a[href!="'+href+'"]').removeClass('wn-active');                
              } 
        }
        
//        setLocation(href);
        return false;
    });
    return this;
};

function setLocation(curLoc){
    try {
      history.pushState(null, null, curLoc);
      return;
    } catch(e) {}
    location.hash = '#' + curLoc;
};



