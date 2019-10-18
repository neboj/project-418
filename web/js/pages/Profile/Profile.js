$("#input-list").keypress(function (event) {
    if (event.which == 13) {
        var podaci = $('input[name=input-new-list]').val();
        var korisnik = $('#input-list').data('current-profile');
        $('input[name=input-new-list]').val('');

        var name_for_id = podaci.replace(" ", "_");
        var eventData = {
            'ime_nove_liste': podaci,
            'korisnik': korisnik
        };
        $.ajax({
            url: 'http://localhost/project-418/web/app_dev.php/profile/' + korisnik,
            type: 'POST',
            data: eventData,
            success: function (data, status) {
                if (data !== 'exists') {
                    Materialize.toast('New list created ' + podaci, 4000);
                    $('.users-lists-ul').append("<div class=\"row\" id='rowrow-'" + name_for_id + ">\n" +
                        "                                <div class=\"col s12 m12\">\n" +
                        "                                    <div class=\"card-panel teal\">\n" +
                        "                                          <span class=\"white-text\"  id='" + podaci + "'>\n" +
                        "                                              <span class=\"list_name_o\">" + podaci + " (0)</span>\n" +
                        "<span class='delete-list' id='row-'" + name_for_id + " data-real_name='" + podaci + "' >x</span>" +
                        "                                          </span>\n" +
                        "                                    </div>\n" +
                        "                                </div>\n" +
                        "                            </div>");
                } else {
                    Materialize.toast('You already have a list named ' + podaci, 4000);
                }
            }, error: function () {
            }
        });
    }
});


$(document).on("click", ".delete-list", function (event) {
    var id = $(event.target).attr('id');
    var real_name = $(event.target).data('real_name');
    if (confirm("Delete list: " + real_name) == false) {
        return;
    }
    var korisnik = $('#input-list').data('current-profile');
    var eventData = {
        'list_name_to_delete': real_name
    };
    $.ajax({
        url: 'http://localhost/project-418/web/app_dev.php/profile/' + korisnik,
        type: 'POST',
        data: eventData,
        success: function (data, status) {
            $('#row' + id).empty();
            $('#row' + id).remove();
        }, error: function () {
        }
    });
});


$(document).on("click", ".delete-item", function (event) {
    var id = $(event.target).attr('id');
    var list_id = $(event.target).data('list_id');
    var movie_id = $(event.target).data('movie_id');
    var korisnik = $('#input-list').data('current-profile');
    var eventData = {
        'list_id': list_id,
        'movie_id_to_delete': movie_id
    };
    $.ajax({
        url: 'http://localhost/project-418/web/app_dev.php/profile/' + korisnik,
        type: 'POST',
        data: eventData,
        success: function (data, status) {
            document.getElementById(id).parentElement.remove();
        }, error: function () {
        }
    });
});


$(document).on("click", ".list-switcher", function (event) {
    var list_id = $(event.target).data('list_id');
    var korisnik = $('#input-list').data('current-profile');
    var eventData = {
        'list_id_for_privacy_change': list_id
    };
    $.ajax({
        url: 'http://localhost/project-418/web/app_dev.php/profile/' + korisnik,
        type: 'POST',
        data: eventData,
        success: function (data, status) {
        }, error: function () {
        }
    });
});