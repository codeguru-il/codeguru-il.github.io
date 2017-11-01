$(function() {
  // Add 'back to the top' button to html with suitable ID and class [that will not show the button]
  // create button
  var button = $('<figure class="up-elevator"><img src="Images/up.png" alt="מעלית למעלה?" title="מעלית למעלה?"><figcaption>מעלית<br>למעלה?</figcaption></figure>');
  button.appendTo($('footer'));
  
  
  // Create elevator object to be used later: //
  var myElevator = new Elevator({
    mainAudio: 'ElevatorAudio/bensound-elevator.mp3', // Music from http://www.bensound.com/
    endAudio: 'ElevatorAudio/ding.mp3'
  });
  
  // When the nav-menu is not visible - show the button
  $(window).scroll(function() {
    if ($('body').scrollTop() > 200 || $('html').scrollTop() > 200) {
      $('.up-elevator').fadeIn();
    } else {
      $('.up-elevator').fadeOut();
    }
  });
  
  // When the button is clicked - elevate: //
  $(".up-elevator").on('click', function() {
    myElevator.elevate();
  });
});