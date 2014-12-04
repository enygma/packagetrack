<?php
$errorMsg = '<font color="red">There was a problem with your entry</font>';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>PackageTrack: Composer Package Tracking</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="/assets/css/site.css"/>
        <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    </head>
    <script type="text/javascript">
    $(function(){
        $('#find').click(function(e) {
            e.preventDefault();
            var results = '';
            var packageName = $('#package-name').val();

            $.ajax({
                url: '/usage',
                data: { name: packageName },
                dataType: 'json',
                type: 'POST',
                success: function(data) {
                    results = 'The <b>'+packageName+'</b> package is being used in <b>'+data.count+' feeds</b>.<br/><br/>';
                    $('#find-results').html(results);
                }
            });
        });
    });
    </script>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-3" style="text-align:right">
                    <br/>
                    <a href="/"><img src="/assets/img/target.png" height="95" border="0"/></a>
                </div>
                <div class="col-md-6">
                    <br/>
                    <form role="form" action="/user/register" class="form-horizontal" method="POST">
                        <div class="form-group">
                            <label>Username:</label>
                            <input type="text" class="form-control" name="username" id="username" value=""/>
                            <?php if (isset($errors['username'])) { echo $errorMsg; } ?>
                        </div>
                        <div class="form-group">
                            <label>Password:</label>
                            <input type="password" class="form-control" name="password" id="password" value=""/>
                            <?php if (isset($errors['password'])) { echo $errorMsg; } ?>
                        </div>
                        <div class="form-group">
                            <label>Email address:</label>
                            <input type="text" class="form-control" name="email" id="email" value=""/>
                            <?php if (isset($errors['email'])) { echo $errorMsg; } ?>
                        </div>
                        <button class="btn btn-primary" id="signup">Signup</button>
                    </form>
                    <br/>
                </div>
            </div>
        </div>
    </body>
    <footer class="default-footer">
        <a href="mailto:ccornutt@phpdeveloper.org" style="color:#FFFFFF">Contact Us</a>
    </footer>
</html>