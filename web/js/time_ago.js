function time_ago_(date) {

    var seconds = Math.floor((new Date() - date) / 1000);

    var interval = Math.floor(seconds / 31536000);
    if (interval > 1) {
        return interval + " years";
    }
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) {
        return interval + " months";
    }
    interval = Math.floor(seconds / 86400);
    if (interval > 1) {
        return interval + " days";
    }
    interval = Math.floor(seconds / 3600);
    if (interval > 1) {
        return interval + " hours";
    }
    interval = Math.floor(seconds / 60);
    if (interval > 1) {
        return interval + " minutes";
    }
    /*return Math.floor(seconds) + " seconds";*/
    return " less than a minute";
}

function max_words_(text) {
 var str = text.length;
 var short = "";
 if(str>140){
     short = text.substring(0,120);
 }
return short;
}


$(document).ready(function(){
    $(document).on("click",".dots-space",function(e){
        /*$(".dot-open-menu").show();*/
        e.stopPropagation();
        var id = e.target.id;
        var ido = id.substring(2,id.length);
        var idos = 'dots-' + ido;
        var b = document.getElementById(idos);
        b.style.display = 'block';

    });

    $(document).on("click",".dot-open-menu",function(e){
        e.stopPropagation();
    });

    $(document).on("click",function(){
        $(".dot-open-menu").hide();
    });
});

$(document).ready(function(){
    $(".showhide-sidebar").click(function(){
        if($(".sidebar-wrapper").css('display') =='block'){
            $(".sidebar-wrapper").css('display','none');
            $(".main").css('margin-left','0px');
            $(".search-header").css('margin-left','0px');

            if($('.main').attr('id')==='messenger-main'){
                $('.ovde').css('width','70%');
            }

        }else{
            $(".sidebar-wrapper").css('display','block');
            $(".main").css('margin-left','15%');
            $(".search-header").css('margin-left','15%');

            if($('.main').attr('id')==='messenger-main'){
                $('.ovde').css('width','60%');
            }
        }
    });
});

$(document).ready(function(){
    $(document).on("click",".showhide-friend-requests",function(){
        if($(".expand-friend-requests").css('display') =='block'){
            $(".expand-friend-requests").css('display','none');

        }else{
            $(".expand-friend-requests").css('display','block');

            var broj = $('.friend_requests').text();
            broj = parseInt(broj);
            if(broj===0){
                $('.expand-friend-requests').css("display","none");
            }

        }
    });
    $(document).mouseup(function(e)
    {
        var container = $(".expand-friend-requests");

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0)
        {
            container.css('display','none');
        }
    });
});


$(document).ready(function(){
    $(document).on("click",".showhide-unread-notifications",function(){
        if($(".expand-unread-notifications").css('display') =='block'){
            $(".expand-unread-notifications").css('display','none');
        }else{
            $('.expand-unread-notifications').css('display','block');

            var broj = $('.notifications_count').text();
            broj = parseInt(broj);
            if(broj===0){
                $('.expand-unread-notifications').css("display","none");
            }

        }
    });
    $(document).mouseup(function(e)
    {
        var container = $('.expand-unread-notifications');

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0)
        {
            container.css('display','none');
        }
    });
});


