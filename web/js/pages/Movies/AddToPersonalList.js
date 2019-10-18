import {WebHelper} from "../../components/Helpers/WebHelper.js";

function AddToPersonalList(event, classname, endpoint) {
    const id = $(event.target).attr('id');
    const niz = id.split('-') || [];
    const userId = niz[0].substring(4,niz[0].length);
    const listId = niz[1].substring(4,niz[1].length);
    const tmdbId = niz[2].substring(5,niz[2].length);
    const title = niz[3].substring(5,niz[3].length);

    const movie__id = $(event.target).data('movie__id');
    const movie__title= $(event.target).data('movie__title');
    const movie__poster_path= $(event.target).data('movie__poster_path');
    const movie__vote_average= $(event.target).data('movie__vote_average');
    const movie__overview= $(event.target).data('movie__overview');
    const movie__genres= $(event.target).data('movie__genres');
    const movie__backdrop_path= $(event.target).data('movie__backdrop_path');

    const webHelper = new WebHelper();
    const controllerEndpoint = endpoint ? webHelper.generateEndpoint(endpoint) : tmdbId;
    const movieData = {user:userId,list:listId,tmdbid:tmdbId,title:title,
        movie__id:movie__id,
        movie__title:movie__title,
        movie__poster_path:movie__poster_path,
        movie__vote_average:movie__vote_average,
        movie__overview:movie__overview,
        movie__genres:movie__genres,
        movie__backdrop_path:movie__backdrop_path
    };
    // console.log('kurcina',JSON.stringify(movieData));
    $.ajax({
        url: controllerEndpoint,
        type:'POST',
        data: {movieData: movieData, methodName: 'AddToPersonalList'},
        success: function(data,status) {
            console.log(data);
            console.log('kurcina',JSON.stringify(movieData));
        },
        error:function(err) { console.log(err); }
    });
}
export {AddToPersonalList};