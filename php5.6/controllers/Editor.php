<?php

use App\Models\Editor;

class EditorController extends Base{

    protected $editor;

    public function init(){
        $this->editor=new Editor();
    }
    //获取列表  全部的插件文件 包括启用和禁用的
    public function getlistAction(){

        $name = input('get.name');

        $plugins=$this->editor->get_all_plugins($name);

        jsonResult($plugins);
    }

    //获取已经启用的插件
    public function getEnabledPluginAction(){
        $name = input('get.name');
        $content=$this->editor->get_enabled_plugins($name);

        foreach ($content as $k => $v) {
            list($rs[$k]['name'],$rs[$k]['path']) = explode('=', $v);
            $explode =  explode('=', $v);
            $rs[$k]['path'] = $rs[$k]['realname']=$explode[count($explode)-1];
            //unset($rs[$k]['path']);
        }
        jsonResult($rs);
    }
    /**
     * @abstract 获取全部的可以用的插件(被禁用的插件)
     * @return [type] [description]
     */
    public function getUnenabledPluginAction(){

        $name = input('get.name');

        $all = array_column($this->editor->get_all_plugins(), 'path');

        $enabled = $this->editor->get_enabled_plugins();



        // var_dump($all);die;
        foreach ($all as $k => $v) {
            $explode =  explode('/', $v);
            $rs[$k]['name'] = $rs[$k]['path']= $rs[$k]['realname'] =$explode[count($explode)-1];
            // $rs[$k]['path'] = $v;

        }
        foreach ($rs as $k => $v) {

            foreach ($enabled as $ke => $va) {
                if (strpos($va, $v['name']) !== false) {
                    unset($rs[$k]);continue;
                }
            }

            if (!empty($name) && strpos($v,$name ) === false)  unset($rs[$k]);

        }
        jsonResult(array_values($rs));
    }

    //获取文件内容
    public function getPluginInfoAction(){

        $url = urldecode(input('get.realname'));

        !$url && jsonError('无效的参数');

        //限定只可以获取插件内容
        if(strpos($url,'.cfg') === false) jsonError('您无权获取此文件详情');

        $url = 'etc/agent/plugins/'.$url;

        $content=$this->editor->get_plugin_content($url);

        if($content == 'Not Found') jsonError('未查询到此文件');

        jsonResult($content['content']);
    }

    /**
     * @abstract 保存一个新的插件
     * @param  name  文件名称
     * @param  content 文件内容
     * @return [type] [description]
     */
    public function savePluginAction(){

        $name = input('post.realname');
        $content =   html_entity_decode(input('post.content'));
        //$name = 'for_test';
        //$content = 'demodemodemodemodemodemodmeocontentcontentcontentcontentcontentcontentcontentcontentcontentcontentcontent';

        !$name && jsonError('无效的参数');
        !$content && jsonError('无效的参数');
        //验证文件后缀 补齐
        if (explode('.', $name)[1] != 'cfg') $name .= '.cfg';

        //先创建这个文件
        $rs = $this->editor->createFile($name);// var_dump($rs);die;
        //如果文件创建成功 开始写入文件
        if ($rs) {
            $resu = $this->editor->save_file_content($name,$content);
            $this->logger(46);
            jsonResult($resu);
        }
        $this->logger(94);
        jsonError('保存失败');
    }

    /**
     * @abstract 编辑插件的启用/禁用
     */
    public function setEnabledPluginAction(){

        $array = input('post.')['arr'];



        $enableds = $this->editor->get_enabled_plugins('',true);

        $enabled = $this->editor->get_enabled_plugins_list($enableds);


        foreach ($array as $k => $v) {
            if (empty($v['path'])) jsonError('无效的参数');
            //name 给上默认值
            if(empty($v['name'])) $v['name'] = explode('etc/agent/plugins/', $v['path'])[1];
            $arr[$k] = $v['name'].'='.$v['path'];
        }

        //开始新的插件整合
        foreach ($enabled as $k => $v) {

            foreach ($arr as $ke=> $va) {
                //如果老的插件不在新的插件列表里面  删除掉
                if (!in_array($v, $va)) {
                    unset($enabled[$k]);
                }
            }
        }
        //获取到最新的启用插件列表
        $enabled = array_values(array_unique(array_merge($enabled,$arr)));

        $enabled_list = explode("\n", $enableds['content']);
        //获取最终的写入config.cfg数据   这个里面有点复杂  大概是 先取出 [plugins] 之前的数据，在放入 新的插件列表，最后 在加上[watchdog]之后的数据，组成一个新的大数组
        $new_enabled_list = implode("\n", array_merge(array_slice($enabled_list, 0,(array_search('[plugins]', $enabled_list)+1)),$enabled,[]/*追加一个空数组方便隔开数据*/,array_slice($enabled_list, (array_search('[watchdog]', $enabled_list)-1))));
        //数据写入
        $rs = $this->editor->save_file_content('config.cfg',$new_enabled_list);
        //大功告成
        $this->logger(46);
        jsonResult($rs);
    }

    public function setEnabledAction(){

        $path = $_POST['path'];

        if(!is_array($path)) jsonError('无效的请求');
    }

    public function deleteAction(){
        $this->editor->deleteFile("tst");
    }

    public function createAction(){
        jsonResult($this->editor->createFile("test"));
    }
}
