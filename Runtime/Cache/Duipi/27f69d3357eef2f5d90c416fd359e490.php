<?php if (!defined('THINK_PATH')) exit();?><html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>页面不存在哦亲</title>
        <style type="text/css">
            *{ padding: 0; margin: 0;}
            body{ text-align: center; padding-top:20px; }
        </style>
    </head>
    <body class="error_body">
        <a href="/"><img src="/Public/images/404.png" /></a>
        <script type="text/javascript">
            setTimeout(function () {
                location.href = '/index/index'
            }, 5000);
        </script>
    </body>
</html>