<?php
include 'db.php'; 

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'];
$user_id = $data['user_id'];
$item_id = $data['item_id'];
$item_type = $data['item_type'];

if (in_array($action, ['like', 'save'])) {
    $reactionType = $action; 
    
    // بررسی وجود واکنش
    $checkSql = "SELECT * FROM user_reactions WHERE user_id = ? AND item_id = ? AND item_type = ? AND reaction_type = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('iiss', $user_id, $item_id, $item_type, $reactionType);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // اگر واکنش قبلاً ثبت شده، آن را حذف می‌کنیم
        $deleteSql = "DELETE FROM user_reactions WHERE user_id = ? AND item_id = ? AND item_type = ? AND reaction_type = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param('iiss', $user_id, $item_id, $item_type, $reactionType);
        $deleteStmt->execute();


    } else {
        // اگر واکنش ثبت نشده، آن را اضافه می‌کنیم
        $insertSql = "INSERT INTO user_reactions (user_id, item_id, item_type, reaction_type) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param('iiss', $user_id, $item_id, $item_type, $reactionType);
        $insertStmt->execute();
    }

    // دریافت تعداد واکنش‌ها (لایک یا ذخیره) برای این آیتم
    $countSql = "SELECT COUNT(*) AS count FROM user_reactions WHERE item_id = ? AND item_type = ? AND reaction_type = ?";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param('iss', $item_id, $item_type, $reactionType);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $count = $countResult->fetch_assoc()['count'];

    //  JSON
    echo json_encode(['success' => true, "new_{$reactionType}_count" => $count]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
?>