$(document).ready(function(){
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() == $(document).height()) {
            /*alert("bottom!");*/
            var page = document.getElementsByClassName('pagination');
            /*alert(page[0].innerHTML);*/
            var number = page[0].innerHTML;

            var podaci = $('input[name=search]').val();

            $.ajax({
                url:'movies',
                type: 'POST',
                data: {page:parseInt(number),foo:podaci},
                success: function (data,status) {
                    var obj = JSON.parse(data);

                    var broj = parseInt(number) + 1;
                    page[0].innerHTML = ''+broj.toString();
                    /*alert('uspeh');*/
                    for(i=0;i<obj.results.length;i++){
                        /*$('.search-container').append(obj.results[i].vote_count);*/

                       /* data-movie__id="{{ i['id'] }}"
                        data-movie__title="{{ i['title'] }}"
                        data-movie__poster_path="{{ i['poster_path'] }}"
                        data-movie__vote_average="{{ i['vote_average'] }}"
                        data-movie__overview="{{ i['overview'] }}"
                        data-movie__genres="{% for genre in i['genre_ids'] %}{{ genre }},{% endfor %}"*/


                       var genres = '';
                       for(h=0;h<obj.results[i].genre_ids.length;h++){
                           genres += obj.results[i].genre_ids[h] + ',';
                       }

                       var allDataNeccessery = "data-movie__id='"+obj.results[i].id+"' data-movie__title='"+obj.results[i].title+"' data-movie__poster_path='"+obj.results[i].poster_path+"' data-movie__vote_average='"+obj.results[i].vote_average+"' data-movie__overview='"+obj.results[i].overview+"' data-movie__backdrop_path='"+obj.results[i].backdrop_path+"'  data-movie__genres='"+genres+"'  ";

                        var  a = $('#lists-and-names').data('mlan');
                        var usssser = a.split("*****ii*");
                        var userid=usssser[0];
                        var listandid=usssser[1].split("*/*");
                        var content = '';
                        for(x=1;x<listandid.length;x++){
                            var blob = listandid[x].split("-**-");
                            var idliste=blob[0];
                            var imeliste=blob[1];
                            content += "<li><span onclick=\"Materialize.toast('Added "+obj.results[i].title+" to "+imeliste+"',4000)\" class=\"add-to-list\" id='user"+userid+"-list"+idliste+"-movie"+obj.results[i].id+"-title"+obj.results[i].title+"' "+allDataNeccessery+">"+imeliste+"</span></li>";
                        }

                        $('.movies-grid-container').append("<div class=\"movie-container\">\n" +
                            "                            <div class=\"movie-content\">\n" +
                            "                                <div class=\"movie-img-container\">\n" +
                            "                                        <div class=\"movie-rating\">\n" +
                            "                                                            <span class=\"movie-rating-text\">\n" +
                            "                                                                <div class=\"movie-rating-circle\">\n" +
                            "\n" +
                            "                                                            "+obj.results[i].vote_average+"\n" +
                            "\n" +
                            "                                                                </div>\n" +
                            "                                                             </span>\n" +
                            "                                        </div>\n" +
                            "                                        <div>\n" +
                            "                                            <img class=\"movie-image\" src=\"https://image.tmdb.org/t/p/w300"+obj.results[i].poster_path+"\">\n" +
                            "                                            <div class=\"dots-space\" id=\"id"+obj.results[i].id+"\">\n" +
                            "                                                <i class=\"material-icons dots-space\" id=\"id"+obj.results[i].id+"\">add</i>\n" +
                            "                                                <div class=\"dot-open-menu\" id=\"dots-"+obj.results[i].id+"\" >\n" +
                            "                                                   <ul> "+content+ "</ul>"+
                            "                                                </div>\n" +
                            "                                            </div>\n" +
                            "                                        </div>\n" +
                            "                                </div>\n" +
                            "                                <div class=\"movie-title-container\">\n" +
                            "                                     <span class=\"movie-title\"><a href=\"/project-418/web/app_dev.php/movies/"+obj.results[i].id+"\">"+obj.results[i].title+"</a></span>\n" +
                            "                                </div>\n" +
                            "\n" +
                            "\n" +
                            "                            </div>\n" +
                            "\n" +
                            "\n" +          "                        </div>")
                    }

                }
            });
        }
    });
});

