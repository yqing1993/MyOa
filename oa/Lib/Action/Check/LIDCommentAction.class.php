<?php
class LIDCommentAction extends loginAction {
	function __construct(){
		parent::__construct();
		role('LIDComment',3);
	}

	public function index(){echo "666";
			ob_end_clean();
			header('HTTP/1.1 301 Moved Permanently');
			header('Location:'.C('OrderUrl').'oadd.php/CommentManage/');
		
	}

}	