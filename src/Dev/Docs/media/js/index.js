$(document).ready(function () {
    var offsetTop = $('nav.sticky-top').wnStickyTop();      
    
    $('aside.sidebar').wnContentsList('article.article-content')
            .wnSlowScroll(offsetTop)
            .wnScrollSpy(offsetTop)
            .wnAffix();
});
