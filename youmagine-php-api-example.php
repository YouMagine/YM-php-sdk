<?php

/**
 * YOUMAGINE API PHP EXAMPLE
 * 
 * This example shows how to interact with the YouMagine API using PHP
 * 
 */





/*--- CONFIGURATION - tweek these settings to have a basic working example ---*/

        /**
         * The name of the integrated application
         */
        define('INTEGRATION', 'phpexample');

        /**
         * Whether or not to use HTTPS.
         * 
         * NOTE: it is highly recommended to use HTTPS because of the 
         * sensitivity of the data that is sent and received across the API
         */
        define('USE_HTTPS', false);
        
/*--- /CONFIGURATION ---------------------------------------------------------*/

        

        
        
/*--- BOOTSTRAP --------------------------------------------------------------*/
        
        // Include the YouMagine API class
        require 'youmagine.php';

        // Create an instance of YouMagine, representing the API
        $youMagine = new YouMagine(INTEGRATION, array(
            'https'     => USE_HTTPS,
            'version'   => YouMagine::API_VERSION_1
        ));

        /***    Some magic to include the right example file
        /**/
        /**/    $apiActions = array(
        /**/        'authorization'                 => 'Authorization' ,
        /**/        'retrieve_designs'              => 'Retrieve designs',
        /**/        'retrieve_designs_with_context' => 'Retrieve designs with context',
        /**/        'retrieve_design_categories'    => 'Retrieve design categories',
        /**/        'create_design'                 => 'Create design',
        /**/        'publish_design'                => 'Publish design'
        /**/    );
        /**/
        /**/    $currentPage = (
        /**/        $youMagine->isAuthorized() && isset($_GET['page'])
        /**/            ? $_GET['page']
        /**/            : 'authorization'
        /**/    );
        /**/
        /**/    $includePath = 'examples/'.preg_replace('/[^A-Z0-9_]/i', '', $currentPage).'.php';
        /**/
        /**/    $includeFile = (
        /**/        file_exists($includePath)
        /**/            ? $includePath
        /**/            : null
        /**/    );

/*--- /BOOTSTRAP -------------------------------------------------------------*/
        
        

