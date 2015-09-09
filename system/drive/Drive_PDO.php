<?php
/**
 * PDO驱动类
 * @author kubi
 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo
 */
class Drive_PDO {
	private $pdo;
	public function __construct($dsn,$username,$passwd,$option,$sql=NULL){
	    $this->pdo = new PDO($dsn, $username, $passwd, $option);
        if(!empty($sql)){
            $this->pdo->exec($sql);
        }
	    $this->pdo_trigger_error(null,null,$this->pdo->errorInfo ());
	}

	/**
	 * 查询数据库操作
	 * @param string $sql
	 * @param array $params
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-query
	 * @return unknown|boolean
	 */
	public function query($sql, $params = array()) {
		if (empty ( $params )) {
			$result = $this->pdo->exec ( $sql );
			if (bootstrap::$debug) {
				$this->pdo_trigger_error($sql,null,$this->pdo->errorInfo ());
			}
			return $result;
		}
		$statement = $this->pdo->prepare ( $sql );
		$result = $statement->execute ( $params );
		$this->pdo_trigger_error($sql,$params,$statement->errorInfo ());
		
		if (! $result) {
			return false;
		} else {
			return $this->pdo->lastInsertId ();
		}
	}

	/**
	 * 获取列操作
	 * @param string $sql sql
	 * @param array $params 查询参数
	 * @param number $column 列
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-fetchcolumn
	 * @return boolean
	 */
	public function fetchcolumn($sql, $params = array(), $column = 0) {
		$statement = $this->pdo->prepare ( $sql );
		$result = $statement->execute ( $params );
		$this->pdo_trigger_error($sql,$params,$statement->errorInfo ());
		if (! $result) {
			return false;
		} else {
			return $statement->fetchColumn ( $column );
		}
	}
	
	/**
	 * 获取最后插入数据的ID
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-lastInsertId
	 * @return string
	 */
	public function lastInsertId(){
		return $this->pdo->lastInsertId ();
	}

	/**
	 * 查询单条数据
	 * @param unknown $sql sql
	 * @param unknown $params 参数以:命名
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-fetch
	 * @return boolean|mixed
	 */
	public function fetch($sql, $params = array()) {
		$statement = $this->pdo->prepare ( $sql );
		$result = $statement->execute ( $params );
		$this->pdo_trigger_error($sql,$params,$statement->errorInfo ());
		if (! $result) {
			return false;
		} else {
			return $statement->fetch ( PDO::FETCH_ASSOC );
		}
	}

	/**
	 * 查询所有数据
	 * @param string $sql sql
	 * @param array $params 传递的参数,以:开始
	 * @param string $keyfield
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-fetchAll
	 * @return boolean|multitype:unknown
	 */
	public function fetchAll($sql, $params = array(), $keyfield = '') {
		$statement = $this->pdo->prepare($sql );
		$result = $statement->execute ( $params );
		$this->pdo_trigger_error($sql,$params,$statement->errorInfo ());
		if (! $result) {
			return false;
		} else {
			if (empty ( $keyfield )) {
				$r= $statement->fetchAll ( PDO::FETCH_ASSOC );
				return $r;
			} else {
				$temp = $statement->fetchAll ( PDO::FETCH_ASSOC );
				$rs = array ();
				if (! empty ( $temp )) {
					foreach ( $temp as $key => &$row ) {
						if (isset ( $row [$keyfield] )) {
							$rs [$row [$keyfield]] = $row;
						} else {
							$rs [] = $row;
						}
					}
				}
				return $rs;
			}
		}
	}

	/**
	 * 更新数据
	 * @param string $table 表名
	 * @param array $data	数据数组
	 * @param array $params 更新条件
	 * @param string $glue 条件与/或
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-update
	 * @return Ambigous <unknown, boolean, unknown>
	 */
	public function update($table, $data = array(), $params = array(), $glue = 'AND') {
		$fields = $this->implode ( $data, ',' );
		$condition = $this->implode ( $params, $glue );
		$params = array_merge ( $fields ['params'], $condition ['params'] );
		$sql = "UPDATE " . $this->tablename ( $table ) . " SET {$fields['fields']}";
		$sql .= $condition ['fields'] ? ' WHERE ' . $condition ['fields'] : '';
		return $this->query ( $sql, $params );
	}

	/**
	 * 插入数据
	 * @param string $table 表名
	 * @param array $data 数据数组
	 * @param string $replace 是否使用REPLACE INTO
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-insert
	 * @return Ambigous <unknown, boolean, unknown>
	 */
	public function insert($table, $data = array(), $replace = FALSE) {
		$cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';
		$condition = $this->implode ( $data, ',' );
		return $this->query ( "$cmd " . $this->tablename ( $table ) . " SET {$condition['fields']}", $condition ['params'] );
	}

