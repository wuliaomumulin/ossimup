<?php

namespace App\Models;

class Industrialhost extends Model
{

    protected $dbName = 'alienvault';
    protected $tableName = 'vuln';
    protected $tablePrefix = 'ics_';
    protected $pk = 'id';


    public function getIndustrialHostVul()
    {
        $da = $this->table('host a')->field('a.hostname,INET6_NTOA(d.ip) ip,c.extra')->join('left join host_types b on a.id = b.host_id left join host_properties c on b.host_id = c.host_id left join host_ip d on a.id = d.host_id')->where(['b.type' => 6])->select();

        //过滤掉平台 采集器 监测审计
        $data = $this->table('udp_sensor')->field('ip')->select();

        foreach ($da as $a => $b){
            foreach ($data as $c => $d){
                if($b['ip'] == $d['ip']){
                    unset($da[$a]);
                }
            }
        }
        $da = array_values($da);

        foreach ($da as $key => $value) {
            $da[$key]['ext'] = [];
            if (strstr($value['extra'], ',') === false) {
                array_push($da[$key]['ext'], $value['extra']);
            } else {
                $arr = explode(',', $value['extra']);
                foreach ($arr as $ke => $val) {
                    array_push($da[$key]['ext'], $val);
                }
            }
            unset($da[$key]['extra']);
        }

        foreach ($da as $k => $v) {

            foreach ($v['ext'] as $kk => $vv) {

                $list = $this->field('id,vul_title,vul_type,vul_class,vul_field,vul_tag,cnnvd,cnvd,vul_info,vul_position,vul_cause_clase,product,public_time')->where(['product' => ['like', "%{$vv}%"]])->select();

                if (!empty($list)) {
                    $list[0]['hostname'] = $v['hostname'];
                    $list[0]['ip'] = $v['ip'];
                    unset($da[$k]['ext']);
                    unset($da[$k]['hostname']);
                    unset($da[$k]['ip']);
                    $da[$k] = $list[0];
                    break;
                }

            }
        }
        return $da;
        
    }

}

?>