?>
<html>
    <head>
        <title><?php echo $apiActions[$currentPage] ?> - YouMagine API PHP example</title>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
        
        <style>
            body { margin: 20px; }
            .nav { margin: 20px 0; }
            p + p .btn { margin-top: 20px; }
            .nested-form { position: relative; }
            #main { margin-top: 20px; }
            
            .nested-form + .nested-form {
                border-top: 1px black dashed;
                padding-top: 14px;
            }
            
            .btn.remove-row {
                left: 5px;
                padding: 5px 3px;
                position: absolute;
                top: 5px;
            }
            
            .nested-form:only-child .btn.remove-row {
                display: none;
            }

            .navbar-brand img {
                float: left;
                margin-right: 10px;
            }
        </style>
    </head>
    
    <body>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">
                        <img alt="YouMagine logo" width="20" height="20" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3wMGCzs1KyPlrAAAAuNJREFUOMutlV1ozWEcxz+/5382w6zQxs44O9awWYtJzM6ZKC4IWaRkoZOScoEL5bgQteNKuZRySsqNRqKUJOwgryuv8742B6tpOp1jZ+3/fx4X/ue/Yxfk5Xv19Lx8n+/ze77P9xFchCNREvEY4UhUgGJgMbAVaAKmudM+ALeBU8AdIJ2Ix0xuLYCMIisCVgJtQC2/xgvgAHA5EY9lcxySR1YCHAJ282c4BhxMxGOpcCTqKSwCjvwFWT7p/kQ8lhW3Zi1AO/+G9cB55V5AG4ASQYl4M0RAKeW1R2NUXxtQrIAQUKONoXn+HNYtW4TlktRXV7JxRROFBQWE5tXiaO2tdrQmPK8Wx/H6aoCQcq2B4zhMKilmz+Y1tCxrpHRiCYd3bqKxfhaWJRzZ1cqcqgDDto1tO9RVTefwzk2MLSpEG5Mj3aJcn+Hz+Th79TbX7j9he8tyNixvQkQ4duYS5ZMnAbBt9VIsZWFrzd7WtWhtmFFRhh5RHlJARc6QllLETrZj2zYrFs3lxLkrdL1PUhP0AzB3dpAFddWsCs2neno5IhAsn4KjPYUV6uciC5lslosdDxCBp296cLTDzMoKAAaHhlmzZAFbVi/lU/8AlrII+kvzFaKA5OjbU0q8DWxHUxP0k8oMcvT0BRbWVRP0lxGLt5MZGiLoL8OM1DDpA24BgXzCAp+PsWPGICIUFRYyq9LPu94+7j59xaf+AdLfsjx+3c3Hvi8EppZSMn4cw7aNiNyyAg3NGaA1/9ip9CDP3vXQ1Z1EWQIGbnY+52V3koFUmusPn/Hla5rUt0F6+/p52/uZYdtBRPZJOBKdANxzfTSSGCLeUSxLYbRBG4MSwWAw5sdDEJGcP7uAhQpIu6nhwUB+XXAc7XlNmx9kuXae2aNA2urp7CDQ0PzefYKN/xAOxxPxmK3c+MoCB90B/jK+suFIFKunsyMXsEOBhuYbwCOgHij9DdFzYIerLOMF7P/+Ar4DKoE4K1pVcFkAAAAASUVORK5CYII="/>
                        YouMagine PHP API Example
                    </a>
                </div>
            </div>
        </nav>

        <?php foreach (array('POST', 'FILES') as $globalName):
            $variableName = '$_'.$globalName;
            ?>
            <?php if (isset($$variableName)): ?>
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            There is <?php echo $globalName ?> data available
                        </div>

                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#show<?php echo $globalName ?>">Show <?php echo $globalName ?> data</button>
                        </div>
                    </div>

                    <div class="collapse" id="show<?php echo $globalName ?>">
                        <br/>
                        <pre><?php print_r($$variableName) ?></pre>
                    </div>
                </div>
            <?php endif ?>
        <?php endforeach ?>
        
        <?php if ($youMagine->isAuthorized()): ?>
            <?php if (!USE_HTTPS): ?>
                <div class="alert alert-warning">
                    NOTE: it is highly recommended to use HTTPS because of the sensitivity
                    of the data that is sent and received across the API
                </div>
            <?php endif ?>

            <div class="row">
                <div class="col-sm-3 col-md-2 col-lg-2">
                    <ul class="nav nav-pills nav-stacked">
                        <?php foreach ($apiActions as $page => $description): ?>
                            <li 
                                <?php if ($currentPage == $page): ?>
                                    class="active"
                                <?php endif ?>
                            >
                                <a href="?page=<?php echo $page ?>"><?php echo $description ?></a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>

                <div class="col-sm-9 col-md-10 col-lg-10" id="main">
                    <?php if ($includeFile) { include $includeFile; } ?>
                </div>
            </div>
        <?php else: ?>
            <?php if ($includeFile) { include $includeFile; } ?>
        <?php endif ?>
        
        <?php

        if (!isset($hideErrors)) {
            $lastResponse = $youMagine->getLastResponse();

            if ($lastResponse && ($lastResponse->status < 200 || $lastResponse->status >= 300)) {
                echo '<div class="alert alert-danger">The request was not succesful. Below are the technical details</div>';
                echo '<pre>';
                $youMagine->debug();
                echo '</pre>';
            }
        }
        
        ?>
                
        <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
        
        <script>
            (function ($, window) {
                window.addFileInput = function (triggerButton) {
                    var container = $(triggerButton).closest('.form-group').children('div'),
                        inputGroups = container.find('.form-horizontal'),
                        newInputGroup = inputGroups.first().clone();

                    newInputGroup.find(':input').val('');
                    newInputGroup.appendTo(container);
                };
                
                window.removeFileInput = function (triggerButton) {
                    $(triggerButton).closest('.nested-form').remove();
                };
            }(jQuery, window));
        </script>
    </body>
</html>
