<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Password reset request</title>
<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
    .container { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    .button { background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; display: inline-block; font-size: 16px; margin: 10px 0; cursor: pointer; border-radius: 5px; text-decoration: none; }
</style>
</head>
<body>
<div class="container">
    <h1>Camagru Password Reset</h1>
    <p>Hello {{user_name}},</p>
    <p>{{notification_body}}</p>
    <p>Click the link below to reset your password:</p>
    <p><a href="{{reset_link}}" class="button">Reset Password</a></p>
    <p>Thank you for using Camagru.</p>
    <p>Regards,</p>
</div>
</body>
</html>