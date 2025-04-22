<div class="fixed bottom-4 right-2 z-50" dir="rtl">

  <button 
    id="toggleButton"
    style="background-image: linear-gradient(to top, #0ba360 0%, #3cba92 100%);"
    class="text-white p-3 rounded-full shadow-lg hover:bg-blue-600 focus:outline-none"
    onclick="toggleMessageForm()">
    <i id="toggleIcon" class="bi bi-chat-dots"></i>
  </button>

  <!-- فرم ارسال پیام -->
  <div 
    id="messageForm" 
    class="hidden bg-white p-4 rounded-lg shadow-lg mt-2 w-72 transition-all duration-300 ease-in-out">
    <form method="POST" action="send_message.php">
      <textarea name="message" class="w-full h-20 p-2 border rounded-md focus:outline-none" placeholder="پیام خود را بنویسید..."></textarea>
      <input type="hidden" name="receiver_id" value="1"> 
      <button type="submit" class="bg-green-500 text-white mt-2 w-full px-4 py-2 rounded-md hover:bg-green-600 focus:outline-none">ارسال</button>
    </form>
  </div>
</div>

<script>
  function toggleMessageForm() {
    const form = document.getElementById('messageForm');
    const toggleIcon = document.getElementById('toggleIcon');
    form.classList.toggle('hidden');

    if (form.classList.contains('hidden')) {
      toggleIcon.classList.remove('bi-x-lg');
      toggleIcon.classList.add('bi-chat-dots');
    } else {
      toggleIcon.classList.remove('bi-chat-dots');
      toggleIcon.classList.add('bi-x-lg');
    }
  }

  function showAlert(message) {
    alert(message);
  }

  window.onload = function () {
    <?php if (isset($_SESSION['message_success'])): ?>
      showAlert("<?= $_SESSION['message_success']; ?>");
      <?php unset($_SESSION['message_success']); ?>
    <?php elseif (isset($_SESSION['message_error'])): ?>
      showAlert("<?= $_SESSION['message_error']; ?>");
      <?php unset($_SESSION['message_error']); ?>
    <?php endif; ?>
  };
</script>
