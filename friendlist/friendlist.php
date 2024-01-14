<div id="friendListContainer">
    <button id="toggleFriendList">
        <span class="toggle-text">Friends</span>
        <svg id="fl-arrow" class="arrow" xmlns="http://www.w3.org/2000/svg" viewBox="3 3 18 18" align="center" width="30" height="30" fill-rule="evenodd" clip-rule="evenodd"><path d="M16 15a1 1 0 0 1-.707-.293L12 11.414l-3.293 3.293a1 1 0 1 1-1.414-1.414l4-4a1 1 0 0 1 1.414 0l4 4A1 1 0 0 1 16 15z" style="fill:#ff8e31;"></path></svg>
    </button>
    <div id="friendListContent" style="display: none;">
        <div id="friendListTabs">
            <button class="tab-btn active" onclick="openTab('friends')"><img src="./friendlist/img/friends.png" alt="add-friend-icon"></button>
            <button class="tab-btn" onclick="openTab('incoming')"><img src="./friendlist/img/notification.png" alt="friend-requests-icon"></button>
            <button class="tab-btn" onclick="openTab('pending')"><img src="./friendlist/img/envelope.png" alt="pending-friend-requests-icon"></button>
            <button class="tab-btn" onclick="openTab('search')"><img src="./friendlist/img/friend-add.png" alt="search-icon"></button>
        </div>
        <div class="tab-content" id="friends" style="display:none;">
            <!-- Friends will be listed here -->
        </div>
        <div class="tab-content" id="incoming" style="display:none;">
            <!-- Incoming requests will be loaded here -->
        </div>
        <div class="tab-content" id="pending">
            <!-- Pending requests will be loaded here -->
        </div>
        <div class="tab-content" id="search" style="display:none;">
            <input type="text" id="friendSearch" placeholder="Search friends...">
            <div id="friendSearchResults"></div>
        </div>
    </div>
</div>
<script src="./friendlist/js/friendlist.js"></script>