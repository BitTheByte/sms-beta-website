
var res_pup= "";
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    if (response.status === 'connected') {
      testAPI();
    } else {
      document.getElementById('status').innerHTML = '';
    }
  }
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : '286944368384009',
    cookie     : true,  // enable cookies to allow the server to access 
                        // the session
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.8' // use graph api version 2.8
  });

  FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };

  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      console.log('Successful login for: ' + response.name);
      res_pup = response.name;
      document.getElementById('status').innerHTML =
        ' Thanks for logging in, ' + response.name + '!';
    });
  }

function send(){
var city = document.getElementById('city').value;
var num = document.getElementById('num').value;
var msg = document.getElementById('msg').value;
var token = grecaptcha.getResponse();

if(document.getElementById('status').innerHTML != ""){
     $.ajax({
                    url: "send.php",
                    type: "post",
                    data: "num="+city+num+"&msg="+msg+"&token="+token+"&sender="+res_pup,
                    success: function (response) {
                        if(response.includes('url founded')){
                         Materialize.toast('Please remove Urls from message', 4000);
                        }else{
                           try
                           {
                                var json_r = JSON.parse(response);
                                if(json_r.banned == "false"){
                                  if(json_r.token == "true"){
                                      if(json_r.send=="true"){Materialize.toast('Your message has been sent successfully', 4000);
                document.getElementById('btn-send').innerHTML = "Send New mesesage";              
                document.getElementById('btn-send').onclick = function(){
                window.location.href="http://www.ae-messenger.tk";
}
}else{Materialize.toast("Sorry now i have issue with API Providers :( , come back later", 4000);}
                                          
                                      }
                                  else{Materialize.toast('Please Check your CAPTCHA or Refresh the page', 4000);}
                                }else
                                {
                                  Materialize.toast('Banned: Please contact me', 4000)
                                }



                          }
                          catch(err)
                          {
                                 Materialize.toast('Please contact me to solve the issue', 4000)
                          }
                    }},
                    error: function(jqXHR, textStatus, errorThrown) {
                    }
            
            
                });
}
else{
 Materialize.toast('Please login with Facebook first', 4000);
}
}
          function validate(evt) {
          var theEvent = evt || window.event;
          var key = theEvent.keyCode || theEvent.which;
          key = String.fromCharCode( key );
          var regex = /[0-9]|\./;
          if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
          }
        }
          setInterval(function() {
                var num = document.getElementById('num');
               var msg = document.getElementById('msg');
               var button = document.getElementById('btn-send');
               if(num != null &&msg != null){
                   if(num.value.toString().length >= 10 && msg.value != ""){

                       button.className = "waves-effect waves-light btn blue";
                   }else{
                         button.className = "btn disabled";
                   }
               }
        }, 500);
        
        function textCounter(field,field2,maxlimit)
{
 var countfield = document.getElementById(field2);
 if ( field.value.length > maxlimit ) {
  field.value = field.value.substring( 0, maxlimit );
  return false;
 } else {
  countfield.value = maxlimit - field.value.length;
 }
}