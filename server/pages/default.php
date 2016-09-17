<?php $collections = get_collections(); ?>

<div class="container" id="homepage-form">
    <div class="row">
        <div class="col-sm-6">
            <h3>Create a collection</h3>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Collection name<input type="text" class="form-control" name="name" value="" placeholder="Your collection name..."></label>
                </div>
                <div class="form-group">
                    <label>Description<textarea class="form-control" name="description" value="" placeholder="Your description..."></textarea></label>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="create_collection" value="Create">
                </div>
            </form>
        </div>
        <div class="col-sm-6">
            <h3>Create an item</h3>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Collection<select class="form-control" name="coll_id" style="min-width: 190px;">
                            <?php foreach($collections as $coll){ ?>
                                <option value="<?php echo $coll->id; ?>"><?php echo $coll->name; ?></option>
                            <?php } ?>
                        </select></label>
                </div>
                <div class="form-group">
                    <label>Item name<input type="text" class="form-control" name="name" value="" placeholder="Your item name..."></label>
                </div>
                <div class="form-group">
                    <label>Description<textarea class="form-control" name="description" value="" placeholder="Your description..."></textarea></label>
                </div>
                <div class="form-group">
                    <label>Number of steps<input type="number" class="form-control" name="nr_steps" value="" placeholder="100000"></label>
                </div>
                <div class="form-group">
                    <label>Reward (leave empty if no reward)<input type="text" class="form-control" name="reward" value="" placeholder="Reward..."></label>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="create_item" value="Create">
                </div>
            </form>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-sm-12">
            <h3>All collections</h3>
            <ul>
                <?php foreach($collections as $coll){ ?>
                    <li><a href="?page=collections&id=<?php echo $coll->id;?>"><?php echo $coll->name; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>