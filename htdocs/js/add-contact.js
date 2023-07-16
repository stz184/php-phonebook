$(document).ready(function() {
    var form            = $('#add-contact-form');
    var alertsHolder    = $('#alerts-placeholder');
    form.submit(function(e) {
        e.preventDefault();

        $.post('/phonebook/add/', form.serialize(), function(data) {
            alertsHolder.empty();
            form.find('div.form-group').removeClass('has-error');
            if (data.success) {
                alertsHolder.append('<div class="alert alert-info" role="alert">The contact is successfully added</div>');
                form.find('div.form-group').find('input[type="text"],input[type="email"]').val('');
            } else {
                if (typeof data.errors == 'object') {
                    var errors = [];
                    for (var key in data.errors) {
                        form.find('input[name="' + key + '"]').closest('div.form-group').addClass('has-error');
                        errors.push(data.errors[key]);
                    }
                    alertsHolder.append('<div class="alert alert-danger" role="alert">' + errors.join('<br />') + '</div>');
                }
            }
        }, 'json');
    });
});