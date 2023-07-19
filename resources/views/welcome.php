<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>lite-view</title>
    <style>
        a {
            padding-left: 20px;
        }
    </style>
</head>
<body>

<div style="margin: auto;text-align: center;">
    <a href="db">数据库测试</a>
    <a href="javascript:log()">日志测试</a>
    <a href="group/exception">exception</a>
    <a href="group/error">error</a>
    <a href="group/test/curl">curl测试</a>
    <a href="group/test/render">视图render测试</a>
</div>

<script>
    function xhr(fd, url) {
        var request = new XMLHttpRequest();
        request.open('POST', url);

        request.onreadystatechange = function () {
            if (request.readyState === 4) {
                if (request.status === 200) {
                    let rsp = JSON.parse(request.responseText);
                    console.log(rsp);
                    alert(rsp.message);
                }
            }
        };
        request.send(fd);
    }

    function log() {
        var fd = new FormData();
        fd.append('msg', 'test log');
        xhr(fd, '/log');
    }
</script>
</body>
</html>