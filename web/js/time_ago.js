function time_ago_(date) {
//REWRITE THIS IN PHP FOO
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
 // REMOVE THIS FN, OR REWRITE
return short;
}


$(document).ready(function() {
    // the "href" attribute of the modal trigger must specify the modal ID that wants to be triggered
    $('.modal').modal();
});

/*RECOMMEND A MOVIE , ADD RECOMMEND*/
// TODO NOT WORKING BELOW
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
           url: 'http://localhost:8888/project-418/web/app_dev.php/movies/'+movie_id,
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