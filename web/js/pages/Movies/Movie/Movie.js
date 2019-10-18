import { WebHelper } from "../../../components/Helpers/WebHelper.js";
import {AddToPersonalList} from "../AddToPersonalList.js";

document.getElementById('src-ppl').addEventListener('keypress', function (ev) {
    if (ev.keyCode === 13) {
        const webHelper = new WebHelper();
        const profileUrl = webHelper.generateEndpoint('profile/');
        const searchEndpoint = webHelper.generateEndpoint('search');
        const inputElement = document.getElementsByName('search-people')[0];
        const userData = inputElement.value;
        const eventData = { 'query': userData };
        const resultElement = document.getElementById('rez-pretrage');
        resultElement.innerHTML = '';
        resultElement.innerHTML = "<li><div class=\"preloader-wrapper small active\">\n" +
            "    <div class=\"spinner-layer spinner-green-only\">\n" +
            "      <div class=\"circle-clipper left\">\n" +
            "        <div class=\"circle\"></div>\n" +
            "      </div><div class=\"gap-patch\">\n" +
            "        <div class=\"circle\"></div>\n" +
            "      </div><div class=\"circle-clipper right\">\n" +
            "        <div class=\"circle\"></div>\n" +
            "      </div>\n" +
            "    </div>\n" +
            "  </div></li>";
        $.ajax({
            url: searchEndpoint, type: 'POST', data: eventData,
            success: function (data, status) {
                console.log(data);
                let users = JSON.parse(data)['responseData'];
                resultElement.innerHTML = '';
                let secondary_content_span = '';
                for (let i = 0; i < users.length; i++) {
                    if (inputElement.dataset.is_rec) {
                        secondary_content_span = "<span class='secondary-content' id='rec-sec-cont' data-received_by='" +
                            users[i]['id'] + "' data-ime_i_prezime='" +
                            users[i]['ime_i_prezime'] + "'><i class='material-icons'>send</i></span>";
                    }
                    resultElement.innerHTML = "<li class=\"collection-item avatar\">\n" +
                        "<a href='" + profileUrl + users[i]['id'] + "'><img src='" + profileUrl + users[i]['profile_image'] + "' alt=\"\" class=\"circle\"></a>\n" +
                        "<a href='" + profileUrl + users[i]['id'] + "'><span class=\"title\">" + users[i]['ime_i_prezime'] + "</span></a>\n" +
                        "<p>" + users[i]['username'] + " <br>\n" +
                        "" +
                        "</p>\n" +
                        "" + secondary_content_span +
                        "</li>";
                }
            }, error: function (err) {console.log(err)}
        });
    }
});






var classname = document.getElementsByClassName("add-to-list1");
for (var i = 0; i < classname.length; i++) {
    classname[i].addEventListener('click', (event) => new AddToPersonalList(event, classname, false), false);
}





// Get the modal
var modal = document.getElementById('myModal');

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
    modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}









//SEARCH GIF
$("#src-gifs").keypress(function (event) {
    if (event.which == 13) {
        var podacigif = $('input[name=search-gifs]').val();
        podacigif = podacigif.replace(" ","-");
        var  mslug = $('input[name=review-input]').data('movid');
        $('.ul-gifs').empty();
        $('.ul-gifs').append("<li><div class=\"preloader-wrapper small active\">\n" +
            "    <div class=\"spinner-layer spinner-green-only\">\n" +
            "      <div class=\"circle-clipper left\">\n" +
            "        <div class=\"circle\"></div>\n" +
            "      </div><div class=\"gap-patch\">\n" +
            "        <div class=\"circle\"></div>\n" +
            "      </div><div class=\"circle-clipper right\">\n" +
            "        <div class=\"circle\"></div>\n" +
            "      </div>\n" +
            "    </div>\n" +
            "  </div></li>")
        $.ajax({
            url: mslug,
            type: 'POST',
            data: {podacigif: podacigif},
            async: true,
            success: function (data, status) {
                console.log(data);
                var obj = JSON.parse(data);
                /*alert(obj['results'][0]['media'][0]['tinygif']['url']);
                https://media.tenor.com/images/0da01ce7acaf020a0678cac3cdedab58/tenor.gif*/
                $('.ul-gifs').empty();
                for(var i=0;i<obj['results'].length;i++){
                    var gifurl = obj['results'][i]['media'][0]['tinygif']['url'];
                    $('.ul-gifs').append("<li><span class='add-gif-review' id="+gifurl+"><img src="+gifurl+"></span></li>");
                }

            },error:function(){

            }
        });
    }
});



