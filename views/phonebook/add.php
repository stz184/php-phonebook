<div class="row">
    <div class="col-lg-6 col-md-6">
        <form action="/phonebook/add" method="POST" class="form-horizontal" id="add-contact-form">
            <div id="registration_form">

                <div class="form-group">
                    <label class="col-sm-4 control-label required" for="add_form_full_name">Full Name:<br /><small>Alphabetical symbols only</small></label>
                    <div class="col-sm-8">
                        <input
                            type="text"
                            id="add_form_full_name"
                            name="full_name"
                            required="required"
                            pattern="([a-zа-яA-ZА-Я]{2,}){1}(\s+[a-zа-яA-ZА-Я]{2,}){1,}"
                            class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label required" for="add_form_email">E-mail:</label>
                    <div class="col-sm-8">
                        <input
                            type="email"
                            id="add_form_email"
                            name="email"
                            required="required"
                            class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-4 control-label required" for="add_form_phone">Phone:</label>
                    <div class="col-sm-8">
                        <input
                            type="text"
                            id="add_form_phone"
                            name="phone"
                            required="required"
                            class="form-control">
                    </div>
                </div>

                <input type="hidden" name="token" class="form-control" value="<?=$token?>">
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <button type="submit" class="btn btn-default">Add Contact</button>
                </div>
            </div>
        </form>
    </div>
</div>