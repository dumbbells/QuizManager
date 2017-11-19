<?php
include 'dataAccess.php';
include 'functions.php';
include 'Tag.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Math Quiz Manager</title>
        <meta charset="utf-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script type="text/x-mathjax-config">
            MathJax.Hub.Config({
            tex2jax: {
            inlineMath: [["$","$"],["\\(","\\)"]]
            }
            });
        </script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.2/MathJax.js?config=TeX-MML-AM_CHTML"></script>
        <script src="scripts.js"></script>
    </head>
    <body>
        <div class="container">
            <div class='jumbotron' style="background-color:#5bc0de">
                <h1>Math Quiz Manager</h1>
            </div>
            <div class="row">
                <div class="col-sm-5">
                    <!-- add validation for new question -->
                    <form method="POST" action="dataAccess.php" id="newQuestion" style="display:none">
                        <textarea rows="5" style="min-width: 100%" name="content"></textarea><br />
                        <input type="hidden" name="newQuestionAdded" value="true"> 
                        <input class="btn btn-info pull-right" type="submit" value="Save">
                    </form>
                </div>
                <div class="col-sm-7">
                    <div class="input-group pull-right">
		                <input type="search" placeholder="keywords">
                        <button class="btn btn-info"><span class="glyphicon glyphicon-search"></span></button>    
                        <button class="btn btn-info" data-toggle="tooltip" title="Undo previous deletion" onclick="undoDeletion();">
                            <span class="glyphicon glyphicon-chevron-left"></span></button>
                        <button class="btn btn-info" data-toggle="tooltip" title="Add new question" onclick="$(newQuestion).toggle();">
                            <span class="glyphicon glyphicon-plus"></span></button>
                    </div>
                </div>
            </div>
                <div class="row" style="padding: 5px;">
                    <div class="col-sm-12 pull-right">
                        <div class="pull-right">
                            <p>Placeholder for search tags</p>
                        </div>
                    </div>
                </div>
        </div>
        <?php loadProblems(); ?>
    </body>
</html>
