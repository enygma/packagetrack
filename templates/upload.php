<html>
	<head>
		<title>PackageTrack: Composer Package Tracking</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="/assets/css/site.css"/>
		<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	</head>
	<body>
		<div class="container">
			<br/>
			<div class="col-md-3"></div>
			<div class="col-md-6">
				<div class="row">
					<div class="col-md-3">
						<br/>
						<img src="/assets/img/target.png" height="95"/>
					</div>
					<div class="col-md-7">
						<h2>Your Feed URL</h2>
						<p>
							Here's your hand-crafted feed URL, customized to your <code>composer.lock</code>
							contents:
						</p>
						<p>
							<a href="http://packagetracker.io/feed/<?php echo $hash; ?>">
								http://packagetrack.io/feed/<?php echo $hash; ?>
							</a>
						</p>
					</div>
				</div>
				<br/>
			</div>
			<div class="col-md-3"></div>
		</div>
	</body>
	<footer class="default-footer">
		<a href="/" style="color:#FFFFFF">Take me home</a>
	</footer>
</html>