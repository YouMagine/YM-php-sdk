<form action="" class="form form-inline">
    <input name="page" type="hidden" value="<?php echo $currentPage ?>">

    <div class="form-group">
        <label 
            for="designs_with_context_user_slug_or_id"
        >User slug or ID</label>

        <input 
            class="form-control" 
            id="designs_with_context_user_slug_or_id" 
            name="user_slug_or_id"
            value="<?php if (isset($_GET['user_slug_or_id'])) { echo $_GET['user_slug_or_id']; } ?>"
        >
    </div>

    <button class="btn btn-default" type="submit">Go</button>
</form>

<?php

$userSlugOrId = (
    isset($_GET['user_slug_or_id'])
        ? $_GET['user_slug_or_id'] 
        : null
);

if ($userSlugOrId) {
    $designs = $youMagine->designsWithContext($userSlugOrId);
    $request = $youMagine->getLastRequest();
}

?>
    
<?php if ($userSlugOrId): ?>
    <fieldset>
        <legend>
            Request: <?php echo $request->method ?> <?php echo $request->url ?>
            <a class="btn btn-default" href="javascript:window.location.reload()"><i class="glyphicon glyphicon-repeat"></i></a>
        </legend>

        <pre><code>
            <?php print_r($designs) ?>
        </code></pre>
    </fieldset>
<?php endif ?>
