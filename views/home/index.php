<?php
use app\views\helpers\WordHelper;

/* @var $this yii\web\View */
$this->title = 'vDict - Where words are connected';

$this->registerCssFile('css/bootstrap.min.css');
$this->registerCssFile('css/bootstrap-select.min.css');
$this->registerCssFile('css/bootstrap-notify.css');
$this->registerCssFile('css/alert-bangtidy.css');
$this->registerCssFile('css/style.css');

$this->registerJsFile('js/jquery-1.11.3.min.js');
$this->registerJsFile('js/jquery.scrollTo.min.js');
$this->registerJsFile('js/bootstrap.min.js');
$this->registerJsFile('js/bootstrap-select.min.js');
$this->registerJsFile('js/bootstrap-notify.js');
$this->registerJsFile('js/typeahead.bundle.min.js');
$this->registerJsFile('js/d3.v3.js');
$this->registerJsFile('js/cola.v3.min.js');
$this->registerJsFile('js/script.js');
?>

<?php $this->beginBlock('langOptions'); ?>
<?php foreach (Yii::$app->params['langs'] as $lang): ?>
<option value="<?= $lang ?>"><?= ucfirst($lang) ?></option>
<?php endforeach; ?>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('connOptions'); ?>
<?php foreach (Yii::$app->params['connTypes'] as $conn => $rConn): ?>
<option value="<?= $conn ?>" data-content="<span class='label label-<?= Yii::$app->params['connStyles'][$conn] ?>'><?= Yii::$app->params['connLabels'][$conn] ?></span>">
    <?= Yii::$app->params['connLabels'][$conn] ?>
</option>
<?php endforeach; ?>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('connSenseOptions'); ?>
<?php if (isset($word['snss'])): ?>
    <?php foreach ($word['snss'] as $senseId => $sense): ?>
    <option value="<?= $senseId ?>"><?= str_replace('_', '.', $senseId) ?></option>
    <?php endforeach; ?>
    <option value=""></option>
<?php endif; ?>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('connPanel'); ?>
<?php if (!empty($word['conns'])): ?>
<div id="conn-panel" class="list-group">
    <?php foreach ($word['conns'] as $connType => $connGroup): ?>
    <div class="w-list conn-cat list-group-item">
        <h5 class="list-group-item-heading"><?= Yii::$app->params['connLabels'][$connType] ?></h5>
        <ul class="list-inline">
            <?php foreach ($connGroup as $id => $conn): ?> 
            <li><a class="conn label-<?= Yii::$app->params['connStyles'][$connType] ?>" from-id="<?= $word['_id'] ?>" from-sns="<?= $conn['f_sns'] ?>" to-id="<?= $id ?>" to-sns="<?= $conn['t_sns'] ?>">
                <?= $conn['t_name'] ?>
            </a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endforeach; ?>
    <div class="w-list conn-cat list-group-item">
        <button id="btn-graph" class="btn btn-default btn-sm" data-target="#graph-modal" data-toggle="modal">
            <span class=""></span>
        </button>
    </div>
</div>
<?php endif; ?>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('historyPanel'); ?>
<?php if (!empty($history)): ?>
<div id="hist-panel" class="w-list list-group">
    <div class="list-group-item">
        <h5 class="list-group-item-heading">History</h5>
        <ul class="list-inline">
            <?php foreach ($history as $key => $value): ?>
            <li><a class="hist label-grey" href="/home?wid=<?= $value['wid'] ?>">
                <?= $value['name'] ?>
            </a></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('left'); ?>
<?php if (!empty($word)): ?>
<?= WordHelper::outWordLeft($word) ?>
<?php endif; ?>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('right'); ?>
<?php if (!empty($conns)): ?>
    <?php foreach ($conns as $conn): ?>
        <?php if (!empty($conn)): ?>
        <?= WordHelper::outWordRight($conn) ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
<?php $this->endBlock(); ?>