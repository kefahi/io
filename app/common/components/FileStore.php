<?php

namespace common\components;

use \yii\base\Component;
use \yii\base\InvalidConfigException;

class FileStore extends Component {
	public $path;
  public $ignore = ['.', '..', 'index.sqlite3'];
  private $db;
  private $command;
  private $finfo;
  private $stats = ['total'=>0, 'folders'=>0, 'files'=>0, 'other'=>0];

  public function init() {
    $this->db = new \yii\db\Connection (['dsn' => "sqlite:{$this->path}/index.sqlite3"]);
    $this->db->open();
		$ddl_sql = <<<EOL
			DROP TABLE IF EXISTS Entry;
			CREATE TABLE IF NOT EXISTS Entry (
				id INTEGER PRIMARY KEY,
				embdedded BLOB NULL,
				path TEXT NULL,
        name TEXT NULL,
				checksum TEXT NOT NULL,
				'type' TEXT NOT NULL,
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
				UNIQUE (path, name)
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

	public function index($path = '.') { // Indexes all files/folders under $this->path 
		$list = scandir("{$this->path}/$path" );
    echo "processing path $path\n";
    foreach($list as $item) {
			if(in_array($item, $this->ignore))
				continue;
			$one = "{$this->path}/$path/$item";
      $stat = stat($one);
      $mime = finfo_file($this->finfo, $one);
      echo "Entry: $item type: $mime \n";
			
			/*print_r($stat);
			$attrs = xattr_list($one);
			foreach($attrs as $attr) {
				echo 'Attribute ' . $attr . ' is ' . xattr_get($one, $attr) . PHP_EOL;
			}*/
			$data = array();
			$data['path'] = $path;
      $data['name']= $item;
			$data['byte_size'] = $stat['size'];
			$data['format'] = $mime;
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
 			$data['type'] = file_type($stat['mode']);

			$this->command->insert('Entry', $data)->execute();
			//print_r($data);
			switch($data['type']) {
				case 'directory':
					$this->stats['folders'] +=1;
					break;
				case 'regular':
					$this->stats['files'] +=1;
					break;
				default:
					$this->stats['other'] +=1;
					break;
			}
			$this->stats['total']+=1;
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
