<?php
// fetch.php
 
// 定义用于保存时间戳的文件路径
$timestampFile = '/tmp/api_call_timestamp.txt';
$cooldownTime = 60; // 冷却时间（秒）
 
// 当前时间戳
$currentTime = time();
 
// 检查文件是否存在以及是否在冷却中
if (file_exists($timestampFile)) {
    $lastCalled = file_get_contents($timestampFile);
    $timeElapsed = $currentTime - (int)$lastCalled;
     
    if ($timeElapsed < $cooldownTime) {
        $remainingTime = $cooldownTime - $timeElapsed;
        exit(json_encode(['code' => 1, 'message' => '请求过于频繁，请稍后再试', 'cooldown' => $remainingTime]));
    }
}
 
// 执行外部 API 调用
$apiUrl = 'https://sctapi.ftqq.com/<sendkey>.send?title=挪车通知&desp=有人扫码通知你挪车啦';  //<sendkey>请自行为自己的SCT开头的密钥
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
 
if ($response === false) {
    echo json_encode(['code' => 1, 'message' => '请求失败: ' . curl_error($ch)]);
} else {
    // 保存当前时间戳到文件中
    file_put_contents($timestampFile, $currentTime);
    echo $response;
}
 
curl_close($ch);
?>