$(document).ready(function(){
    $("#src").keypress(function (event) {
        if(event.which==13){
            var podaci = $('input[name=search]').val();
            $.ajax({
                url: 'movies',
                type: 'POST',
                data: {foo:podaci},
                async: true,
                success: function(data,status) {
                    var page = document.getElementsByClassName('pagination');
                    page[0].innerHTML = 1;

                    var obj = JSON.parse(data);
                    $('.movies-grid-container').remove();
                    $('.main').append('<div class="movies-grid-container">');
                    for(i=0;i<obj.results.length;i++){
                        /*$('.search-container').append(obj.results[i].vote_count);*/
                        var genres = '';
                        for(h=0;h<obj.results[i].genre_ids.length;h++){
                            genres += obj.results[i].genre_ids[h] + ',';
                        }
 
                        var allDataNeccessery = "data-movie__id='"+obj.results[i].id+"' data-movie__title='"+obj.results[i].title+"' data-movie__poster_path='"+obj.results[i].poster_path+"' data-movie__vote_average='"+obj.results[i].vote_average+"' data-movie__overview='"+obj.results[i].overview+"' data-movie__backdrop_path='"+obj.results[i].backdrop_path+"'  data-movie__genres='"+genres+"'  ";
 
                        var  a = $('#lists-and-names').data('mlan');
                        var usssser = a.split("*****ii*");
                        var userid=usssser[0];
                        var listandid=usssser[1].split("*/*");
                        var content = '';
                        for(x=1;x<listandid.length;x++){
                            var blob = listandid[x].split("-**-");
                            var idliste=blob[0];
                            var imeliste=blob[1];
                            content += "<li><span onclick=\"Materialize.toast('Added "+obj.results[i].title+" to "+imeliste+"',4000)\" class=\"add-to-list\" id='user"+userid+"-list"+idliste+"-movie"+obj.results[i].id+"-title"+obj.results[i].title+"' "+allDataNeccessery+">"+imeliste+"</span></li>";
                        }

                        $('.movies-grid-container').append("<div class=\"movie-container\">\n" +
                            "                            <div class=\"movie-content\">\n" +
                            "                                <div class=\"movie-img-container\">\n" +
                            "                                        <div class=\"movie-rating\">\n" +
                            "                                                            <span class=\"movie-rating-text\">\n" +
                            "                                                                <div class=\"movie-rating-circle\">\n" +
                            "\n" +
                            "                                                            "+obj.results[i].vote_average+"\n" +
                            "\n" +
                            "                                                                </div>\n" +
                            "                                                             </span>\n" +
                            "                                        </div>\n" +
                            "                                        <div>\n" +
                            "                                            <img class=\"movie-image\" src=\"https://image.tmdb.org/t/p/w300"+obj.results[i].poster_path+"\">\n" +
                            "                                            <div class=\"dots-space\" id=\"id"+obj.results[i].id+"\">\n" +
                            "                                                <i class=\"material-icons dots-space\" id=\"id"+obj.results[i].id+"\">add</i>\n" +
                            "                                                <div class=\"dot-open-menu\" id=\"dots-"+obj.results[i].id+"\" >\n" +
                            "                                                   <ul> "+content+ "</ul>"+
                            "                                                </div>\n" +
                            "                                            </div>\n" +
                            "                                        </div>\n" +
                            "                                </div>\n" +
                            "                                <div class=\"movie-title-container\">\n" +
                            "                                     <span class=\"movie-title\"><a href=\"/project-418/web/app_dev.php/movies/"+obj.results[i].id+"\">"+obj.results[i].title+"</a></span>\n" +
                            "                                </div>\n" +
                            "\n" +
                            "\n" +
                            "                            </div>\n" +
                            "\n" +
                            "\n" +          "                        </div>")
                    }

                },error: function(){
                    $('.main').html("<span>Nije pronadjen nijedan rezultat.</span>");
                }
            });

        }
    });
});

