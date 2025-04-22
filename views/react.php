
    <div class="flex items-center justify-end">
        <form class="flex flex-col space-y-3 p-2 mb-0 text-white">
 
            <button class="reaction-like" type="button" data-item-id="<?php echo $row['user_id']; ?>" data-item-type="book">
                <i class="bi bi-heart-fill"></i> 
                <span class="like-count">
                    <?php
                    // دریافت تعداد لایک‌ها از دیتابیس
                    $likeSql = "SELECT COUNT(*) AS like_count FROM user_reactions WHERE item_id = ? AND item_type = 'book' AND reaction_type = 'like'";
                    $likeStmt = $conn->prepare($likeSql);
                    $likeStmt->bind_param('i', $row['user_id']);
                    $likeStmt->execute();
                    $likeResult = $likeStmt->get_result();
                    echo $likeResult->fetch_assoc()['like_count'];
                    ?>
                </span>
            </button>
            

            <button class="reaction-save" type="button" data-item-id="<?php echo $row['user_id']; ?>" data-item-type="book">
                <i class="bi bi-bookmark-fill"></i> 
                <span class="save-count">
                    <?php
                    // دریافت تعداد سیو‌ها از دیتابیس
                    $saveSql = "SELECT COUNT(*) AS save_count FROM user_reactions WHERE item_id = ? AND item_type = 'book' AND reaction_type = 'save'";
                    $saveStmt = $conn->prepare($saveSql);
                    $saveStmt->bind_param('i', $row['user_id']);
                    $saveStmt->execute();
                    $saveResult = $saveStmt->get_result();
                    echo $saveResult->fetch_assoc()['save_count'];
                    ?>
                </span>
            </button>
        </form>
    </div>



