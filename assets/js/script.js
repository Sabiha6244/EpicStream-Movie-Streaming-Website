

function volumeToggle(button) {

   var muted = $(".previewVideo").prop("muted");
   $(".previewVideo").prop("muted", !muted);

   $(button).find("i").toggleClass("fa-volume-xmark");
   $(button).find("i").toggleClass("fa-volume-high");

}

function previewEnded() {
   $(".previewVideo").toggle();
   $(".previewImage").toggle();
}
function goBack() {
   window.history.back();
}
/*function startHideTimer() {
   var timeout = null;
   $(document).on("mousemove", function () {

      clearTimeout(timeout);
      $(".watchNav").fadeIn();

      timeout = $setTimeout(function () {
         $(".watchNav").fadeOut();
      }, 2000);
   })

}*/
function startHideTimer() {
   var timeout = null;

   $(document).on("mousemove", function () {
      clearTimeout(timeout); // Clear the previous timeout
      $(".watchNav").fadeIn(); // Show the navigation

      // Set a new timeout to hide the navigation after 2 seconds
      timeout = setTimeout(function () {
         $(".watchNav").fadeOut(); // Hide the navigation
      }, 2000);
   });
}


function initVideo(){
   startHideTimer();
}
