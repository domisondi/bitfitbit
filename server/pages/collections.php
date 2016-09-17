<?php

try {
    $collection = new Collection($_GET['id']);
    $items = $collection->get_items();
    ?>
<div class="row">
    <div class="col-sm-12">
        <h3>Collection: <?php echo $collection->name; ?></h3>
        <form action="<?php echo BITFIT_HOME_URL; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $collection->id; ?>">
            <input type="submit" class="btn btn-danger" name="delete_collection" value="Delete Collection">
        </form>
        <hr>
        <h4>Items</h4>
        <ul>
            <?php foreach($items as $item){ ?>
                <li><?php echo $item->name; ?></li>
            <?php } ?>
        </ul>
    </div>
</div>

<?php }
catch(Exception $ex){ ?>
    <h3 style="color:red;">Invalid collection id</h3>
<?php }