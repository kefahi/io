<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Entity */

$this->title = Yii::t('app', 'Create Entity');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Entities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entity-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
