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

use EasySwoole\EasySwoole\Command\CommandInterface  	as EasySwooleCommandInterface;
use szjcomo\szjcore\Mysql 								as AppMysql;
use szjcomo\odaHelp\Fields 								as AppFields;
use szjcomo\odaHelp\Beans 								as AppBeans;
use szjcomo\odaHelp\Service 							as AppService;

/**
 * 自定义命令的实现
 */
class Szjkj implements EasySwooleCommandInterface
{

	/**
	 * [commandName 命令名称]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @return     [type]     [description]
	 */
	public function commandName():string
	{
		return 'szjkj';
	}
	/**
	 * [exec 执行命令]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      array      $args [description]
	 * @return     [type]           [description]
	 */
	public function exec(array $args):?string
	{
		go(function() use (&$args){
			$this->parseCommandArgs($args);
		});
		return '';
	}

	/**
	 * [parseCommandArgs 分析命令]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      array      $args [description]
	 * @return     [type]           [description]
	 */
	protected function parseCommandArgs(array $args)
	{
		foreach($args as $key=>$val){
			list($command,$param) = explode('=', $val);
			call_user_func([&$this,$command],$param);
		}
	}
	/**
	 * [parseNamespace 分析命名空间]
	 * @author 	   szjcomo
	 * @createTime 2019-11-19
	 * @return     [type]     [description]
	 */
	protected function parseNamespace()
	{
		$composerEnv = file_get_contents(EASYSWOOLE_ROOT.'/composer.json');
		$arr = json_decode($composerEnv,true);
		if(!empty($arr['autoload']['psr-4']['app\\'])){
			return $arr['autoload']['psr-4']['app\\'];
		}
		if(!empty($arr['autoload']['psr-4']['App\\'])){
			return $arr['autoload']['psr-4']['App\\'];
		}
		return 'app';
	}


	/**
	 * [service 执行service命令参数]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      string     $tableName [description]
	 * @return     [type]                [description]
	 */
	protected function service(string $tableName)
	{
		if(empty($tableName)) return '数据表名称不能为空';
		$sql = 'show full fields from '.$tableName;
		$result = AppMysql::DB()->rawQuery($sql,[]);
		if(empty($result)) {
			echo "\e[31m ".'['.date('Y-m-d').' 数据表不存在,请检查]'.PHP_EOL;
			return;
		}
		$appRoot = $this->parseNamespace();
		$beanName = $this->BeanClassName($tableName);
		if(!file_exists($appRoot.'/models/'.$beanName.'.php')){
			$this->bean($tableName,$result);
		}
		$beanclass = '\app\models\\'.$beanName;
		$serviceObj = new AppService(new $beanclass,$beanName,$result,$tableName);
		$template = $serviceObj->addServiceTemplate();
		$savepath = $appRoot.'/service/'.$beanName.'.php';
		$bool = $this->saveServiceFile($template,$savepath);
		if($bool) {
			echo "\e[32m ".'['.date('Y-m-d')." ".$savepath." generate successful \e[0m ".']'.PHP_EOL;
		} else {
			echo "\e[31m ".'['.date('Y-m-d')." ".$savepath." generate fail \e[0m ".']'.PHP_EOL;
		}
	}

	/**
	 * [saveServiceFile 保存service类]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 * @param      string     $template [description]
	 * @param      string     $savepath [description]
	 * @return     [type]               [description]
	 */
	protected function saveServiceFile(string $template,string $savepath)
	{
		$number = file_put_contents($savepath,$template);
		chown($savepath,'www') && chgrp($savepath,'www');
		return $number;
	}

	/*======================开始生成beanclass文件==========================*/
	/**
	 * [bean 执行bean命令参数]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      string     $tableName [description]
	 * @return     [type]                [description]
	 */
	protected function bean(string $tableName,array $result = [])
	{
		if(empty($result)){
			$sql = 'show full fields from '.$tableName;
			$result = AppMysql::DB()->rawQuery($sql,[]);
		}
		if(empty($result)) {
			echo "\e[31m ".'['.date('Y-m-d').' 数据表不存在,请检查]'.PHP_EOL;
			return;
		}
		$this->setBeanClass($result,$this->BeanClassName($tableName));
	}

	/**
	 * [BeanClassName 获取bean类名称]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 * @param      string     $beanClassName [description]
	 */
	protected function BeanClassName(string $beanClassName)
	{
		$tmp = explode('_',$beanClassName);
		array_shift($tmp);
		$beanName = '';
		foreach($tmp as $key=>$val){
			$beanName .= ucwords($val);
		}
		return $beanName;
	}
	/**
	 * [saveBeanFile 保存beanclass 文件]
	 * @author 	   szjcomo
	 * @createTime 2019-11-18
	 * @param      string     $template [description]
	 * @return     [type]               [description]
	 */
	protected function saveBeanFile(string $template,string $savepath)
	{
		$number = file_put_contents($savepath,$template);
		chown($savepath,'www') && chgrp($savepath,'www');
		return $number;
	}

	/**
	 * [setBeanClass 生成model文件]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      array      $result [description]
	 */
	protected function setBeanClass(array $result,$beanName)
	{
		$appRoot = $this->parseNamespace();
		$savepath = $appRoot.'/models/'.$beanName.'.php';
		$beanarr = [];
		foreach($result as $key=>$val){
			$fields = new AppFields($val);
			$beanarr[] = $fields->toBeanArray();
		}
		$beanObj = new AppBeans($beanarr,$beanName);
		$template = $beanObj->addBeanFile();
		$bool = $this->saveBeanFile($template,$savepath);
		if($bool) {
			echo "\e[32m ".'['.date('Y-m-d')." ".$savepath." generate successful \e[0m ".']'.PHP_EOL;
		} else {
			echo "\e[31m ".'['.date('Y-m-d')." ".$savepath." generate fail \e[0m ".']'.PHP_EOL;
		}
	}

	/*======================开始打印控制台信息==========================*/
	/**
	 * [help 帮助信息]
	 * @author 	   szjcomo
	 * @createTime 2019-11-16
	 * @param      array      $args [description]
	 * @return     [type]           [description]
	 */
	public function help(array $args):?string
	{
		return <<<HELP_START
\e[33mOperation:\e[0m
\e[31m  php easyswoole szjkj [arg1] [arg2]\e[0m
\e[33mIntro:\e[0m
\e[36m  Quickly generate services and beans required by the project \e[0m
\e[33mArg:\e[0m
\e[32m  service \e[0m              快速生成service数据库操作 示例用法:\e[31mservice=szj_admin(表名称)\e[0m
\e[32m  bean \e[0m                 快速生成数据表结构 示例用法:\e[31mbean=szj_admin(表名称)\e[0m
HELP_START;
	}
}