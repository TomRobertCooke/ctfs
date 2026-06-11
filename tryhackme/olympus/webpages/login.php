HTTP/1.1 302 Found
Date: Tue, 02 Jun 2026 03:34:17 GMT
Server: Apache/2.4.41 (Ubuntu)
Location: login.php
Content-Length: 0
Content-Type: text/html; charset=UTF-8

HTTP/1.1 200 OK
Date: Tue, 02 Jun 2026 03:34:17 GMT
Server: Apache/2.4.41 (Ubuntu)
Set-Cookie: PHPSESSID=ig77mgn1aa01kbuiud6nm4qiog; path=/
Expires: Thu, 19 Nov 1981 08:52:00 GMT
Cache-Control: no-store, no-cache, must-revalidate
Pragma: no-cache
Vary: Accept-Encoding
Content-Length: 1577
Content-Type: text/html; charset=UTF-8


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="http://chat.olympus.thm/static/style.css" rel="stylesheet">
    <link href="http://chat.olympus.thm/static/normalize.css" rel="stylesheet">
    <style>
        body{ font: 14px sans-serif; text-align: center; align: center;}
        .wrapper{ width: 360px; padding: 20px; text-align: center;}
    </style>
</head>
<body>
  <div id="content" align="center">
    <div class="wrapper" style="text-align: center; margin-top: 20px;">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>

        
        <form action="/login.php" method="post">
            <div class="form-group" style="text-align: center; ">
                <label>Username</label>
                <input type="text" name="username" class="form-control " value="">
                <span class="invalid-feedback"></span>
            </div>
            <div class="form-group" style="text-align: center; ">
                <label>Password</label>
                <input type="password" name="password" class="form-control ">
                <span class="invalid-feedback"></span>
            </div>
            <div class="form-group" style="text-align: center; ">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>
  </div>
</body>
</html>
chat.olympus.thm
