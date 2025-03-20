<?php
$pageTitle = "Messages";
require_once 'includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ' . SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get user ID
$userId = $_SESSION['user_id'];

// Get conversation partner ID from URL
$partnerId = isset($_GET['user']) ? intval($_GET['user']) : null;

// Get provider ID from URL (for new conversations)
$providerId = isset($_GET['provider']) ? intval($_GET['provider']) : null;

if ($providerId && !$partnerId) {
    // Get provider user ID
    $provider = $db->selectOne("SELECT user_id FROM providers WHERE id = ?", [$providerId]);
    if ($provider) {
        $partnerId = $provider['user_id'];
    }
}

// Get all conversations
$conversations = $db->select("
    SELECT 
        u.id as user_id,
        u.first_name,
        u.last_name,
        u.user_type,
        p.id as provider_id,
        p.business_name,
        m.message,
        m.created_at,
        m.sender_id,
        m.receiver_id,
        COUNT(unread.id) as unread_count
    FROM (
        SELECT 
            CASE 
                WHEN sender_id = ? THEN receiver_id
                ELSE sender_id
            END as partner_id,
            MAX(id) as latest_message_id
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
        GROUP BY partner_id
    ) latest
    JOIN messages m ON m.id = latest.latest_message_id
    JOIN users u ON u.id = latest.partner_id
    LEFT JOIN providers p ON u.user_type = 'provider' AND p.user_id = u.id
    LEFT JOIN messages unread ON unread.sender_id = latest.partner_id AND unread.receiver_id = ? AND unread.is_read = 0
    GROUP BY latest.partner_id
    ORDER BY m.created_at DESC
", [$userId, $userId, $userId, $userId]);

// If no partner selected, select the first conversation
if (!$partnerId && !empty($conversations)) {
    $partnerId = $conversations[0]['user_id'];
}

// Get partner info if partner is selected
$partner = null;
if ($partnerId) {
    $partner = $db->selectOne("
        SELECT u.*, p.id as provider_id, p.business_name
        FROM users u
        LEFT JOIN providers p ON u.user_type = 'provider' AND p.user_id = u.id
        WHERE u.id = ?
    ", [$partnerId]);
}

// Get messages for selected conversation
$messages = [];
if ($partnerId) {
    $messages = $db->select("
        SELECT m.*, u.first_name, u.last_name, u.user_type, p.business_name
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        LEFT JOIN providers p ON u.user_type = 'provider' AND p.user_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.created_at ASC
    ", [$userId, $partnerId, $partnerId, $userId]);
    
    // Mark messages as read
    $db->update('messages', ['is_read' => 1], 'sender_id = ? AND receiver_id = ? AND is_read = 0', [$partnerId, $userId]);
}

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $partnerId) {
    $message = sanitizeInput($_POST['message']);
    
    if (!empty($message)) {
        // Insert message
        $messageId = $db->insert('messages', [
            'sender_id' => $userId,
            'receiver_id' => $partnerId,
            'message' => $message,
            'is_read' => 0
        ]);
        
        if ($messageId) {
            // Success! Refresh the page to show the new message
            header('Location: ' . SITE_URL . '/messages.php?user=' . $partnerId);
            exit;
        } else {
            $error = "Failed to send message. Please try again.";
        }
    } else {
        $error = "Please enter a message.";
    }
}
?>

<div class="container">
    <div class="messages-container">
        <div class="conversations-list">
            <div class="conversations-header">
                <h2>Messages</h2>
            </div>
            
            <?php if (empty($conversations)): ?>
            <div class="empty-conversations">
                <p>No conversations yet.</p>
                <a href="<?php echo SITE_URL; ?>/listings.php" class="btn btn-outline btn-sm">Find Service Providers</a>
            </div>
            <?php else: ?>
            <div class="conversations">
                <?php foreach ($conversations as $conversation): ?>
                <a href="?user=<?php echo $conversation['user_id']; ?>" class="conversation-item <?php echo $partnerId == $conversation['user_id'] ? 'active' : ''; ?>">
                    <div class="conversation-avatar">
                        <?php if ($conversation['user_type'] === 'provider'): ?>
                        <span class="avatar-text"><?php echo substr($conversation['business_name'] ?: ($conversation['first_name'] . ' ' . $conversation['last_name']), 0, 1); ?></span>
                        <?php else: ?>
                        <span class="avatar-text"><?php echo substr($conversation['first_name'] . ' ' . $conversation['last_name'], 0, 1); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="conversation-info">
                        <div class="conversation-header">
                            <h3>
                                <?php if ($conversation['user_type'] === 'provider' && $conversation['business_name']): ?>
                                <?php echo $conversation['business_name']; ?>
                                <?php else: ?>
                                <?php echo $conversation['first_name'] . ' ' . $conversation['last_name']; ?>
                                <?php endif; ?>
                            </h3>
                            <span class="conversation-time"><?php echo getTimeAgo($conversation['created_at']); ?></span>
                        </div>
                        <div class="conversation-preview">
                            <?php if ($conversation['sender_id'] == $userId): ?>
                            <span class="sent-indicator">You:</span>
                            <?php endif; ?>
                            <span><?php echo substr($conversation['message'], 0, 40); ?><?php echo strlen($conversation['message']) > 40 ? '...' : ''; ?></span>
                            <?php if ($conversation['unread_count'] > 0): ?>
                            <span class="unread-badge"><?php echo $conversation['unread_count']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="messages-content">
            <?php if ($partner): ?>
            <div class="messages-header">
                <div class="partner-info">
                    <div class="partner-avatar">
                        <?php if ($partner['user_type'] === 'provider'): ?>
                        <span class="avatar-text"><?php echo substr($partner['business_name'] ?: ($partner['first_name'] . ' ' . $partner['last_name']), 0, 1); ?></span>
                        <?php else: ?>
                        <span class="avatar-text"><?php echo substr($partner['first_name'] . ' ' . $partner['last_name'], 0, 1); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="partner-details">
                        <h2>
                            <?php if ($partner['user_type'] === 'provider' && $partner['business_name']): ?>
                            <?php echo $partner['business_name']; ?>
                            <?php else: ?>
                            <?php echo $partner['first_name'] . ' ' . $partner['last_name']; ?>
                            <?php endif; ?>
                        </h2>
                        <?php if ($partner['user_type'] === 'provider'): ?>
                        <a href="<?php echo SITE_URL; ?>/provider-profile.php?id=<?php echo $partner['provider_id']; ?>" class="view-profile-link">View Profile</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="messages-list" id="messages-list">
                <?php foreach ($messages as $message): ?>
                <div class="message-item <?php echo $message['sender_id'] == $userId ? 'sent' : 'received'; ?>">
                    <div class="message-content">
                        <p><?php echo nl2br($message['message']); ?></p>
                        <span class="message-time"><?php echo formatTime($message['created_at']); ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="message-form">
                <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="message-input">
                        <textarea name="message" placeholder="Type a message..." required></textarea>
                        <button type="submit" class="send-button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="no-conversation-selected">
                <div class="empty-state">
                    <i class="fas fa-comment-alt"></i>
                    <h2>No conversation selected</h2>
                    <p>Select a conversation from the list to start chatting</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to bottom of messages list
    const messagesList = document.getElementById('messages-list');
    if (messagesList) {
        messagesList.scrollTop = messagesList.scrollHeight;
    }
    
    // Auto-resize textarea
    const textarea = document.querySelector('.message-input textarea');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