$(document).ready(function(){
    $(document).on("click",".add-to-list",function(event){
        var text = $(event.target).text();
        var id = $(event.target).attr('id');
        var niz = [];
        niz = id.split('-');
        var userId = niz[0].substring(4,niz[0].length);
        var listId = niz[1].substring(4,niz[1].length);
        var tmdbId = niz[2].substring(5,niz[2].length);
        var title = niz[3].substring(5,niz[3].length);

        var movie__id = $(event.target).data('movie__id');
        var movie__title= $(event.target).data('movie__title');
        var movie__poster_path= $(event.target).data('movie__poster_path');
        var movie__vote_average= $(event.target).data('movie__vote_average');
        var movie__overview= $(event.target).data('movie__overview');
        var movie__genres= $(event.target).data('movie__genres');
        var movie__backdrop_path= $(event.target).data('movie__backdrop_path');


        /*alert('user: '+userId + ' & list: '+listId);*/
        $.ajax({
            url:'movies',
            type:'POST',
            data: {user:userId,list:listId,tmdbid:tmdbId,title:title,
                    movie__id:movie__id,
                    movie__title:movie__title,
                    movie__poster_path:movie__poster_path,
                    movie__vote_average:movie__vote_average,
                    movie__overview:movie__overview,
                    movie__genres:movie__genres,
                    movie__backdrop_path:movie__backdrop_path
            },
            success: function(data,status){

               /* alert(data[0].broj);*/
               /*alert(data);*/
            },error:function(){
               /* alert('');*/
            }
        });
    });
});


$(document).ready(function(){
    $(document).on("click",".add-to-list1",function(event){
        var text = $(event.target).text();
        var id = $(event.target).attr('id');
        var niz = [];
        niz = id.split('-');
        var userId = niz[0].substring(4,niz[0].length);
        var listId = niz[1].substring(4,niz[1].length);
        var tmdbId = niz[2].substring(5,niz[2].length);
        var title = niz[3].substring(5,niz[3].length);
        /*alert('user: '+userId + ' & list: '+listId);*/

        var movie__id = $(event.target).data('movie__id');
        var movie__title= $(event.target).data('movie__title');
        var movie__poster_path= $(event.target).data('movie__poster_path');
        var movie__vote_average= $(event.target).data('movie__vote_average');
        var movie__overview= $(event.target).data('movie__overview');
        var movie__genres= $(event.target).data('movie__genres');
        var movie__backdrop_path= $(event.target).data('movie__backdrop_path');


        $.ajax({
            url:tmdbId,
            type:'POST',
            data: {user:userId,list:listId,tmdbid:tmdbId,title:title,
                movie__id:movie__id,
                movie__title:movie__title,
                movie__poster_path:movie__poster_path,
                movie__vote_average:movie__vote_average,
                movie__overview:movie__overview,
                movie__genres:movie__genres,
                movie__backdrop_path:movie__backdrop_path
            },
            success: function(data,status){

                /* alert(data[0].broj);*/
                /*alert(data);*/
            },error:function(){
                /* alert('');*/
            }
        });
    });
});


/* ADD REVIEW */
$(document).ready(function(){
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
                alert('less success, still awesome');
           }
       });
   });
});

/*LIKE REVIEW*/
$(document).ready(function(){
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

            }
            if(data==='ok'){

            }else{
                console.log(data);
            }
        },error:function(){
        }
    });
});
});

$(document).ready(function(){
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
});


$(document).ready(function(){
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
});
/*ADD GIF REVIEW*/
$(document).ready(function(){
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
});


$(document).ready(function () {
   $(document).on("click",".accept-friend",function (event) {
       var id = $(event.target).attr('id');
       var answer = $(event.target).data('answer');

       var eventData = {
           'sent_by': id,
           'accept_friend_request'   : answer
       };

       var broj = $('.friend_requests').text();
       broj = parseInt(broj);
       broj--;
       $('.friend_requests').text(broj);
       event.target.parentElement.parentElement.remove();




       $.ajax({
           url: 'http://localhost/project-418/web/app_dev.php/friend/add',
           type: 'POST',
           data: eventData,
           success:function(data,status){
           },error:function(){
           }
       });
   });
});
/*START NEW CHAT FROM /CHAT*/
$(document).ready(function () {
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
});

