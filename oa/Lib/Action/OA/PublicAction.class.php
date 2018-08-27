<?php
class PublicAction extends Action{
    Public function verify(){
    	ob_clean();//清空缓存，不然验证码图片无法加载
        import('ORG.Util.Image');
        Image::buildImageVerify();
    }
}
?>