	/**
	 * 删除字段
	 * @param string $table	表名
	 * @param array $params	条件
	 * @param string $glue 条件与或
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-delete
	 * @return Ambigous <unknown, boolean, unknown>
	 */
	public function delete($table, $params = array(), $glue = 'AND') {
		$condition = $this->implode ( $params, $glue );
		$sql = "DELETE FROM " . $this->tablename ( $table );
		$sql .= $condition ['fields'] ? ' WHERE ' . $condition ['fields'] : '';
		return $this->query ( $sql, $condition ['params'] );
	}

	/**
	 * 事务开始
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-delete
	 */
	public function begin() {
		$this->pdo->beginTransaction ();
	}

	/**
	 * 确认事务
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-commit
	 */
	public function commit() {
		$this->pdo->commit ();
	}

	/**
	 * 回滚
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-rollback
	 */
	public function rollback() {
		$this->pdo->rollBack ();
	}

	private function implode($params, $glue = ',') {
		$result = array (
				'fields' => ' 1 ',
				'params' => array ()
		);
		$split = '';
		$suffix = '';
		if (in_array ( strtolower ( $glue ), array (
				'and',
				'or'
		) )) {
			$suffix = '__';
		}
		if (! is_array ( $params )) {
			$result ['fields'] = $params;
			return $result;
		}
		if (is_array ( $params )) {
			$result ['fields'] = '';
			foreach ( $params as $fields => $value ) {
				$result ['fields'] .= $split . "`$fields` =  :{$suffix}$fields";
				$split = ' ' . $glue . ' ';
				$result ['params'] [":{$suffix}$fields"] = is_null ( $value ) ? '' : $value;
			}
		}
		return $result;
	}

	/**
	 * 执行sql
	 * @param unknown $sql 查询语句
	 * @param unknown $tablepre 表名
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-run
	 */
	public function run($sql, $tablepre) {
		if (! isset ( $sql ) || empty ( $sql ))
			return;

		$sql = str_replace ( "\r", "\n", str_replace ( $tablepre, $sql ) );
		$sql = str_replace ( "\r", "\n", str_replace ( $tablepre, $sql ) );
		$ret = array ();
		$num = 0;
		foreach ( explode ( ";\n", trim ( $sql ) ) as $query ) {
			$ret [$num] = '';
			$queries = explode ( "\n", trim ( $query ) );
			foreach ( $queries as $query ) {
				$ret [$num] .= (isset ( $query [0] ) && $query [0] == '#') || (isset ( $query [1] ) && isset ( $query [1] ) && $query [0] . $query [1] == '--') ? '' : $query;
			}
			$num ++;
		}
		unset ( $sql );
		foreach ( $ret as $query ) {
			$query = trim ( $query );
			if ($query) {
				$this->query ( $query );
			}
		}
	}

	/**
	 * 显示字段是否存在
	 * @param unknown $tablename 表名
	 * @param unknown $fieldname 字段名
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-fieldexists
	 * @return boolean
	 */
	public function fieldexists($tablename, $fieldname) {
		$isexists = $this->fetch ( "DESCRIBE " . $this->tablename ( $tablename ) . " `{$fieldname}`" );
		return ! empty ( $isexists ) ? true : false;
	}

	/**
	 * 显示主键是否存在
	 * @param unknown $tablename 表名
	 * @param unknown $indexname 索引名
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-indexname
	 * @return boolean
	 */
	public function indexexists($tablename, $indexname) {
		if (! empty ( $indexname )) {
			$indexs = $this->fetchall ( "SHOW INDEX FROM " . $this->tablename ( $tablename ) );
			if (! empty ( $indexs ) && is_array ( $indexs )) {
				foreach ( $indexs as $row ) {
					if ($row ['Key_name'] == $indexname) {
						return true;
					}
				}
			}
		}
		return false;
	}

	/**
	 * 获取表名
	 * @param unknown $table 表名
	 * @return string
	 * @link http://www.xiaokubi.com/kubiphp/?tag=drive_pdo-tablename
	 */
	public function tablename($table) {
		return "`$table`";
	}

	/**
	 * 调试输出
	 * @param unknown $sql
	 * @param string $params
	 * @param string $error
	 */
	private function pdo_trigger_error($sql,$params=null,$error=null){
	    if(!bootstrap::$debug) return;
        if($error && $error[0]!= '00000'){
        	$text=sprintf(
        			"[error %s(%s)]%s;%s",
        			(string)$error[1],
        			(string)$error[0],
        			(string)$error[2],
        			json_encode($params));
        	kubi_error_tigger($text,500);
        }
	}
	
	function __destruct(){
		$this->pdo=null;
	}
}
?>