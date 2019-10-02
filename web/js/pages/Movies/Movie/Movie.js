import { WebHelper } from "../../../components/Helpers/WebHelper.js";

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