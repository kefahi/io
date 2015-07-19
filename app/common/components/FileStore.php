<?php

namespace common\components;

use \yii\base\Component;
use \yii\base\InvalidConfigException;

class FileStore extends Component {
	public $path;
  public $ignore = ['.', '..', 'index.sqlite3'];
  public $index_path;
  private $db;
  private $command;
  private $finfo;

  public function init() {
// Make sure you have read access to all files chmod -R . g+r , and read/execute access to all folders: find ./ -type d -exec chmod g+x {} \;
		echo "Initializing Filestore, {$this->index_path}\n";
    $this->db = new \yii\db\Connection (['dsn' => "sqlite:{$this->index_path}/index.sqlite3"]);
    $this->db->open();
		$ddl_sql = <<<EOL
			DROP TABLE IF EXISTS Entry;
			CREATE TABLE IF NOT EXISTS Entry (
				id INTEGER PRIMARY KEY,
				embdedded BLOB NULL, -- if this is null then its an external file
				path TEXT NULL,
        name TEXT NULL,
				checksum TEXT NULL, -- make it not null later
				-- 'type' TEXT NOT NULL,
				byte_size INTEGER NOT NULL,
				format TEXT NULL, -- Mime
				device INTEGER NULL, -- Device id. Null in non-linux
				inode INTEGER NULL, -- I-Node. Null in non-linux
				nlink INTEGER NULL, -- Number of links. Null in non-linux
				uid INTEGER NULL, -- User Id. Null on windows
				gid INTEGER NULL, -- Group Id. Null on windows
				access_time INTEGER,
				create_time INTEGER,
				modify_time INTEGER,
				block_size INTEGER, -- Could be null on non-linux
				blocks INTEGER, -- Could be null on non-linux
				UNIQUE (path,name)
			);
		CREATE INDEX entry_checksum on Entry (checksum);
EOL;

    $this->db->pdo->exec($ddl_sql);
		$this->command = $this->db->createCommand();

		$this->finfo = finfo_open(FILEINFO_MIME_TYPE);
  }

  public function __destruct() {
		echo "Destructor called\n";
		if(isset($this->db)) $this->db->close();
    if(isset($this->finfo)) finfo_close($this->finfo);
	}

  /*private $stats = ['total'=>0, 'folders'=>0, 'files'=>0, 'skips'=>0];
	private function stats ($type) {
			switch($type) {
				case 'directory':
					$this->stats['folders'] +=1;
					break;
				case 'regular':
					$this->stats['files'] +=1;
					break;
				default:
					$this->stats['skips'] +=1;
					break;
			}
			$this->stats['total']+=1;
	}*/

