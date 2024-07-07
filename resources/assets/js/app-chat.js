/**
 * App Chat
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  (function () {
    const chatContactsBody = document.querySelector('.app-chat-contacts .sidebar-body'),
      chatContactListItems = [].slice.call(
        document.querySelectorAll('.chat-contact-list-item:not(.chat-contact-list-item-title)')
      ),
      chatHistoryBody = document.querySelector('.chat-history-body'),
      chatSidebarLeftBody = document.querySelector('.app-chat-sidebar-left .sidebar-body'),
      chatSidebarRightBody = document.querySelector('.app-chat-sidebar-right .sidebar-body'),
      chatUserStatus = [].slice.call(document.querySelectorAll(".form-check-input[name='chat-user-status']")),
      chatSidebarLeftUserAbout = $('.chat-sidebar-left-user-about'),
      formSendMessage = document.querySelector('.form-send-message'),
      messageInput = document.querySelector('.message-input'),
      searchInput = document.querySelector('.chat-search-input'),
      speechToText = $('.speech-to-text'), // ! jQuery dependency for speech to text
      userStatusObj = {
        active: 'avatar-online',
        offline: 'avatar-offline',
        away: 'avatar-away',
        busy: 'avatar-busy'
      };

    // Initialize PerfectScrollbar
    // ------------------------------

    // Chat contacts scrollbar
    if (chatContactsBody) {
      new PerfectScrollbar(chatContactsBody, {
        wheelPropagation: false,
        suppressScrollX: true
      });
    }

    // Chat history scrollbar
    if (chatHistoryBody) {
      new PerfectScrollbar(chatHistoryBody, {
        wheelPropagation: false,
        suppressScrollX: true
      });
    }

    // Sidebar left scrollbar
    if (chatSidebarLeftBody) {
      new PerfectScrollbar(chatSidebarLeftBody, {
        wheelPropagation: false,
        suppressScrollX: true
      });
    }

    // Sidebar right scrollbar
    if (chatSidebarRightBody) {
      new PerfectScrollbar(chatSidebarRightBody, {
        wheelPropagation: false,
        suppressScrollX: true
      });
    }

    // Scroll to bottom function
    function scrollToBottom() {
      chatHistoryBody.scrollTo(0, chatHistoryBody.scrollHeight);
    }
    scrollToBottom();

    // User About Maxlength Init
    if (chatSidebarLeftUserAbout.length) {
      chatSidebarLeftUserAbout.maxlength({
        alwaysShow: true,
        warningClass: 'label label-success bg-success text-white',
        limitReachedClass: 'label label-danger',
        separator: '/',
        validate: true,
        threshold: 120
      });
    }

    // Update user status
    chatUserStatus.forEach(el => {
      el.addEventListener('click', e => {
        let chatLeftSidebarUserAvatar = document.querySelector('.chat-sidebar-left-user .avatar'),
          value = e.currentTarget.value;
        //Update status in left sidebar user avatar
        chatLeftSidebarUserAvatar.removeAttribute('class');
        Helpers._addClass('avatar avatar-xl ' + userStatusObj[value] + '', chatLeftSidebarUserAvatar);
        //Update status in contacts sidebar user avatar
        let chatContactsUserAvatar = document.querySelector('.app-chat-contacts .avatar');
        chatContactsUserAvatar.removeAttribute('class');
        Helpers._addClass('flex-shrink-0 avatar ' + userStatusObj[value] + ' me-3', chatContactsUserAvatar);
      });
    });
    // Fetch initial chat list
    fetchChatList();

    // Fetch and display chat list
function fetchChatList() {
  fetch('/api/messenger/threads')
      .then(response => response.json())
      .then(responseData => {
          let data = responseData.data; // Assuming data is an array

          let chatList = document.getElementById('chat-list');
          // chatList.innerHTML = ''; // Clear existing chats

          if (Array.isArray(data) && data.length > 0) {
              data.forEach(chat => {

                  let chatItem = document.createElement('li');
                  let avatarImg;

                    if (chat.resources.recipient.base.picture === null ) {
                                               // Generate initials and random background color
                        var stateNum = Math.floor(Math.random() * 6);
                        var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
                        var $state = states[stateNum];
                        var $name = chat.name; // Use chat name for initials
                        var $initials = $name.match(/\b\w/g) || [];
                        $initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
                        var $output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';
                        avatarImg = $output;
                    } else {
                        avatarImg = `<img src="${chat.avatar.sm}" alt="Avatar" class="rounded-circle">`;
                    }
                  chatItem.classList.add('chat-contact-list-item');
                  chatItem.innerHTML = `
                      <a class="d-flex align-items-center">
                          <div class="flex-shrink-0 avatar ">
                          ${avatarImg}
                          </div>
                          <div class="chat-contact-info flex-grow-1 ms-2">
                              <h6 class="chat-contact-name text-truncate m-0">${chat.name}</h6>
                              <p class="chat-contact-status text-muted text-truncate mb-0">${chat.resources.latest_message.body}</p>
                          </div>
                          <small class="text-muted mb-auto">${getTimePassed(chat.resources.latest_message.updated_at)}</small>
                      </a>
                  `;
                  chatList.appendChild(chatItem);
              });
              // Add event listeners to the new chat items
                addChatItemEventListeners();
          } else {
              // No chats found, display a message
              chatList.innerHTML = `
                  <li class="chat-contact-list-item chat-list-item-0">
                      <h6 class="text-muted mb-0">No Chats Found</h6>
                  </li>
              `;
          }
      })
      .catch(error => {
          console.error('Error fetching chat list:', error);
      });
}
// Helper function to get time passed since the given timestamp
function getTimePassed(timestamp) {
  const now = new Date();
  const updatedAt = new Date(timestamp);
  const diffInMs = now - updatedAt;

  const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
  const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60));
  const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));

  if (diffInMinutes < 1) {
      return 'Just now';
  } else if (diffInMinutes < 60) {
      return `${diffInMinutes} minute${diffInMinutes > 1 ? 's' : ''} ago`;
  } else if (diffInHours < 24) {
      return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
  } else {
      return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`;
  }
}

// Add event listeners to chat items
function addChatItemEventListeners() {
  const chatContactListItems = document.querySelectorAll('.chat-contact-list-item:not(.chat-contact-list-item-title)');
  chatContactListItems.forEach(chatContactListItem => {
      chatContactListItem.addEventListener('click', e => {
          chatContactListItems.forEach(item => {
              item.classList.remove('active');
          });
          e.currentTarget.classList.add('active');
      });
  });
}


    // Filter Chats
    if (searchInput) {
      searchInput.addEventListener('keyup', e => {
        let searchValue = e.currentTarget.value.toLowerCase(),
          searchChatListItemsCount = 0,
          searchContactListItemsCount = 0,
          chatListItem0 = document.querySelector('.chat-list-item-0'),
          contactListItem0 = document.querySelector('.contact-list-item-0'),
          searchChatListItems = [].slice.call(
            document.querySelectorAll('#chat-list li:not(.chat-contact-list-item-title)')
          ),
          searchContactListItems = [].slice.call(
            document.querySelectorAll('#contact-list li:not(.chat-contact-list-item-title)')
          );

        // Search in chats
        searchChatContacts(searchChatListItems, searchChatListItemsCount, searchValue, chatListItem0);
        // Search in contacts
        searchChatContacts(searchContactListItems, searchContactListItemsCount, searchValue, contactListItem0);
      });
    }

    // Search chat and contacts function
    function searchChatContacts(searchListItems, searchListItemsCount, searchValue, listItem0) {
      searchListItems.forEach(searchListItem => {
        let searchListItemText = searchListItem.textContent.toLowerCase();
        if (searchValue) {
          if (-1 < searchListItemText.indexOf(searchValue)) {
            searchListItem.classList.add('d-flex');
            searchListItem.classList.remove('d-none');
            searchListItemsCount++;
          } else {
            searchListItem.classList.add('d-none');
          }
        } else {
          searchListItem.classList.add('d-flex');
          searchListItem.classList.remove('d-none');
          searchListItemsCount++;
        }
      });
      // Display no search fount if searchListItemsCount == 0
      if (searchListItemsCount == 0) {
        listItem0.classList.remove('d-none');
      } else {
        listItem0.classList.add('d-none');
      }
    }

    // Send Message
    formSendMessage.addEventListener('submit', e => {
       e.preventDefault();
      // if (messageInput.value) {
      //   // Create a div and add a class
      //   let renderMsg = document.createElement('div');
      //   renderMsg.className = 'chat-message-text mt-2';
      //   renderMsg.innerHTML = '<p class="mb-0 text-break">' + messageInput.value + '</p>';
      //   document.querySelector('li:last-child .chat-message-wrapper').appendChild(renderMsg);
      //   messageInput.value = '';
      //   scrollToBottom();
      // }
      // Send knock
    sendKnock('9c1b9a73-8a17-4a50-8baa-3f46a7349ce7'); // Replace threadId with your actual thread ID
    });
    // Knock Action
    function sendKnock(threadId) {
      $.ajax({
        url: `/api/messenger/threads/9c1b9a73-8a17-4a50-8baa-3f46a7349ce7/knock-knock`,
        type: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: JSON.stringify({
          thread_id: threadId,
        }),
        success: function(data) {
          console.log('Knock sent successfully', data);

        },
        error: function(xhr, status, error) {
          console.error('Error sending knock:', error);
        }
      });
    }


// Function to play notification sound
function playNotificationSound() {
  let audio = new Audio(`${baseUrl}assets/audio/knok.mp3`);
  audio.play();
}
    // on click of chatHistoryHeaderMenu, Remove data-overlay attribute from chatSidebarLeftClose to resolve overlay overlapping issue for two sidebar
    let chatHistoryHeaderMenu = document.querySelector(".chat-history-header [data-target='#app-chat-contacts']"),
      chatSidebarLeftClose = document.querySelector('.app-chat-sidebar-left .close-sidebar');
    chatHistoryHeaderMenu.addEventListener('click', e => {
      chatSidebarLeftClose.removeAttribute('data-overlay');
    });
    // }

    // Speech To Text
    if (speechToText.length) {
      var SpeechRecognition = SpeechRecognition || webkitSpeechRecognition;
      if (SpeechRecognition !== undefined && SpeechRecognition !== null) {
        var recognition = new SpeechRecognition(),
          listening = false;
        speechToText.on('click', function () {
          const $this = $(this);
          recognition.onspeechstart = function () {
            listening = true;
          };
          if (listening === false) {
            recognition.start();
          }
          recognition.onerror = function (event) {
            listening = false;
          };
          recognition.onresult = function (event) {
            $this.closest('.form-send-message').find('.message-input').val(event.results[0][0].transcript);
          };
          recognition.onspeechend = function (event) {
            listening = false;
            recognition.stop();
          };
        });
      }
    }
    //ECHO LISTNER
    // Access userData passed from Blade template
    let userId = window.userData.id;
    Echo.private(`messenger.user.${userId}`)
      .listen('.new.message', (e) => console.log(e))
      .listen('.thread.archived', (e) => console.log(e))
      .listen('.message.archived', (e) => console.log(e))
      .listen('.knock.knock', (e) =>  {
        console.log(e);
        // Play sound notification
        playNotificationSound();
    });
  })();

});
