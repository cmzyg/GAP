<!DOCTYPE html>
<html>
<head>
    <title>Test Manager</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap-theme.min.css">
    <!-- Latest compiled and minified JavaScript -->

    <style>
        .failed
        {
            color:red;
        }

        .passed
        {
            color:green;
        }



    </style>
</head>
<body>
    <div class="container" role="main">
        <div class="jumbotron">
            <h1>Welcome to Test Manager!</h1>
            <p>This is a simple UI for managing automated tests</p>
            <p><a class="btn btn-primary btn-lg" href="#" role="button">Run All Test</a></p>
        </div>

        <div class="page-header">
            <h1>Accounting Testing
            </h1>

        </div>

        <?php $testPath = dirname(realpath(dirname(__FILE__))).DIRECTORY_SEPARATOR."component".DIRECTORY_SEPARATOR;
        $response = shell_exec("./test.sh ".dirname(dirname(realpath(dirname(__FILE__)))).DIRECTORY_SEPARATOR." ".$testPath."accounting");
        $use = explode("Scenario Outline", $response);
        list($feature, $theRest) = $use;

        list($tag, $feature) = explode("Feature", $feature);
        echo '<div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Accounting Test</h3>
              </div>
              <div class="panel-body">
                <b>Feature</b>'.$feature.'
              </div>
            </div>';

        if(is_array($use) && sizeof($use) > 1)
        {
            $last = sizeof($use);
            $k = 0;
            foreach($use as $scenario)
            {
                if($k > 0)
                {
                    list($header, $rem) = explode("#", $scenario);
                    $scenario = str_replace($header, "", $scenario);
                    list($unwanted, $examples) = explode("Examples:", $scenario);
                    $rows = explode("\n", $examples);

                    echo '<div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">Scenario '.$header.'</h3>
              </div>
              <div class="panel-body">
                '.$scenario.'';
                    $tableHeaders = explode("|", $rows[1]);
                    echo '<table class="table">
                    <tr>
                    ';
                    $tableHeaderSize = sizeof($tableHeaders);

                    for($z=1; $z < ($tableHeaderSize -1); $z++)
                    {
                        echo '<th>'.$tableHeaders[$z].'</th>';
                    }
                    echo '
                    </tr>';

                    $tableDataSize = sizeof($rows);

                    var_dump($rows);
                    for($n=2; $n < ($tableDataSize - 1); $n++)
                    {
                        $status = false;
                        echo "<tr class='alert alert-danger'>";
                        $td = explode("|", $rows[$n]);
                        if(is_array($td) && sizeof($td) == 1 && strlen(trim($td[0])) > 2)
                        {
                            $status = true;
                        }
                        $tdSize = sizeof($td);
                        for($m=1; $m < $tdSize ; $m++)
                        {
                            echo '<td>'.$td[$m].'</td>';
                        }

                        echo "</tr>";
                    }

                    echo '
                    </table>
              </div>
            </div>';
                }

                $k++;
            }
        }
        ?>


    </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $("table").addClass('table');
            $("table").addClass('table-striped');
        });
        </script>

</body>
</html>
