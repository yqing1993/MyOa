<?php

    class AlreadySendAction extends loginAction{
        function __construct(){
            parent::__construct();
            role('PublishMessage',3);
        }
        public function index(){
            $this->assign('ActionName',$this->getActionName());
            $this->display();
        }


        public function AlreadySendPage(){
            //根据当前的id去查找
            $data = DataPage(array('database'=>'message','order'=>'addtime desc'));
            //获取已读未读人数  部门
            foreach ($data['list'] as $key => $value){

                if($value['readed'] == ''){
                    $readed = 0;
                }else{
                    $readed = count(explode(',',$value['readed']));
                }

                if($value['noread'] == ''){
                    $noread = 0;
                }else{
                    $noread = count(explode(',',$value['noread']));
                }

                $dapts = explode(',',$value['dapts']);
                $d_name = array();
                for($i=0;$i<count($dapts);$i++){
                    $n = m('department')->where("id = $dapts[$i]")->field('DepartmentName')->find();
                    $n = $n['DepartmentName'];
                    $d_name[] = $n;
                }
                $data['list'][$key]['noread_man'] = $noread;
                $data['list'][$key]['readed_man'] = $readed;
                $data['list'][$key]['d_name'] = implode(',',$d_name);
            }
            echo json_encode($data);
        }

        public function AlreadySendData(){
            $id = I('post.id');

            if ($id != '') {
                $operate = I('post.operate', '');
                $database = 'message';
                if ($operate == 'Get') {
                    $info = m($database)->where("id = '" . $id . "'")->find();

                    if (!empty($info)) {
                        $info['error'] = '1';
                        $info['content'] = htmlspecialchars_decode($info['content']);
                        echo json_encode($info);//$this->display();
                    } else {
                        alert("该条信息没有找到，请刷新页面重试");
                    }
                }elseif($operate == 'del'){
                    $info = m($database)->where("id = '" . $id . "'")->find();

                    if (!empty($info)) {
                        $res = m($database)->where("id = '" . $id . "'")->delete();
                        if($res){
                            records('MessageDelete',$_SESSION['username'].'删除了消息：'.$info['title']);
                            alert('删除成功',1);
                        }else{
                            alert("删除失败，请刷新页面重试");
                        }
                    } else {
                        alert("该条信息没有找到，请刷新页面重试");
                    }
                }else{
                    alert("不知道你要干啥");
                }
            } else {
                alert("出错，提交的ID为空");
            }
        }

    }