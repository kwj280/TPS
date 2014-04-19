<?php
    include("../TPSBIN/functions.php");
    include("../TPSBIN/db_connect.php");

    // Establish Session
    sec_session_start();

    // GET data
?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Login for TPS Radio System">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="James Oliver">
        <!-- Latest compiled and minified CSS -->
        <!-- HOSTED -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

        <!-- Optional theme -->
        <!-- HOSTED -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
        <!--<link href=\"js/css/bootstrap.min.css\" rel=\"stylesheet\">-->

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src=\"https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js\"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script>
            function submitted() {
                $("#input").hide();
                $("#processing").show();
            }
        </script>
    </head>
    <body role="document" style="">
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php echo $_SESSION['BASE_REF'];?>/">TPS Radio System</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="<?php echo $_SESSION['BASE_REF'];?>/">Home</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Host / Program<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">DJ / Hosts</li>
                <li><a href="#">New User</a></li>
                <li><a href="#">Edit User</a></li>
                <li><a href="#">Remove User</a></li>
                <li class="divider"></li>
                <li class="dropdown-header">Programs</li>
                <li><a href="#">New Program</a></li>
                <li><a href="#">Edit Program</a></li>
                <li><a href="#">Edit Logs</a></li>
              </ul>
            </li>
            <li><a href="/TPSlogin">Login</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
    <div class="container" style="margin-top: 30px">
        <div class="page-header">Library Upload</div>
            <div class="ui-state-highlight">
                <div id="input" class="panel panel-success">
                    <div class="panel-heading">File To Upload</div>
                    <div class="panel-body"><form action="upload_playlist.php" method="post" onsubmit="submitted();"
                        enctype="multipart/form-data">
                            <fieldset title="XLS / XLSX Library Update">
                                <label for="file">Filename:</label>
                                <input type="file" name="file" id="file" required><br>
                                <fieldset title="Range">
                                    <label for="start">Start Row</label>
                                    <input type="number" name="START" step="1" min="1" id="start"><br>
                                    <label for="end">End Row (blank for all)</label>
                                    <input type="number" name="END" step="1" min="1" id="end"><br>
                                </fieldset>
                                <input type="submit" name="submit" value="Submit">
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div id="processing" class="panel panel-warning" style="display: none;">
                    <div class="panel-heading">Processing Please Wait...</div>
                    <div class="panel-body">
                        <div ><progress></progress></div>
                    </div>
                </div>
                <span>
                    RUNTIME: 0:00
                </span>
                <div class="progress">
                    <!--<div class="progress-bar progress-bar-success" style="width: <?php echo round(($_SESSION['COMPLETE']/$_SESSION['TOTAL'])*100,2);?>%"><span class="sr-only"><?php echo ($_SESSION['COMPLETE']/$_SESSION['TOTAL'])*100;?>% Complete (success)</span></div>
                    <div class="progress-bar progress-bar-success" style="width: <?php echo round(($_SESSION['DUPLICATE_COUNT']/$_SESSION['TOTAL'])*100,2);?>%"><span class="sr-only"><?php echo ($_SESSION['DUPLICATE_COUNT']/$_SESSION['TOTAL'])*100;?>% Duplicate (Omitted)</span></div>
                    <div class="progress-bar progress-bar-success" style="width: <?php echo round(($_SESSION['ERROR_COUNT']/$_SESSION['TOTAL'])*100,2);?>%"><span class="sr-only"><?php echo ($_SESSION['ERROR_COUNT']/$_SESSION['TOTAL'])*100;?>% Errors (Failed)</span></div>-->
                </div>
            </div>
        </div>
    </body>
</html>
