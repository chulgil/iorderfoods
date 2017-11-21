

 $(document).ready(function() {
     
        //js for hamburger menu//
		
		(function() {
		
		"use strict";
		
		var toggles = document.querySelectorAll(".c-hamburger");
		
		for (var i = toggles.length - 1; i >= 0; i--) {
		  var toggle = toggles[i];
		  toggleHandler(toggle);
		};
		
		function toggleHandler(toggle) {
		  toggle.addEventListener( "click", function(e) {
			e.preventDefault();
			(this.classList.contains("is-active") === true) ? this.classList.remove("is-active") : this.classList.add("is-active");
		  });
		}
		
		})();
		
		
		$('#vt-hambug').on('click', function(){   	 
		$(".vt-header").toggleClass('slide-in-out');
		
		});
	  
	  
	  
	  // owl carousal
	  
	  var owl = $(".owl-carousel");
     
            owl.owlCarousel({

                itemsCustom : [
                  [0, 1],
                  [450, 2],
                  [600, 6],
                  [700, 3],
                  [1000, 4],
                  [1200, 4],
                      [1400, 5]
                ],
                navigation : true

            });
	  
	  
	  
	   // number spinner //
  
		(function ($) {
		  $('.spinner .btn:first-of-type').on('click', function() {
			$('.spinner input').val( parseInt($('.spinner input').val(), 10) + 1);
		  });
		  $('.spinner .btn:last-of-type').on('click', function() {
			$('.spinner input').val( parseInt($('.spinner input').val(), 10) - 1);
		  });
		})(jQuery);
			
			 window.asd = $('.SlectBox').SumoSelect({ csvDispCount: 3, captionFormatAllSelected: "Yeah, OK, so everything." });
			 $('.datepicker').datepicker();


		// carousal js
	  
	   $(".carousel").carousel({
    interval: 4000
  });
  $(".carousel").on("slid", function() {
    var to_slide;
    to_slide = $(".carousel-item.active").attr("data-slide-no");
    $(".myCarousel-target.active").removeClass("active");
    $(".carousel-indicators [data-slide-to=" + to_slide + "]").addClass("active");
  });
  $(".myCarousel-target").on("click", function() {
    $(this).preventDefault();
    $(".carousel").carousel(parseInt($(this).attr("data-slide-to")));
    $(".myCarousel-target.active").removeClass("active");
    $(this).addClass("active");
  });  
	  
	  
	  
	  
     
 });


