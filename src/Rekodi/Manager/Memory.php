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

namespace Rekodi\Manager;

use Rekodi\Table;

/**
 * @todo Use exceptions when appropriate.
 * @todo Check value type and nullity in create() and update().
 * @todo Optimize research in using indexes in delete(), get() and update().
 */
final class Memory extends ManagerAbstract
{
	//--------------------------------------
	// Instanciation.
	//--------------------------------------

	/**
	 *
	 */
	static function createFromState(array $state)
	{
		$memory = new static;
		$memory->_tables = $state;

		return $memory;
	}

	/**
	 *
	 */
	function __construct()
	{
		parent::__construct();
	}

	//--------------------------------------
	// Table manipulation.
	//--------------------------------------

	/**
	 *
	 */
	function createTable($name, $callback)
	{
		if (isset($this->_tables[$name]))
		{
			trigger_error(
				'there is already a table named '.$name,
				E_USER_ERROR
			);
		}

		$schema = new Table;
		call_user_func($callback, $schema);

		$table = &$this->_tables[$name];
		$table = array(
			'defaults'  => array(),
			'fields'    => array(), // Whether the fields are nullable.
			'uniques'   => array(),
			'sequences' => array(),

			'entries'   => array(),
		);

		foreach ($schema->getFields() as $name => $props)
		{
			$table['fields'][$name] = isset($props['nullable']);

			if (isset($props['default']))
			{
				$table['defaults'][$name] = $props['default'];
			}
			elseif ($table['fields'][$name])
			{
				// If no default value and is nullable, default is null.
				$table['defaults'][$name] = null;
			}

			if (isset($props['unique']))
			{
				$table['uniques'][$name] = array();
			}

			if (isset($props['auto-incremented']))
			{
				$table['sequences'][$name] = 0;
			}
		}
	}

	/**
	 *
	 */
	function deleteTable($name)
	{
		// Checks existence.
		$this->_getTable($name);

		unset($this->_tables[$name]);
	}

	/**
	 *
	 */
	function getTables()
	{
		return array_keys($this->_tables);
	}

	//--------------------------------------
	// Data manipulation.
	//--------------------------------------

	/**
	 *
	 */
	function create($tbl_name, array $entries)
	{
		$table = &$this->_getTable($tbl_name);

		foreach ($entries as &$entry)
		{
			if (!is_array($entry))
			{
				trigger_error(
					'not an array',
					E_USER_ERROR
				);
			}

			// Default values.
			$entry += $table['defaults'];

			// Adds sequence values.
			foreach ($table['sequences'] as $field => &$value)
			{
				if (!isset($entry[$field]))
				{
					$entry[$field] = $value++;
				}
			}
			unset($value);

			// Checks all fields are defined.
			if (array_diff_key($table['fields'], $entry))
			{
				trigger_error(
					'invalid entry',
					E_USER_ERROR
				);
			}

			// Inserts the entry and gets its internal id.
			$table['entries'][] = $entry;
			end($table['entries']);
			$id = key($table['entries']);

			// Checks unicity and updates indexes.
			foreach ($table['uniques'] as $field => &$entries_)
			{
				$value = $entry[$field];
				if (isset($entries_[$value]))
				{
					trigger_error(
						"unique constraint violated for $tbl_name:$field ({$entry[$field]})",
						E_USER_ERROR
					);
				}
				$entries_[$value] = $id;
			}
			unset($entries_);
		}

		return $entries;
	}

	/**
	 *
	 */
	function delete($table, array $filter)
	{
		$table = &$this->_getTable($table);

		$uniques = array_intersect_key(
			$filter,
			$table['uniques']
		);



		$n = 0;
		foreach ($table['entries'] as $key => $entry)
		{
			if ($this->_match($entry, $filter))
			{
				foreach ($table['uniques'] as $field => &$entries)
				{
					unset($entries[$entry[$field]]);
				}
				unset($table['entries'][$key]);
				++$n;

				/* If there were unique fields in the filter we can
				 * stop now.
				 */
				if ($uniques)
				{
					break;
				}
			}
		}

		return $n;
	}

	/**
	 *
	 */
	function get($table, array $filter = null, array $fields = null)
	{
		$table = &$this->_getTable($table);

		$fields = $fields ? array_flip($fields) : false;

		$entries = array();
		foreach ($table['entries'] as $entry)
		{
			if (!$filter || $this->_match($entry, $filter))
			{
				$entries[] = $fields
					? array_intersect_key($entry, $fields)
					: $entry;
			}
		}
		return $entries;
	}

	/**
	 *
	 */
	function update($table, array $filter, array $properties)
	{
		$table = &$this->_getTable($table);

		$n = 0;
		foreach ($table['entries'] as &$entry)
		{
			if ($this->_match($entry, $filter))
			{
				$entry = $properties + $entry;
				++$n;
			}
		}
		return $n;
	}

	//--------------------------------------
	// Various.
	//--------------------------------------

	/**
	 * @return array
	 */
	function getState()
	{
		return $this->_tables;
	}

	//--------------------------------------
	// Internal stuff.
	//--------------------------------------

	/**
	 *
	 */
	private $_tables = array();

	/**
	 *
	 */
	private function &_getTable($name)
	{
		if (!isset($this->_tables[$name]))
		{
			trigger_error(
				'no such table '.$name,
				E_USER_ERROR
			);
		}

		return $this->_tables[$name];
	}

	/**
	 *
	 */
	private function _match(array $entry, array $filter)
	{
		foreach ($filter as $key => $value)
		{
			if (!(
				(isset($entry[$key]) || array_key_exists($key, $entry))
				&& ($entry[$key] === $value)
			))
			{
				return false;
			}
		}
		return true;
	}
}
