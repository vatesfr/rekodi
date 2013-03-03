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

use Rekodi\Manager\ManagerAbstract;

/**
 *
 */
final class Memory extends ManagerAbstract
{
	/**
	 *
	 */
	static function createFromState(array $state)
	{
		$memory = new static;
		$memory->_entries = $state;

		return $memory;
	}

	/**
	 *
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 *
	 */
	function create(array $entries)
	{
		foreach ($entries as $entry)
		{
			if (!is_array($entry))
			{
				trigger_error(
					'not an array',
					E_USER_ERROR
				);
			}
			$this->_entries[] = $entry;
		}
	}

	/**
	 *
	 */
	function delete(array $filter)
	{
		$n = 0;
		foreach ($this->_entries as $key => $entry)
		{
			if ($this->_match($entry, $filter))
			{
				unset($this->_entries[$key]);
				++$n;
			}
		}
		return $n;
	}

	/**
	 *
	 */
	function get(array $filter = null, array $fields = null)
	{
		$fields = $fields ? array_flip($fields) : false;

		$entries = array();
		foreach ($this->_entries as $entry)
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
	function update(array $filter, array $properties)
	{
		$n = 0;
		foreach ($this->_entries as &$entry)
		{
			if ($this->_match($entry, $filter))
			{
				$entry = $properties + $entry;
				++$n;
			}
		}
		return $n;
	}

	/**
	 * @return array
	 */
	function getState()
	{
		return $this->_entries;
	}


	/**
	 *
	 */
	private $_entries = array();

	/**
	 *
	 */
	private function _match(array $entry, array $filter)
	{
		return !array_diff_assoc($filter, $entry);
	}
}
