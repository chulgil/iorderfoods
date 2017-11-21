// MENU //
$( document ).ready(function() {
	$( ".small-screen-menu" ).click(function() {
		$( ".nav-menu ul" ).slideToggle( "fast" );
	});
	
	$( ".welcome-user" ).click(function() {
		$( "ul.welcome-user-menu" ).slideToggle( "fast" );
	});
});		




// RESPONSIVE SILDER //
$(function () {
	$("#slider4").responsiveSlides({
		auto: true,
		nav: true,
		speed: 500,
		namespace: "callbacks",
		before: function () {
			$('.events').append("<li>before event fired.</li>");
		},
		after: function () {
			$('.events').append("<li>after event fired.</li>");
		}
	});
});