//LIKE REVIEW
$(document).on("click",".like-review",function(event){
    var reviewid = event.target.parentElement.id;
    var  mslug = $('input[name=review-input]').data('movid');
    var usrid =$('input[name=review-input]').data('usrid');
    var title =$('input[name=review-input]').data('title');

    var br = $('#no'+reviewid+'').text();
    var tokeni = br.split('(');
    var lajkovi  =tokeni[1].substring(0,tokeni[1].length-1);
    lajkovi=parseInt(lajkovi)+1;

    var route = "{{ path('moviePage', {'slug': mslug})|escape('js') }}";
    $('input[name=review-input]').val('');
    $('#no'+reviewid+'').text(' ('+lajkovi+')');
    $(event.target).css("background-color","#bbbb");
    $(event.target).css("box-shadow","none");
    $(event.target).css("cursor","context-menu");

    $.ajax({
        url: mslug,
        type: 'POST',
        data: {user:usrid,reviewid:reviewid,title:title},
        success: function(data,status){
            if(data==='alreadyLiked'){
                // do stuff
            }
            if(data==='ok'){
                // do other stuff
            }else{
                console.log(data);
            }
        },error:function(){
        }
    });
});



$(document).on("click",".add-gif-review",function(event){
    var gifurl = event.target.parentElement.id;
    var usrid =$('input[name=review-input]').data('usrid');
    var  mslug = $('input[name=review-input]').data('movid');
    var  title = $('input[name=review-input]').data('title');
    var modal = document.getElementById('myModal');
    modal.style.display='none';

    Materialize.toast('You reacted with a gif!', 4000);

    var movie__id = $('.add-to-list1').data('movie__id');
    var movie__title= $('.add-to-list1').data('movie__title');
    var movie__poster_path= $('.add-to-list1').data('movie__poster_path');
    var movie__vote_average= $('.add-to-list1').data('movie__vote_average');
    var movie__overview= $('.add-to-list1').data('movie__overview');
    var movie__genres= $('.add-to-list1').data('movie__genres');
    var movie__backdrop_path= $('.add-to-list1').data('movie__backdrop_path');

    $.ajax({
        url: mslug,
        type: 'POST',
        data: {gifurl: gifurl,user:usrid,title:title,
            movie__id:movie__id,
            movie__title:movie__title,
            movie__poster_path:movie__poster_path,
            movie__vote_average:movie__vote_average,
            movie__overview:movie__overview,
            movie__genres:movie__genres,
            movie__backdrop_path:movie__backdrop_path
        },
        async: true,
        success: function (data, status) {
            console.log(data);
            var obj = JSON.parse(data);
            var userNiz = obj['kor'];
            var revNiz  = obj['kom'];
            var likesNiz = obj['lik'];
            $('#reviews-collection').empty();
            /*var konj='';
            for(var i=0;i<userNiz.length;i++){
                konj+=userNiz[i]['firstName'] + ' ';
            }
            /!*alert(userNiz[0]['id']);*!/
            alert(konj);*/
            for(var i=0;i<revNiz.length;i++){
                var identif = 'no'+revNiz[i]['id'];


                if(revNiz[i]['movie']==mslug){
                    var brojLajkova=0;
                    for(var u=0;u<likesNiz.length;u++){
                        if(likesNiz[u]['reviewId']===revNiz[i]['id']){
                            brojLajkova++;
                        }
                    }

                    var tree = "<a class=\"waves-effect waves-light btn btn-small like-review likerev-no\" >like</a><span class=\"broj\" id="+identif+">("+brojLajkova+")</span>\n" ;
                    for(var u=0;u<likesNiz.length;u++){
                        if((likesNiz[u]['reviewId']===revNiz[i]['id']) && (likesNiz[u]['user']===parseInt(usrid))){
                            tree="<a class=\"waves-effect waves-light btn btn-small like-review likerev-yes\">like</a><span class=\"broj\" id="+identif+">("+brojLajkova+")</span>\n" ;
                            break;
                        }
                    }

                    var tm = revNiz[i]['createdAt']['timestamp'];
                    var d = new Date(tm*1000);
                    var n = d.toDateString();
                    var s = n.split(' ');
                    var vr = s[1]+' '+s[2]+', '+s[3];
                    console.log(vr);

                    var imeprezime=$('.profile-name-container').text();
                    var slika='';
                    for(var j=0;j<userNiz.length;j++){
                        if(revNiz[i]['user']===userNiz[j]['id']){
                            imeprezime = userNiz[j]['firstName'] + ' ' + userNiz[j]['lastName'];
                            slika=userNiz[j]['profileImage'];
                        }
                    }
                    $('#reviews-collection').append(
                        "                                    <li class=\"collection-item avatar\" id='"+revNiz[i]['id']+"'>\n" +
                        "                                        <img src='http://localhost/project-418/web/images/profile/"+slika+"' alt=\"\" class=\"circle\">\n" +
                        "                                        <span class=\"title\">"+imeprezime+"</span>\n" +
                        "                                        <p>\n" +
                        "                                            "+revNiz[i]['reviewTxt']+"<img src='"+revNiz[i]['gifUrl']+"'>"+"\n" +
                        "                                        </p>\n" +
                        "<span href=\"#!\" class=\"secondary-content rev-time\"> "+vr+" </span>"+
                        "                                       " +tree+
                        "                                    </li>")
                }
            }
        },error:function(){

        }
    });
});


