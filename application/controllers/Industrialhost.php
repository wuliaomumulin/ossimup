<?php

use App\Models\Industrialhost;

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

class IndustrialhostController extends Base
{
    public function init()
    {
        parent::init();

        $this->model = new Industrialhost();

    }

    public function getIndustrialHostVulAction()
    {
        $res = $this->model->getIndustrialHostVul();

        $data['total_num'] = count($res);
        $data['total_page'] = ceil($data['total_num'] / 50);
        $database = $this->split($res, 50);
        $page = input('page', 1);
        $data['list'] = $database[$page - 1];
        jsonResult($data);
    }

    //数组按指定10个为一组的分割数组
    public function split($data, $num = 5)
    {

        $arrRet = array();
        if (!isset($data) || empty($data)) {
            return $arrRet;
        }

        $iCount = count($data) / $num;
        if (!is_int($iCount)) {
            $iCount = ceil($iCount);
        } else {
            $iCount += 1;
        }
        for ($i = 0; $i < $iCount; ++$i) {
            $arrInfos = array_slice($data, $i * $num, $num);
            if (empty($arrInfos)) {
                continue;
            }
            $arrRet[] = $arrInfos;
            unset($arrInfos);
        }

        return $arrRet;

    }
}

