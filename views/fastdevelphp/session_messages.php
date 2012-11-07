<div class="albox informationbox">
	<b>Information :</b> Please set your menu items in backend layout. 
	<a href="#" class="close tips" title="close">close</a>
</div>
<?php foreach($this->Session->getFlashMessages() as $FMessage): ?>
    <?php if($FMessage["type"] == 0): ?>
        <div class="albox succesbox">
        	<b>Succes :</b> <?php echo $FMessage["message"] ?> 
        	<a href="#" class="close tips" title="close">close</a>
        </div>
    <?php endif ?>
    
    <?php if($FMessage["type"] == 1): ?>
        <div class="albox errorbox">
        	<b>Error :</b> <?php echo $FMessage["message"] ?> 
        	<a href="#" class="close tips" title="close">close</a>
        </div>
    <?php endif ?>
    
    <?php if($FMessage["type"] == 2): ?>
        <div class="albox warningbox">
        	<b>Warning :</b> <?php echo $FMessage["message"] ?>
        	<a href="#" class="close tips" title="close">close</a>
        </div>
    <?php endif ?>
<?php endforeach ?>
<!-- end messages -->