$(document).ready(function(){
    $("#src-ppl").keypress(function (event) {
        if(event.which==13){
            var podaci = $('input[name=search-people]').val();
            var eventData = {
                'query': podaci
            };
            $('#rez-pretrage').empty();
            $('#rez-pretrage').append("<li><div class=\"preloader-wrapper small active\">\n" +
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
                url: 'http://localhost/project-418/web/app_dev.php/search',
                type: 'POST',
                data: eventData,
                success: function (data, status) {
                    console.log(data);
                    var obj = JSON.parse(data);
                    var userNiz = obj['rez'];
                    $('#rez-pretrage').empty();

                    var is_rec = $('input[name=search-people]').data('is_rec');
                    var secondary_content_span = '';


                    for(var i=0;i<userNiz.length;i++){

                        if(is_rec==='yes'){
                            secondary_content_span = "<span class='secondary-content' id='rec-sec-cont' data-received_by='"+userNiz[i]['id']+"' data-ime_i_prezime='"+userNiz[i]['ime_i_prezime']+"'><i class='material-icons'>send</i></span>";
                        }


                        $('#rez-pretrage').append("<li class=\"collection-item avatar\">\n" +
                            "                   <a href='http://localhost/project-418/web/app_dev.php/profile/"+userNiz[i]['id']+"'><img src='http://localhost/project-418/web/images/profile/"+userNiz[i]['profile_image']+"' alt=\"\" class=\"circle\"></a>\n" +
                            "                   <a href='http://localhost/project-418/web/app_dev.php/profile/"+userNiz[i]['id']+"'><span class=\"title\">"+userNiz[i]['ime_i_prezime']+"</span></a>\n" +
                            "                        <p>"+userNiz[i]['username']+" <br>\n" +
                            "                           " +
                            "                        </p>\n" +
                            "                        " + secondary_content_span+
                            "                    </li>");
                        }
                }, error: function () {
                }
            });
        }
    });
});








/*RECOMMEND A MOVIE , ADD RECOMMEND*/
$(document).ready(function(){
   $(document).on("click","#rec-sec-cont",function(target){
       /*send recommend movie */
       var movie_id = $('input[name=search-people]').data('movie_id');
       var received_by = $('#rec-sec-cont').data('received_by');
       var title = $('input[name=search-people]').data('title');
       var ime_i_prezime = $('#rec-sec-cont').data('ime_i_prezime');
       $('input[name=input-new-list]').val('');


       var movie__id = $('.add-to-list1').data('movie__id');
       var movie__title= $('.add-to-list1').data('movie__title');
       var movie__poster_path= $('.add-to-list1').data('movie__poster_path');
       var movie__vote_average= $('.add-to-list1').data('movie__vote_average');
       var movie__overview= $('.add-to-list1').data('movie__overview');
       var movie__genres= $('.add-to-list1').data('movie__genres');
       var movie__backdrop_path= $('.add-to-list1').data('movie__backdrop_path');


       var eventData = {
           'movie_id_to_recommend': movie_id,
           'received_by' : received_by,
           'title' : title,
           movie__id:movie__id,
           movie__title:movie__title,
           movie__poster_path:movie__poster_path,
           movie__vote_average:movie__vote_average,
           movie__overview:movie__overview,
           movie__genres:movie__genres,
           movie__backdrop_path:movie__backdrop_path
       };
       $.ajax({
           url: 'http://localhost/project-418/web/app_dev.php/movies/'+movie_id,
           type: 'POST',
           data: eventData,
           success: function (data, status) {
               if(data!=='exists'){
                   Materialize.toast('Recommended '+title+' to '+ime_i_prezime, 4000);
               }else{
                   Materialize.toast('You have already recommended this movie to '+ime_i_prezime, 4000);
               }
           }, error: function () {
           }
       });
   });
});

