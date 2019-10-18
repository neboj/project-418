$("#src").keypress(function (event) {
    const paginationEl = document.getElementById('pagination');
    const pageNumber = 0;
    if(event.which==13){
        var podaci = $('input[name=search]').val();
        $.ajax({
            url: 'movies',
            type: 'POST',
            data: {movieName:podaci, page: pageNumber, methodName:'SearchMovieByString'},
            success: function(data,status) {
                console.log('rezultat: ' + data);
                paginationEl.innerHTML = 1;
                var obj = JSON.parse(data);
                $('.movies-grid-container').remove();
                $('.main').append('<div class="movies-grid-container">');
                for(i=0;i<obj.results.length;i++){
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

            },error: function(err){
                $('.main').html("<span>Nije pronadjen nijedan rezultat.</span>");
            }
        });
    }
});