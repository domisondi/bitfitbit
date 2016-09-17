<?php

define(BITFIT_HOME_URL, '/bitfit/server/');

require_once 'core/core.php';
global $database;

$status = '';

// parse post data
if(isset($_POST['create_item'])){
    $item = new Item(0, $_POST['coll_id'], $_POST);
    $res = $item->insert_into_database();
    if($res) $status = '<h4 style="color:green;">Item added!</h4>';
    else $status = '<h4 style="color:red;">Item could not be added!</h4>';
}
else if(isset($_POST['create_collection'])){
    $collection = new Collection(0, $_POST);
    $res = $collection->insert_into_database();
    if($res) $status = '<h4 style="color:green;">Collection added!</h4>';
    else $status = '<h4 style="color:red;">Collection could not be added!</h4>';
}
else if(isset($_POST['delete_collection'])){
    if(Collection::delete_collection($_POST['id'])){
        $status = '<h4 style="color:green;">Collection removed!</h4>';
    }
    else $status = '<h4 style="color:red;">Collection could not be removed!</h4>';
}

?>
<!DOCTYPE html>
<html>
    <head>
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        
        <link rel="stylesheet" href="css/style.css">
        
        <meta charset="UTF-8">
        <title>BitFit Store</title>
    </head>
    <body>
        <div id="main-wrapper">
            <div id="nav-wrapper" class="navbar navbar-default" role="navigation">
                <div class="container">
                    <div class="navbar-header">
                        <button id="navbar-collapse-button" type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <h3 id="title"><a href="<?php echo BITFIT_HOME_URL; ?>"><img id="logo" height="30" src="res/logo.png" alt="<?php echo 'BitFit Store'; ?>"></a></h3>
                    </div>
                    <div class="navbar-collapse collapse">
                        <ul id="navbar-content" class="nav navbar-nav navbar-right">
                            <li><a href=""><?php echo 'Home'; ?></a></li>       
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
            <br>
            <div id="main-container-wrapper" class="container">
                <div id="main-container" class="container" style="padding-left:0;">
                    <div id="head" class="row">
                        <div class="col-sm-12">
                            <?php echo $status; ?>
                        </div>
                    </div>
                    <div id="main-content-container" class="row">
                        <div id="content-wrapper" class="col-sm-12">
                            <?php switch($_GET['page']){
                                case 'callback': 
                                    require 'pages/callback.php';
                                    break;
                                case 'collections': 
                                    require 'pages/collections.php';
                                    break;
                                default:
                                    require 'pages/default.php';
                                    break;
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div style="height:45px;"></div>
            <div id="footer" class="navbar navbar-inverse navbar-fixed-bottom" role="navigation">
                <div class="container">
                    <div class="navbar-text">
                        <p class="text-center">
                            <?php echo 'Copyright &copy; 2016 – BitFit'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
