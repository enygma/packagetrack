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
						<?php if(!empty($items)): ?>
							<div class="alert alert-warning" role="alert">
							<p>
								The following packages for this feed are still in the queue:
							</p>
							<?php
							foreach ($items as $item) {
								echo '<b>'.$item['package_name'].'</b><br/>';
							}
							?>
							</div>
						<?php else: ?>
							<div class="alert alert-success">
								<p>
									There are no outstanding packages in this feed's queue - <b>rock on!</b>
								</p>
							</div>
						<?php endif; ?>
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