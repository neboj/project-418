// START NEW CHAT FROM /CHAT
var nesredjeno = $('.all-friends').text();
var niz = nesredjeno.split(",");
$(document).on("keyup",".input-new-chat",function (event) {
    var podaci = $('input[name=start-new-chat]').val();
    if(podaci.length<1){
        $('.results-input-new-chat').css("display","none");

    }else{
        $('.results-input-new-chat').css("display","block");

    }
    $('.results-input-new-chat').empty();
    for(var i=0;i<niz.length-1;i++){
        var tokeni = niz[i].split(" ");/*npr: boki coki 7*/
        var ime_prezime ='';
        for(var j=0;j<tokeni.length-1;j++){/*boki coki*/
            ime_prezime+= ' '+tokeni[j];
        }
        var ident = tokeni[tokeni.length-1]; /*uzmi samo id: 7*/
        if((niz[i].toUpperCase()).indexOf(podaci.toUpperCase()) >= 0){
            $('.results-input-new-chat').append("<p class='start_private_chat' id='"+ident+"'>"+ime_prezime+"</p>");
        }else{
            continue;
        }
    }
    /*uzmi niz prijatelja i search,kada kliknes na nekog,pozovi ajax,napravi chat*/
});