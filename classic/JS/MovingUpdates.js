  /* **********************************************
   * Based on Cross browser Marquee II- (c) Dynamic Drive (www.dynamicdrive.com)
   ********************************************* */

$(function() {
  var copyspeed = 2; //Specify marquee scroll speed (larger is faster 1-10)
  var delay = 1000;
  var marquee = $('#updates');
  var marqueeContainer = marquee.parent();
  
  if (marqueeContainer.innerHeight() > marquee.innerHeight()) { // adjust the container height if needed (make it be smaller the the height of the updates list itself)
    marqueeContainer.css('height', marquee.innerHeight()-10);
  }
  initializeMarquee()

  function initializeMarquee() {
    
    marquee.css('top', '0'); // To make sure, for cross-browsers support
    marqueeContainer.css('overflow', 'hidden'); // For support in no-JS users
    
    totalUpdatesHeight = marquee.outerHeight();
    showAreaHeight = marqueeContainer.outerHeight();
    var intervalID = 0;
    var timeoutID = setTimeout(function() {intervalID = setInterval(scrollMarquee ,45);}, delay);
    
    marqueeContainer.mouseenter(function() {
      if (intervalID === 0) {
        clearTimeout(timeoutID)
      } else {
        clearInterval(intervalID);
      }
    });
      
    marqueeContainer.mouseleave(function() {
      intervalID = setInterval(scrollMarquee, 45);
    });
  }
  
  function scrollMarquee() {
    if (Math.abs(parseInt(marquee.css('top'))) < (totalUpdatesHeight + 8)) {
      marquee.css('top', parseInt(marquee.css('top')) - copyspeed + "px");
    } else {
      marquee.css('top', showAreaHeight + 8 + "px");
    }
  }

});
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
    
  
  /*
  
  function a() {
    $('nav').text($('dl#updates').css('bottom'));
    $('dl#updates').animate({bottom:'+=25px'}, 1000, a);
  }
  
  var text = $('dl#updates');
  $('dl#updates').on('click', function() {
    $(this).text('yo');
    $(this).slideToggle();
    $(this).animate({fontSize:"5em"});
    $(this).animate({bottom:'+=25px'}, 1000, a);
    });
});

*/