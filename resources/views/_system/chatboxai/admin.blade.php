@extends('_layout._layadmin.app')

@section('title', 'Quản lý Chatbot - Admin')

@section('chatbotai')

<div class="admin-chat-container">
  <!-- Header -->
  <div class="page-header">
    <div class="header-content">
      <div class="header-info">
        <h1><i class="fas fa-robot"></i> Quản lý Chatbot AI</h1>
        <p>Hỗ trợ khách hàng và quản lý hệ thống chatbot</p>
      </div>
      <div class="header-actions">
        <a href="{{ route('admin.chatbot.demo') }}" class="btn btn-outline-primary">
          <i class="fas fa-eye"></i> Demo
        </a>
        <a href="{{ route('admin.chatbot.faq.index') }}" class="btn btn-primary">
          <i class="fas fa-question-circle"></i> Quản lý FAQ
        </a>
      </div>
    </div>
  </div>

  <!-- Stats Cards -->
  <div class="stats-row">
    <div class="stat-card pending">
      <div class="stat-icon">
        <i class="fas fa-clock"></i>
      </div>
      <div class="stat-info">
        <h3 id="pendingCount">0</h3>
        <p>Cần hỗ trợ</p>
      </div>
    </div>
    <div class="stat-card total">
      <div class="stat-icon">
        <i class="fas fa-comments"></i>
      </div>
      <div class="stat-info">
        <h3 id="totalCount">0</h3>
        <p>Tổng cuộc hội thoại</p>
      </div>
    </div>
    <div class="stat-card online">
      <div class="stat-icon">
        <i class="fas fa-users"></i>
      </div>
      <div class="stat-info">
        <h3 id="onlineCount">0</h3>
        <p>Người dùng online</p>
      </div>
    </div>
    <div class="stat-card ai">
      <div class="stat-icon">
        <i class="fas fa-brain"></i>
      </div>
      <div class="stat-info">
        <h3 id="aiCount">0</h3>
        <p>Trả lời AI</p>
      </div>
    </div>
  </div>

  <!-- Navigation Tabs -->
  <div class="nav-tabs-container">
    <nav class="nav-tabs">
      <button class="nav-tab active" data-tab="pending">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Cần hỗ trợ</span>
        <span class="badge" id="pendingBadge">0</span>
      </button>
      <button class="nav-tab" data-tab="all">
        <i class="fas fa-list"></i>
        <span>Tất cả</span>
      </button>
      <button class="nav-tab" data-tab="messaging">
        <i class="fas fa-paper-plane"></i>
        <span>Gửi tin nhắn</span>
      </button>
      <button class="nav-tab" data-tab="settings">
        <i class="fas fa-cog"></i>
        <span>Cài đặt</span>
      </button>
    </nav>
  </div>

  <!-- Tab Content -->
  <div class="tab-content-container">

    <!-- Pending Conversations Tab -->
    <div id="pendingTab" class="tab-content active">
      <div class="content-header">
        <h3><i class="fas fa-exclamation-triangle text-warning"></i> Conversations cần hỗ trợ</h3>
        <button class="btn btn-refresh" onclick="loadPendingConversations()">
          <i class="fas fa-sync-alt"></i> Làm mới
        </button>
      </div>
      <div id="pendingConversations" class="conversations-grid">
        <div class="loading-state">
          <div class="spinner"></div>
          <p>Đang tải conversations...</p>
        </div>
      </div>
    </div>

    <!-- All Conversations Tab -->
    <div id="allTab" class="tab-content">
      <div class="content-header">
        <h3><i class="fas fa-list text-info"></i> Tất cả conversations</h3>
        <div class="search-filter">
          <input type="text" class="form-control" placeholder="Tìm kiếm conversation..." id="searchConversations">
          <select class="form-select" id="filterStatus">
            <option value="">Tất cả trạng thái</option>
            <option value="pending">Cần hỗ trợ</option>
            <option value="resolved">Đã giải quyết</option>
            <option value="active">Đang hoạt động</option>
          </select>
        </div>
      </div>
      <div id="allConversations" class="conversations-grid">
        <div class="loading-state">
          <div class="spinner"></div>
          <p>Đang tải conversations...</p>
        </div>
      </div>
    </div>

    <!-- Messaging Tab -->
    <div id="messagingTab" class="tab-content">
      <div class="messaging-container">
        <div class="messaging-header">
          <h3><i class="fas fa-paper-plane text-primary"></i> Gửi tin nhắn cho người dùng</h3>
          <p>Tìm kiếm và gửi tin nhắn trực tiếp đến người dùng qua Firebase</p>
        </div>

        <div class="messaging-grid">
          <!-- User Search & Send Message -->
          <div class="message-card enhanced">
            <div class="card-header">
              <h5><i class="fas fa-user-search"></i> Tìm kiếm & gửi tin nhắn</h5>
            </div>
            <div class="card-body">
              <!-- User Search with Live Autocomplete -->
              <div class="user-search-container">
                <label class="form-label">
                  <i class="fas fa-search text-primary me-1"></i>
                  Tìm kiếm người dùng
                </label>
                <div class="search-input-wrapper position-relative">
                  <input type="text"
                         class="form-control search-input"
                         id="userSearchInput"
                         placeholder="Nhập tên, email hoặc role..."
                         autocomplete="off">
                  <i class="fas fa-search search-icon"></i>
                  <div class="search-loading d-none">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                      <span class="visually-hidden">Đang tìm...</span>
                    </div>
                  </div>
                </div>

                <!-- Live Search Results -->
                <div class="search-results" id="userSearchResults">
                  <!-- Dynamic search results will be loaded here -->
                </div>

                <!-- Default Users Display -->
                <div class="default-users-container" id="defaultUsersContainer">
                  <div class="default-users-header">
                    <span class="text-muted">
                      <i class="fas fa-users me-1"></i>
                      Người dùng gần đây
                    </span>
                  </div>
                  <div class="default-users-list" id="defaultUsersList">
                    <!-- Default users will be loaded here -->
                  </div>
                </div>

                <!-- Selected User Display -->
                <div class="selected-user animate-fade-in" id="selectedUserDisplay" style="display: none;">
                  <div class="user-avatar">
                    <i class="fas fa-user"></i>
                  </div>
                  <div class="user-info">
                    <div class="user-name"></div>
                    <div class="user-role badge"></div>
                    <div class="user-email text-muted"></div>
                    <div class="user-status">
                      <i class="fas fa-circle online-indicator"></i>
                      <span class="status-text">Online</span>
                    </div>
                  </div>
                  <button type="button" class="btn-remove" onclick="clearSelectedUser()">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>

              <!-- Message Form -->
              <form id="sendToUserForm" class="message-form">
                <input type="hidden" id="selectedUserId" name="user_id">
                <input type="hidden" id="selectedUserName" name="user_name">

                <div class="form-group">
                  <label class="form-label">
                    <i class="fas fa-comment text-primary me-1"></i>
                    Tin nhắn
                  </label>
                  <div class="message-input-wrapper">
                    <textarea class="form-control message-textarea"
                             name="message"
                             rows="4"
                             required
                             placeholder="Nhập tin nhắn muốn gửi..."></textarea>
                    <div class="message-tools">
                      <button type="button" class="btn btn-sm btn-outline-primary" onclick="insertTemplate('greeting')">
                        <i class="fas fa-hand-wave"></i> Chào hỏi
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-info" onclick="insertTemplate('support')">
                        <i class="fas fa-headset"></i> Hỗ trợ
                      </button>
                      <button type="button" class="btn btn-sm btn-outline-warning" onclick="insertTemplate('reminder')">
                        <i class="fas fa-bell"></i> Nhắc nhở
                      </button>
                    </div>
                  </div>
                  <div class="char-counter">
                    <span id="charCount">0</span>/500 ký tự
                  </div>
                </div>

                <div class="form-actions">
                  <button type="submit" class="btn btn-primary btn-send" disabled id="sendMessageBtn">
                    <i class="fas fa-paper-plane"></i>
                    <span>Gửi tin nhắn</span>
                  </button>
                  <button type="button" class="btn btn-outline-secondary" onclick="clearMessageForm()">
                    <i class="fas fa-eraser"></i> Xóa
                  </button>
                  <button type="button" class="btn btn-outline-info" onclick="previewMessage()">
                    <i class="fas fa-eye"></i> Xem trước
                  </button>
                </div>

                <!-- Keyboard Shortcuts Hint -->
                <div class="keyboard-hints">
                  <strong>Phím tắt:</strong> Ctrl+Enter để gửi tin nhắn • Ctrl+K để xóa form
                </div>
              </form>
            </div>
          </div>

          <!-- Broadcast Message -->
          <div class="message-card">
            <div class="card-header">
              <h5><i class="fas fa-bullhorn"></i> Gửi thông báo chung</h5>
            </div>
            <div class="card-body">
              <form id="broadcastForm" class="broadcast-form">
                <div class="form-group">
                  <label class="form-label">Đối tượng nhận</label>
                  <div class="target-options">
                    <div class="target-option">
                      <input class="form-check-input" type="checkbox" value="Customer" id="targetCustomers">
                      <label class="form-check-label" for="targetCustomers">
                        <i class="fas fa-users text-info"></i>
                        Khách hàng
                      </label>
                    </div>
                    <div class="target-option">
                      <input class="form-check-input" type="checkbox" value="Agent" id="targetAgents">
                      <label class="form-check-label" for="targetAgents">
                        <i class="fas fa-user-tie text-success"></i>
                        Môi giới
                      </label>
                    </div>
                    <div class="target-option">
                      <input class="form-check-input" type="checkbox" value="Owner" id="targetOwners">
                      <label class="form-check-label" for="targetOwners">
                        <i class="fas fa-home text-warning"></i>
                        Chủ nhà
                      </label>
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label class="form-label">Thông báo</label>
                  <textarea class="form-control message-textarea"
                           name="message"
                           rows="4"
                           required
                           placeholder="Nhập thông báo chung..."></textarea>
                </div>

                <div class="form-actions">
                  <button type="submit" class="btn btn-warning">
                    <i class="fas fa-bullhorn"></i> Gửi thông báo
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Settings Tab -->
    <div id="settingsTab" class="tab-content">
      <div class="settings-container">
        <div class="content-header">
          <h3><i class="fas fa-cog text-secondary"></i> Cài đặt hệ thống</h3>
        </div>

        <div class="settings-grid">
          <div class="setting-card">
            <div class="setting-icon">
              <i class="fas fa-robot"></i>
            </div>
            <div class="setting-content">
              <h5>AI Chatbot</h5>
              <p>Cấu hình và quản lý AI chatbot</p>
              <div class="setting-actions">
                <label class="switch">
                  <input type="checkbox" id="aiEnabled" checked>
                  <span class="slider"></span>
                </label>
              </div>
            </div>
          </div>

          <div class="setting-card">
            <div class="setting-icon">
              <i class="fas fa-bell"></i>
            </div>
            <div class="setting-content">
              <h5>Thông báo</h5>
              <p>Cài đặt thông báo cho admin</p>
              <div class="setting-actions">
                <label class="switch">
                  <input type="checkbox" id="notificationsEnabled" checked>
                  <span class="slider"></span>
                </label>
              </div>
            </div>
          </div>

          <div class="setting-card">
            <div class="setting-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="setting-content">
              <h5>Auto Response</h5>
              <p>Thời gian chờ trước khi tự động trả lời</p>
              <div class="setting-actions">
                <select class="form-select">
                  <option value="30">30 giây</option>
                  <option value="60" selected>1 phút</option>
                  <option value="120">2 phút</option>
                  <option value="300">5 phút</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Chat panel -->
  <div id="chatpanel" class="chat-panel" style="display:none">
    <div class="chat-header">
      <h4 id="chatTitle">Chat với User</h4>
      <button onclick="closeChatPanel()" class="close-btn">&times;</button>
    </div>

    <div id="chatbox" class="chat-messages"></div>

    <div class="chat-input-section">
      <form id="sendMsg" class="admin-chat-form">
        <input type="text" id="msgInput" required autocomplete="off" placeholder="Nhập tin nhắn trả lời..." />
        <button type="submit">Gửi</button>
      </form>
      <button id="autoReply" class="auto-reply-btn">Gợi ý AI</button>
    </div>
  </div>

  <!-- Message Preview Modal -->
  <div class="modal fade" id="messagePreviewModal" tabindex="-1" aria-labelledby="messagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="messagePreviewModalLabel">
            <i class="fas fa-eye text-primary me-2"></i>Xem trước tin nhắn
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="preview-container">
            <div class="preview-header">
              <strong>Người nhận:</strong> <span id="previewRecipient">Chưa chọn người dùng</span>
            </div>
            <div class="preview-message">
              <div class="message-bubble admin-message">
                <div class="message-content" id="previewContent">
                  Tin nhắn sẽ hiển thị ở đây...
                </div>
                <div class="message-time">
                  <i class="fas fa-clock"></i> {{ date('H:i d/m/Y') }}
                </div>
              </div>
            </div>
            <div class="preview-stats">
              <div class="stat-item">
                <i class="fas fa-font"></i>
                <span id="previewCharCount">0</span> ký tự
              </div>
              <div class="stat-item">
                <i class="fas fa-paragraph"></i>
                <span id="previewWordCount">0</span> từ
              </div>
              <div class="stat-item">
                <i class="fas fa-list-ol"></i>
                <span id="previewLineCount">0</span> dòng
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary" onclick="sendMessageFromPreview()">
            <i class="fas fa-paper-plane"></i> Gửi ngay
          </button>
        </div>
      </div>
    </div>
  </div>

