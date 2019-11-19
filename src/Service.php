<?php
/**
 * |-----------------------------------------------------------------------------------
 * @Copyright (c) 2014-2018, http://www.sizhijie.com. All Rights Reserved.
 * @Website: www.sizhijie.com
 * @Version: 思智捷信息科技有限公司
 * @Author : szjcomo 
 * |-----------------------------------------------------------------------------------
 */
namespace szjcomo\odaHelp;

use EasySwoole\Spl\SplBean 	 as EasySwooleBean;

/**
 * 生成service类
 */
class Service
{
	/**
	 * [$beanClass 依赖于beanclass]
	 * @var null
	 */
	protected $beanClass = null;
	/**
	 * [$beanName beanClass类名称]
	 * @var string
	 */
	protected $beanName = 'models';
	/**
	 * [$fieldObj 字段信息值]
	 * @var null
	 */
	protected $fieldsObj = [];
	/**
	 * [$indexes 索引字段集]
	 * @var array
	 */
	protected $indexes = [];
	/**
	 * [$pkField 表的主键]
	 * @var string
	 */
	protected $pkField = '';
	/**
	 * [$indexesFlag 索引标签特征]
	 * @var [type]
	 */
	protected $indexesFlag = ['PRI','UNI','MUL'];
	/**
	 * [$tableName 数据库表名]
	 * @var string
	 */
	protected $tableName = '';

