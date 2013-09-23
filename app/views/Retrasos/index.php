 
        <div class='panel_listado'>
            <h1>Lista de Retrasos</h1>            
            <table id='listado_Retrasos'>
                <thead>
                    <tr>
                        <th>Usuario</th>
                    <th>Fecha</th>
                    <th>Tiempo de retraso</th>
                    <th>Comentarios</th>
                    <th>Estado</th>
                    <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php  if(count($Retrasoss)): ?>
                        <?php  foreach($Retrasoss as $Retrasos): ?>
                            <tr>
                            <td><?php  echo $this->DB->get_object_by_id('Company_user', $Retrasos->id)?$this->DB->get_object_by_id('Company_user', $Retrasos->id)->name:'No definido'; ?></td>
                            <td><?php  echo $Retrasos->date_retraso ?></td>
                            <td><?php  echo $Retrasos->time_retraso ?></td>
                            <td><?php  echo $Retrasos->descr_retraso ?></td>
                            <td><?php  echo $Status_retrasos[$Retrasos->status_retraso] ?></td>
                                <td class='actions'>
                                    <a href='<?php echo ROOT_PATH ?>Retrasos/edit/<?php echo $Retrasos->id_retraso ?>' class='editar' title='Edit element'>Editar</a>
                                    <a href='<?php echo ROOT_PATH ?>Retrasos/delete/<?php echo $Retrasos->id_retraso ?>' title='Delete element' onclick="var d = confirm('Esta seguro de eliminar este Item?'); return d;" class='eliminar'>Eliminar</a>
                                </td>
                            </tr>
                        <?php  endforeach ?>
                    <?php  else: ?>
                        <tr>
                            <td colspan='99'>No existen Retrasos registradas</td>
                        </tr>
                    <?php  endif ?>
                </tbody>
            </table>
            <div class='panel_controls'>
                <a id='btn_registrar' href='<?php echo ROOT_PATH ?>Retrasos/create'>Registrar nuevo</a>
            </div>
        </div>
            