</div>

<style>
/* Variables */
:root {
  --primary-color: #667eea;
  --secondary-color: #764ba2;
  --success-color: #28a745;
  --warning-color: #ffc107;
  --danger-color: #dc3545;
  --info-color: #17a2b8;
  --light-color: #f8f9fa;
  --dark-color: #343a40;
  --border-radius: 12px;
  --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  --transition: all 0.3s ease;
}

/* Main Container */
.admin-chat-container {
  padding: 20px;
  max-width: 1400px;
  margin: 0 auto;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  min-height: 100vh;
}

/* Page Header */
.page-header {
  margin-bottom: 30px;
}

.header-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  padding: 25px;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
}

.header-info h1 {
  font-size: 2rem;
  font-weight: 700;
  color: var(--dark-color);
  margin: 0;
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-info h1 i {
  color: var(--primary-color);
}

.header-info p {
  margin: 8px 0 0 0;
  color: #6c757d;
  font-size: 1.1rem;
}

.header-actions {
  display: flex;
  gap: 12px;
}

/* Stats Row */
.stats-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  padding: 25px;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  height: 4px;
  width: 100%;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
}

.stat-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.stat-card.pending::before { background: linear-gradient(135deg, #ffc107, #ff8c00); }
.stat-card.total::before { background: linear-gradient(135deg, #17a2b8, #007bff); }
.stat-card.online::before { background: linear-gradient(135deg, #28a745, #20c997); }
.stat-card.ai::before { background: linear-gradient(135deg, #6f42c1, #e83e8c); }

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: white;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
}

.stat-card.pending .stat-icon { background: linear-gradient(135deg, #ffc107, #ff8c00); }
.stat-card.total .stat-icon { background: linear-gradient(135deg, #17a2b8, #007bff); }
.stat-card.online .stat-icon { background: linear-gradient(135deg, #28a745, #20c997); }
.stat-card.ai .stat-icon { background: linear-gradient(135deg, #6f42c1, #e83e8c); }

.stat-info h3 {
  font-size: 2rem;
  font-weight: 700;
  margin: 0;
  color: var(--dark-color);
}

.stat-info p {
  margin: 0;
  color: #6c757d;
  font-weight: 500;
}

/* Navigation Tabs */
.nav-tabs-container {
  margin-bottom: 30px;
}

.nav-tabs {
  display: flex;
  background: white;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  padding: 8px;
  gap: 8px;
}

.nav-tab {
  flex: 1;
  padding: 15px 20px;
  border: none;
  background: transparent;
  border-radius: 8px;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 500;
  color: #6c757d;
  position: relative;
}

.nav-tab:hover {
  background: #f8f9fa;
  color: var(--primary-color);
}

.nav-tab.active {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
}

.nav-tab .badge {
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border-radius: 50%;
  padding: 4px 8px;
  font-size: 12px;
  min-width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.nav-tab:not(.active) .badge {
  background: var(--primary-color);
}

/* Tab Content */
.tab-content-container {
  background: white;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  overflow: hidden;
}

.tab-content {
  display: none;
  padding: 30px;
  min-height: 500px;
  opacity: 0;
  transform: translateY(10px);
  transition: all 0.3s ease;
}

.tab-content.active {
  display: block;
  opacity: 1;
  transform: translateY(0);
  animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.content-header {
  display: flex;
  justify-content: between;
  align-items: center;
  margin-bottom: 25px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f8f9fa;
}

.content-header h3 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 10px;
}

.search-filter {
  display: flex;
  gap: 15px;
  align-items: center;
}

.search-filter .form-control,
.search-filter .form-select {
  min-width: 200px;
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 60px 20px;
  color: #6c757d;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid var(--primary-color);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Conversations Grid */
.conversations-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

/* Messaging Container */
.messaging-container {
  max-width: 1200px;
  margin: 0 auto;
}

.messaging-header {
  text-align: center;
  margin-bottom: 40px;
}

.messaging-header h3 {
  margin: 0 0 10px 0;
  font-size: 1.8rem;
  font-weight: 600;
}

.messaging-header p {
  margin: 0;
  color: #6c757d;
  font-size: 1.1rem;
}

.messaging-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

/* Message Cards */
.message-card {
  background: white;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  border: 1px solid #e3f2fd;
  transition: var(--transition);
}

.message-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
}

.message-card.enhanced {
  border: 2px solid var(--primary-color);
}

.message-card .card-header {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  padding: 20px 25px;
  margin: 0;
  position: relative;
  overflow: hidden;
}

.message-card .card-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
  opacity: 0.3;
}

.message-card .card-header h5 {
  margin: 0;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 10px;
  position: relative;
  z-index: 1;
}

.message-card .card-body {
  padding: 30px;
}

/* Enhanced User Search */
.user-search-container {
  margin-bottom: 30px;
}

.search-input-wrapper {
  position: relative;
  margin-bottom: 10px;
}

.search-input {
  padding: 12px 45px 12px 15px;
  border-radius: 10px;
  border: 2px solid #e9ecef;
  transition: var(--transition);
  font-size: 14px;
  background: #f8f9fa;
}

.search-input:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
  background: white;
  transform: translateY(-1px);
}

.search-icon {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  transition: var(--transition);
}

.search-input:focus + .search-icon {
  color: var(--primary-color);
}

.search-loading {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
}

/* Enhanced Search Results */
.search-results {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 10px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
  max-height: 350px;
  overflow-y: auto;
  z-index: 1000;
  display: none;
  border-top: 3px solid var(--primary-color);
}

.search-results::-webkit-scrollbar {
  width: 6px;
}

.search-results::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.search-results::-webkit-scrollbar-thumb {
  background: var(--primary-color);
  border-radius: 3px;
}

.search-result-item {
  padding: 15px;
  border-bottom: 1px solid #f8f9fa;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 15px;
}

.search-result-item:hover {
  background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
  transform: translateX(5px);
}

.search-result-item:last-child {
  border-bottom: none;
}

.search-result-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  font-weight: 600;
  text-transform: uppercase;
}

.search-result-info {
  flex: 1;
}

.search-result-name {
  font-weight: 600;
  color: var(--dark-color);
  margin-bottom: 4px;
  font-size: 14px;
}

.search-result-role {
  font-size: 12px;
  padding: 2px 8px;
  border-radius: 12px;
  color: white;
  font-weight: 500;
  display: inline-block;
  margin-bottom: 2px;
}

.search-result-role.Customer { background: #28a745; }
.search-result-role.Agent { background: #007bff; }
.search-result-role.Owner { background: #ffc107; color: #000; }
.search-result-role.Admin { background: #dc3545; }

.search-result-email {
  font-size: 12px;
  color: #6c757d;
}

/* Default Users Container */
.default-users-container {
  margin-top: 15px;
  border: 1px solid #e9ecef;
  border-radius: 10px;
  background: #f8f9fa;
  overflow: hidden;
}

.default-users-header {
  padding: 12px 15px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  border-bottom: 1px solid #dee2e6;
  font-size: 13px;
  font-weight: 600;
}

.default-users-list {
  max-height: 300px;
  overflow-y: auto;
}

.default-user-item {
  padding: 12px 15px;
  border-bottom: 1px solid #e9ecef;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 12px;
}

.default-user-item:hover {
  background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
  transform: translateX(3px);
}

.default-user-item:last-child {
  border-bottom: none;
}

.default-user-avatar {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 600;
  text-transform: uppercase;
}

.default-user-info {
  flex: 1;
}

.default-user-name {
  font-weight: 600;
  color: var(--dark-color);
  margin-bottom: 2px;
  font-size: 13px;
}

.default-user-role {
  font-size: 11px;
  padding: 2px 6px;
  border-radius: 10px;
  color: white;
  font-weight: 500;
  display: inline-block;
  margin-bottom: 2px;
}

.default-user-role.Customer { background: #28a745; }
.default-user-role.Agent { background: #007bff; }
.default-user-role.Owner { background: #ffc107; color: #000; }
.default-user-role.Admin { background: #dc3545; }

.default-user-email {
  font-size: 11px;
  color: #6c757d;
}

.default-user-status {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  color: #6c757d;
}

/* Enhanced Selected User */
.selected-user {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 20px;
  background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
  border-radius: 12px;
  border: 2px solid var(--primary-color);
  margin-top: 15px;
  position: relative;
  overflow: hidden;
}

.selected-user::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  background: var(--primary-color);
}

.selected-user .user-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.selected-user .user-info {
  flex: 1;
}

.selected-user .user-name {
  font-weight: 600;
  color: var(--dark-color);
  margin-bottom: 4px;
  font-size: 16px;
}

.selected-user .user-role {
  font-size: 12px;
  padding: 4px 10px;
  border-radius: 15px;
  color: white;
  font-weight: 500;
  display: inline-block;
  margin-bottom: 4px;
}

.selected-user .user-email {
  font-size: 13px;
  color: #666;
  margin-bottom: 4px;
}

.selected-user .user-status {
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 12px;
}

.online-indicator {
  color: #28a745;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% { opacity: 1; }
  50% { opacity: 0.5; }
  100% { opacity: 1; }
}

.selected-user .btn-remove {
  border: none;
  background: #ff4444;
  color: white;
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: var(--transition);
}

.selected-user .btn-remove:hover {
  background: #cc0000;
}

/* Form Groups */
.form-group {
  margin-bottom: 20px;
}

.form-label {
  font-weight: 600;
  color: var(--dark-color);
  margin-bottom: 8px;
  display: block;
}

.message-textarea {
  border-radius: 8px;
  border: 2px solid #e9ecef;
  transition: var(--transition);
  resize: vertical;
}

.message-textarea:focus {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Form Actions */
.form-actions {
  display: flex;
  gap: 12px;
  align-items: center;
  flex-wrap: wrap;
  margin-top: 20px;
  padding-top: 15px;
  border-top: 1px solid #e9ecef;
}

.btn-send {
  position: relative;
  overflow: hidden;
}

.btn-send:not(:disabled):hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-send:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* Target Options */
.target-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.target-option {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px;
  background: white;
  border-radius: 8px;
  border: 2px solid #e9ecef;
  transition: var(--transition);
}

.target-option:hover {
  border-color: var(--primary-color);
}

.target-option .form-check-input:checked ~ .form-check-label {
  color: var(--primary-color);
  font-weight: 600;
}

.target-option .form-check-label {
  margin: 0;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 500;
}

/* Settings */
.settings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 25px;
}

.setting-card {
  background: #f8f9fa;
  padding: 25px;
  border-radius: var(--border-radius);
  display: flex;
  align-items: center;
  gap: 20px;
  transition: var(--transition);
}

.setting-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.setting-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.setting-content {
  flex: 1;
}

.setting-content h5 {
  margin: 0 0 5px 0;
  font-weight: 600;
  color: var(--dark-color);
}

.setting-content p {
  margin: 0 0 15px 0;
  color: #6c757d;
  font-size: 14px;
}

/* Switch */
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: var(--primary-color);
}

input:checked + .slider:before {
  transform: translateX(26px);
}

/* Buttons */
.btn {
  border-radius: 8px;
  padding: 10px 20px;
  font-weight: 500;
  transition: var(--transition);
  border: none;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
}

.btn-warning {
  background: linear-gradient(135deg, #ffc107, #ff8c00);
  color: white;
}

.btn-warning:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4);
}

.btn-outline-primary {
  border: 2px solid var(--primary-color);
  color: var(--primary-color);
  background: transparent;
}

.btn-outline-primary:hover {
  background: var(--primary-color);
  color: white;
}

.btn-refresh {
  background: #17a2b8;
  color: white;
}

.btn-refresh:hover {
  background: #138496;
}

/* Message Form Enhancements */
.message-input-wrapper {
  position: relative;
  border-radius: 12px;
  overflow: hidden;
  border: 2px solid #e9ecef;
  transition: var(--transition);
}

.message-input-wrapper:focus-within {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.message-textarea {
  border: none !important;
  box-shadow: none !important;
  resize: vertical;
  min-height: 120px;
  padding: 15px;
  font-size: 14px;
  line-height: 1.5;
}

.message-tools {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 10px;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.message-tools .btn {
  font-size: 12px;
  padding: 6px 12px;
  border-radius: 20px;
}

/* Character Counter */
.char-counter {
  text-align: right;
  font-size: 12px;
  color: #6c757d;
  margin-top: 5px;
}

.char-counter.warning {
  color: #ffc107;
}

.char-counter.danger {
  color: #dc3545;
}

/* Keyboard Hints */
.keyboard-hints {
  margin-top: 15px;
  padding: 10px;
  background: #e3f2fd;
  border-radius: 6px;
  font-size: 12px;
  color: #1976d2;
  border-left: 3px solid #2196f3;
}

/* Message Preview Modal */
.preview-container {
  padding: 15px;
}

.preview-header {
  margin-bottom: 20px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
  border-left: 4px solid var(--primary-color);
}

.preview-message {
  margin-bottom: 20px;
  min-height: 100px;
  background: #f5f5f5;
  padding: 20px;
  border-radius: 10px;
  position: relative;
}

.message-bubble {
  max-width: 80%;
  margin-left: auto;
  background: linear-gradient(135deg, #28a745, #20c997);
  color: white;
  padding: 15px;
  border-radius: 18px 18px 4px 18px;
  position: relative;
}

.message-bubble::after {
  content: '';
  position: absolute;
  bottom: 0;
  right: -8px;
  width: 0;
  height: 0;
  border: 8px solid transparent;
  border-top-color: #28a745;
  border-right: 0;
  margin-bottom: -8px;
}

.message-content {
  margin-bottom: 8px;
  line-height: 1.4;
}

.message-time {
  font-size: 11px;
  opacity: 0.8;
  display: flex;
  align-items: center;
  gap: 4px;
}

.preview-stats {
  display: flex;
  gap: 20px;
  padding: 15px;
  background: #e8f5e8;
  border-radius: 8px;
  justify-content: center;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #495057;
}

.stat-item i {
  color: var(--primary-color);
}

/* Search Results Enhanced */
.search-message {
  padding: 20px;
  text-align: center;
  color: #6c757d;
  font-style: italic;
}

.search-result-item mark {
  background: linear-gradient(135deg, #fff3cd, #ffeaa7);
  padding: 2px 4px;
  border-radius: 3px;
  font-weight: 600;
}

/* Animations */
.animate-fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-down {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Role Badge Classes */
.badge-primary { background: #007bff; }
.badge-success { background: #28a745; }
.badge-warning { background: #ffc107; color: #000; }
.badge-danger { background: #dc3545; }
.badge-secondary { background: #6c757d; }

/* Tab Content */
.tab-content {
  display: none;
}

.tab-content.active {
  display: block;
}

/* Conversation List */
.conversations-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 15px;
  margin-bottom: 20px;
}

.conversation-card {
  border: 1px solid #dee2e6;
  border-radius: 8px;
  padding: 15px;
  background: white;
  cursor: pointer;
  transition: all 0.2s;
}

.conversation-card:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transform: translateY(-1px);
}

.conversation-card.urgent {
  border-left: 4px solid #dc3545;
}

.conversation-card.pending {
  border-left: 4px solid #ffc107;
}

.conversation-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.conversation-id {
  font-weight: bold;
  font-size: 12px;
  color: #6c757d;
}

.conversation-status {
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: bold;
}

.status-pending {
  background: #fff3cd;
  color: #856404;
}

.status-urgent {
  background: #f8d7da;
  color: #721c24;
}

.conversation-message {
  background: #f8f9fa;
  padding: 10px;
  border-radius: 6px;
  font-size: 14px;
  margin-bottom: 10px;
  border-left: 3px solid #007bff;
}

.conversation-time {
  font-size: 11px;
  color: #6c757d;
  text-align: right;
}

/* Chat Panel */
.chat-panel {
  position: fixed;
  right: 20px;
  top: 100px;
  width: 400px;
  height: 600px;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.15);
  display: flex;
  flex-direction: column;
  z-index: 1000;
}

.chat-header {
  background: #007bff;
  color: white;
  padding: 15px;
  border-radius: 10px 10px 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.chat-header h4 {
  margin: 0;
  font-size: 16px;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 20px;
  cursor: pointer;
  padding: 0;
  width: 25px;
  height: 25px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.chat-messages {
  flex: 1;
  overflow-y: auto;
  padding: 15px;
  background: #f8f9fa;
}

.msg {
  margin-bottom: 10px;
  padding: 8px 12px;
  border-radius: 12px;
  max-width: 80%;
  word-wrap: break-word;
}

.msg.user {
  background: #007bff;
  color: white;
  margin-left: auto;
  text-align: right;
}

.msg.admin {
  background: #28a745;
  color: white;
}

.msg.bot {
  background: #e9ecef;
  color: #333;
}

.msg.guest {
  background: #ffc107;
  color: #000;
}

.chat-input-section {
  padding: 15px;
  border-top: 1px solid #dee2e6;
  background: white;
  border-radius: 0 0 10px 10px;
}

.admin-chat-form {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
}

.admin-chat-form input {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 20px;
  outline: none;
}

.admin-chat-form button {
  padding: 8px 16px;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 20px;
  cursor: pointer;
}

.auto-reply-btn {
  width: 100%;
  padding: 8px;
  background: #28a745;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 12px;
}

.loading {
  text-align: center;
  padding: 40px;
  color: #6c757d;
}

.empty-state {
  text-align: center;
  padding: 40px;
  color: #6c757d;
}

.card {
  border: 1px solid #dee2e6;
  border-radius: 8px;
  margin-bottom: 20px;
}

.card-header {
  background: #f8f9fa;
  padding: 15px;
  border-bottom: 1px solid #dee2e6;
  border-radius: 8px 8px 0 0;
}

.card-header h5 {
  margin: 0;
  font-weight: 600;
}

.card-body {
  padding: 20px;
}

.form-label {
  font-weight: 500;
  margin-bottom: 8px;
}

.form-control, .form-select {
  border-radius: 6px;
  border: 1px solid #ced4da;
  padding: 8px 12px;
}

.form-control:focus, .form-select:focus {
  border-color: #007bff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn {
  border-radius: 6px;
  padding: 8px 16px;
  font-weight: 500;
}

.btn-primary {
  background: #007bff;
  border-color: #007bff;
}

.btn-warning {
  background: #ffc107;
  border-color: #ffc107;
  color: #000;
}

.form-check {
  margin-bottom: 8px;
}

.form-check-input:checked {
  background-color: #007bff;
  border-color: #007bff;
}

@media (max-width: 768px) {
  .header-content {
    flex-direction: column;
    gap: 20px;
    text-align: center;
  }

  .stats-row {
    grid-template-columns: 1fr;
  }

  .nav-tabs {
    flex-direction: column;
  }

  .messaging-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .search-filter {
    flex-direction: column;
    align-items: stretch;
  }

  .search-filter .form-control,
  .search-filter .form-select {
    min-width: auto;
  }

  .settings-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>

<!-- Firebase SDK -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

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

let currentConversationId = null;
let chatRef = null;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin chat interface loaded');

    // Setup tab functionality
    setupTabs();

    // Test Firebase connection
    db.ref('.info/connected').on('value', function(snapshot) {
        if (snapshot.val() === true) {
            console.log('Firebase connected successfully');
        } else {
            console.log('Firebase connection failed');
        }
    });

    loadPendingConversations();
    loadAllConversations();

    // Auto refresh every 30 seconds
    setInterval(() => {
        if (document.getElementById('pendingTab').classList.contains('active')) {
            loadPendingConversations();
        }
        if (document.getElementById('allTab').classList.contains('active')) {
            loadAllConversations();
        }
    }, 30000);

    // Setup form handlers
    setupFormHandlers();

    // Initialize message form
    initializeMessageForm();
});

// Setup tab functionality
function setupTabs() {
    const tabButtons = document.querySelectorAll('.nav-tab');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            showTab(tabName, this);
        });
    });
}

// Setup form handlers function
function setupFormHandlers() {
    // Form handlers are initialized within DOMContentLoaded event listeners below
    console.log('Form handlers setup completed');
}

// Tab functions
function showTab(tabName, buttonElement) {
    // Add loading state to button
    if (buttonElement) {
        const originalHTML = buttonElement.innerHTML;
        buttonElement.innerHTML = `<i class="fas fa-spinner fa-spin"></i> <span>Đang tải...</span>`;
        buttonElement.disabled = true;
    }

    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.nav-tab').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab after a short delay
    setTimeout(() => {
        document.getElementById(tabName + 'Tab').classList.add('active');
        if (buttonElement) {
            buttonElement.classList.add('active');
            // Restore original button content
            if (tabName === 'pending') {
                buttonElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i><span>Cần hỗ trợ</span><span class="badge" id="pendingBadge">0</span>';
            } else if (tabName === 'all') {
                buttonElement.innerHTML = '<i class="fas fa-list"></i><span>Tất cả</span>';
            } else if (tabName === 'messaging') {
                buttonElement.innerHTML = '<i class="fas fa-paper-plane"></i><span>Gửi tin nhắn</span>';
            } else if (tabName === 'settings') {
                buttonElement.innerHTML = '<i class="fas fa-cog"></i><span>Cài đặt</span>';
            }
            buttonElement.disabled = false;
        }
    }, 150);

    // Load specific content based on tab
    switch(tabName) {
        case 'pending':
            setTimeout(() => loadPendingConversations(), 200);
            break;
        case 'all':
            setTimeout(() => loadAllConversations(), 200);
            break;
        case 'messaging':
            setTimeout(() => {
                loadDefaultUsers();
                initializeUserSearch();
                initializeMessageForm();
            }, 200);
            break;
        case 'settings':
            // Initialize settings if needed
            console.log('Settings tab loaded');
            break;
    }
}

// Load pending conversations
function loadPendingConversations() {
    console.log('Loading pending conversations...');

    fetch('/api/chat/conversations?type=pending', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => {
            console.log('Pending conversations response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Pending conversations data:', data);
            const container = document.getElementById('pendingConversations');

            if (!data.success || !data.conversations || data.conversations.length === 0) {
                container.innerHTML = '<div class="empty-state">Không có conversation nào cần hỗ trợ</div>';
                return;
            }

            const conversations = data.conversations;
            container.innerHTML = conversations.map(conv => `
                <div class="conversation-card pending" onclick="selectConversation('${conv.conversation_id}', '${conv.user_id || 'Guest'}')">
                    <div class="conversation-header">
                        <span class="conversation-id">${conv.conversation_id}</span>
                        <span class="conversation-status status-pending">Cần hỗ trợ</span>
                    </div>
                    <div class="conversation-message">
                        "${conv.original_message}"
                    </div>
                    <div class="conversation-time">
                        ${new Date(conv.timestamp * 1000).toLocaleString('vi-VN')}
                    </div>
                </div>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading pending conversations:', error);
            document.getElementById('pendingConversations').innerHTML =
                '<div class="empty-state">Lỗi khi tải dữ liệu: ' + error.message + '</div>';
        });
}

// Load all conversations
function loadAllConversations() {
    console.log('Loading all conversations...');

    fetch('/api/chat/conversations?type=all', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('All conversations data:', data);
        const container = document.getElementById('allConversations');

        if (!data.success || !data.conversations || data.conversations.length === 0) {
            container.innerHTML = '<div class="empty-state">Chưa có conversation nào</div>';
            return;
        }

        container.innerHTML = data.conversations.map(conv => `
            <div class="conversation-card ${conv.needs_admin ? 'urgent' : ''}" onclick="selectConversation('${conv.id}', '${conv.user_name}')">
                <div class="conversation-header">
                    <span class="conversation-id">${conv.id}</span>
                    <span class="conversation-user">${conv.user_name}</span>
                    ${conv.needs_admin ? '<span class="conversation-status status-urgent">Cần hỗ trợ</span>' : ''}
                </div>
                <div class="conversation-message">
                    ${conv.last_message ? conv.last_message.substring(0, 100) : 'Chưa có tin nhắn'}
                    ${conv.last_message && conv.last_message.length > 100 ? '...' : ''}
                </div>
                <div class="conversation-time">
                    ${conv.last_message_time ? new Date(conv.last_message_time * 1000).toLocaleString('vi-VN') : ''}
                </div>
            </div>
        `).join('');
    })
    .catch(error => {
        console.error('Error loading all conversations:', error);
        document.getElementById('allConversations').innerHTML =
            '<div class="empty-state">Lỗi tải dữ liệu: ' + error.message + '</div>';
    });
}

// Select conversation
function selectConversation(conversationId, userName) {
    currentConversationId = conversationId;
    document.getElementById('chatTitle').textContent = `Chat với ${userName}`;
    document.getElementById('chatpanel').style.display = 'flex';
    document.getElementById('chatbox').innerHTML = '<div class="loading">Đang tải tin nhắn...</div>';

    // Load messages via API
    fetch(`/api/chat/messages/${conversationId}`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        const chatbox = document.getElementById('chatbox');
        if (data.success && data.messages) {
            chatbox.innerHTML = '';
            data.messages.forEach(msg => {
                addMessageToChat(msg);
            });
        } else {
            chatbox.innerHTML = '<div class="empty-state">Chưa có tin nhắn nào</div>';
        }
    })
    .catch(error => {
        console.error('Error loading messages:', error);
        document.getElementById('chatbox').innerHTML = '<div class="empty-state">Lỗi tải tin nhắn: ' + error.message + '</div>';
    });

    // Setup Firebase real-time listener for new messages
    if (chatRef) chatRef.off();
    chatRef = db.ref('chats/' + conversationId + '/messages');
    chatRef.on('child_added', function(snapshot) {
        const msg = snapshot.val();
        // Only add if not already in the chat (avoid duplicates from API load)
        if (!document.querySelector(`[data-msg-id="${snapshot.key}"]`)) {
            addMessageToChat(msg, snapshot.key);
        }
    });
}

// Add message to chat
function addMessageToChat(msg, msgId = null) {
    const chatbox = document.getElementById('chatbox');
    let senderClass = 'user';
    let senderName = msg.sender_name || msg.sender_id;

    if (msg.sender_id === 'admin') {
        senderClass = 'admin';
    } else if (msg.sender_id === 'bot') {
        senderClass = 'bot';
        senderName = 'AI Bot';
    } else if (msg.sender_id === 'guest') {
        senderClass = 'guest';
        senderName = 'Khách';
    }

    const messageDiv = document.createElement('div');
    messageDiv.className = `msg ${senderClass}`;
    if (msgId) {
        messageDiv.setAttribute('data-msg-id', msgId);
    }

    const timestamp = msg.timestamp ? new Date(msg.timestamp * 1000).toLocaleTimeString('vi-VN') : '';
    messageDiv.innerHTML = `
        <div class="msg-content">
            <strong>${senderName}:</strong> ${msg.content}
        </div>
        <div class="msg-time">${timestamp}</div>
    `;

    chatbox.appendChild(messageDiv);
    chatbox.scrollTop = chatbox.scrollHeight;
}

// Send admin message
document.getElementById('sendMsg').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!currentConversationId) return;

    const content = document.getElementById('msgInput').value.trim();
    if (!content) return;

    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Đang gửi...';
    submitBtn.disabled = true;

    fetch('/api/chat/admin-reply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            conversation_id: currentConversationId,
            message: content
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('msgInput').value = '';
            // Refresh pending conversations to update status
            loadPendingConversations();
        } else {
            alert('Lỗi gửi tin nhắn: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Có lỗi xảy ra khi gửi tin nhắn!');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

// Auto reply with AI suggestion
document.getElementById('autoReply').addEventListener('click', function() {
    if (!currentConversationId) return;

    // Get the last user message to suggest reply
    db.ref('chats/' + currentConversationId + '/messages')
        .orderByChild('timestamp')
        .limitToLast(10)
        .once('value', function(snapshot) {
            const messages = [];
            snapshot.forEach(child => {
                messages.push(child.val());
            });

            // Find the last user message
            const lastUserMessage = messages.reverse().find(msg =>
                msg.sender_id !== 'admin' && msg.sender_id !== 'bot'
            );

            if (lastUserMessage) {
                // Try to find AI answer for the message
                fetch('/api/chatbot/answer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: lastUserMessage.content,
                        conversation_id: 'temp_' + Date.now(),
                        user_type: 'user'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.type === 'bot') {
                        document.getElementById('msgInput').value = data.response;
                    } else {
                        document.getElementById('msgInput').value = 'Cảm ơn bạn đã liên hệ. Tôi sẽ hỗ trợ bạn ngay.';
                    }
                })
                .catch(error => {
                    document.getElementById('msgInput').value = 'Cảm ơn bạn đã liên hệ. Tôi sẽ hỗ trợ bạn ngay.';
                });
            }
        });
});

// Close chat panel
function closeChatPanel() {
    document.getElementById('chatpanel').style.display = 'none';
    if (chatRef) {
        chatRef.off();
        chatRef = null;
    }
    currentConversationId = null;
}

// Load users list for messaging
function loadUsersList() {
    // Load 5 default users first
    loadDefaultUsers();
    // Initialize user search functionality
    initializeUserSearch();
}

// Load 5 default users when messaging tab opens
function loadDefaultUsers() {
    const defaultUsersContainer = document.getElementById('defaultUsersContainer');
    const defaultUsersList = document.getElementById('defaultUsersList');

    // Show loading state
    defaultUsersList.innerHTML = `
        <div class="text-center p-3">
            <div class="spinner-border spinner-border-sm text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <div class="mt-2 text-muted">Đang tải người dùng...</div>
        </div>
    `;

    fetch('/admin/chatbot/users-list?limit=5', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.users) {
            displayDefaultUsers(data.users);
        } else {
            defaultUsersList.innerHTML = `
                <div class="text-center p-3 text-muted">
                    <i class="fas fa-users"></i>
                    <div class="mt-2">Chưa có người dùng nào</div>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading default users:', error);
        defaultUsersList.innerHTML = `
            <div class="text-center p-3 text-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="mt-2">Lỗi khi tải danh sách người dùng</div>
            </div>
        `;
    });
}

// Display default users list
function displayDefaultUsers(users) {
    const defaultUsersList = document.getElementById('defaultUsersList');

    if (users.length === 0) {
        defaultUsersList.innerHTML = `
            <div class="text-center p-3 text-muted">
                <i class="fas fa-users"></i>
                <div class="mt-2">Chưa có người dùng nào</div>
            </div>
        `;
        return;
    }

    defaultUsersList.innerHTML = users.map((user, index) => {
        const roleDisplay = getRoleDisplayName(user.Role);
        const avatarInitial = user.Name.charAt(0).toUpperCase();

        return `
            <div class="default-user-item"
                 data-user-index="${index}"
                 data-user-id="${user.UserID}"
                 data-user-name="${user.Name}"
                 data-user-email="${user.Email}"
                 data-user-role="${user.Role}">
                <div class="default-user-avatar">
                    ${avatarInitial}
                </div>
                <div class="default-user-info">
                    <div class="default-user-name">${user.Name}</div>
                    <div class="default-user-role ${user.Role}">${roleDisplay}</div>
                    <div class="default-user-email">${user.Email}</div>
                </div>
                <div class="default-user-status">
                    <i class="fas fa-circle ${getOnlineStatus(user.UserID)}"></i>
                </div>
            </div>
        `;
    }).join('');

    // Store users data globally for selection
    window.defaultUsersData = users;
}

// Get role display name in Vietnamese
function getRoleDisplayName(role) {
    const roleNames = {
        'Admin': 'Quản trị viên',
        'Agent': 'Môi giới',
        'Customer': 'Khách hàng',
        'Owner': 'Chủ nhà'
    };
    return roleNames[role] || role;
}

// Get online status (mock - you can integrate with real presence system)
function getOnlineStatus(userId) {
    // Mock online status - randomly assign for demo
    return Math.random() > 0.5 ? 'online-indicator' : 'offline-indicator';
}

// Initialize user search with autocomplete
function initializeUserSearch() {
    const searchInput = document.getElementById('userSearchInput');
    const searchResults = document.getElementById('userSearchResults');
    const searchLoading = document.querySelector('.search-loading');

    let searchTimeout;
    let allUsers = [];

    // Load all users first
    loadAllUsers();

    // Search input event
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        const defaultUsersContainer = document.getElementById('defaultUsersContainer');

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (query.length >= 2) {
                searchUsers(query);
                // Hide default users when searching
                defaultUsersContainer.style.display = 'none';
            } else {
                searchResults.innerHTML = '';
                searchResults.style.display = 'none';
                // Show default users when not searching
                defaultUsersContainer.style.display = 'block';
            }
        }, 300);
    });

    // Show/hide default users on focus/blur
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length === 0) {
            document.getElementById('defaultUsersContainer').style.display = 'block';
        }
    });

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.user-search-container')) {
            searchResults.style.display = 'none';
            // Show default users when clicking outside if no search text
            if (searchInput.value.trim().length === 0) {
                document.getElementById('defaultUsersContainer').style.display = 'block';
            }
        }
    });

    // Load all users for local search
    function loadAllUsers() {
        fetch('/admin/chatbot/users-list', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allUsers = data.users;
                console.log('Loaded users for search:', allUsers.length);
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
        });
    }

    // Search users locally
    function searchUsers(query) {
        if (!allUsers.length) {
            searchResults.innerHTML = '<div class="search-message">Đang tải danh sách người dùng...</div>';
            searchResults.style.display = 'block';
            return;
        }

        showSearchLoading(true);

        // Filter users locally
        const filtered = allUsers.filter(user =>
            user.Name.toLowerCase().includes(query.toLowerCase()) ||
            user.Email.toLowerCase().includes(query.toLowerCase()) ||
            user.Role.toLowerCase().includes(query.toLowerCase())
        );

        setTimeout(() => {
            showSearchLoading(false);
            displaySearchResults(filtered, query);
        }, 200); // Simulate loading for better UX
    }

    // Show search loading
    function showSearchLoading(show) {
        const searchIcon = document.querySelector('.search-icon');

        if (show) {
            searchLoading.classList.remove('d-none');
            searchIcon.style.display = 'none';
        } else {
            searchLoading.classList.add('d-none');
            searchIcon.style.display = 'block';
        }
    }

    // Display search results
    function displaySearchResults(users, query) {
        if (users.length === 0) {
            searchResults.innerHTML = `
                <div class="search-message">
                    <i class="fas fa-search text-muted"></i>
                    Không tìm thấy người dùng với từ khóa "${query}"
                </div>
            `;
        } else {
            searchResults.innerHTML = users.map((user, index) => `
                <div class="search-result-item"
                     data-user-index="${index}"
                     data-user-id="${user.UserID}"
                     data-user-name="${user.Name}"
                     data-user-email="${user.Email}"
                     data-user-role="${user.Role}">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-info">
                        <div class="user-name">${highlightMatch(user.Name, query)}</div>
                        <div class="user-email text-muted">${highlightMatch(user.Email, query)}</div>
                        <div class="user-role badge badge-${getRoleBadgeClass(user.Role)}">${getRoleDisplayName(user.Role)}</div>
                    </div>
                    <div class="user-status">
                        <i class="fas fa-circle ${getOnlineStatus(user.UserID)}"></i>
                    </div>
                </div>
            `).join('');

            // Store search results data globally
            window.searchUsersData = users;
        }

        searchResults.style.display = 'block';
    }

    // Highlight matching text
    function highlightMatch(text, query) {
        if (!query) return text;
        const regex = new RegExp(`(${query})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }

    // Get role badge class
    function getRoleBadgeClass(role) {
        const roleClasses = {
            'Admin': 'danger',
            'Agent': 'success',
            'Customer': 'primary',
            'Owner': 'warning'
        };
        return roleClasses[role] || 'secondary';
    }

    // Get online status (mock - you can integrate with real presence system)
    function getOnlineStatus(userId) {
        // Mock online status - randomly assign for demo
        return Math.random() > 0.5 ? 'online-indicator' : 'offline-indicator';
    }
}

// Global helper functions
function getRoleBadgeClass(role) {
    const roleClasses = {
        'Admin': 'danger',
        'Agent': 'success',
        'Customer': 'primary',
        'Owner': 'warning'
    };
    return roleClasses[role] || 'secondary';
}

// Select user from search results
function selectUser(user) {
    const selectedUserDisplay = document.getElementById('selectedUserDisplay');
    const searchResults = document.getElementById('userSearchResults');
    const searchInput = document.getElementById('userSearchInput');

    // Hide search results
    searchResults.style.display = 'none';
    searchInput.value = '';

    // Fill hidden inputs
    document.getElementById('selectedUserId').value = user.UserID;
    document.getElementById('selectedUserName').value = user.Name;

    // Update display with Vietnamese role names
    const roleDisplayName = getRoleDisplayName(user.Role);
    selectedUserDisplay.querySelector('.user-name').textContent = user.Name;
    selectedUserDisplay.querySelector('.user-email').textContent = user.Email;
    selectedUserDisplay.querySelector('.user-role').textContent = roleDisplayName;
    selectedUserDisplay.querySelector('.user-role').className = `badge badge-${getRoleBadgeClass(user.Role)}`;

    // Show selected user display
    selectedUserDisplay.style.display = 'flex';
    selectedUserDisplay.classList.add('animate-fade-in');

    // Update send button state
    updateSendButtonState();

    console.log('User selected:', user);
}

// Clear selected user
function clearSelectedUser() {
    const selectedUserDisplay = document.getElementById('selectedUserDisplay');

    // Hide display
    selectedUserDisplay.style.display = 'none';

    // Clear hidden inputs
    document.getElementById('selectedUserId').value = '';
    document.getElementById('selectedUserName').value = '';

    // Update send button state
    updateSendButtonState();

    console.log('User selection cleared');
}

// Insert message template
function insertTemplate(templateType) {
    const textarea = document.querySelector('.message-textarea');
    const templates = {
        greeting: 'Xin chào! Tôi là admin của hệ thống. Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi. Tôi có thể hỗ trợ gì cho bạn?',
        support: 'Chúng tôi đã nhận được yêu cầu hỗ trợ của bạn. Đội ngũ kỹ thuật sẽ kiểm tra và phản hồi trong thời gian sớm nhất. Cảm ơn bạn đã kiên nhẫn!',
        reminder: 'Nhắc nhở: Vui lòng hoàn tất các thủ tục cần thiết để chúng tôi có thể hỗ trợ bạn tốt nhất. Nếu có thắc mắc, đừng ngần ngại liên hệ với chúng tôi.'
    };

    if (templates[templateType]) {
        textarea.value = templates[templateType];
        updateCharCounter();
        updateSendButtonState();

        // Focus textarea
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
    }
}

// Update character counter
function updateCharCounter() {
    const textarea = document.querySelector('.message-textarea');
    const charCount = document.getElementById('charCount');
    const counter = document.querySelector('.char-counter');

    if (textarea && charCount) {
        const currentLength = textarea.value.length;
        charCount.textContent = currentLength;

        // Update counter styling based on length
        counter.classList.remove('warning', 'danger');
        if (currentLength > 400) {
            counter.classList.add('danger');
        } else if (currentLength > 300) {
            counter.classList.add('warning');
        }
    }
}

// Update send button state
function updateSendButtonState() {
    const sendBtn = document.getElementById('sendMessageBtn');
    const selectedUserId = document.getElementById('selectedUserId').value;
    const messageContent = document.querySelector('.message-textarea').value.trim();

    if (selectedUserId && messageContent && messageContent.length > 0) {
        sendBtn.disabled = false;
        sendBtn.classList.remove('btn-secondary');
        sendBtn.classList.add('btn-primary');
    } else {
        sendBtn.disabled = true;
        sendBtn.classList.remove('btn-primary');
        sendBtn.classList.add('btn-secondary');
    }
}

// Clear message form
function clearMessageForm() {
    const form = document.getElementById('sendToUserForm');
    const textarea = form.querySelector('.message-textarea');

    // Clear textarea
    textarea.value = '';

    // Update counters and button state
    updateCharCounter();
    updateSendButtonState();

    // Focus textarea
    textarea.focus();

    console.log('Message form cleared');
}

// Preview message
function previewMessage() {
    const selectedUserName = document.getElementById('selectedUserName').value;
    const messageContent = document.querySelector('.message-textarea').value.trim();

    if (!selectedUserName || !messageContent) {
        alert('Vui lòng chọn người dùng và nhập tin nhắn trước khi xem trước!');
        return;
    }

    // Update preview content
    document.getElementById('previewRecipient').textContent = selectedUserName;
    document.getElementById('previewContent').textContent = messageContent;
    document.getElementById('previewCharCount').textContent = messageContent.length;
    document.getElementById('previewWordCount').textContent = messageContent.split(/\s+/).filter(word => word.length > 0).length;
    document.getElementById('previewLineCount').textContent = messageContent.split('\n').length;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('messagePreviewModal'));
    modal.show();
}

// Send message from preview
function sendMessageFromPreview() {
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('messagePreviewModal'));
    modal.hide();

    // Trigger form submission
    document.getElementById('sendToUserForm').dispatchEvent(new Event('submit'));
}

// Initialize message form
function initializeMessageForm() {
    const textarea = document.querySelector('.message-textarea');
    const form = document.getElementById('sendToUserForm');

    // Character counter event
    textarea.addEventListener('input', function() {
        updateCharCounter();
        updateSendButtonState();
    });

    // Keyboard shortcuts
    textarea.addEventListener('keydown', function(e) {
        // Ctrl+Enter to send
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            if (!document.getElementById('sendMessageBtn').disabled) {
                form.dispatchEvent(new Event('submit'));
            }
        }

        // Ctrl+K to clear
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            clearMessageForm();
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = document.getElementById('sendMessageBtn');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        submitBtn.disabled = true;

        // Send message
        fetch('/admin/chatbot/send-to-user', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('Tin nhắn đã được gửi thành công!', 'success');

                // Clear form
                clearMessageForm();
                clearSelectedUser();
            } else {
                showNotification('Lỗi: ' + (data.message || 'Không thể gửi tin nhắn'), 'error');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            showNotification('Có lỗi xảy ra khi gửi tin nhắn!', 'error');
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            updateSendButtonState();
        });
    });

    // Initial state
    updateCharCounter();
    updateSendButtonState();
}

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.admin-notification');
    existingNotifications.forEach(n => n.remove());

    // Create notification
    const notification = document.createElement('div');
    notification.className = `admin-notification alert alert-${type === 'error' ? 'danger' : type} alert-dismissible`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);';

    const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle';

    notification.innerHTML = `
        <i class="fas fa-${icon} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Get role badge class (make it global)
function getRoleBadgeClass(role) {
    const roleClasses = {
        'Admin': 'danger',
        'Agent': 'success',
        'Customer': 'primary',
        'Owner': 'warning'
    };
    return roleClasses[role] || 'secondary';
}

// Initialize message form
function initializeMessageForm() {
    const textarea = document.querySelector('.message-textarea');
    const form = document.getElementById('sendToUserForm');

    if (!textarea || !form) return;

    // Character counter event
    textarea.addEventListener('input', function() {
        updateCharCounter();
        updateSendButtonState();
    });

    // Keyboard shortcuts
    textarea.addEventListener('keydown', function(e) {
        // Ctrl+Enter to send
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            if (!document.getElementById('sendMessageBtn').disabled) {
                form.dispatchEvent(new Event('submit'));
            }
        }

        // Ctrl+K to clear
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            clearMessageForm();
        }
    });

    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const submitBtn = document.getElementById('sendMessageBtn');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        submitBtn.disabled = true;

        // Send message
        fetch('/admin/chatbot/send-to-user', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Tin nhắn đã được gửi thành công!', 'success');
                clearMessageForm();
                clearSelectedUser();
            } else {
                showNotification('Lỗi: ' + (data.message || 'Không thể gửi tin nhắn'), 'error');
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            showNotification('Có lỗi xảy ra khi gửi tin nhắn!', 'error');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            updateSendButtonState();
        });
    });

    // Initial state
    updateCharCounter();
    updateSendButtonState();
}

// Update character counter
function updateCharCounter() {
    const textarea = document.querySelector('.message-textarea');
    const charCount = document.getElementById('charCount');
    const counter = document.querySelector('.char-counter');

    if (textarea && charCount) {
        const currentLength = textarea.value.length;
        charCount.textContent = currentLength;

        // Update counter styling based on length
        counter.classList.remove('warning', 'danger');
        if (currentLength > 400) {
            counter.classList.add('danger');
        } else if (currentLength > 300) {
            counter.classList.add('warning');
        }
    }
}

// Update send button state
function updateSendButtonState() {
    const sendBtn = document.getElementById('sendMessageBtn');
    const selectedUserId = document.getElementById('selectedUserId').value;
    const messageContent = document.querySelector('.message-textarea').value.trim();

    if (selectedUserId && messageContent && messageContent.length > 0) {
        sendBtn.disabled = false;
        sendBtn.classList.remove('btn-secondary');
        sendBtn.classList.add('btn-primary');
    } else {
        sendBtn.disabled = true;
        sendBtn.classList.remove('btn-primary');
        sendBtn.classList.add('btn-secondary');
    }
}

// Clear message form
function clearMessageForm() {
    const form = document.getElementById('sendToUserForm');
    const textarea = form.querySelector('.message-textarea');

    if (textarea) {
        textarea.value = '';
        updateCharCounter();
        updateSendButtonState();
        textarea.focus();
    }
}

// Event delegation for user selection
document.addEventListener('click', function(e) {
    // Handle default user item clicks
    if (e.target.closest('.default-user-item')) {
        const userItem = e.target.closest('.default-user-item');
        const userIndex = parseInt(userItem.dataset.userIndex);

        if (window.defaultUsersData && window.defaultUsersData[userIndex]) {
            const user = window.defaultUsersData[userIndex];
            selectUser(user);
        }
    }

    // Handle search result item clicks
    if (e.target.closest('.search-result-item')) {
        const userItem = e.target.closest('.search-result-item');
        const userIndex = parseInt(userItem.dataset.userIndex);

        if (window.searchUsersData && window.searchUsersData[userIndex]) {
            const user = window.searchUsersData[userIndex];
            selectUser(user);
        }
    }
});

// Insert message template
function insertTemplate(templateType) {
    const textarea = document.querySelector('.message-textarea');
    const templates = {
        greeting: 'Xin chào! Tôi là admin của hệ thống. Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi. Tôi có thể hỗ trợ gì cho bạn?',
        support: 'Chúng tôi đã nhận được yêu cầu hỗ trợ của bạn. Đội ngũ kỹ thuật sẽ kiểm tra và phản hồi trong thời gian sớm nhất. Cảm ơn bạn đã kiên nhẫn!',
        reminder: 'Nhắc nhở: Vui lòng hoàn tất các thủ tục cần thiết để chúng tôi có thể hỗ trợ bạn tốt nhất. Nếu có thắc mắc, đừng ngần ngại liên hệ với chúng tôi.'
    };

    if (templates[templateType] && textarea) {
        textarea.value = templates[templateType];
        updateCharCounter();
        updateSendButtonState();
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
    }
}

// Preview message
function previewMessage() {
    const selectedUserName = document.getElementById('selectedUserName').value;
    const messageContent = document.querySelector('.message-textarea').value.trim();

    if (!selectedUserName || !messageContent) {
        alert('Vui lòng chọn người dùng và nhập tin nhắn trước khi xem trước!');
        return;
    }

    // Update preview content
    document.getElementById('previewRecipient').textContent = selectedUserName;
    document.getElementById('previewContent').textContent = messageContent;
    document.getElementById('previewCharCount').textContent = messageContent.length;
    document.getElementById('previewWordCount').textContent = messageContent.split(/\s+/).filter(word => word.length > 0).length;
    document.getElementById('previewLineCount').textContent = messageContent.split('\n').length;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('messagePreviewModal'));
    modal.show();
}
</script>

@endsection
