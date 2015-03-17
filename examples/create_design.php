<?php

$posted = ($_SERVER['REQUEST_METHOD'] == 'POST');
$requests = array();
$responses = array();
$results = array();
$hideErrors = true;

if ($posted) {
    $design = $youMagine->createDesign($_POST['design']);
    $results []= $design;
    $requests []= $youMagine->getLastRequest();
    $responses []= $youMagine->getLastResponse();
    $uploadErrors = array();
    
    if (!empty($design->id)) {
        foreach (array('document', 'image') as $field) {
            for ($i = 0, $count = count($_POST[$field]['name']); $i < $count; $i++) {
                $fileError = $_FILES[$field]['error']['file'][$i];
                $fileName = $_FILES[$field]['name']['file'][$i];

                if ($fileError == UPLOAD_ERR_OK) {
                    $fileTempPath = $_FILES[$field]['tmp_name']['file'][$i];
                    $fileMimeType = $_FILES[$field]['type']['file'][$i];

                    $item = array(
                        'name'          => $_POST[$field]['name'][$i],
                        'description'   => $_POST[$field]['description'][$i],
                        'file'          => YouMagine::createUploadFile($fileTempPath, $fileName, $fileMimeType)
                    );

                    if ($field == 'document') {
                        $results []= $youMagine->addDesignDocument($design->id, $item);
                    } elseif ($field == 'image') {
                        $results []= $youMagine->addDesignImage($design->id, $item);
                    }
                    
                    $requests []= $youMagine->getLastRequest();
                    $responses []= $youMagine->getLastResponse();
                } else {
                    $uploadErrors[$fileName] = YouMagine::explainUploadError($fileError);
                }
            }
        }
    }
}

?>
<?php if ($posted): ?>
    <?php if (count($uploadErrors)): ?>
        <div class="alert alert-warning">
            Some files were not uploaded succesfully:

            <ul>
                <?php foreach ($uploadErrors as $file => $explanation): ?>
                    <li>
                        <strong><?php echo $file ?>:</strong>
                        <?php echo $explanation ?>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <?php foreach ($requests as $index => $request):
        $response = $responses[$index];
        $result = $results[$index];
        ?>
        <fieldset>
            <legend>
                Request: <?php echo $request->method ?> <?php echo $request->url ?> 
                <span class="label label-default"><?php echo $response->status ?></span>
                <pre><?php print_r($request->params) ?></pre>
            </legend>

            <pre><?php print_r($result) ?></pre>
            <pre><?php echo htmlentities($response->body) ?></pre>
        </fieldset>
    <?php endforeach ?>
<?php else: ?>
    <form action="" class="form form-horizontal" enctype="multipart/form-data" method="post">
        <div class="form-group">
            <label class="control-label col-lg-2 col-md-3">Name</label>

            <div class="col-lg-10 col-md-9">
                <input class="form-control" name="design[name]" required/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2 col-md-3">Excerpt</label>

            <div class="col-lg-10 col-md-9">
                <textarea class="form-control" name="design[excerpt]" required></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2 col-md-3">Design category ID</label>

            <div class="col-lg-10 col-md-9">
                <input class="form-control" name="design[design_category_id]" required type="number" min="1"/>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2 col-md-3">Description</label>

            <div class="col-lg-10 col-md-9">
                <textarea class="form-control" name="design[description]"></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2 col-md-3">License</label>

            <div class="col-lg-10 col-md-9">
                <select class="form-control" name="design[license]">
                    <option value="cc0">CC</option>
                    <option value="gplv3">GPL V3</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2 col-md-3">
                Images
                <a href="javascript:void(0);" onclick="addFileInput(this);" class="btn btn-link">(add)</a>
            </label>

            <div class="col-lg-10 col-md-9">
                <div class="form-horizontal nested-form">
                    <div class="form-group">
                        <label class="control-label col-lg-2 col-md-3">File</label>

                        <div class="col-lg-10 col-md-9">
                            <input class="form-control" name="image[file][]" type="file">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-2 col-md-3">Name</label>

                        <div class="col-lg-10 col-md-9">
                            <input class="form-control" name="image[name][]">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-2 col-md-3">Description</label>

                        <div class="col-lg-10 col-md-9">
                            <input class="form-control" name="image[description][]">
                        </div>
                    </div>

                    <a class="btn btn-danger btn-xs remove-row" href="#" onclick="removeFileInput(this)">
                        <i class="glyphicon glyphicon-remove"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label col-lg-2 col-md-3">
                Documents
                <a href="javascript:void(0);" onclick="addFileInput(this);" class="btn btn-link">(add)</a>
            </label>

            <div class="col-lg-10 col-md-9">
                <div class="form-horizontal nested-form">
                    <div class="form-group">
                        <label class="control-label col-lg-2 col-md-3">File</label>

                        <div class="col-lg-10 col-md-9">
                            <input class="form-control" name="document[file][]" type="file">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-2 col-md-3">Name</label>

                        <div class="col-lg-10 col-md-9">
                            <input class="form-control" name="document[name][]">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-2 col-md-3">Description</label>

                        <div class="col-lg-10 col-md-9">
                            <input class="form-control" name="document[description][]">
                        </div>
                    </div>

                    <a class="btn btn-danger btn-xs remove-row" href="#" onclick="removeFileInput(this)">
                        <i class="glyphicon glyphicon-remove"></i>
                    </a>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Create design</button>
    </form>
<?php endif ?>
