<?php

$designSlugOrId = (
    isset($_POST['design_slug_or_id'])
        ? $_POST['design_slug_or_id']
        : null
);

if ($designSlugOrId) {
    $publishedDesign = $youMagine->publishDesign($designSlugOrId);
    $request = $youMagine->getLastRequest();
    $response = $youMagine->getLastResponse();
}

?>
    
<?php if ($designSlugOrId): ?>
    <fieldset>
        <legend>
            Request: <?php echo $request->method ?> <?php echo $request->url ?>
            <span class="label label-default"><?php echo $response->status ?></span>
            <pre><?php print_r($request->params) ?></pre>
        </legend>

        <pre><?php print_r($publishedDesign) ?></pre>
        <pre><?php echo htmlentities($response->body) ?></pre>
    </fieldset>
<?php else: ?>
    <form action="<?php echo url('?page='.$currentPage) ?>" class="form form-inline" method="post">
        <div class="form-group">
            <label for="publish_design__design_slug_or_id">Design slug or ID</label>
            <input class="form-control" id="publish_design__design_slug_or_id" name="design_slug_or_id">
        </div>

        <button class="btn btn-default" type="submit">Go</button>
    </form>
<?php endif ?>
