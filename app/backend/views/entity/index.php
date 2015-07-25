<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Entities');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="entity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Create Entity'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'uri',
            'source',
            'updated_at',
            // 'created_at',
            // 'owner_id',
            // 'author_id',
            // 'description',
            // 'public_key',
            // 'credentials',
            // 'type',
            // 'sub_type',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
