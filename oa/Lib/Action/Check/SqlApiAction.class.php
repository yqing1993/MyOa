<?php
class SqlApiAction extends Action {
	function __construct(){
		parent::__construct();
	}

	public function index(){
		$Str = I('post.Str','');
		if($Str == ''){
			$arr = D("SqlApi")->decode($Str);
			//$arr = array('type'=>'Group','data'=>array('database'=>'order','para'=>'and id = 1'));
			if(!empty($arr) && isset($arr['data']) && isset($arr['type']) && $arr['type'] != ''){
				$GetData = $arr['data'];

				if($arr['type'] == 'Sql' || $arr['type'] == 'Group' || $arr['type'] == 'SqlPage'){
					$DB_NAME = (isset($GetData['DB_NAME']) && $GetData['DB_NAME'] != '') ? $GetData['DB_NAME'] : 'order';
					$DB_PREFIX = (isset($GetData['DB_PREFIX']) && $GetData['DB_PREFIX'] != '') ? $GetData['DB_PREFIX'] : 'o_';
					$database = (isset($GetData['database']) && $GetData['database'] != '') ? $GetData['database'] : '';
					$para = (isset($GetData['para']) && $GetData['para'] != '') ? $GetData['para'] : '';
    				$field = (isset($GetData['field']) && $GetData['field'] != '') ? $GetData['field'] : '';
    				$group = (isset($GetData['group']) && $GetData['group'] != '') ? $GetData['group'] : '';
    				$order = (isset($GetData['order']) && $GetData['order'] != '') ? $GetData['order'] : 'id desc';
    				$limit = (isset($GetData['limit']) && $GetData['limit'] != '') ? $GetData['limit'] : 'id desc';
    				$page = (isset($GetData['page']) && $GetData['page'] != '') ? $GetData['page'] : 1;
    				$way = (isset($GetData['way']) && $GetData['way'] != '') ? $GetData['way'] : 'select';

    				if($database != ''){
    					C('DB_NAME',$DB_NAME);
    					C('DB_PREFIX',$DB_PREFIX);

    					if($arr['type'] == 'Group'){
    						$data = m($database)->where("1=1 ".$para)->field($field)->group($group)->order($order)->$way();
    						
    						echo json_encode(array('error'=>'1','data'=>$data));
    					}
    				}


				}
			}
		}
	}
}