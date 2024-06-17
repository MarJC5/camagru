<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notification</title>
<style>
    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
    .container { width: 80%; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
    h1 { color: #444; }
    p { margin: 10px 0; }
</style>
</head>
<body>
<div class="container">
    <h1>Camagru Notification</h1>
    <p>Hello {{user_name}},</p>
    <p>{{notification_body}}</p>
    <p>Click <a href="{{site_url}}">here</a> to view the post.</p>
    <p>Thank you for using Camagru.</p>
    <p>Regards,</p>
</div>
</body>
</html>