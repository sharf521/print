<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>layim - layui</title>
</head>
<body>
<link rel="stylesheet" href="/plugin/layui/css/layui.css">
<link rel="stylesheet" href="/themes/chat/chat.css">
<script src="/themes/chat/js/swfobject.js"></script>
<script src="/themes/chat/js/web_socket.js"></script>
<script src="/themes/chat/js/jquery.min.js"></script>
<script src="/plugin/layui/layui.js"></script>
<?php
$user=new \App\Model\User();
$user=$user->findOrFail($_GET['id']);
?>
<script type="text/javascript">
    userinfo = {};
    userinfo['id'] = '<?=$user->id?>';
    userinfo['avatar'] = '<?=$user->headimgurl?>';
    userinfo['sign'] = 'sign';
    userinfo['username'] = '<?=$user->nickname?>';
</script>
<script src="/themes/chat/chat.js"></script>
</body>
</html>