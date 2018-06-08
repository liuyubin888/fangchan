<?php
namespace Extracts\CacheMysql;
use app\index\model\CoreCache;
class CacheMysql{
    static public function cache_read($key) {
    	$CacheModel = new CoreCache();

    	$data = $CacheModel->field('value')->where('key',$key)->find();
        
        if($data){
            $data = $data->toArray();
            return unserialize($data['value']);
        }else{
            return array();
        }
    	
    }

    static public function cache_write($key,$data) {
    	if (empty($key) || !isset($data)) {
			return false;
		}
    	$CacheModel = new CoreCache();
    	$cache = $CacheModel->field('value')->where('key',$key)->find();
    	if($cache){
    		$data = [
	            'value'=>serialize($data),
	        ];
	        $ret = $CacheModel->where('key',$key)->update($data);
    	}else{
    		$record = array();
			$record['key'] = $key;
			$record['value'] = serialize($data);
			$CacheModel->data($record);
			$ret = $CacheModel->save();
    	}
    	return $ret;
        
    }
}