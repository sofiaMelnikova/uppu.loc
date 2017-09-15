<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<form class="form-horizontal" action="http://127.0.0.1/registration" method="post">

    <input type="hidden" name="csrfToken" value="<?php if (!empty($csrfToken)): echo spCh($csrfToken); endif;?>">

    <fieldset>

        <!-- Form Name -->
        <legend>Check in</legend>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="email">Email</label>
            <div class="col-md-4">
                <input id="email" name="email" type="text" placeholder="email" class="form-control input-md" required="">

            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="email">Number</label>
            <div class="col-md-4">
                <input name="phone" type="number" placeholder="88003330033" class="form-control input-md" required="">

            </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
            <label class="col-md-4 control-label" for="email">Password</label>
            <div class="col-md-4">
                <input name="password" type="password" placeholder="Password" class="form-control input-md" required="">

            </div>
        </div>

        <!-- Button -->
        <div class="form-group">
            <label class="col-md-4 control-label" for="submit"></label>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">SUBMIT</button>
            </div>
        </div>

    </fieldset>
</form>

<div>
    {{error}}
</div>

</body>
</html>