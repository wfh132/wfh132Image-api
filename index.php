<?php
// 文件路径常量
define('MOBILE_LINKS_FILE', 'pe.txt');   // 手机设备的链接文件
define('DESKTOP_LINKS_FILE', 'pc.txt');  // 电脑设备的链接文件

// 改进后的设备检测函数（基于 User-Agent 和浏览器特征）
function isMobile() {
    // 获取用户的 User-Agent 并转换为小写，以便进行统一的字符串匹配
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);

    // 定义常见移动设备的关键字
    $mobileAgents = [
        'android', 'iphone', 'ipod', 'blackberry', 'windows phone', 'opera mini', 'mobile', 
        'palm', 'symbian', 'webos', 'sony', 'nokia', 'samsung', 'htc', 'huawei', 'xiaomi', 
        'redmi', 'vivo', 'oppo', 'lenovo', 'zte', 'alcatel', 'meizu', 'oneplus', 'motorola', 
        'lg', 'asus', 'micromax', 'infinix', 'tecno', 'jio', 'kaios', 'generic feature phone'
    ];

    // 定义平板设备的关键字，用于区分平板和手机
    $tabletAgents = ['ipad', 'tablet', 'kindle', 'playbook', 'nexus 7', 'nexus 10', 'galaxy tab', 'nook'];

    // 定义常见移动浏览器的关键字
    $mobileBrowsers = ['mobile safari', 'mobile chrome', 'mobile ucbrowser', 'mobile qqbrowser', 'samsungbrowser'];

    // 检查是否为平板设备，优先排除平板设备
    foreach ($tabletAgents as $tablet) {
        if (strpos($userAgent, $tablet) !== false) {
            return false; // 如果检测到是平板设备，直接返回 false，视为非手机
        }
    }

    // 检查是否为手机设备
    foreach ($mobileAgents as $device) {
        if (strpos($userAgent, $device) !== false) {
            return true; // 如果匹配到手机设备的关键字，则返回 true
        }
    }

    // 如果未在设备关键字中找到，进一步检查是否是移动浏览器
    foreach ($mobileBrowsers as $browser) {
        if (strpos($userAgent, $browser) !== false) {
            return true; // 如果匹配到移动浏览器关键字，则视为手机设备
        }
    }

    // 如果没有任何匹配，则默认视为电脑设备
    return false;
}

// 读取链接文件内容并返回有效的链接列表
function getLinksFromFile($filePath) {
    if (file_exists($filePath)) {
        // 使用 file() 函数读取文件，并忽略空行
        $links = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!empty($links)) {
            return $links; // 返回有效的链接数组
        } else {
            logError("链接文件 '$filePath' 为空。");
            displayError("链接文件内容为空，请联系管理员。");
        }
    } else {
        logError("链接文件 '$filePath' 不存在。");
        displayError("链接文件不存在，请联系管理员。");
    }
    return [];
}

// 随机选择一个链接并重定向用户
function redirectToRandomLink($links) {
    $randomLink = $links[array_rand($links)]; // 从链接列表中随机选择一个
    if (filter_var($randomLink, FILTER_VALIDATE_URL)) {
        header("Location: $randomLink"); // 使用 header() 函数重定向用户
        exit();
    } else {
        logError("无效的链接：$randomLink");
        displayError("链接无效，请联系管理员。");
    }
}

// 记录错误信息到日志文件
function logError($message) {
    error_log($message, 3, 'error_log.txt'); // 将错误消息记录到 error_log.txt 文件
}

// 显示友好的错误信息给用户
function displayError($message) {
    echo "<p>发生错误：$message</p>"; // 在页面上显示错误消息
    exit();
}

// 根据检测到的设备类型选择合适的链接文件
$file = isMobile() ? MOBILE_LINKS_FILE : DESKTOP_LINKS_FILE;
$links = getLinksFromFile($file); // 获取相应链接文件中的链接列表

// 如果有有效的链接则执行跳转
if (!empty($links)) {
    redirectToRandomLink($links);
}
?>
