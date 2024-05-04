<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luyện thi GoUni - Lấy lại mật khẩu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .logo img {
            max-width: 200px;
            height: auto;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        p {
            margin-bottom: 15px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://cdn.discordapp.com/attachments/1100753623849377835/1226526118157352960/logo.png?ex=66251676&is=6612a176&hm=7c0991e55081e469401566a0387e3bb43e24d416ed5e8fce51d3ef840ccea672&" alt="Luyện thi GoUni">
        </div>
        <h1>Lấy lại mật khẩu</h1>
        <p>Xin chào,</p>
        <p>Chúng tôi đã nhận được yêu cầu lấy lại mật khẩu của bạn.</p>
        <p>Hãy click vào <a href="{{ env('FORGOT_PASSWORD_CALLBACK') . '?reset_token=' . $token . '&email=' . $email }}" target="_blank">đây</a> để tiến hành đổi mật khẩu.</p>
        <p>Trân trọng!</p>
        <p>{{ env('MAIL_FROM_NAME') }}</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} Luyện thi GoUni. All rights reserved.</p>
    </div>
</body>
</html>
