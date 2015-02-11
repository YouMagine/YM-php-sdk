<?php

$designCategories = $youMagine->designCategories();
$request = $youMagine->getLastRequest();

?>
<fieldset>
    <legend>
        Request: <?php echo $request->method ?> <?php echo $request->url ?>
        <a class="btn btn-default" href="javascript:window.location.reload()"><i class="glyphicon glyphicon-repeat"></i></a>
    </legend>

    <pre><code><?php print_r($designCategories) ?></code></pre>
</fieldset>