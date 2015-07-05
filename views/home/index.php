<?php
use app\views\helpers\Word;

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
$this->registerJsFile('js/script.js');

$labels = [
    'synonym' => 'label-success',
    'antonym' => 'label-danger',
];
?>

<?php $this->beginBlock('langOptions'); ?>
<?php foreach (Yii::$app->params['langs'] as $lang): ?>
<option value="<?= $lang ?>"><?= ucfirst($lang) ?></option>
<?php endforeach; ?>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('connOptions'); ?>
<?php foreach (Yii::$app->params['connTypes'] as $conn => $rConn): ?>
<option value="<?= $conn ?>"><?= ucfirst($conn) ?></option>
<?php endforeach; ?>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('connPanel'); ?>
<?php if (!empty($word['conns'])): ?>
<div id="conn-panel" class="list-group">
    <?php foreach ($word['conns'] as $connType => $connLabels): ?>
    <div class="w-list conn-cat list-group-item">
        <h5 class="list-group-item-heading"><?= ucfirst($connType) ?></h5>
        <ul class="list-inline">
            <?php foreach ($connLabels as $key => $connLabel): ?> 
            <li><a class="conn label-<?= Yii::$app->params['connStyles'][$connType] ?>" from-sns="<?= $connLabel['f_sns'] ?>" to-id="<?= $connLabel['t_id'] ?>" to-sns="<?= $connLabel['t_sns'] ?>">
                <?= $connLabel['t_name'] ?>
            </a></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endforeach; ?>
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
            <li><a class="hist label-default" href="/home?wid=<?= $value['wid'] ?>">
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
<?= Word::outWordLeft($word) ?>
<?php endif; ?>
<?php $this->endBlock(); ?>

<?php $this->beginBlock('right'); ?>
<?php if (!empty($conns)): ?>
    <?php foreach ($conns as $conn): ?>
        <?php if (!empty($conn)): ?>
        <?= Word::outWordRight($conn) ?>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>
<?php $this->endBlock(); ?>