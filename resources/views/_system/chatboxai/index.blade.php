@extends('_layout._layadmin.app')

@section('title', 'Quản lý câu hỏi Chatbot')

@section('chatbotai')

<h2>Admin - Quản lý chat</h2>
  <div id="users"></div>
  <div id="chatpanel" style="display:none">
    <div id="chatbox"></div>
    <form id="sendMsg">
      <input type="text" id="msgInput" required autocomplete="off" placeholder="Nhập tin nhắn..." />
      <button type="submit">Trả lời</button>
    </form>
    <button id="autoReply">Trả lời AI</button>
  </div>

@endsection

<script>
     const firebaseConfig = {
        apiKey: "AIzaSyDqqenpK-EGlKcvjgl-kBHyQ9V8BSSTEkg",
        authDomain: "bds-chat-8ea88.firebaseapp.com",
        databaseURL: "https://bds-chat-8ea88-default-rtdb.asia-southeast1.firebasedatabase.app",
        projectId: "bds-chat-8ea88",
        storageBucket: "bds-chat-8ea88.firebasestorage.app",
        messagingSenderId: "468265302327",
        appId: "1:468265302327:web:1c610887df3380953a02b2",
        measurementId: "G-J0MTJ840VS"
     };
    firebase.initializeApp(firebaseConfig);
    const db = firebase.database();

    // 1. Lấy danh sách user đang chat
    db.ref('chats').once('value', function(snapshot) {
      const usersDiv = document.getElementById('users');
      const users = snapshot.val();
      for (let user in users) {
        const btn = document.createElement('button');
        btn.innerText = 'User ' + user;
        btn.onclick = () => selectUser(user);
        usersDiv.appendChild(btn);
        usersDiv.appendChild(document.createElement('br'));
      }
    });

    let currentUser = null;
    let chatRef = null;

    // 2. Hiển thị chat khi chọn user
    function selectUser(user_id) {
      currentUser = user_id;
      document.getElementById('chatpanel').style.display = 'block';
      document.getElementById('chatbox').innerHTML = '';
      if (chatRef) chatRef.off(); // Remove old listener
      chatRef = db.ref('chats/' + user_id + '/messages');
      chatRef.on('child_added', function(snapshot) {
        const msg = snapshot.val();
        const chatbox = document.getElementById('chatbox');
        const cls = msg.sender_id === 'admin' ? 'admin' : 'user';
        chatbox.innerHTML += `<div class="msg ${cls}"><b>${msg.sender_id}:</b> ${msg.content}</div>`;
        chatbox.scrollTop = chatbox.scrollHeight;
      });
    }

    // 3. Gửi tin nhắn với vai trò admin
    document.getElementById('sendMsg').onsubmit = function(e) {
      e.preventDefault();
      if (!currentUser) return;
      const content = document.getElementById('msgInput').value;
      fetch('/api/chat/send', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          conversation_id: currentUser,
          sender_id: 'admin',
          content: content
        })
      }).then(() => {
        document.getElementById('msgInput').value = '';
      });
    };

    // 4. Trả lời tự động bằng AI (gợi ý)
    document.getElementById('autoReply').onclick = function() {
      // Giả sử bạn có route gọi AI như /api/chat/admin-reply
      fetch('/api/chat/admin-reply', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
          conversation_id: currentUser
        })
      }).then(res => res.json()).then(data => {
        // Nếu muốn, có thể update giao diện...
        alert('Đã trả lời AI!');
      });
    };

</script>
