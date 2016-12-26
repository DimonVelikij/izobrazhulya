$(document).ready(function() {
    var settings = {
        overlayColor: '#000',
        overlayOpacity: .5,
        transitionIn: 'elastic',
        transitionOut: 'elastic',
        easingIn: 'easeInSine',
        easingOut: 'easeOutSine',
        titlePosition: 'outside' ,
        changeSpeed: 500,
        cyclic: true
    };

    $('.fancybox').fancybox(settings);
});