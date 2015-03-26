$(document).ready(function() {

$( "form" ).submit(function( event ) {

  var b1 = 0;
  var b2 = 0;
  var user = $( "input[name='username']").val();
  var password = $( "input[name='password']").val();


  if ( user !== "") {
    $( "span.username-field" ).text( "Valide..." ).show();
    b1 = 1;
  }else {
    $( "span.username-field" ).text( "Non valide!" ).show().fadeOut( 2000 );
    b1 = 0;
  }


   if ( password !== "") {
    $( "span.password-field" ).text( "Valide..." ).show();
    b2 = 1;
   }else {
    $( "span.password-field" ).text( "Non valide!" ).show().fadeOut( 2000 );
     b2 = 0;
    }

    if (b1==1 && b2==1){
      return;
    } else {
      event.preventDefault();
    }
});


});