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
						<a href="/"><img src="/assets/img/target.png" height="95" border="0"/></a>
					</div>
					<div class="col-md-7">
						<?php if (!empty($queued)): ?>
						<div class="alert alert-warning" role="alert">
							<p>
							These packages were not found in our data and have been queued for fetching.
							As soon as we have them, you will too!<br/><br/>
							<?php foreach ($queued as $package) { echo '<b>'.$package.'</b><br/>'; } ?>
							</p>
							<p>
								You can keep up with their queue status
								<a href="/queue/<?php echo $hash; ?>">over on this page</a>.
							</p>
						</div>
						<?php endif; ?>
						<p>
							This feed will provide you with up-to-date information about the packages you currently
							have installed (based on the `composer.lock` you uploaded). As soon as a new version of the
							package is released, your feed will update!
						</p>
						<h2>Your Feed URL</h2>
						<p>
							Here's your hand-crafted feed URL, customized to your <code>composer.lock</code>
							contents:
						</p>
						<p>
							<a href="http://packagetrack.io/feed/<?php echo $hash; ?>">
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