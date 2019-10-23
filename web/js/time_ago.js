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

