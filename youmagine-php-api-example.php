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
        
        // Handy utility function to generate an URL, based on the current URL
        require 'url.php';

        // Include the YouMagine API class
        require 'youmagine.php';

        // Create an instance of YouMagine, representing the API
        $youMagine = new YouMagine(INTEGRATION, array('https' => USE_HTTPS, 'host' => 'youmagine.local:3000'));

        /**/    // Some magic to include the right example file
        /**/    $apiActions = array(
        /**/        'Authorization'                 => 'authorization',
        /**/        'Retrieve designs'              => 'retrieve_designs',
        /**/        'Retrieve designs with context' => 'retrieve_designs_with_context',
        /**/        'Retrieve design categories'    => 'retrieve_design_categories',
        /**/        'Create design'                 => 'create_design'
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
        </style>
    </head>
    
    <body>
        <h1>YouMagine PHP API Example</h1>
        
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
                        <?php foreach ($apiActions as $description => $page): ?>
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
        
        $lastResponse = $youMagine->getLastResponse();

        if ($lastResponse && $lastResponse->status != 200) {
            echo '<div class="alert alert-danger">The request was not succesful. Below are the technical details</div>';
            echo '<pre>';
            $youMagine->debug();
            echo '</pre>';
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
