

<?php $__env->startSection('title', 'Messages'); ?>

<?php $__env->startSection('styles'); ?>
<style>
    .messages-container {
        display: flex;
        gap: 1.5rem;
        height: calc(100vh - 200px);
        min-height: 500px;
    }

    .users-list-container {
        width: 320px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .users-list-header {
        padding: 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    .users-list-header h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .users-list {
        flex: 1;
        overflow-y: auto;
        padding: 0.5rem;
    }

    .user-item {
        padding: 1rem;
        margin-bottom: 0.5rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid transparent;
    }

    .user-item:hover {
        background: #f3f4f6;
        border-color: #e5e7eb;
    }

    .user-item.active {
        background: #eff6ff;
        border-color: #3b82f6;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-weight: 500;
        color: #1f2937;
        font-size: 0.875rem;
        margin-bottom: 0.125rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-email {
        font-size: 0.75rem;
        color: #6b7280;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chat-container {
        flex: 1;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .chat-header {
        padding: 1.25rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    .chat-header h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .empty-state {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 1rem;
        color: #6b7280;
    }

    .empty-state svg {
        width: 64px;
        height: 64px;
        opacity: 0.3;
        fill: currentColor;
    }

    .empty-state-text {
        font-size: 1.125rem;
        font-weight: 500;
    }

    .message-bubble {
        max-width: 70%;
        padding: 0.875rem 1.125rem;
        border-radius: 12px;
        word-wrap: break-word;
    }

    .message-sent {
        align-self: flex-end;
        background: #3b82f6;
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message-received {
        align-self: flex-start;
        background: #f3f4f6;
        color: #1f2937;
        border-bottom-left-radius: 4px;
    }

    .message-text {
        margin: 0;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .message-time {
        font-size: 0.7rem;
        opacity: 0.7;
        margin-top: 0.25rem;
    }

    .chat-input-container {
        padding: 1.25rem;
        border-top: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    .chat-input-form {
        display: flex;
        gap: 0.75rem;
    }

    .chat-input {
        flex: 1;
        padding: 0.75rem 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.875rem;
        font-family: inherit;
        resize: none;
        min-height: 44px;
        max-height: 120px;
    }

    .chat-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .send-button {
        padding: 0.75rem 1.5rem;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .send-button:hover {
        background: #2563eb;
    }

    .send-button:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .send-button svg {
        width: 16px;
        height: 16px;
        fill: currentColor;
    }

    /* Scrollbar Styling */
    .users-list::-webkit-scrollbar,
    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .users-list::-webkit-scrollbar-track,
    .chat-messages::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 3px;
    }

    .users-list::-webkit-scrollbar-thumb,
    .chat-messages::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    .users-list::-webkit-scrollbar-thumb:hover,
    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    @media (max-width: 768px) {
        .messages-container {
            flex-direction: column;
            height: auto;
        }

        .users-list-container {
            width: 100%;
            max-height: 300px;
        }

        .chat-container {
            min-height: 400px;
        }
    }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="content-header">
    <h1 class="content-title">Messages</h1>
    <p class="content-subtitle">Communication and notifications center</p>
</div>

<div class="messages-container">
    <!-- Users List -->
    <div class="users-list-container">
        <div class="users-list-header">
            <h3>Users</h3>
        </div>
        <div class="users-list" id="usersList">
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="user-item" data-user-id="<?php echo e($user->id); ?>" data-user-name="<?php echo e(addslashes($user->firstname)); ?> <?php echo e(addslashes($user->lastname)); ?>">
                    <div class="user-avatar">
                        <?php echo e(strtoupper(substr($user->firstname, 0, 1))); ?><?php echo e(strtoupper(substr($user->lastname, 0, 1))); ?>

                    </div>
                    <div class="user-info">
                        <div class="user-name"><?php echo e($user->firstname); ?> <?php echo e($user->lastname); ?></div>
                        <div class="user-email"><?php echo e($user->email); ?></div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state">
                    <p>No users found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Chat Container -->
    <div class="chat-container">
        <div class="chat-header" id="chatHeader">
            <h3 id="chatHeaderTitle">Select a chat to proceed</h3>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="empty-state">
                <svg viewBox="0 0 24 24">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
                <p class="empty-state-text">Select a chat to proceed</p>
            </div>
        </div>
        <div class="chat-input-container" id="chatInputContainer" style="display: none;">
            <form class="chat-input-form" id="messageForm">
                <textarea 
                    class="chat-input" 
                    id="messageInput" 
                    placeholder="Type a message..." 
                    rows="1"
                    required
                ></textarea>
                <button type="submit" class="send-button" id="sendButton">
                    <svg viewBox="0 0 24 24">
                        <line x1="22" y1="2" x2="11" y2="13"/>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    Send
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Hidden data for JavaScript -->
<div id="app-data" 
     data-current-admin-id="<?php echo e(auth()->id()); ?>" 
     style="display: none;">
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
    let currentUserId = null;
    let currentUserName = '';
    let messageCheckInterval = null;

    // Get data from HTML attributes
    const appData = {
        currentAdminId: parseInt(document.getElementById('app-data').dataset.currentAdminId)
    };

    document.addEventListener('DOMContentLoaded', function() {
        // Add click event listeners to user items
        document.querySelectorAll('.user-item').forEach(item => {
            item.addEventListener('click', function() {
                const userId = parseInt(this.dataset.userId);
                const userName = this.dataset.userName;
                selectUser(userId, userName);
            });
        });

        // Add submit event listener to message form
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage(e);
        });

        // Auto-resize textarea
        const messageInput = document.getElementById('messageInput');
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Allow Enter to send, Shift+Enter for new line
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('messageForm').dispatchEvent(new Event('submit'));
            }
        });
    });

    function selectUser(userId, userName) {
        currentUserId = userId;
        currentUserName = userName;

        // Update active state
        document.querySelectorAll('.user-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-user-id="${userId}"]`).classList.add('active');

        // Update chat header
        document.getElementById('chatHeaderTitle').textContent = userName;

        // Show input container
        document.getElementById('chatInputContainer').style.display = 'block';

        // Load conversation
        loadConversation(userId);

        // Set up auto-refresh
        if (messageCheckInterval) {
            clearInterval(messageCheckInterval);
        }
        messageCheckInterval = setInterval(() => loadConversation(userId), 3000);
    }

    function loadConversation(userId) {
        fetch(`/messages/conversation/${userId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMessages(data.messages);
            }
        })
        .catch(error => {
            console.error('Error loading conversation:', error);
        });
    }

    function displayMessages(messages) {
        const chatMessages = document.getElementById('chatMessages');
        const currentAdminId = appData.currentAdminId;

        if (messages.length === 0) {
            chatMessages.innerHTML = `
                <div class="empty-state">
                    <svg viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <p class="empty-state-text">No messages yet. Start the conversation!</p>
                </div>
            `;
            return;
        }

        chatMessages.innerHTML = messages.map(msg => {
            const isSent = msg.sender_id === currentAdminId;
            const messageClass = isSent ? 'message-sent' : 'message-received';
            
            // Format time in Philippine time
            let time;
            try {
                time = new Date(msg.sent_at).toLocaleString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true,
                    month: 'short',
                    day: 'numeric',
                    timeZone: 'Asia/Manila'
                });
            } catch (e) {
                // Fallback if timeZone is not supported
                time = new Date(msg.sent_at).toLocaleString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true,
                    month: 'short',
                    day: 'numeric'
                });
            }

            return `
                <div class="message-bubble ${messageClass}">
                    <p class="message-text">${escapeHtml(msg.message)}</p>
                    <div class="message-time">${time}</div>
                </div>
            `;
        }).join('');

        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function sendMessage(event) {
        event.preventDefault();

        if (!currentUserId) {
            return;
        }

        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();

        if (!message) {
            return;
        }

        const sendButton = document.getElementById('sendButton');
        sendButton.disabled = true;

        fetch('/messages/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                receiver_id: currentUserId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                messageInput.style.height = 'auto';
                loadConversation(currentUserId);
            }
        })
        .catch(error => {
            console.error('Error sending message:', error);
            alert('Failed to send message. Please try again.');
        })
        .finally(() => {
            sendButton.disabled = false;
            messageInput.focus();
        });
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Clean up interval on page unload
    window.addEventListener('beforeunload', function() {
        if (messageCheckInterval) {
            clearInterval(messageCheckInterval);
        }
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Codes\Backups\AlertDavao2.0\AdminSide\admin\resources\views/messages.blade.php ENDPATH**/ ?>