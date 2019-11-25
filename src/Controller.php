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
 * 生成控制器类
 */
class Controller
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
	 * [$pkInfo 主键的描述信息]
	 * @var string
	 */
	protected $pkInfo = '';
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
	 * [$module 模版名]
	 * @var string
	 */
	protected $module = 'Admin';

	public function __construct(EasySwooleBean $bean,$beanName = 'models',$fields = [],$tableName = '',$module = 'Admin')
	{
		$this->beanClass 	= $bean;
		$this->beanName 	= $beanName;
		$this->fields 		= $fields;
		$this->tableName 	= $tableName;
		$this->attachFieldObjParase();
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
				$this->pkInfo = $tmpObj->getComment();
			}
			if(empty($key)) continue;
			if(in_array($key, $this->indexesFlag)) {
				$this->indexes[$val['Field']] = $key;
			}
		}
	}
	/**
	 * [addControllerTemplate 自动生成控制器模版类]
	 * @author 	   szjcomo
	 * @createTime 2019-11-23
	 */
	public function addControllerTemplate()
	{
		$template = '';
		$template .= $this->addControllerHeader();
		$template .= $this->addServiceClassName();
		$template .= $this->addControllerTemplateList();
		$template .= $this->addControllerTemplateInfo();
		$template .= $this->addControllerTemplateDelete();
		$template .= $this->addControllerTemplateAdd();
		return $template."}";
	}
	/**
	 * [addServiceHeader 生成service类的头信息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addControllerHeader()
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
		$str .= 'namespace app\controllers\\'.$this->module.';'."\r\n"."\r\n";
		$controllerName = 'use app\controllers\\'.$this->module;
		$serviceName = 'use app\service\\'.$this->beanName;
		$modelName = 'use app\models\\'.$this->beanName;
		$str .= $controllerName.str_repeat(' ',50 - strlen($controllerName)).' as '.$this->module.'Controller;'."\r\n";
		$str .= $serviceName.str_repeat(' ',50 - strlen($serviceName)).' as '.$this->beanName.'Service;'."\r\n";
		$str .= $modelName.str_repeat(' ',50 - strlen($modelName)).' as '.$this->beanName.'Model;'."\r\n"."\r\n";
		return $str;
	}
	/**
	 * [addServiceClassName 生成类名称]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addServiceClassName()
	{
		$str = $this->addControllerTemplateComment()."\r\n";
		$str .= 'class '.$this->beanName.' extends '.$this->module.'Controller'."\r\n";
		$str .= "{"."\r\n";
		$str .= "\t".'/**'."\r\n";
		$str .= "\t".' * [$addRules 添加时数据验证]'."\r\n";
		$str .= "\t".' * @var array'."\r\n";
		$str .= "\t".' */'."\r\n";
		$str .= "\t".'protected $addRules 	= [];'."\r\n";
		$str .= "\t".'/**'."\r\n";
		$str .= "\t".' * [$updateRules 更新时数据验证]'."\r\n";
		$str .= "\t".' * @var array'."\r\n";
		$str .= "\t".' */'."\r\n";
		$str .= "\t".'protected $updateRules = ['."\r\n".$this->addUpdateRulesTemplate()."\t".'];'."\r\n";
		return $str;
	}

	/**
	 * [addUpdateRulesTemplate 生成规则模版]
	 * @author 	   szjcomo
	 * @createTime 2019-11-25
	 */
	protected function addUpdateRulesTemplate()
	{
		$arr = [];
		$arr[] = "\t"."\t".'[\''.$this->pkField.'\',\'require\',\''.$this->pkInfo.'不能为空\'];'."\r\n";
		return implode("\r\n",$arr);
	}

	/**
	 * [addServiceTemplateComment 添加类头部注释信息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 */
	protected function addControllerTemplateComment()
	{
		$arr 	= ['/**'];
		$arr[] 	= ' * '.$this->tableName.' 控制器操作';
		$arr[] 	= ' * @category  PHP';
		$arr[] 	= ' * @author    szjcomo';
		$arr[] 	= ' * @methods   list,info,add,delete';
		$arr[] 	= '*/';
		return implode("\r\n",$arr);
	}
	/**
	 * [addControllerTemplateMain 生成list方法]
	 * @author 	   szjcomo
	 * @createTime 2019-11-25
	 */
	protected function addControllerTemplateList()
	{
		$arr   = ["\t".'/**'];
		$arr[] = "\t".' * [list 获取列表]';
		$arr[] = "\t".' * @author     szjcomo';
		$arr[] = "\t".' * @createTime '.date('Y-m-d');
		$arr[] = "\t".' * @return     void     [响应http数据列表]';
		$arr[] = "\t".'*/';
		$arr[] = "\t".'public function list()';
		$arr[] = "\t".'{';
		$arr[] = "\t"."\t".'$limit  = $this->context->get(\'limit\',0,\'intval\');';
		$arr[] = "\t"."\t".'$page   = $this->context->get(\'page\',0,\'intval\');';
		$arr[] = "\t"."\t".'$param  = [\'limit\'=>$limit,\'page\'=>$page,\'where\'=>[]];';
		$arr[] = "\t"."\t".'$result = '.$this->beanName.'Service::getInstance()->select($param);';
		$arr[] = "\t"."\t".'return $this->appJson($result);';
		$arr[] = "\t".'}';
		return implode("\r\n",$arr)."\r\n";
	}
	/**
	 * [addControllerTemplateInfo 生成info方法]
	 * @author 	   szjcomo
	 * @createTime 2019-11-25
	 */
	protected function addControllerTemplateInfo()
	{
		$arr   = ["\t".'/**'];
		$arr[] = "\t".' * [info 获取详情]';
		$arr[] = "\t".' * @author     szjcomo';
		$arr[] = "\t".' * @createTime '.date('Y-m-d');
		$arr[] = "\t".' * @return     void     [响应http数据详情]';
		$arr[] = "\t".'*/';
		$arr[] = "\t".'public function info()';
		$arr[] = "\t".'{';
		$arr[] = "\t"."\t".'try{';
		$arr[] = "\t"."\t"."\t".'$param  = [\'where\'=>[\''.$this->pkField.'\'=>$this->getParamsId(\''.$this->pkField.'\',\''.$this->pkInfo.'\')]];';
		$arr[] = "\t"."\t"."\t".'$result = '.$this->beanName.'Service::getInstance()->find($param);';
		$arr[] = "\t"."\t"."\t".'return $this->appJson($result);';
		$arr[] = "\t"."\t".'} catch(\Exception $err) {';
		$arr[] = "\t"."\t"."\t".'return $this->appJson($this->appResult($err->getMessage()));';
		$arr[] = "\t"."\t".'}';
		$arr[] = "\t".'}';
		return implode("\r\n",$arr)."\r\n";
	}
	/**
	 * [addControllerTemplateDelete 添加删除方法]
	 * @author 	   szjcomo
	 * @createTime 2019-11-25
	 */
	protected function addControllerTemplateDelete()
	{
		$arr   = ["\t".'/**'];
		$arr[] = "\t".' * [delete 删除数据]';
		$arr[] = "\t".' * @author     szjcomo';
		$arr[] = "\t".' * @createTime '.date('Y-m-d');
		$arr[] = "\t".' * @return     void     [响应http数据删除请求]';
		$arr[] = "\t".'*/';
		$arr[] = "\t".'public function delete()';
		$arr[] = "\t".'{';
		$arr[] = "\t"."\t".'try{';
		$arr[] = "\t"."\t"."\t".'$param  = [\'where\'=>[\''.$this->pkField.'\'=>$this->getParamsId(\''.$this->pkField.'\',\''.$this->pkInfo.'\')]];';
		$arr[] = "\t"."\t"."\t".'$result = '.$this->beanName.'Service::getInstance()->delete'.$this->beanName.'($param);';
		$arr[] = "\t"."\t"."\t".'return $this->appJson($result);';
		$arr[] = "\t"."\t".'} catch(\Exception $err) {';
		$arr[] = "\t"."\t"."\t".'return $this->appJson($this->appResult($err->getMessage()));';
		$arr[] = "\t"."\t".'}';
		$arr[] = "\t".'}';
		return implode("\r\n",$arr)."\r\n";
	}
	/**
	 * [addControllerTemplateAdd 添加和更新方法汇总]
	 * @author 	   szjcomo
	 * @createTime 2019-11-25
	 */
	protected function addControllerTemplateAdd()
	{
		$arr   = ["\t".'/**'];
		$arr[] = "\t".' * [add 添加和更新数据]';
		$arr[] = "\t".' * @author     szjcomo';
		$arr[] = "\t".' * @createTime '.date('Y-m-d');
		$arr[] = "\t".' * @return     void     [响应http数据添加/更新请求]';
		$arr[] = "\t".'*/';
		$arr[] = "\t".'public function add()';
		$arr[] = "\t".'{';
		$arr[] = "\t"."\t".'try{';
		$arr[] = "\t"."\t"."\t".'$data = $this->context->post();';
		$arr[] = "\t"."\t"."\t".'$model = new '.$this->beanName.'Model($data);';
		$arr[] = "\t"."\t"."\t".'if(empty($model->getAdminId())) $model->setAdminId($this->getAdminUserId());';
		$arr[] = "\t"."\t"."\t".'if(empty($model->get'.$this->pkToBeanPk().'())) {';
		$arr[] = "\t"."\t"."\t"."\t".'$this->addValidate($model,$this->addRules);';
		$arr[] = "\t"."\t"."\t"."\t".'$model->setCreateTime(date(\'Y-m-d H:i:s\'));';
		$arr[] = "\t"."\t"."\t"."\t".'$result = '.$this->beanName.'Service::getInstance()->add'.$this->beanName.'($model);';
		$arr[] = "\t"."\t"."\t".'} else {';
		$arr[] = "\t"."\t"."\t"."\t".'$this->addValidate($model,$this->updateRules);';
		$arr[] = "\t"."\t"."\t"."\t".'unset($data[\''.$this->pkField.'\']);';
		$arr[] = "\t"."\t"."\t"."\t".'$result = '.$this->beanName.'Service::getInstance()->update'.$this->beanName.'($model,[\'field\'=>array_keys($data),\'where\'=>[\''.$this->pkField.'\'=>$model->get'.$this->pkToBeanPk().'()]]);';
		$arr[] = "\t"."\t"."\t".'}';
		$arr[] = "\t"."\t"."\t".'return $this->appJson($result);';
		$arr[] = "\t"."\t".'} catch(\Exception $err) {';
		$arr[] = "\t"."\t"."\t".'return $this->appJson($this->appResult($err->getMessage()));';
		$arr[] = "\t"."\t".'}';
		$arr[] = "\t".'}';
		return implode("\r\n",$arr)."\r\n";
	}
	/**
	 * [pkToBeanPk pk转beanPk]
	 * @author 	   szjcomo
	 * @createTime 2019-11-25
	 * @return     [type]     [description]
	 */
	protected function pkToBeanPk()
	{
		$actionName = '';
		$arr = explode('_',$this->pkField);
		foreach($arr as $key=>$val){
			$actionName .= ucwords($val);
		}
		return $actionName;
	}


}