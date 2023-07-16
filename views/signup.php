<div class="row">
    <div class="col-lg-5 col-md-6">
        <form action="/signup" method="POST" class="form-horizontal">
            <div id="registration_form">

                <div class="form-group<?=(isset($errors['username']) ? ' has-error' : '')?>">
                    <label class="col-sm-4 control-label required" for="registration_form_username">Username:</label>
                    <div class="col-sm-8">
                        <input
                            type="text"
                            id="registration_form_username"
                            name="username"
                            required="required"
                            pattern="[\w]{5,}"
                            class="form-control"
                            value="<?=isset($userData['username']) ? $userData['username'] : ''?>">
                    </div>
                </div>

                <div class="form-group<?=(isset($errors['email']) ? ' has-error' : '')?>">
                    <label class="col-sm-4 control-label required" for="registration_form_email">E-mail:</label>
                    <div class="col-sm-8">
                        <input
                            type="email"
                            id="registration_form_email"
                            name="email"
                            required="required"
                            class="form-control"
                            value="<?=isset($userData['email']) ? $userData['email'] : ''?>">
                        </div>
                </div>

                <div class="form-group<?=(isset($errors['password']) ? ' has-error' : '')?>">
                    <label class="col-sm-4 control-label required" for="registration_form_plainPassword_first">Password:</label>
                    <div class="col-sm-8">
                        <input
                            type="password"
                            id="registration_form_plainPassword_first"
                            name="password"
                            required="required"
                            class="form-control"
                            value="">
                    </div>
                </div>

                <div class="form-group<?=(isset($errors['password_repeat']) ? ' has-error' : '')?>">
                    <label class="col-sm-4 control-label required" for="registration_form_plainPassword_second">Repeat password:</label>
                    <div class="col-sm-8">
                        <input
                            type="password"
                            id="registration_form_plainPassword_second"
                            name="password_repeat"
                            required="required"
                            class="form-control">
                    </div>
                </div>

                <div class="form-group<?=(isset($errors['captcha']) ? ' has-error' : '')?>">
                    <label class="col-sm-4 control-label required" for="registration_form_captcha">Security Code</label>

                    <div class="col-sm-8">
                        <img src="/captcha/" alt="captcha" title="captcha" width="146" height="30" id="captcha-image">
                        <script type="text/javascript">
                            function reloadCaptcha() {
                                var img = document.getElementById('captcha-image');
                                img.src = '/captcha/?n=' + Math.random();
                            }
                        </script>
                        <a class="captcha_reload"
                           href="javascript:reloadCaptcha();">Refresh</a>
                        <input
                            type="text"
                            id="registration_form_captcha"
                            name="captcha"
                            required="required"
                            class="form-control">
                    </div>
                </div>
                <input type="hidden" name="token" class="form-control" value="<?=$token?>">
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <button type="submit" class="btn btn-default">Sign Up</button>
                </div>
            </div>
        </form>
    </div>
</div>