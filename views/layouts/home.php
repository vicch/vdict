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

                <!-- Tools panel -->
                <div id="tools-panel" class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-inline">
                            <div class="form-group form-group-sm">
                                <input id="w-search" type="text" class="form-control" id="" placeholder="Enter word">
                            </div>
                            <!-- <button type="button" id="btn-add-w" class="btn btn-default btn-sm" data-toggle="modal" data-target="#add-w-modal">
                                <span class="glyphicon glyphicon-plus"></span>
                            </button> -->
                            <!-- <button type="button" class="btn btn-default btn-sm">
                                <span class="glyphicon glyphicon-search"></span>
                            </button> -->
                        </form>
                    </div>
                </div>
                <!-- Tools panel end -->

            </div>
            <!-- Left sidebar end -->

            <!-- Left word list -->
            <div id="left-list" class="col-sm-5">
            </div>
            <!-- Left word end -->

            <!-- Right word list -->
            <div id="right-list" class="col-sm-5">
            </div>
            <!-- Right word list end -->

        </div>
    </div>

    <?= $content ?>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
