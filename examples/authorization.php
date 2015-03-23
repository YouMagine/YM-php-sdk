<?php

$event = (
    isset($_GET['event']) 
        ? $_GET['event'] 
        : null
);

if ($event == 'clear') {
    $youMagine->clearSession();
    echo '<script>window.location="'.YouMagine::url().'";</script>';
}

if ($event == 'redirected') {
    $youMagine->authorize();
    echo '<script>window.location="'.YouMagine::url().'";</script>';
}

$isAuthorized = $youMagine->isAuthorized();

if (!$isAuthorized) {
    $redirectUrl = YouMagine::url('?page=authorization&event=redirected');
    $deniedUrl = YouMagine::url('?page=authorization&event=denied');
    $authorizeUrl = $youMagine->getAuthorizeUrl($redirectUrl, $deniedUrl);
}

?>

<?php if ($isAuthorized): ?>
    <div class="alert alert-info">
        <div class="row">
            <div class="col-sm-6 col-md-6 col-lg-6">
                Authentication token: <?php echo $youMagine->getAuthToken() ?>
            </div>

            <div class="col-sm-6 col-md-6 col-lg-6">
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#userInfo">Show user info</button>
                <a href="<?php echo YouMagine::url('?page=authorization&event=clear') ?>" class="btn btn-large btn-danger">Clear session</a>
            </div>
        </div>

        <div class="collapse" id="userInfo">
            <br/>
            <pre><?php print_r($youMagine->getUser()) ?></pre>
        </div>
    </div>
<?php elseif ($event == 'denied'): ?>
    <div class="alert alert-danger">
        Account access was denied.
        <a href="<?php echo $authorizeUrl ?>">Click here</a> to try again.
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <p>You are not authorized.</p>
        <p><a href="<?php echo $authorizeUrl ?>" class="btn btn-success btn-large">Authorize</a></p>
    </div>
<?php endif ?>
