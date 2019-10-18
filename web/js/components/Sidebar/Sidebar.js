import { VisibilitySwitcher } from "./VisibilitySwitcher.js";

const showFriendRequestsEl = document.getElementById('showhide-friend-requests');
const expandFriendRequestsEl = document.getElementById('expand-friend-requests');
const friendRequestsCountEl = document.getElementById('friend_requests');
const friendRequestsVisibilitySwitcher = new VisibilitySwitcher(
    showFriendRequestsEl, expandFriendRequestsEl, friendRequestsCountEl
);

const showUnreadNotificationsEl = document.getElementById('showhide-unread-notifications');
const expandUnreadNotificationsEl = document.getElementById('expand-unread-notifications');
const notificationsCountEl = document.getElementById('notifications_count');
const notificationsVisibilitySwitcher = new VisibilitySwitcher(
    showUnreadNotificationsEl, expandUnreadNotificationsEl, notificationsCountEl
);

const sidebarEl = document.getElementById('showhide-sidebar');
sidebarEl.addEventListener('click', () => {
    const sidebarWrapperEl = document.getElementById('sidebar-wrapper');
    const mainEl = document.getElementsByClassName('main')[0];
    const searchHeaderEl = document.getElementsByClassName('search-header')[0];
    const nnElement = document.getElementsByClassName('ovde')[0];
    if (sidebarWrapperEl.style.display === 'block') {
        sidebarWrapperEl.style.display = 'none';
        mainEl.style.marginLeft = '0px';
        searchHeaderEl.style.marginLeft = '0px';
        if (mainEl.id === 'messenger-main'){
            nnElement.style.width = '70%';
        }
    } else {
        sidebarWrapperEl.style.display = 'block';
        mainEl.style.marginLeft = '15%';
        searchHeaderEl.style.marginLeft = '15%';

        if(mainEl.id === 'messenger-main'){
            nnElement.style.width = '60%';
        }
    }
});



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
