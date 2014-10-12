<html>
	<head>
		<title>PackageTrack: Composer Package Tracking</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
		<link rel="stylesheet" href="/assets/css/site.css"/>
		<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
	</head>
	<script type="text/javascript">
	$(function(){
    	$('#upload-select').click(function(e){
    		e.preventDefault();
        	$('#composerlock').click();
    	});
    	$('#composerlock').change(function(e) {
    		$('#upload-composer').prop('disabled', false);

    		var filename = $('#composerlock').val();
    		if (filename.indexOf('composer.lock') > -1) {
    			$('#file-selected-good').css('display', 'block');
    			$('#file-selected-bad').css('display', 'none');
    		} else {
    			$('#file-selected-good').css('display', 'none');
    			$('#file-selected-bad').css('display', 'block');
    		}
    	});
	});
	</script>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-md-3"></div>
				<div class="col-md-6">
					<br/>
					<div class="jumbotron">
					<h2 class="header">Stay Up to Date with PackageTrack!</h2><br/>
					<a href="/"><img src="/assets/img/target.png" height="100" class="index-target-img" border="0"/></a>
					<p>
						<b>PackageTrack</b> helps you keep track of the latest updates to the
						<a href="http://packagist.org">Composer</a> packages you use via a handy RSS feed
						based on your <i>composer.lock</i> file.
					</p>
					<center>
						<form action="/upload" method="POST" enctype="multipart/form-data">
							<input type="file" style="display:none" id="composerlock" name="composerlock"/><br/>
							<button class="btn" id="upload-select" name="upload-select">Select File</button>
							<button class="btn btn-success" id="upload-composer" name="upload-composer" disabled="disabled">Upload</button>
						</form>
						<div id="file-selected-good" style="display:none">
							<img src="/assets/img/green-checkmark.gif" height="20"/>
							<b style="color:#669666">Lock file selected, party on Wayne!</b>
						</div>
						<div id="file-selected-bad" style="display:none">
							<img src="/assets/img/red-x-icon.png" height="20"/>
							<b style="color:#FF0000">Bummer, looks like that's not right</b>
						</div>
					</center>
					</div>
					<p>
						Creating the feed is easy - just upload your lock file and a unique RSS URL
						will be generated for you, sharing the latest package updates as they roll in.
					</p>
				<center>

				</center>
				<p>
					<b>NOTE:</b> This service only works for packages with actual releases, not for ones that only pull
					from <code>dev-master</code>. If there's a repository you're currently using that doesn't have tagged releases,
					send the maintainer an email and let them know to start!
				</p>
				<p>
					For security reasons, the contents of your <code>composer.lock</code> file will not be stored. They are only used to
					generate the unique hash representing your installed packages.
				</p>
				</div>
				<div class="col-md-3"></div>
			</div>
		</div>
		<br/>
	</body>
	<footer class="default-footer">
		<a href="mailto:ccornutt@phpdeveloper.org" style="color:#FFFFFF">Contact Us</a>
	</footer>
</html>