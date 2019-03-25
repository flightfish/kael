<?php

/**
 * 组件属性
 * @author auto create
 */
class FormComponentPropVo
{
	
	/** 
	 * 业务别名, 当组件属于业务套件的一部分时方便业务识别(DDBizSuite)
	 **/
	public $biz_alias;
	
	/** 
	 * 业务套件类型(DDBizSuite)
	 **/
	public $biz_type;
	
	/** 
	 * 套件内子组件可见性，key为label，value=false不可见
	 **/
	public $child_field_visible;
	
	/** 
	 * 是否可编辑
	 **/
	public $disable;
	
	/** 
	 * id
	 **/
	public $id;
	
	/** 
	 * 隐藏字段
	 **/
	public $invisible;
	
	/** 
	 * 标题
	 **/
	public $label;
	
	/** 
	 * 是否参与打印(1表示不打印, 0表示打印)
	 **/
	public $not_print;
	
	/** 
	 * 必填
	 **/
	public $required;	
}
?>