<form action="" class="form form-inline">
    <input name="page" type="hidden" value="<?php echo $currentPage ?>">

    <div class="form-group">
        <label for="designs_paginate_page">Page #</label>

        <input
            class="form-control" id="designs_paginate_page" min="1" name="paginate_page" type="number"

            value="<?php
                echo (
                    isset($_GET['paginate_page'])
                        ? $_GET['paginate_page']
                        : YouMagine::PAGINATION_DEFAULT_PAGE
                )
            ?>"
        >
    </div>

    <div class="form-group">
        <label for="designs_paginate_limit">Items per page</label>

        <input
            class="form-control" id="designs_paginate_limit" min="1" name="paginate_limit" type="number"

            value="<?php
                echo (
                    isset($_GET['paginate_limit'])
                        ? $_GET['paginate_limit']
                        : YouMagine::PAGINATION_DEFAULT_LIMIT
                )
            ?>"
        >
    </div>

    <button class="btn btn-default" type="submit">Go</button>
</form>

<?php

$paginatePage = (
    isset($_GET['paginate_page'])
        ? $_GET['paginate_page'] 
        : null
);

$paginateLimit = (
    isset($_GET['paginate_limit'])
        ? $_GET['paginate_limit'] 
        : null
);

$designs = $youMagine->designs($paginatePage, $paginateLimit);
$request = $youMagine->getLastRequest();

?>
<fieldset>
    <legend>
        <?php echo $request->method ?> <?php echo $request->url ?>
        <a class="btn btn-default" href="javascript:window.location.reload()"><i class="glyphicon glyphicon-repeat"></i></a>
    </legend>

    <pre><code>
        <?php print_r($designs) ?>
    </code></pre>
</fieldset>
