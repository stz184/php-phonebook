<div class="row">
    <div class="col-lg-5 col-md-6">
        <form action="/change-password" method="post" class="form-horizontal">
            <input type="hidden" name="token" value="<?=$token?>" />

            <div class="form-group<?=(isset($errors['password']) ? ' has-error' : '')?>">
                <label for="password" class="col-sm-4 control-label">Current Password:</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" id="password" name="password" required="required">
                </div>
            </div>
            <div class="form-group<?=(isset($errors['new_password']) ? ' has-error' : '')?>">
                <label for="new_password" class="col-sm-4 control-label">New password:</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" id="new_password" name="new_password" required="required">
                </div>
            </div>
            <div class="form-group<?=(isset($errors['new_password_repeat']) ? ' has-error' : '')?>">
                <label for="new_password_repeat" class="col-sm-4 control-label">Repeat Password:</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" id="new_password_repeat" name="new_password_repeat" required="required">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <button type="submit" class="btn btn-default">Update Password</button>
                </div>
            </div>
        </form>
    </div>
</div>