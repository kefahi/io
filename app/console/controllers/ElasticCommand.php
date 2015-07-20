<?php
namespace console\controllers;
use Yii;

class ElasticController extends \yii\console\Controller {
  const LOG = 'elastic';
  public $mongodb = 'trackerdb';
  public $mapping = array (
  "embedded" => array('type'=>'string', 'index'=>'not_analyzed'),
  "path" => array('type'=>'string', 'index'=>'not_analyzed'),
  "name" => array('type'=>'string', 'index'=>'not_analyzed'),
  "checksum" => array('type'=>'string', 'index'=>'not_analyzed'),
	"byte_size" => array('type' => 'integer'),
  "format" => array('type'=>'string', 'index'=>'not_analyzed'),
	"device" => array('type' => 'integer'),
	"inode" => array('type' => 'integer'),
	"nlink" => array('type' => 'integer'),
	"uid" => array('type' => 'integer'),
	"gid" => array('type' => 'integer'),
	"access_time" => array('type' => 'date'),
	"create_time" => array('type' => 'date'),
	"modify_time" => array('type' => 'date'),
	"block_size" => array('type' => 'integer'),
	"blocks" => array('type' => 'integer'),
  );

  public function actionCreateIndex($index) {
    Yii::app()->elastic->index = $index;
    try { Yii::app()->elastic->createIndex(); } catch (Exception $e) { echo $e->getMessage(), PHP_EOL; }
  }

  public function actionDelete($index, $type = null) {
    Yii::app()->elastic->index = $index;
    try { Yii::app()->elastic->delete($type); } catch (Exception $e) { echo $e->getMessage(), PHP_EOL; }
  }

  public function actionMap($index, $type) {
    Yii::app()->elastic->index = $index;
    try { Yii::app()->elastic->map($type, $this->mapping); } catch (Exception $e) { echo $e->getMessage(), PHP_EOL; }
  }

  public function actionUpdate($index, $type, $id, $doc){
    Yii::app()->elastic->index = $index;
    try { Yii::app()->elastic->update($type , $id, $doc); } catch (Exception $e) {echo $e->getMessage(), PHP_EOL;}
  }

  public function actionSearch($index, $type, $term) {
    Yii::app()->elastic->index = $index;
    try { $response = Yii::app()->elastic->search($type, $term); } catch (Exception $e) { echo $e->getMessage(), PHP_EOL; }
    echo $response, PHP_EOL;
  }
 
  public function actionInsert($index, $type , $doc){
    Yii::app()->elastic->index = $index;
    try{  $response = Yii::app()->elastic->insert($type, $doc); } catch (Exception $e) { echo $e->getMessage(), PHP_EOL;}
    echo $response, PHP_EOL;
  }

  public function actionImport($index, $type, $time_unit='context.created_at') {
    Yii::app()->elastic->index = $index;
    $counter = 0;

    while (true) {
      $progress = 0;
      $time1 = microtime(true);
      echo "Import started at: ". date('c') . " Index / Type  $index/$type", PHP_EOL;

      $docs = array();
      foreach ($cursor as $doc) {
        try {
          $data = Elastic::mapData($this->mapping,$doc);
          if (isset($data['user']['state'])) {
            $state = strtolower($data['user']['state']);
            if (!empty($state) && isset($this->states[$state])) {
              $data['user']['state'] = $this->states[$state];
            }
          }
          $data['_id'] = $doc['_id'];
          $docs [] = $data;

          $counter++;
          $progress++;
          if ($incremental) {
           if($time_unit == 'batch_sequence'){
            $max_sequence = $doc['batch_sequence'];
           }
            if ($time_unit == 'context.created_at') {
              $max_time = $doc['context']['created_at'];
            } elseif ($time_unit == 'created_at') {
               $max_time = $doc['created_at'];
            }
          }

          if ($counter % 5000 == 0) {
            $time2 = microtime(true);
            Yii::app()->elastic->import($type, $docs);
            echo "Time to prepare " . ($time2-$time1) . " Time to push " . (microtime(true) - $time2) , PHP_EOL;
            $docs = array();
            echo "Completed processing $counter", PHP_EOL;
          }
        } catch (Exception $e) {
          print_r($doc);
          throw $e;
        }
      }

      if ($docs) {
        echo "Processing less than 5000 $progress \n";
        Yii::app()->elastic->import($type, $docs);
        $docs = array();
      }

      if (isset($max_time)) {
        file_put_contents($timestamp_file, $max_time->sec);
      }

      if (isset($max_sequence)) {
        file_put_contents($sequence_file, $max_sequence->sec);
      }
 

      echo "Processed $progress items\n";
      if ($incremental == 0 && $progress == 0) {
        die();
      }

      if ($progress < 1000) {
        break;
      }
    }

  }

}
