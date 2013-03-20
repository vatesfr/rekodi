<?php
/**
 * This file is a part of Rekodi.
 *
 * Rekodi is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Rekodi is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Rekodi. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Julien Fontanet <julien.fontanet@vates.fr>
 * @license http://www.gnu.org/licenses/gpl-3.0-standalone.html GPLv3
 *
 * @package Rekodi
 */

namespace Rekodi;

/**
 *
 */
final class Table
{
	/**
	 * @param string $name
	 * @param array  $params
	 */
	function __call($name, array $params)
	{
		static $types;
		if (!$types)
		{
			$types = array_flip(array(
				'binary',
				'boolean',
				'date',
				'float',
				'integer',
				'string',
			));
		}

		if (isset($types[$name]))
		{
			if (!isset($params[0]))
			{
				trigger_error(
					'missing field name',
					E_USER_ERROR
				);
			}

			return $this->_field($params[0], $name);
		}

		// Reserved PHP keywords are trapped using __call().
		return call_user_func_array(
			array($this, $name.'_'),
			$params
		);
	}

	/**
	 * Defines the field as auto-incremented.
	 *
	 * @return Table For chaining.
	 */
	function autoIncremented()
	{
		$this->_checkField();

		$this->_currentField['auto-incremented'] = true;
		return $this;
	}

	/**
	 * Sets a default value for the current field.
	 *
	 * @param mixed $value
	 *
	 * @return Table For chaining.
	 */
	function default_($value)
	{
		$this->_checkField();

		$this->_currentField['default'] = $value;
		return $this;
	}

	/**
	 * @return Table For chaining.
	 */
	function nullable()
	{
		$this->_checkField();

		$this->_currentField['nullable'] = true;
		return $this;
	}

	/**
	 * @return Table For chaining.
	 */
	function unique()
	{
		$this->_checkField();

		$this->_currentField['unique'] = true;
		return $this;
	}

	/**
	 *
	 */
	function getFields()
	{
		return $this->_fields;
	}

	/**
	 * @var array[]
	 */
	private $_fields = array();

	/**
	 * @var null|string
	 */
	private $_currentField;

	/**
	 *
	 */
	protected function _checkField()
	{
		if (!$this->_currentField)
		{
			trigger_error(
				'no current field',
				E_USER_ERROR
			);
		}
	}

	/**
	 * @param string $name The name of the field.
	 * @param string $type The type of the field
	 *
	 * @return Table
	 */
	protected function _field($name, $type)
	{
		if (isset($this->_fields[$name]))
		{
			trigger_error(
				'there is already a field named '.$name,
				E_USER_ERROR
			);
		}

		$this->_fields[$name] = array(
			'type' => $type,
		);
		$this->_currentField  = &$this->_fields[$name];

		return $this;
	}
}
