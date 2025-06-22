<?php
require_once '../includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if (!isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Post ID not provided']);
    exit;
}

$post_id = clean($_POST['post_id']);
$liked = toggleLike($post_id);
$likes_count = getPostLikes($post_id);

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'likes_count' => $likes_count
]);