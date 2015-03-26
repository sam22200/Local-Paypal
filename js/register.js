$(document).ready(function() {

$( "form" ).submit(function( event ) {

  var b1 = 0;
  var b2 = 0;
  var b3 = 0;
  var mail = $( "input[name='email']").val();
  var user = $( "input[name='username']").val();
  var password = $( "input[name='password']").val();

  if ( validateEmail(mail))  {
    $( "span.email-field" ).text( "Valide..." ).show();
    b1 = 1;
  } else {
    $( "span.email-field" ).text( "Non valide!" ).show().fadeOut( 2000 );
    b1 = 0;
  }

  if ( user !== "") {
    $( "span.username-field" ).text( "Valide..." ).show();
    b2 = 1;
  }else {
    $( "span.username-field" ).text( "Non valide!" ).show().fadeOut( 2000 );
    b2 = 0;
  }


   if ( password !== "") {
    $( "span.password-field" ).text( "Valide..." ).show();
    b3 = 1;
   }else {
    $( "span.password-field" ).text( "Non valide!" ).show().fadeOut( 2000 );
     b3 = 0;
    }

    if (b1==1 && b2==1 && b3==1){
      return;
    } else {
      event.preventDefault();
    }
});

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

});