//ADD REVIEW, SUBMIT REVIEW
$(document).on("click",".submit-review-btn",function(event){
    var  mslug = $('input[name=review-input]').data('movid');
    var usrid =$('input[name=review-input]').data('usrid');
    var podaci = $('input[name=review-input]').val();
    var route = "{{ path('moviePage', {'slug': mslug})|escape('js') }}";
    var title = $('input[name=review-input]').data('title');
    $('input[name=review-input]').val('');


    var movie__id = $('.add-to-list1').data('movie__id');
    var movie__title= $('.add-to-list1').data('movie__title');
    var movie__poster_path= $('.add-to-list1').data('movie__poster_path');
    var movie__vote_average= $('.add-to-list1').data('movie__vote_average');
    var movie__overview= $('.add-to-list1').data('movie__overview');
    var movie__genres= $('.add-to-list1').data('movie__genres');
    var movie__backdrop_path= $('.add-to-list1').data('movie__backdrop_path');


    $.ajax({
        url: mslug,
        type: 'POST',
        data: {user:usrid,review:podaci,title:title,
            movie__id:movie__id,
            movie__title:movie__title,
            movie__poster_path:movie__poster_path,
            movie__vote_average:movie__vote_average,
            movie__overview:movie__overview,
            movie__genres:movie__genres,
            movie__backdrop_path:movie__backdrop_path
        },
        success: function(data,status){
            console.log(data);
            var obj = JSON.parse(data);
            var userNiz = obj['kor'];
            var revNiz  = obj['kom'];
            var likesNiz = obj['lik'];
            $('#reviews-collection').empty();
            /*var konj='';
            for(var i=0;i<userNiz.length;i++){
                konj+=userNiz[i]['firstName'] + ' ';
            }
            /!*alert(userNiz[0]['id']);*!/
            alert(konj);*/
            for(var i=0;i<revNiz.length;i++){
                var identif = 'no'+revNiz[i]['id'];


                if(revNiz[i]['movie']==mslug){
                    var brojLajkova=0;
                    for(var u=0;u<likesNiz.length;u++){
                        if(likesNiz[u]['reviewId']===revNiz[i]['id']){
                            brojLajkova++;
                        }
                    }

                    var tree = "<a class=\"waves-effect waves-light btn btn-small like-review likerev-no\" >like</a><span class=\"broj\" id="+identif+">("+brojLajkova+")</span>\n" ;
                    for(var u=0;u<likesNiz.length;u++){
                        if((likesNiz[u]['reviewId']===revNiz[i]['id']) && (likesNiz[u]['user']===parseInt(usrid))){
                            tree="<a class=\"waves-effect waves-light btn btn-small like-review likerev-yes\">like</a><span class=\"broj\" id="+identif+">("+brojLajkova+")</span>\n" ;
                            break;
                        }
                    }

                    var tm = revNiz[i]['createdAt']['timestamp'];
                    var d = new Date(tm*1000);
                    var n = d.toDateString();
                    var s = n.split(' ');
                    var vr = s[1]+' '+s[2]+', '+s[3];
                    console.log(vr);

                    var imeprezime=$('.profile-name-container').text();
                    var slika = '';
                    for(var j=0;j<userNiz.length;j++){
                        if(revNiz[i]['user']===userNiz[j]['id']){
                            imeprezime = userNiz[j]['firstName'] + ' ' + userNiz[j]['lastName'];
                            slika = userNiz[j]['profileImage'];
                        }
                    }
                    $('#reviews-collection').append(
                        "                                    <li class=\"collection-item avatar\" id='"+revNiz[i]['id']+"'>\n" +
                        "                                        <img src='http://localhost:8888/project-418/web/images/profile/"+slika+"' alt=\"\" class=\"circle\">\n" +
                        "                                        <span class=\"title\">"+imeprezime+"</span>\n" +
                        "                                        <p>\n" +
                        "                                            "+revNiz[i]['reviewTxt']+"<img src='"+revNiz[i]['gifUrl']+"'>"+"\n" +
                        "                                        </p>\n" +
                        "<span href=\"#!\" class=\"secondary-content rev-time\"> "+vr+" </span>"+
                        "                                       " +tree+
                        "                                    </li>")
                }
            }
        },error:function(){
            alert('less success, still awesome');
        }
    });
});