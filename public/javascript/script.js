$(document).ready(function(){

    // open external links in new window
    $("a[href^='http']").not("[href*='" + location.hostname + "']").attr('target', '_blank');

});
