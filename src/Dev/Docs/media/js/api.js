$(document).ready(function () {
    var offsetTop = $('nav.sticky-top').wnStickyTop();
    
    $('aside.sidebar').wnSlowScroll(offsetTop)
            .wnScrollSpy(offsetTop)
            .wnAffix();
});