	/**
	 * [__construct 构造函数]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 * @param      EasySwooleBean $beanClass [description]
	 */
	public function __construct(EasySwooleBean $beanClass,$beanName = 'models',$fields = [],$tableName = '')
	{	
		$this->beanClass 	= $beanClass;
		$this->beanName 	= $beanName;
		$this->fields 		= $fields;
		$this->tableName 	= $tableName;
		$this->attachFieldObjParase();
	}
	/**
	 * [addServiceTemplate 生成service模版]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	public function addServiceTemplate()
	{
		$template = '';
		$template .= $this->addServiceHeader();
		$template .= $this->addServiceClassName();
		$template .= $this->addServiceListAction();
		$template .= $this->addServiceInfoAction();
		$template .= $this->addServiceAddDataAction();
		$template .= $this->addServiceUdateDataAction();
		$template .= $this->addServiceDelDataAction();
		return $template."}";
	}
	/**
	 * [addServiceTemplateComment 添加类头部注释信息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceTemplateComment()
	{
		$arr 	= ['/**'];
		$arr[] 	= ' * '.$this->tableName.' 数据表操作';
		$arr[] 	= ' * @category  PHP';
		$arr[] 	= ' * @author    szjcomo';
		$arr[] 	= ' * @methods   select,find,delete'.$this->beanName.',update'.$this->beanName.',add'.$this->beanName;
		$arr[] 	= '*/';
		return implode("\r\n",$arr);
	}
	/**
	 * [addServiceHeader 生成service类的头信息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceHeader()
	{
		$str  = '<?php'."\r\n";
		$str .= '/**'."\r\n";
		$str .= '* |-----------------------------------------------------------------------------------'."\r\n";
		$str .= '* copyright (c) 2014-2018, http://www.sizhijie.com. All Rights Reserved.'."\r\n";
		$str .= '* Website: www.sizhijie.com'."\r\n";
		$str .= '* Version: 思智捷信息科技有限公司'."\r\n";
		$str .= '* author : szjcomo '."\r\n";
		$str .= '* |-----------------------------------------------------------------------------------'."\r\n";
		$str .= '*/'."\r\n"."\r\n";
		$str .= 'namespace app\service;'."\r\n"."\r\n";
		$mysqlstr = 'use szjcomo\szjcore\Mysql';
		$beanstr = 'use app\models\\'.$this->beanName;
		$str .= $mysqlstr.str_repeat(' ',40 - strlen($mysqlstr)).' as AppMysql;'."\r\n";
		$str .= $beanstr.str_repeat(' ',40 - strlen($beanstr)).' as App'.$this->beanName.';'."\r\n";
		$str .= 'use app\service\Base'.str_repeat(' ',40 - strlen('use app\service\Base')).' as AppServiceBase;'."\r\n"."\r\n";
		return $str;
	}
	/**
	 * [addServiceClassName 生成类名称]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceClassName()
	{
		$str = $this->addServiceTemplateComment()."\r\n";
		$str .= 'class '.$this->beanName.' extends AppServiceBase'."\r\n";
		$str .= "{"."\r\n";
		return $str;
	}
	/**
	 * [addServiceDelDataAction 删除数据操作]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceDelDataAction()
	{
		$str = $this->addServiceDelDataActionComment();
		$str .= "\t".'public function delete'.$this->beanName.'(array $where = [])'."\r\n";
		$str .= "\t".'{'."\r\n";
		$str .= $this->addServiceDelDataActionMain();
		return $str."\t".'}'."\r\n"."\r\n"."\r\n";
	}
	/**
	 * [addServiceDelDataActionMain 删除数据操作]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceDelDataActionMain()
	{
		$arr = [];
		$arr[] = "\t"."\t".'try {';
		$arr[] = "\t"."\t"."\t".'if(empty($where)) throw new \Exception(\'删除条件不能为空\');';
		$arr[] = "\t"."\t"."\t".'$res = AppMysql::name(\''.str_replace('szj_','',$this->tableName).'\')->where($where)->delete();';
		$arr[] = "\t"."\t"."\t".'if(empty($res)) return $this->appResult(\'数据删除失败\');';
		$arr[] = "\t"."\t"."\t".'return $this->appResult(\'数据删除成功\',$res,false,0);';
		$arr[] = "\t"."\t".'} catch(\Throwable $err) {';
		$arr[] = "\t"."\t"."\t".'return $this->appResult($err->getMessage());';
		$arr[] = "\t"."\t".'}'."\r\n";
		return implode("\r\n",$arr);
	}
	/**
	 * [addServiceDelDataActionComment 删除操作头注释信息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceDelDataActionComment()
	{
		$arr = ["\t".'/**'];
		$arr[] = "\t".' * [delete'.$this->beanName.' 删除表名称 [`'.$this->tableName.'`] 的数据';
		$arr[] = "\t".' * @author'. str_repeat(' ',15 - strlen('@author')) . 'szjcomo';
		$arr[] = "\t".' * @createTime'. str_repeat(' ',15 - strlen('@createTime')). date('Y-m-d');
		$arr[] = "\t".' * @param      array $where [需要传入删除条件]';
		$arr[] = "\t".' * @return     array  [返回一个信息数组]';
		$arr[] = "\t".' */';
		return implode("\r\n",$arr)."\r\n";
	}

	/*======================更新数据操作=========================*/
	/**
	 * [addServiceUdateDataAction 更新数据操作]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceUdateDataAction()
	{
		$str = $this->addServiceUpdateDataActionComment();
		$str .= "\t".'public function update'.$this->beanName.'(App'.$this->beanName.' $bean,array $options = [])'."\r\n";
		$str .= "\t".'{'."\r\n";
		$str .= $this->addServiceUdateDataActionMain();
		return $str."\t".'}'."\r\n"."\r\n"."\r\n";
	}
	/**
	 * [addServiceUdateDataActionMain 更新主体信息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceUdateDataActionMain()
	{
		$arr = [];
		$arr[] = "\t"."\t".'try {';
		$arr[] = "\t"."\t"."\t".'$defaultOptions = [';
		$arr[] = "\t"."\t"."\t"."\t".'\'where\'=>[],';
		$arr[] = "\t"."\t"."\t"."\t".'\'field\'=>['.implode(',',$this->addServiceUdateDataActionField()).']';
		$arr[] = "\t"."\t"."\t".'];';
		$arr[] = "\t"."\t"."\t".'$params = array_merge($defaultOptions,$options);';
		$arr[] = "\t"."\t"."\t".'if(empty($params[\'where\'])) throw new \Exception(\'更新条件不能为空\');';
		$arr[] = "\t"."\t"."\t".'$res    = AppMysql::name(\''.str_replace('szj_','',$this->tableName).'\')->where($params[\'where\'])->update($bean->toArray($params[\'field\'], $bean::FILTER_NOT_NULL));';
		$arr[] = "\t"."\t"."\t".'if(empty($res)) return $this->appResult(\'数据更新失败\');';
		$arr[] = "\t"."\t"."\t".'return $this->appResult(\'数据更新成功\',$res,false,0);';
		$arr[] = "\t"."\t".'} catch(\Throwable $err) {';
		$arr[] = "\t"."\t"."\t".'return $this->appResult($err->getMessage());';
		$arr[] = "\t"."\t".'}'."\r\n";
		return implode("\r\n",$arr);
	}
	/**
	 * [addServiceUdateDataActionField 获取需要更新字段]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceUdateDataActionField()
	{
		$arr = [];
		foreach($this->beanClass->allProperty() as $key=>$val){
			if($val == $this->pkField) continue;
			$arr[] = "'".$val."'";
		}
		return $arr;
	}
	/**
	 * [addServiceUpdateDataActionComment 更新操作的注释文档]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceUpdateDataActionComment()
	{
		$arr = ["\t".'/**'];
		$arr[] = "\t".' * [update'.$this->beanName.' 更新表名称 [`'.$this->tableName.'`] 的数据';
		$arr[] = "\t".' * @author'. str_repeat(' ',15 - strlen('@author')) . 'szjcomo';
		$arr[] = "\t".' * @createTime'. str_repeat(' ',15 - strlen('@createTime')). date('Y-m-d');
		$arr[] = "\t".' * @param      AppArticle $bean [需要传入一个app\models\\'.$this->beanName.'的实例]';
		$arr[] = "\t".' * @param      array $options[field,where] [需要传入更新字段，更新的条件]';
		$arr[] = "\t".' * @return     array  [返回一个信息数组]';
		$arr[] = "\t".' */';
		return implode("\r\n",$arr)."\r\n";
	}
	/*======================插入数据操作=========================*/
	/**
	 * [addServiceAddDataAction 添加数据操作]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceAddDataAction()
	{
		$str = $this->addServiceAddDataComment();
		$str .= "\t".'public function add'.$this->beanName.'(App'.$this->beanName.' $bean)'."\r\n";
		$str .= "\t".'{'."\r\n";
		$str .= $this->addServiceAddDataMain();
		return $str."\t".'}'."\r\n"."\r\n"."\r\n";
	}
	/**
	 * [addServiceAddDataMain 添加数据主体信息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceAddDataMain()
	{
		$arr = [];
		$arr[] = "\t"."\t".'try {';
		$arr[] = "\t"."\t"."\t".'$res = AppMysql::name(\''.str_replace('szj_','',$this->tableName).'\')->insert($bean->toArray(null, $bean::FILTER_NOT_NULL));';
		$arr[] = "\t"."\t"."\t".'if(empty($res)) return $this->appResult(\'数据添加失败\');';
		$arr[] = "\t"."\t"."\t".'return $this->appResult(\'数据添加成功\',$res,false,0);';
		$arr[] = "\t"."\t".'} catch(\Throwable $err) {';
		$arr[] = "\t"."\t"."\t".'return $this->appResult($err->getMessage());';
		$arr[] = "\t"."\t".'}'."\r\n";
		return implode("\r\n",$arr);
	}
	/**
	 * [addServiceAddDataComment 添加方法的注释说明]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceAddDataComment()
	{
		$arr = ["\t".'/**'];
		$arr[] = "\t".' * [add'.$this->beanName.' 新增一条表名称 [`'.$this->tableName.'`] 的数据';
		$arr[] = "\t".' * @author'. str_repeat(' ',15 - strlen('@author')) . 'szjcomo';
		$arr[] = "\t".' * @createTime'. str_repeat(' ',15 - strlen('@createTime')). date('Y-m-d');
		$arr[] = "\t".' * @param      AppArticle $bean [需要传入一个app\models\\'.$this->beanName.'的实例]';
		$arr[] = "\t".' * @return     array  [返回一个信息数组]';
		$arr[] = "\t".' */';
		return implode("\r\n",$arr)."\r\n";
	}
	/*======================获取数据详情操作=========================*/
	/**
	 * [addServiceInfoAction 获取数据详情操作]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceInfoAction()
	{
		$str = $this->addServiceInfoComment();
		$str .= "\t".'public function find(array $options = [])'."\r\n";
		$str .= "\t".'{'."\r\n";
		$str .= $this->addServiceInfoMain();
		$str .= $this->addServiceInfoMainHandler();
		return $str."\t".'}'."\r\n"."\r\n"."\r\n";
	}
	/**
	 * [addServiceInfoComment 生成数据详情注释头信息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceInfoComment()
	{
		$arr = ["\t".'/**'];
		$arr[] = "\t".' * [select 获取表名称 `'.$this->tableName.'` 数据详情]';
		$arr[] = "\t".' * @author'. str_repeat(' ',15 - strlen('@author')) . 'szjcomo';
		$arr[] = "\t".' * @createTime'. str_repeat(' ',15 - strlen('@createTime')). date('Y-m-d');
		$arr[] = "\t".' * @param      array      $options [field,order,where 参数条件]';
		$arr[] = "\t".' * @return     array              [数据详情]';
		$arr[] = "\t".' */'."\r\n";
		return implode("\r\n",$arr);
	}
	/**
	 * [addServiceInfoMain 生成详情查询主题]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceInfoMain()
	{
		$str = "\t"."\t".'try {'."\r\n";
		$str .= "\t"."\t"."\t".'$defaultOptions = ['."\r\n";
		$str.= "\t"."\t"."\t"."\t".'"field"' . str_repeat(' ',10 - strlen('field')) . '=> ['.implode(',', $this->getBeanFields()).'],'."\r\n";
		$str.= "\t"."\t"."\t"."\t".'"order"' . str_repeat(' ',10 - strlen('order')) .  '=> '.$this->getBeanOrder().','."\r\n";
		$str.= "\t"."\t"."\t"."\t".'"where"' . str_repeat(' ',10 - strlen('where')) .  '=> []'."\r\n";
		$str.= "\t"."\t"."\t".'];'."\r\n";
		$str.= "\t"."\t"."\t".'$params = array_merge($defaultOptions,$options);'."\r\n";
		return $str;
	}
	/**
	 * [addServiceInfoMainHandler 查询详情主体信息生成]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceInfoMainHandler()
	{
		$arr = [];
		$arr[] = "\t"."\t"."\t".'$info   = AppMysql::name(\''.str_replace('szj_','',$this->tableName).'\')->alias(\''.$this->beanName.'\')';
		$arr[] = "\t"."\t"."\t"."\t"."\t".'  ->field($params[\'field\'])';
		$arr[] = "\t"."\t"."\t"."\t"."\t".'  ->where($params[\'where\'])';
		$arr[] = "\t"."\t"."\t"."\t"."\t".'  ->order($params[\'order\'])';
		$arr[] = "\t"."\t"."\t"."\t"."\t".'  ->find();';
		$arr[] = "\t"."\t"."\t".'return $this->appResult(\'SUCCESS\',$info,false,0);';
		$arr[] = "\t"."\t".'} catch(\Throwable $err) {';
		$arr[] = "\t"."\t"."\t".'return $this->appResult($err->getMessage());'."\r\n"."\t"."\t"."}"."\r\n";
		return implode("\r\n",$arr);
	}


	/*======================获取数据列表操作=========================*/

	/**
	 * [addServiceListAction 生成列表查询方法]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceListAction()
	{
		$str  = $this->addServiceListComment();
		$str .= "\t".'public function select(array $options = [])'."\r\n";
		$str .= "\t".'{'."\r\n";
		$str .= $this->addDefaultOptions();
		$str .= $this->addSserviceListActionMain();
		$str .= $this->addServiceListActionTotal();
		return $str."\t".'}'."\r\n"."\r\n"."\r\n";
	}
	/**
	 * [addServiceListComment 添加函数参数注释文档]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceListComment()
	{
		$arr = ["\t".'/**'];
		$arr[] = "\t".' * [select 获取表名称 `'.$this->tableName.'` 数据列表]';
		$arr[] = "\t".' * @author'. str_repeat(' ',15 - strlen('@author')) . 'szjcomo';
		$arr[] = "\t".' * @createTime'. str_repeat(' ',15 - strlen('@createTime')). date('Y-m-d');
		$arr[] = "\t".' * @param      array      $options [field,limit,page,order,where 参数条件]';
		$arr[] = "\t".' * @return     array              [数据列表与总条数]';
		$arr[] = "\t".' */'."\r\n";
		return implode("\r\n",$arr);
	}
	/**
	 * [addSserviceListActionMain 生成sevice方法主体内容]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addSserviceListActionMain()
	{
		$arr = [];
		$arr[] = "\t"."\t"."\t".'$rows = AppMysql::name(\''.str_replace('szj_','',$this->tableName).'\')->alias("'.$this->beanName.'")';
		$arr[] = "\t"."\t"."\t"."\t"."\t".'->limit(($params[\'page\'] - 1) * $params[\'limit\'],$params[\'limit\'])';
		$arr[] = "\t"."\t"."\t"."\t"."\t".'->field($params[\'field\'])';
		$arr[] = "\t"."\t"."\t"."\t"."\t".'->where($params[\'where\'])';
		$arr[] = "\t"."\t"."\t"."\t"."\t".'->order($params[\'order\'])';
		$arr[] = "\t"."\t"."\t"."\t"."\t".'->select();'."\r\n";
		return implode("\r\n",$arr);
	}
	/**
	 * [addServiceListActionTotal 获取总条数]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceListActionTotal()
	{
		$str = "\t"."\t"."\t".'$total = AppMysql::name(\''.str_replace('szj_','',$this->tableName).'\')->alias(\''.$this->beanName.'\')->where($params[\'where\'])->count();'."\r\n";
		$str .= "\t"."\t"."\t".'return $this->appResult(\'SUCCESS\',[\'total\'=>$total,\'rows\'=>$rows],false,0);'."\r\n";
		$str .= "\t"."\t".'} catch(\Throwable $err) {'."\r\n";
		$str .= "\t"."\t"."\t".'return $this->appResult($err->getMessage());'."\r\n"."\t"."\t"."}"."\r\n";
		return $str;
	}
	/**
	 * [getBeanFields 获取字段的查询参数]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 * @return     [type]     [description]
	 */
	protected function getBeanFields()
	{
		$arr = [];
		foreach($this->beanClass->allProperty() as $key=>$val){
			$arr[] = "'".$this->beanName.'.'.$val."'";
		}
		return $arr;
	}
	/**
	 * [getBeanSort 获取排序字段]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 * @return     [type]     [description]
	 */
	protected function getBeanOrder()
	{
		$str = '';
		foreach($this->beanClass->allProperty() as $key=>$val){
			if(stripos($val,'sort')){
				$str .= "'".$this->beanName.'.'.$val.' asc'."'";
				break;
			}
		}
		if(empty($str)){
			$str .= "'".$this->beanName . '.'.$this->pkField . ' desc'."'";
		}
		return $str;
	}

	/**
	 * [getFieldPk 获取主键]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 * @return     [type]     [description]
	 */
	protected function attachFieldObjParase()
	{
		foreach($this->fields as $k=>$val) {
			$tmpObj = new Fields($val);
			$key = $tmpObj->getKey();
			if($key == 'PRI') {
				$this->pkField = $val['Field'];
			}
			if(empty($key)) continue;
			if(in_array($key, $this->indexesFlag)) {
				$this->indexes[$val['Field']] = $key;
			}
		}
	}
	/**
	 * [addDefaultOptions 生成默认参数合集]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addDefaultOptions()
	{
		$str = "\t"."\t".'try {'."\r\n";
		$str.= "\t"."\t"."\t".'$defaultOptions = ['."\r\n";
		$str.= "\t"."\t"."\t"."\t".'"field"' . str_repeat(' ',10 - strlen('field')) . '=> ['.implode(',', $this->getBeanFields()).'],'."\r\n";
		$str.= "\t"."\t"."\t"."\t".'"page"' . str_repeat(' ',10 - strlen('page')) . '=> 1,'."\r\n";
		$str.= "\t"."\t"."\t"."\t".'"limit"' . str_repeat(' ',10 - strlen('limit')) .  '=> 30,'."\r\n";
		$str.= "\t"."\t"."\t"."\t".'"order"' . str_repeat(' ',10 - strlen('order')) .  '=> '.$this->getBeanOrder().','."\r\n";
		$str.= "\t"."\t"."\t"."\t".'"where"' . str_repeat(' ',10 - strlen('where')) .  '=> []'."\r\n";
		$str.= "\t"."\t"."\t".'];'."\r\n";
		$str.= "\t"."\t"."\t".'$params = array_merge($defaultOptions,$options);'."\r\n";
		return $str;
	}






}