// FORM TOGGLER
$(document).ready(function() {
	$(".fsContent").hide();
    $(".sbsToggler").click(function() {
    $(this).next(".fsContent").slideToggle(); // this fait référence à l'élément sur lequel a eu lieu l'événement (donc ici le click)
    });
});

// MENU TOGGLER
$(document).ready(function() {
    $(".sbsTogglerSidebar").click(function() {
    $(this).next(".fsContentSidebar").slideToggle();
    });
});