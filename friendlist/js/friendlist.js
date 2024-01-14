let path = "./friendlist/";

$(document).ready(function() {
    // Set default tab to 'Friends' when the document is ready
    openTab('friends');
    
    // Toggle friend list and arrow icon
    $('#toggleFriendList').click(function() {
        // Toggle arrow immediately on click
        var arrow = document.getElementById('fl-arrow');
        arrow.classList.toggle('rotated'); // Toggle the 'rotated' class on click

        // Toggle friend list content
        $('#friendListContent').slideToggle();
    });

    // Keyup event for search
    $('#friendSearch').keyup(function() {
        loadSearchResults();
    });

    // Function to add a friend
    window.addFriend = function(friendId) {
        $.ajax({
            url: path + 'sendFriendRequest.php',
            type: 'POST',
            data: { friendId: friendId },
            success: function(response) {
                var responseData = JSON.parse(response);
                if (responseData.status === "success") {
                    $('#addFriendBtn' + friendId).replaceWith('<span>Pending</span>');
                } else {
                    alert(responseData.message);
                }
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    };
});

// Function for opening the different tabs
function openTab(tabName) {
    // Hide all tab content
    $('.tab-content').hide();
    // Show the selected tab content
    $('#' + tabName).show();

    // Remove 'active' class from all tab buttons
    $('.tab-btn').removeClass('active');
    // Add 'active' class to the clicked tab button
    $('.tab-btn').each(function() {
        if ($(this).attr("onclick").includes(tabName)) {
            $(this).addClass('active');
        }
    });

    // Update the title of the toggle button based on the active tab and change the tab
    switch(tabName) {
        case 'friends':
            $('#toggleFriendList .toggle-text').text('Friends');
            loadFriends();
            break;
        case 'incoming':
            $('#toggleFriendList .toggle-text').text('Friend Requests');
            loadIncomingRequests();
            break;
        case 'pending':
            $('#toggleFriendList .toggle-text').text('Pending Requests');
            loadPendingRequests();
            break;
        case 'search':
            $('#toggleFriendList .toggle-text').text('Add Friends');
            // No additional function needs to be called for the search tab
            break;
    }
}

// Load friends
function loadFriends() {
    $.ajax({
        url: path + 'fetchFriends.php',
        type: 'POST',
        success: function(response) {
            var friends = JSON.parse(response);
            if (friends.length === 0) {
                $('#friends').html('<div class="no-items-message">No friends yet.</div>');
            } else {
                var html = "";
                friends.forEach(function(friend) {
                    html += "<div class='friend'>" +
                            friend.username +
                            " <button class='friendlist-content-btn' onclick='unfriend(" + friend.friendId + ")'><img class='result-icon' src='" + path + "/img/delete-user.png'></button>" +
                            "</div>";
                });
                $('#friends').html(html);
            }
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}

// Function to load search results
function loadSearchResults() {
    var searchTerm = $('#friendSearch').val();
    if (searchTerm.length > 2) {
        $.ajax({
            url: path + 'searchUsers.php',
            type: 'POST',
            data: { searchTerm: searchTerm },
            success: function(response) {
                var users = JSON.parse(response);
                var html = "";
                users.forEach(function(user) {
                    if (user.relationshipStatus === 'friend') {
                        html += "<div class='friend'>" + user.username + " <button class='friendlist-content-btn' onclick='unfriend(" + user.id + ")'><img class='result-icon' src='" + path + "/img/delete-user.png'></button></div>";
                    } else if (user.relationshipStatus === 'pending') {
                        html += "<div class='friend'>" + user.username + " <span>Pending</span></div>";
                    } else {
                        html += "<div class='friend'>" + user.username + " <button class='friendlist-content-btn' id='addFriendBtn" + user.id + "' onclick='addFriend(" + user.id + ")'>Add</button></div>";
                    }
                });
                $('#friendSearchResults').html(html);
            },
            error: function(xhr, status, error) {
                console.log("Error: " + error);
            }
        });
    }
}

// Display incoming friend requests
function loadIncomingRequests() {
    $.ajax({
        url: path + 'fetchIncomingRequests.php',
        type: 'POST',
        success: function(response) {
            var requests = JSON.parse(response);
            if (requests.length === 0) {
                $('#incoming').html('<div class="no-items-message">No incoming requests.</div>');
            } else {
                var html = "";
                requests.forEach(function(request) {
                    html += "<div class='request'>" +
                            "From: " + request.username +
                            " <button class='friendlist-content-btn' onclick='acceptRequest(" + request.requestId + ")'>Accept</button>" +
                            " <button class='friendlist-content-btn' onclick='rejectRequest(" + request.requestId + ")'>Reject</button>" +
                            "</div>";
                });
                $('#incoming').html(html);
            }
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}

// Gather outgoing pending friend requests
function loadPendingRequests() {
    $.ajax({
        url: path + 'fetchPendingRequests.php',
        type: 'POST',
        success: function(response) {
            var requests = JSON.parse(response);
            if (requests.length === 0) {
                $('#pending').html('<div class="no-items-message">No pending requests.</div>');
            } else {
                var html = "";
                requests.forEach(function(request) {
                    html += "<div class='request'>" +
                            "To: " + request.username +
                            " <button class='friendlist-content-btn' onclick='cancelRequest(" + request.requestId + ")'><img class='result-icon' src='" + path + "/img/cross-circle.png'></button>" +
                            "</div>";
                });
                $('#pending').html(html);
            }
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}

// Cancel friend request
function cancelRequest(requestId) {
    $.ajax({
        url: path + 'cancelRequest.php',
        type: 'POST',
        data: { requestId: requestId },
        success: function(response) {
            // Reload pending requests to update the list
            loadPendingRequests();
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}

// Remove friend
function unfriend(friendId) {
    $.ajax({
        url: path + 'unfriend.php',
        type: 'POST',
        data: { friendId: friendId },
        success: function(response) {
            // Reload friends list to update the UI
            loadFriends();
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}

// Accept friend request
function acceptRequest(requestId) {
    $.ajax({
        url: path + 'acceptRequest.php',
        type: 'POST',
        data: { requestId: requestId },
        success: function(response) {
            // Reload incoming requests to update the list
            loadIncomingRequests();
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}

// Reject friend request
function rejectRequest(requestId) {
    $.ajax({
        url: path + 'rejectRequest.php',
        type: 'POST',
        data: { requestId: requestId },
        success: function(response) {
            // Reload incoming requests to update the list
            loadIncomingRequests();
        },
        error: function(xhr, status, error) {
            console.log("Error: " + error);
        }
    });
}