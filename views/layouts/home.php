<?php
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div id="frame" class="container-fluid">
        <div class="row">

            <!-- Left sidebar -->
            <div id="left-side" class="col-md-2">

                <!-- Search panel -->
                <div id="search-panel" class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-inline">
                            <div class="form-group form-group-sm">
                                <input type="text" id="w-search" class="form-control" placeholder="Enter word" autocomplete="off">
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Connections panel -->
                <?= $this->blocks['connPanel'] ?>

                <!-- History panel -->
                <?= $this->blocks['historyPanel'] ?>

            </div>
            <!-- Left sidebar end -->

            <!-- Left word -->
            <div id="left-list" class="col-sm-5">
                <?= $this->blocks['left'] ?>
            </div>

            <!-- Right word list -->
            <div id="right-list" class="col-sm-5">
                <?= $this->blocks['right'] ?>
                <div id="empty-holder"></div>
            </div>

        </div>
    </div>

    <!-- Add word modal -->
    <div id="add-w-modal" class="modal" tabindex="-1" role="dialog" aria-labelledby="add-w-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="add-w-label">Add new word</h4>
                </div>
                <div class="modal-body">
                    <form class="form-inline">
                        <div class="form-group">
                            <select id="add-w-lang" class="form-control selectpicker">
                                <?= $this->blocks['langOptions'] ?>
                            </select>
                            <input id="add-w-name" type="text" class="form-control" placeholder="Word">
                        </div>
                        <div class="form-group">
                            <textarea id="add-w-snss" class="form-control" rows="8" placeholder="Content" autocomplete="off"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="add-w-btn" class="btn btn-success">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add connection modal -->
    <div id="conn-modal" class="modal" tabindex="-1" role="dialog" aria-labelledby="conn-label" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="conn-label">Add connection</h4>
                </div>
                <div class="modal-body">
                    <form class="form-inline">
                        <div class="form-group">
                            <select id="conn-from-lang" class="form-control selectpicker" disabled>
                                <?= $this->blocks['langOptions'] ?>
                            </select>
                            <input id="conn-from-name" type="text" class="form-control" placeholder="Word" disabled>
                            <!-- <input id="conn-from-sns" type="text" class="form-control" placeholder="Sense"> -->
                            <select id="conn-from-sns" class="form-control selectpicker" title="">
                                <?= $this->blocks['connSenseOptions'] ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="conn-type" class="form-control selectpicker">
                                <?= $this->blocks['connOptions'] ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="conn-to-lang" class="form-control selectpicker">
                                <?= $this->blocks['langOptions'] ?>
                            </select>
                            <input id="conn-to-name" type="text" class="form-control" placeholder="Word">
                            <input id="conn-to-sns" type="text" class="form-control" placeholder="Sense">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="add-conn-btn" class="btn btn-success">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit connection modal -->
    <div id="edit-conn-modal" class="modal" tabindex="-1" role="dialog" aria-labelledby="edit-conn-label" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="edit-conn-label">Edit connection</h4>
                </div>
                <div class="modal-body">
                    <form class="form-inline">
                        <input type='hidden' id="edit-conn-to-id" value="">
                        <div class="form-group">
                            <select id="edit-conn-from-lang" class="form-control selectpicker" disabled>
                                <?= $this->blocks['langOptions'] ?>
                            </select>
                            <input id="edit-conn-from-name" type="text" class="form-control" placeholder="Word" disabled>
                            <!-- <input id="conn-from-sns" type="text" class="form-control" placeholder="Sense"> -->
                            <select id="edit-conn-from-sns" class="form-control selectpicker" title="">
                                <?= $this->blocks['connSenseOptions'] ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="edit-conn-type" class="form-control selectpicker">
                                <?= $this->blocks['connOptions'] ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <select id="edit-conn-to-lang" class="form-control selectpicker" disabled>
                                <?= $this->blocks['langOptions'] ?>
                            </select>
                            <input id="edit-conn-to-name" type="text" class="form-control" placeholder="Word" disabled>
                            <select id="edit-conn-to-sns" class="form-control selectpicker" title="">
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="del-conn-btn" class="btn btn-danger">Delete</button>
                    <button type="button" id="up-conn-btn" class="btn btn-success">Update</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/edit sense modal -->
    <div id="sns-modal" class="modal" tabindex="-1" role="dialog" aria-labelledby="sns-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="sns-label">Edit sense</h4>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-group">
                            <input id="sns-id" type="text" class="form-control" placeholder="Name">
                        </div>
                        <div class="form-group">
                            <textarea id="sns-expl" class="form-control" rows="4" placeholder="Explainations"></textarea>
                        </div>
                        <div class="form-group">
                            <textarea id="sns-snts" class="form-control" rows="4" placeholder="Sentences"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="del-sns-btn" class="btn btn-danger">Delete</button>
                    <button type="button" id="save-sns-btn" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add senses in batch modal -->
    <div id="sns-multi-modal" class="modal" tabindex="-1" role="dialog" aria-labelledby="sns-nulti-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="sns-nulti-label">Add senses</h4>
                </div>
                <div class="modal-body">
                    <form class="">
                        <div class="form-group">
                            <textarea id="sns-multi" class="form-control" rows="8" placeholder="Senses"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" id="save-sns-multi-btn" class="btn btn-success">Add</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Word graph modal -->
    <div id="graph-modal" class="modal" tabindex="-1" role="dialog" aria-labelledby="graph-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="graph-label">Word graph</h4>
                </div>
                <div class="modal-body">
                    <div id="graph-wrapper"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="notifications top-right"></div>
    <div id="string-ruler"></div>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
