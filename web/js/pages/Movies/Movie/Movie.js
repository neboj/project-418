import {WebHelper} from "../../../components/Helpers/WebHelper.js";
import {AddToPersonalList} from "../AddToPersonalList.js";
import {ReviewItem} from "./Review/ReviewItem.js";
const webHelper = new WebHelper();

document.getElementById('src-ppl').addEventListener('keypress', function (ev) {
    if (ev.keyCode === 13) {
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
    classname[i].addEventListener('click', (event) => new AddToPersonalList(
        event, classname, false), false);
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
};

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
    modal.style.display = "none";
};

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
};


//SEARCH GIF
$("#src-gifs").keypress(function (event) {
    if (event.which == 13) {
        var queryStringGIF = $('input[name=search-gifs]').val();
        queryStringGIF = queryStringGIF.replace(" ","-");
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
            "  </div></li>");
        const methodName = "SearchGifs";
        const gifData = {queryStringGIF};
        $.ajax({
            url: mslug,
            type: 'POST',
            data: {gifData, methodName},
            async: true,
            success: function (data, status) {
                console.log(data);
                var obj = JSON.parse(data);
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

    $('input[name=review-input]').val('');
    $('#no'+reviewid+'').text(' ('+lajkovi+')');
    $(event.target).css("background-color","#bbbb");
    $(event.target).css("box-shadow","none");
    $(event.target).css("cursor","context-menu");
    const reviewData = {user:usrid,reviewid:reviewid,title:title};
    const methodName = "LikeReview";
    $.ajax({
        url: mslug,
        type: 'POST',
        data: {reviewData, methodName},
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
        var recommendationData = {
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
        const methodName = "SendMovieRecommendation";
        var  mslug = movie_id;
        $.ajax({
            url: mslug,
            type: 'POST',
            data: {recommendationData, methodName},
            success: function (data, status) {
                if (data!=='Exists') {
                    Materialize.toast('Recommended '+title+' to '+ime_i_prezime, 4000);
                } else {
                    Materialize.toast('You have already recommended this movie to '+ime_i_prezime, 4000);
                }
            }, error: function () {
            }
        });
    });
});


const inputElement = document.getElementsByName('review-input')[0];
const usrid = inputElement.dataset.usrid;
const title = inputElement.dataset.title;
const dataElement = document.getElementsByClassName('add-to-list1')[0];
const movie__id = dataElement.dataset.movie__id;
const movie__title= dataElement.dataset.movie__title;
const movie__poster_path = dataElement.dataset.movie__poster_path;
const movie__vote_average = dataElement.dataset.movie__vote_average;
const movie__overview = dataElement.dataset.movie__overview;
const movie__genres = dataElement.dataset.movie__genres;
const movie__backdrop_path = dataElement.dataset.movie__backdrop_path;
const reviewsCollection = document.getElementById("reviews-collection");
const movieData = {
    user:usrid,
    title:title,
    movie__id:movie__id,
    movie__title:movie__title,
    movie__poster_path:movie__poster_path,
    movie__vote_average:movie__vote_average,
    movie__overview:movie__overview,
    movie__genres:movie__genres,
    movie__backdrop_path:movie__backdrop_path
};

//ADD GIF REVIEW
$(document).on("click",".add-gif-review",function(event){
    var gifurl = event.target.parentElement.id;
    var modal = document.getElementById('myModal');
    modal.style.display='none';
    Materialize.toast('You reacted with a gif!', 4000);
    const methodName = "AddGifReaction";
    movieData.gifurl = gifurl;
    $.ajax({url: movie__id, type: 'POST', data: {movieData, methodName}, async: true,
        success: function (data, status) {
            const obj = JSON.parse(data);
            const reviewItem = new ReviewItem(true, obj.review_id, obj.review_date, '', gifurl);
            reviewsCollection.appendChild(reviewItem);
        },error:function(err){
            console.log('operation failed');
            console.log(err);
        }
    });
});


//ADD REVIEW, SUBMIT REVIEW
$(document).on("click",".submit-review-btn",function(event){
    const reviewInput = inputElement.value;
    movieData.review = reviewInput;
    inputElement.value = '';
    $.ajax({url: movie__id, type: 'POST', data: {movieData: movieData, methodName: "AddMovieReview"},
        success: function(data,status){
            const obj = JSON.parse(data);
            const reviewItem = new ReviewItem(false, obj.review_id, obj.review_date, reviewInput, '');
            reviewsCollection.appendChild(reviewItem);
        },error:function(err){
            console.log('operation failed');
            console.log(err);
        }
    });
});


