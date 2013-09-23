 
        <div class='panel_listado simplebox'>
            <div class="titleh"><h3>List Backlog</h3></div>
            <div class="body">            
                <table id='listado_Backlog'>
                    <thead>
                        <tr>
                            
                            <th>id</th>
                            <th>date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  if(count($Backlogs)): ?>
                            <?php  foreach($Backlogs as $Backlog): ?>
                                <tr>
                                    <td><?php  echo $Backlog->id_backlog ?></td>
                                    <td><?php  echo $Backlog->createat_backlog ?></td>
                                    <td class='actions'>
                                        <a href='<?php echo ROOT_PATH ?>admin/Backlog/edit/<?php echo $Backlog->id_backlog ?>' class='editar hg-yellow' title='Edit item'>Edit</a>
                                        <a href='<?php echo ROOT_PATH ?>admin/Backlog/delete/<?php echo $Backlog->id_backlog ?>' title='Delete item' onclick="var d = confirm('Are you sure delete this Item?'); return d;" class='eliminar hg-red'>Delete</a>
                                    </td>
                                </tr>
                            <?php  endforeach ?>
                        <?php  else: ?>
                            <tr>
                                <td colspan='99'>Not found Backlog items</td>
                            </tr>
                        <?php  endif ?>
                    </tbody>
                </table>
                <div class='panel_controls padding20'>
                    <a id='btn_registrar' class='button-green' href='<?php echo ROOT_PATH ?>admin/Backlog/create'>Create new</a>
                </div>
            </div>
        </div>
            