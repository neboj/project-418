$(document).ready(function(){
    Pusher.logToConsole = true;
    var chat_name_current = $('.chatName').data('chat_name');
    $('#'+chat_name_current).css("background","#d1d1d1");
    var broj = $('.friend_requests').text();
    broj = parseInt(broj);
    if(broj>0){
        $('.ul-friend-requests').css("display","block");
    }

    var br = $('.notifications_count').text();
    br = parseInt(br);
    if(br>0){
        $('.ul-unread-notifications').css("display","block");
    }




    $('.ovde').animate({scrollTop: $('.ovde').prop('scrollHeight')});
    var currentUser = $('.usr-id').data('uid');
    var receiveUser = $('.usr-id').data('receiver-id');
    var token = $('.usr-id').data('token');
    var privateNotifications = 'private-notifications-'+currentUser;
    /*create pusher instance, open client socket, define auth-endpoint*/
    var pusher = new Pusher('82800fdb37dfd38f4722', {
        cluster: 'eu',
        auth: {
            params: {
                'initiated_by':currentUser,
                'chat_with':receiveUser
            },
            headers: {
                'X-CSRF-Token': token
            }
        },
        authTransport: 'ajax',
        authEndpoint: 'http://localhost/project-418/web/app_dev.php/pusher/auth'
    });
    /*sub to private notifications*/
    var notifications = pusher.subscribe(privateNotifications);

    /*notice success of -sub to private notifications*/
    notifications.bind('pusher:subscription_succeeded',function(data){
        /*$('.ovde').append("<p>successfully subscribed to "+privateNotifications+"</p>");*/
       /* $('.notifikacija-broj').append("<p>successfully subscribed to "+privateNotifications+"</p>");*/
    });


    /*inbox receives new message*/
    notifications.bind('on_message',function (data) {
        /*ako si u current-chatu stavi da je trenutno pristigla poruka procitana, i nemoj da povecavas broj*/
        /*neka ne bude procitana u bazi,jer ce sledecim ulaskom biti procitana,dakle samo na izlazu iz chata da stavi da su sve procitane*/
        /*ali nemoj da povecavas broj neprocitanih*/
        var broj = $('.message-alert').text();
        broj = parseInt(broj);
        broj++;
        $('.message-alert').text(broj);
        /*$('.notifikacija-broj').append("<p>new msg from: "+data.sent_by+data.presence_chat_name+" </p>");*/


        var br = $('#br-'+data.presence_chat_name).text();
        br = parseInt(br);
        br++;
        $('#br-'+data.presence_chat_name).text(br);
        if(br>0){
            $('#br-'+data.presence_chat_name).css("display","inline-block");
        }
        /*ako si u chat-presence-bla-bla prikazi broj trenutno pristiglih poruka u ostalim chatovima*/
        if(data.presence_chat_name!==chat_name_current){

        }
    });


    notifications.bind('one-to-one-chat-request',function(data){
        $('.start_private_chat').attr("disabled", true);
        /*$('.ovde').append("<p>successfully received one-to-one-chat-request, for chat:  "+data.channel_name_4+"</p>");*/
    });

    /*when a user receives a friend request*/
    notifications.bind('on_received_friend_request',function (data) {
        var broj = $('.friend_requests').text();
        broj = parseInt(broj);
        broj++;
        $('.friend_requests').text(broj);
        $('.ul-friend-requests').css("display","block");
        $('.ul-friend-requests').append("<li class='collection-item avatar'>" +
            "<img src='http://localhost/project-418/web/images/profile/"+data.profile_image+"' alt='' class='circle'>"+
            "<span class='title'>"+data.first_name+" "+data.last_name +"</span>"+
            "<p><span class='accept-friend acc_fr' data-answer='accept' id='"+data.sent_by+"'>accept</span>" +
            " <span class='accept-friend dec_fr' data-answer='decline' id='"+data.sent_by +"'>decline</span>" +
            "</p></li>")
        /*$('.notifikacija-broj').append("<p>friend requst from: "+data.first_name+" </p>");*/
    });

    /*when someone accepts my friendship request notify me*/
    notifications.bind('accepted_friendship',function (data) {
        Materialize.toast('accepted friendship : '+data.first_name + " "+data.last_name, 4000);
    });



    $(document).on("click","#add-friend",function(event){
        /*$('.add-friend-clicked-on-profile').text('pending friendship response');*/
        var eventData = {
            'sent_by': $('#add-friend').data('sent_by'),
            'received_by'   : $('#add-friend').data('received_by'),
            'is_friend_request' : 'yes'
        };
        $.ajax({
            url: 'http://localhost/project-418/web/app_dev.php/friend/add',
            type: 'POST',
            data: eventData,
            success:function(data,status){
            },error:function(){

            }
        });
    });


    /*<ul><li>chat-name-1-2 </li> <li> chat-name-1-5 </li> .... </ul>  OVO TREBA DA SE URADI DA BI RADIO SVUDA INBOX */



    var nizRazgovora = $('.chats-array').data('all-chats-array');
    nizRazgovora = nizRazgovora.split(",");
    if(nizRazgovora.length>0){
        for(var i=0;i<nizRazgovora.length-1;i++){
            /*alert('aaaa');*/
            var ime = nizRazgovora[i];

            razgovori = pusher.subscribe(nizRazgovora[i]);
            razgovori.bind('pusher:subscription_succeeded',function (data) {
                /*alert('ok,chat: '+ime);*/
            });
        }
    }


    notifications.bind('on_new_recommendation',function(data){
        var broj = $('.notifications_count').text();
        broj = parseInt(broj);
        broj++;
        $('.notifications_count').text(broj);
        $('.ul-unread-notifications').css("display","block");
        $('.ul-unread-notifications').append("<li class=\"collection-item avatar\"><a href='http://localhost/project-418/web/app_dev.php/movies/"+data.movie+"'>\n" +
            "                                                    <img src='http://localhost/project-418/web/images/profile/"+data.profile_image+"' alt=\"\" class=\"circle\">\n" +
            "                                                    <span class=\"title\">"+ data.first_name +" "+  data.last_name +"<span class=\"notification-text\"> has recommended you a movie "+data.title+"</span></span>\n" +
            "                                                </a>\n" +
            "                                                <span href=\"#!\" class=\"secondary-content dismiss-notif\">x</span>\n" +
            "                                            </li>");
    });

    notifications.bind('on_new_review',function(data){
        var broj = $('.notifications_count').text();
        broj = parseInt(broj);
        broj++;
        $('.notifications_count').text(broj);
        $('.ul-unread-notifications').css("display","block");
        $('.ul-unread-notifications').append("<li class=\"collection-item avatar\"><a href='http://localhost/project-418/web/app_dev.php/movies/"+data.movie +"'>\n" +
            "                                                    <img src='http://localhost/project-418/web/images/profile/"+data.profile_image+"' alt=\"\" class=\"circle\">\n" +
            "                                                    <span class=\"title\">"+ data.first_name +" "+  data.last_name +"<span class=\"notification-text\"> also reviewed "+data.title+"</span></span>\n" +
            "                                                </a>\n" +
            "                                                <span href=\"#!\" class=\"secondary-content dismiss-notif\">x</span>\n" +
            "                                            </li>");
    });

    notifications.bind('on_new_like',function(data){
        var broj = $('.notifications_count').text();
        broj = parseInt(broj);
        broj++;
        $('.notifications_count').text(broj);
        $('.ul-unread-notifications').css("display","block");

        $('.ul-unread-notifications').append("<li class=\"collection-item avatar\"><a href='http://localhost/project-418/web/app_dev.php/movies/"+data.movie+"'>\n" +
            "                                                <img src='http://localhost/project-418/web/images/profile/"+data.profile_image+"' alt=\"\" class=\"circle\">\n" +
            "                                                <span class=\"title\">"+ data.first_name +" "+data.last_name +"<span class=\"notification-text\"> liked a review you left on "+data.title+"</span></span>\n" +
            "                                                </a>\n" +
            "                                                <span href=\"#!\" class=\"secondary-content dismiss-notif\">x</span>\n" +
            "                                            </li>");

    });
/*ako si u chatu ili messengeru subscribe-uj se na chatove*/
    /*if($('.main').attr('id')==='messenger-main'){*/
            /*dont put anything below,below will not work on any other page except /chat/presence-chat-userUno-userDos */
            presence_chats = pusher.subscribe($('.chatName').data('chat_name'));
            final_private_chat_name_from_server=$('.chatName').data('chat_name'); /*dont touch,if receiver,set value here*/
            presence_chats.bind('pusher:subscription_succeeded',function(data){
                /*$('.ovde').append("<p>successfully subscribed "+currentUser+" to "+$('.chatName').data('chat_name') +"</p>");*/
            });
            presence_chats.bind('on_message',function(data){
                /*alert(data.initiated_by+": "+data.message);*/
                if(data.sent_by==currentUser){
                    $('.ovde').append("<p class='sent'><span class='sent-span'>"+data.message +"</span></p>");
                    $('.sent').css("color","white");
                }else{
                    $('.ovde').append("<p class='received'><span class='received-span'>"+data.message +"</span></p>");
                }
                $('.ovde').animate({scrollTop: $('.ovde').prop('scrollHeight')});

                if($('.ovde').is(':focus')===true || $('.msg-content').is(':focus')===true){
                    $('.msg-content').click();
                }


            });

            /*ako sam ja izasao iz trenutnog chata,stavi da su sve neprocitane poruke procitane*/
            presence_chats.bind('pusher:member_removed', function(member) {
                /*!// for example:
                remove_member(member.id, member.info);*/
                if(member.id===currentUser){
                    var eventData = {
                        'read_by': $('.usr-id').data('uid'),
                        'presence_chat_name': $('.chatName').data('chat_name')
                    };
                    $.ajax({
                        url: 'http://localhost/project-418/web/app_dev.php/chat-api/message',
                        type: 'POST',
                        data: eventData,
                        success: function (data, status) {
                        }, error: function () {

                        }
                    });
                }
            });
    /*}*/
    /* end if ako si u chatu ili mesindzeru */
/*SET MESSAGES AS SEEN AS READ HISTORY*/
    $(document).on("click",".ovde",function(event){
        $('.msg-content').click();
    });

/*SET MESSAGES AS SEEN AS READ HISTORY*/
    $(document).on("click",".msg-content",function(event){
        var eventData = {
            'read_by': $('.usr-id').data('uid'),
            'presence_chat_name': $('.chatName').data('chat_name')
        };
        $.ajax({
            url: 'http://localhost/project-418/web/app_dev.php/chat-api/message',
            type: 'POST',
            data: eventData,
            success: function (data, status) {
                /*alert(data.number_of_just_read);*/
                var broj = $('.message-alert').text();
                broj = parseInt(broj);
                broj = broj - data.number_of_just_read;
                $('.message-alert').text(broj);

                var br = $('#br-'+data.presence_chat_name).text();
                br = parseInt(br);
                br = br - data.number_of_just_read;
                $('#br-'+data.presence_chat_name).text(br);
                if(br===0){
                    $('#br-'+data.presence_chat_name).css("display","none");
                }


            }, error: function () {
            }
        });
    });


/*SEND MESSAGE*/
    $(".msg-content").keypress(function (event) {
        if (event.which == 13) {
            var msg = $('.msg-content').val();
            $('.msg-content').val('');
            var currentUser = $('#usr-id-with-in-chat-page').data('uid');
            var receiveUser = $('#usr-id-with-in-chat-page').data('receiver-id');
            var eventData = {
                'sent_by':currentUser,
                'received_by': receiveUser,
                'message': msg,
                'presence_chat_name': $('.chatName').data('chat_name')
            };
            $.ajax({
                url: 'http://localhost/project-418/web/app_dev.php/chat-api/message',
                type: 'POST',
                data: eventData,
                success: function (data, status) {
                }, error: function () {

                }
            });
        }
    });



    $(document).on("click",".start_private_chat",function(event){
        var rec = $(event.target).attr('id');
        var eventData = {
            'initiated_by': currentUser,
            'chat_with'   : rec
        };
        $.ajax({
            url: 'http://localhost/project-418/web/app_dev.php/chat-api/message',
            type: 'POST',
            data: eventData,
            success:function(data,status){
                /*$('.ovde').append("<p> the chat will be in adress <a href='../chat/"+data['channel_name_4']+"'> chat/"+data['channel_name_4'] +"</a></p>");*/
                /*final_private_chat_name_from_server=data['channel_name_4']; /!*if first sender set value here*!/*/
                window.location.replace('http://localhost/project-418/web/app_dev.php/chat/'+data['channel_name_4']); /*redirect to chat url*/
            },error:function(){
            }
        });
    });



});