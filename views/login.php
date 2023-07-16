<div class="row">
    <div class="col-lg-5 col-md-6">
        <form action="/login" method="post" class="form-horizontal">
            <input type="hidden" name="token" value="<?=$token?>" />

            <div class="form-group<?=(isset($errors['username']) ? ' has-error' : '')?>">
                <label for="username" class="col-sm-4 control-label">Username:</label>
                <div class="col-sm-8">
                    <input type="text" class="form-control" id="username" name="username" value="" required="required">
                </div>
            </div>
            <div class="form-group<?=(isset($errors['password']) ? ' has-error' : '')?>">
                <label for="password" class="col-sm-4 control-label">Password:</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" id="password" name="password" required="required">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="remember_me" name="remember_me" value="1"> Remember Me
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <button type="submit" class="btn btn-default">Sign in</button>
                </div>
            </div>
        </form>
    </div>
</div>