 
        <div class='panel_listado simplebox'>
            <div class="titleh"><h3>List Company</h3></div>
            <div class="body">            
                <table id='listado_Company'>
                    <thead>
                        <tr>
                            
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  if(count($Companys)): ?>
                            <?php  foreach($Companys as $Company): ?>
                                <tr>
                            <td><?php  echo $Company->name ?></td>
                            <td><?php  echo $Company->email ?></td>
                            <td><?php  echo $Company->address ?></td>
                            <td><?php  echo $Company->phone ?></td>
                            <td><?php  echo $Enableds[$Company->enabled] ?></td>
                                    <td class='actions'>
                                        <a href='<?php echo ROOT_PATH ?>admin/Company/edit/<?php echo $Company->id ?>' class='editar hg-yellow' title='Edit item'>Edit</a>
                                        <a href='<?php echo ROOT_PATH ?>admin/Company/delete/<?php echo $Company->id ?>' title='Delete item' onclick="var d = confirm('Are you sure delete this Item?'); return d;" class='eliminar hg-red'>Delete</a>
                                    </td>
                                </tr>
                            <?php  endforeach ?>
                        <?php  else: ?>
                            <tr>
                                <td colspan='99'>Not found Company items</td>
                            </tr>
                        <?php  endif ?>
                    </tbody>
                </table>
                <div class='panel_controls padding20'>
                    <a id='btn_registrar' class='button-green' href='<?php echo ROOT_PATH ?>admin/Company/create'>Create new</a>
                </div>
            </div>
        </div>
            