  public function index() {
		$cmd = 'find ./ -type f ' ;
		$last = $this->index_path . 'index.last';
		if(file_exists($last)) 
			$cmd .= " -newer '$last'";

		$descs = array(0=>array('pipe','r'),1=>array('pipe','w'),2=>array('pipe','w'));
    echo "cmd $cmd ; path {$this->path}\n";
		$p = proc_open($cmd, $descs, $pipes, $this->path, array());
		if (is_resource($p)) {
			$count = 0;
			$transaction = $this->db->beginTransaction();
			$start = time();
			try {
				while ($one = fgets($pipes[1])) {
					$one = substr($one,0,-1); // Remove the trailing newline
					$one_pathname= $this->path . $one;
					if(++$count % 1000 == 0) {
						$elapsed = time() - $start;
						echo "\rCount $count/$elapsed";
					}
					if(!is_file($one_pathname)){ 
						echo "\nSkipping file $one_pathname not a file\n";
						continue;
					}

					$previous = error_reporting(E_ERROR); // Suppress E_WARNING caused by lstat
					$stat = lstat($one_pathname);
					error_reporting($previous);
					if(empty($stat) ) {
						echo "\nSkipping file $one_pathname lstat failed\n";
						continue;
					}
					$info = pathinfo($one);
					$data = array();
					$data['path'] = $info['dirname'];
					$data['name']= $info['basename'];
					$data['byte_size'] = $stat['size'];
					$data['device'] = $stat['dev'];
					$data['inode'] = $stat['ino'];
					$data['nlink'] = $stat['nlink'];
					$data['uid'] = $stat['uid'];
					$data['gid'] = $stat['gid'];
					$data['access_time'] = $stat['atime'];
					$data['modify_time'] = $stat['mtime'];
					$data['create_time'] = $stat['ctime'];
					$data['block_size'] = $stat['blksize'];
					$data['blocks'] = $stat['blocks'];
					// $data['checksum'] = hash_file('sha256', $one_pathname);
					/*$mime = finfo_file($this->finfo, $one);
					echo "Entry: $item type: $mime \n";
					$data['format'] = $mime;*/

					$this->command->insert('Entry', $data)->execute();
				}
				echo "\nCommitting transaction\n";
				$transaction->commit();
			} catch (Exception $e) {
				$transaction->rollback();
				throw $e;
			}
			echo "\nCompleted processing $count file\n";
			$count = 0;
			while ($one = fgets($pipes[2])) {
				$count++;
			}
			if($count >0)
				echo "Failed to process $count files\n";
			fclose($pipes[0]);
			fclose($pipes[1]);
			fclose($pipes[2]);
			$return = proc_close($p);
			echo "Command returnd $return\n";
		}
		if(!touch($last)) 
			echo "Touching file failed $last\n";
	}

	public function index_old($path = '.') { // Indexes all files/folders under $this->path 
		$list = scandir("{$this->path}/$path" );
    echo "processing path $path\n";
    foreach($list as $item) {
			if(in_array($item, $this->ignore))
				continue;
			$one = "{$this->path}/$path/$item";
      $stat = lstat($one);
			$mode = file_type($stat['mode']);
      //$this->stats($mode);
      if($mode != 'directory' && $mode != 'regular')
				continue; // Skip non-file/folder
			
			/*print_r($stat);
			$attrs = xattr_list($one);
			foreach($attrs as $attr) {
				echo 'Attribute ' . $attr . ' is ' . xattr_get($one, $attr) . PHP_EOL;
			}*/
			$data = array();
			$data['path'] = $path;
      $data['name']= $item;
			$data['byte_size'] = $stat['size'];
			$data['device'] = $stat['dev'];
			$data['inode'] = $stat['ino'];
			$data['nlink'] = $stat['nlink'];
      $data['uid'] = $stat['uid'];
      $data['gid'] = $stat['gid'];
			$data['access_time'] = $stat['atime'];
			$data['modify_time'] = $stat['mtime'];
			$data['create_time'] = $stat['ctime'];
			$data['block_size'] = $stat['blksize'];
			$data['blocks'] = $stat['blocks'];
      $data['checksum'] = hash_file('sha256', $one);
 			$data['type'] = $mode;
      $mime = finfo_file($this->finfo, $one);
      echo "Entry: $item type: $mime \n";
			$data['format'] = $mime;

			$this->command->insert('Entry', $data)->execute();
      echo "Processed {$this->stats['files']} files,  {$this->stats['folders']} folders out of  {$this->stats['total']}\n";
			if('directory' == $data['type'])
				$this->index("{$path}/{$item}");
		}
  }
}

function file_type($mode) {
  $type = null;
	switch ($mode & 0170000) {
		case 0040000: 
			$type = 'directory';
			break;
		case 0100000:
			$type = 'regular';
			break;
		default: 
			$type = 'other';
			break;
	}

/*
S_IFMT     0170000   bit mask for the file type bit fields
S_IFSOCK   0140000   socket
S_IFLNK    0120000   symbolic link
S_IFREG    0100000   regular file
S_IFBLK    0060000   block device
S_IFDIR    0040000   directory
S_IFCHR    0020000   character device
S_IFIFO    0010000   FIFO
*/
	return $type;
}
