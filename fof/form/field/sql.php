<?php
/**
 * @package    FrameworkOnFramework
 * @subpackage form
 * @copyright  Copyright (C) 2010 - 2014 Akeeba Ltd. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace FOF30\Form\Field;

use FOF30\Form\Field as FOFFormField;
use FOF30\Table\Table as FOFTable;
use FOF30\Platform\Platform as FOFPlatform;
use FOF30\Form\Field\Select as FOFFormFieldSelect;

// Joomla! class inclusion
use JFactory, JHtml, JText, JFormHelper, JFormFieldSql;

// Protect from unauthorized access
defined('FOF30_INCLUDED') or die;

JFormHelper::loadFieldClass('sql');

/**
 * Form Field class for F0F
 * Radio selection listGeneric list from an SQL statement
 *
 * @package  FrameworkOnFramework
 * @since    2.0
 */
class Sql extends JFormFieldSql implements FOFFormField
{
	protected $static;

	protected $repeatable;

	/** @var   FOFTable  The item being rendered in a repeatable form field */
	public $item;

	/** @var int A monotonically increasing number, denoting the row number in a repeatable view */
	public $rowid;

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to the the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   2.0
	 */
	public function __get($name)
	{
		switch ($name)
		{
			case 'static':
				if (empty($this->static))
				{
					$this->static = $this->getStatic();
				}

				return $this->static;
				break;

			case 'repeatable':
				if (empty($this->repeatable))
				{
					$this->repeatable = $this->getRepeatable();
				}

				return $this->repeatable;
				break;

			default:
				return parent::__get($name);
		}
	}

	/**
	 * Get the rendering of this field type for static display, e.g. in a single
	 * item view (typically a "read" task).
	 *
	 * @since 2.0
	 *
	 * @return  string  The field HTML
	 */
	public function getStatic()
	{
		$class = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		return '<span id="' . $this->id . '" ' . $class . '>' .
			htmlspecialchars(FOFFormFieldSelect::getOptionName($this->getOptions(), $this->value), ENT_COMPAT, 'UTF-8') .
			'</span>';
	}

	/**
	 * Get the rendering of this field type for a repeatable (grid) display,
	 * e.g. in a view listing many item (typically a "browse" task)
	 *
	 * @since 2.0
	 *
	 * @return  string  The field HTML
	 */
	public function getRepeatable()
	{
		$class = $this->element['class'] ? (string) $this->element['class'] : '';

		return '<span class="' . $this->id . ' ' . $class . '">' .
			htmlspecialchars(FOFFormFieldSelect::getOptionName($this->getOptions(), $this->value), ENT_COMPAT, 'UTF-8') .
			'</span>';
	}
}
