<p>
    <a class="btn btn-default" href="/phonebook/add" role="button">Add Contact</a>
</p>

<form method="get" action="/phonebook" id="phonebook-grid-form">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th><a href="<?=getSortURL('id')?>">#</a></th>
            <th><a href="<?=getSortURL('full_name')?>">Full Name</a></th>
            <th><a href="<?=getSortURL('email')?>">E-mail</a></th>
            <th><a href="<?=getSortURL('phone')?>">Phone</a></th>
            <th><a href="<?=getSortURL('created_at')?>">Date Created</a></th>
            <th><a href="<?=getSortURL('updated_at')?>">Date Updated</a></th>
            <th class="action-buttons">Actions</th>
        </tr>
        <tr>
            <th></th>
            <th><input type="text" name="search[full_name]" value="<?=(isset($search['full_name']) ? $search['full_name'] : '')?>" class="form-control"></th>
            <th><input type="text" name="search[email]" value="<?=(isset($search['email']) ? $search['email'] : '')?>" class="form-control"></th>
            <th><input type="text" name="search[phone]" value="<?=(isset($search['phone']) ? $search['phone'] : '')?>" class="form-control"></th>
            <th></th>
            <th></th>
            <th class="action-buttons text-center">
                <div class="btn-group">
                    <button class="btn btn-sm btn-default" type="submit"> <span class="glyphicon glyphicon-search"></span></button>
                    <button class="btn btn-sm btn-default" type="reset"> <span class="glyphicon glyphicon-remove"></span></button>
                </div>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($contacts as $contact): ?>
        <tr>
            <td><?=$contact['id']?></td>
            <td><?=htmlentities($contact['full_name'])?></td>
            <td><?=htmlentities($contact['email'])?></td>
            <td><?=htmlentities($contact['phone'])?></td>
            <td><?=$contact['created_at']?></td>
            <td><?=(($contact['updated_at'] == '0000-00-00 00:00:00') ?  '--' : $contact['updated_at'])?></td>
            <td class="action-buttons text-center">
                <div class="btn-group">
                    <a class="btn btn-sm btn-info" href="/phonebook/update/<?=$contact['id']?>"> <span class="glyphicon glyphicon-edit"></span></a>
                    <a class="btn btn-sm btn-danger" href="/phonebook/delete/<?=$contact['id']?>"> <span class="glyphicon glyphicon-trash"></span></a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</form>

<br />
<div class="row">
    <div class="col-lg-6 col-lg-offset-3 text-center"><?=pager($page, $contactsNumber, $perPage)?></div>
</div>
