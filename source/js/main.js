$(document).ready(function() {

  $('#library_caption').click(function() {
    $('#library').toggle();
  });
  
  $('#logoff').click(function() {
    var date = new Date(0);
    document.cookie='uid=;expires=' + date.toUTCString();
    window.location = '/';
  });
});

function login(token){
    $.getJSON("//ulogin.ru/token.php?host=" + encodeURIComponent(location.toString()) + "&token=" + token + "&callback=?",
    function(data){
        data=$.parseJSON(data.toString());
        console.log(data);
        if(!data.error){
            document.cookie = 'uid=' + data.uid;
            window.location.reload(); // Костыль
        }
    });
}