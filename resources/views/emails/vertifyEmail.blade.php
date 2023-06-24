<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>Email Verification</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			background-color: #f4f4f4;
			padding: 20px;
		}

		.container {
			max-width: 600px;
			margin: 0 auto;
			background-color: #ffffff;
			padding: 20px;
			border-radius: 5px;
		}

		h1 {
			color: #333333;
		}

		p {
			color: #555555;
		}

		.verification-code {
			display: block;
			font-size: 49px;
			text-align: center;
			color: #0066cc;
			padding: 10px;
			border: 2px solid #0066cc;
			border-radius: 5px;
			margin-top: 10px;
			margin-bottom: 20px;
		}
	</style>
</head>

<body>
	<div class="container">
		<h1>Email Verification</h1>
		<p>Dear {{ $user->username }},</p>
		<p>Thank you for signing up.<br />To complete your registration, please verify your email address by entering the
			verification code below:</p>
		<div class="verification-code">{{ $user->email_verification_code }}</div>
		<p>If you didn't sign up for this service, you can safely ignore this email.</p>
		<p>Thank you,</p>
		<p>The Team, UniVibe</p>
	</div>
</body>

</html>