$(document).ready(function() {
    $("#input-list").keypress(function (event) {
        if (event.which == 13) {
            var podaci = $('input[name=input-new-list]').val();
            var korisnik = $('#input-list').data('current-profile');
            $('input[name=input-new-list]').val('');

            var name_for_id = podaci.replace(" ","_");
            var eventData = {
                'ime_nove_liste': podaci,
                'korisnik' : korisnik
            };
            $.ajax({
                url: 'http://localhost/project-418/web/app_dev.php/profile/'+korisnik,
                type: 'POST',
                data: eventData,
                success: function (data, status) {
                    if(data!=='exists'){
                        Materialize.toast('New list created '+podaci, 4000);
                        $('.users-lists-ul').append("<div class=\"row\" id='rowrow-'"+name_for_id+">\n" +
                            "                                <div class=\"col s12 m12\">\n" +
                            "                                    <div class=\"card-panel teal\">\n" +
                            "                                          <span class=\"white-text\"  id='"+podaci+"'>\n" +
                            "                                              <span class=\"list_name_o\">"+podaci+" (0)</span>\n" +
                            "<span class='delete-list' id='row-'"+name_for_id+" data-real_name='"+podaci+"' >x</span>"+
                            "                                          </span>\n" +
                            "                                    </div>\n" +
                            "                                </div>\n" +
                            "                            </div>");
                    }else{
                        Materialize.toast('You already have a list named '+podaci, 4000);
                    }
                }, error: function () {
                }
            });
        }
    });
});


$(document).ready(function () {
    $(document).on("click",".delete-list",function (event) {
        var id = $(event.target).attr('id');
        var real_name = $(event.target).data('real_name');

        /*alert(id+" "+real_name);
        return;*/
        if(confirm("Delete list: "+real_name)==false){
            return;
        }


        var korisnik = $('#input-list').data('current-profile');
        var eventData = {
            'list_name_to_delete': real_name
        };




        $.ajax({
            url: 'http://localhost/project-418/web/app_dev.php/profile/'+korisnik,
            type: 'POST',
            data: eventData,
            success:function(data,status){
                $('#row'+id).empty();
                $('#row'+id).remove();
            },error:function(){
            }
        });
    });
});




$(document).ready(function () {
    $(document).on("click",".delete-item",function (event) {
        var id = $(event.target).attr('id');
        var list_id = $(event.target).data('list_id');
        var movie_id = $(event.target).data('movie_id');

        /*alert(id+" "+real_name);
        return;*/
        /*if(confirm("Delete list: "+real_name)==false){
            return;
        }*/


        var korisnik = $('#input-list').data('current-profile');
        var eventData = {
            'list_id': list_id,
            'movie_id_to_delete' : movie_id
        };




        $.ajax({
            url: 'http://localhost/project-418/web/app_dev.php/profile/'+korisnik,
            type: 'POST',
            data: eventData,
            success:function(data,status){
                document.getElementById(id).parentElement.remove();
            },error:function(){
            }
        });
    });
});

$(document).ready(function () {
    $(document).on("click",".list-switcher",function (event) {
        var list_id = $(event.target).data('list_id');
        var korisnik = $('#input-list').data('current-profile');
        /*document.getElementById('mySwitch').checked*/
        /*$('#mySwitch').prop('checked', true) as setter.*/
        /*true = public*/
        /*false = private */
        /*var is_private=false;
        if($(event.target).prop('checked')===false){ /!*set to private*!/
            is_private=true;
        }*/

        /*alert($(event.target).prop('checked'));*/

        var eventData = {
            'list_id_for_privacy_change': list_id
        };
        $.ajax({
            url: 'http://localhost/project-418/web/app_dev.php/profile/'+korisnik,
            type: 'POST',
            data: eventData,
            success:function(data,status){
            },error:function(){
            }
        });
    });
});


$(document).ready(function(){
    // the "href" attribute of the modal trigger must specify the modal ID that wants to be triggered
    $('.modal').modal();
});

