$(function() {
 console.log("Loading zoo");

 function loadZoo() {
 $.getJSON("/api/zoo/", function( zoo ){
   console.log(zoo);
   var message;
   message = zoo[0].name + " " + zoo[0].gender + " " + zoo[0].animal + " " + zoo[0].birthYear;
   $(".zoo").text(message);

  });
 };

  loadZoo();
  setInterval(loadZoo, 2000);
});