import {AddToPersonalList} from "./AddToPersonalList.js";

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

var classname = document.getElementsByClassName("add-to-list");
for (var i = 0; i < classname.length; i++) {
    classname[i].addEventListener('click', (event) => new AddToPersonalList(event, classname, 'movies'), false);
}


