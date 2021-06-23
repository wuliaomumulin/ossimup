<?php
namespace App\Models;

class SystemNews extends Model{
    protected $tableName = 'system_news';
    protected $tablePrefix = '';
    protected $pk = 'id';


    public function __construct($where=[]){
      parent::__construct();


    } 


}