<?php
namespace App\Traits;

trait CustomPaginationTraits {
	public static function isPage($page_no=null)
    {
		$result = array();
		if($page_no>=2)
		{
			$result['offset'] = ($page_no-1) * 40;
			$result['next_page'] = $page_no + 1;
			$result['pre_page'] = $page_no - 1;
		}
		else
		{
			$result['offset'] = 2;
			$result['next_page'] = 2;
			$result['pre_page'] = 0;
		}
		
		return $result;

    }
	public static function prevPage($pageNo)
	{
		
	}
 
}