<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin:0; padding:0;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background: #ffffff; margin: 40px 0; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background-color: #FF750F; color: white; text-align: center; padding: 20px 0; font-size: 24px;">
                            EasyBondi
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="color: #333;">Hello {{ $name }},</h2>
                            <p style="color: #555; font-size: 16px; line-height: 1.5;">
                                We received a request to reset your EasyBondi account password.
                                Click the button below to reset it. This link will expire in 60 minutes.
                            </p>

                            <p style="text-align: center; margin: 30px 0;">
                                <a href="{{ $url }}" style="background-color: #FF750F; color: #fff; text-decoration: none; padding: 12px 30px; border-radius: 5px; font-weight: bold;">Reset Password</a>
                            </p>

                            <p style="color: #555; font-size: 14px;">
                                If you did not request a password reset, you can safely ignore this email.
                            </p>

                            <p style="color: #555; font-size: 14px;">— The EasyBondi Team</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f5f5f5; color: #999; text-align: center; font-size: 12px; padding: 15px;">
                            EasyBondi &copy; {{ date('Y') }}. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
