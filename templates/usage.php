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
                    <h2>Package Use</h2>
                    <p>
                        Interested in seeing how Packagetrack.io users are using your package? Enter the name below
                        to find out.
                    </p>
                    <p>
                        <b>Example:</b> symfony/event-dispatcher
                    </p>
                    <br/>
                    <form role="form" class="form-inline">
                        <div class="form-group">
                            <label>Package Name:</label>
                            <input type="text" class="form-control" id="package-name" name="package_name"/>
                        </div>
                        <button class="btn btn-primary" id="find">Find</button>
                    </form>
                    <br/>
                    <div id="find-results"></div>
                    <br/>
                    <div>
                        <h3>Popular Packages</h3>
                        <table class="table table-striped">
                        <thead>
                            <th>Package Name</th>
                            <th style="text-align:center">Usage Count</th>
                        </thead>
                        <?php
                        foreach ($packages as $package) {
                            echo '<tr><td>'.$package['name'].'</td><td style="text-align:center">'.$package['cid'].'</td></tr>';
                        }
                        ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
    <footer class="default-footer">
        <a href="mailto:ccornutt@phpdeveloper.org" style="color:#FFFFFF">Contact Us</a>
    </footer>
</html>