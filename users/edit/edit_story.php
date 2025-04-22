<?php
session_start();
include '../../bin/db.php';  
include '../../bin/classes.php';  


if (isset($_GET['story_id'])) {
    $storyId = $_GET['story_id'];


    $sql = "SELECT * FROM stories WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $storyId);
    $stmt->execute();
    $result = $stmt->get_result();
    $story = $result->fetch_assoc();

    if (!$story) {
        echo "داستان یافت نشد.";
        exit();
    }
} else {
    echo "شناسه داستان مشخص نیست.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // دریافت اطلاعات فرم
    $newTitle = $_POST['title'];
    $newStoryContent = $_POST['story_content'];


    $auth = new Auth($conn);
    $result = $auth->editStory($storyId, $newTitle, $newStoryContent);


    $_SESSION['message'] = $result;
    header("Location: ../list.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش داستان</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex p-2 justify-center min-h-screen">

    <form method="POST" class="w-full max-w-lg p-6 bg-white border border-gray-300 rounded-lg shadow-xl" dir="rtl">

        <div class="mb-6">
            <label for="title" class="block text-base font-medium text-gray-700 mb-2">عنوان داستان:</label>
            <input 
                type="text" 
                name="title" 
                id="title" 
                value="<?php echo htmlspecialchars($story['text_title']); ?>" 
                required 
                class="block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
        </div>


        <div class="mb-6">
            <label for="story_content" class="block text-base font-medium text-gray-700 mb-2">محتوای داستان:</label>
            <textarea 
                name="story_content" 
                id="story_content" 
                required 
                class="block w-full h-48 px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg shadow-sm resize-y focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            ><?php echo htmlspecialchars($story['story']); ?></textarea>
        </div>



        <div class="d-grid gap-2">
        <button 
            type="submit" 
            class="w-full px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 font-semibold text-lg rounded-lg shadow-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
        >
            ویرایش داستان
        </button>
  <a href="../list.php" class="btn btn-outline-danger">انصراف</a>
</div>

    </form>

</body>
</html>



