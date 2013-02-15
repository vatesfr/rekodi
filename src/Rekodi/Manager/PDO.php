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
final class PDO extends ManagerAbstract
{
	/**
	 *
	 */
	function __construct(\PDO $pdo, $table)
	{
		parent::__construct();

		$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		$this->_pdo = $pdo;

		$this->_table = $this->_quoteIdentifier($table);
	}

	/**
	 *
	 */
	function create(array $entries)
	{
		$n = 0;
		foreach ($entries as $entry)
		{
			$fields = implode(', ', $this->_quoteIdentifier(array_keys($entry)));
			$values = implode(', ', $this->_quoteValue($entry));

			$n += $this->_pdo->exec(
				"INSERT INTO {$this->_table} ($fields) VALUES ($values)"
			);
		}
		return $n;
	}

	/**
	 *
	 */
	function delete(array $filter)
	{
		return $this->_pdo->exec(
			"DELETE FROM {$this->_table}".$this->_where($filter)
		);
	}

	/**
	 *
	 */
	function get(array $filter = null, array $fields = null)
	{
		$fields = $fields
			? implode(', ', $this->_quoteIdentifier($fields))
			: '*';
		$sql = "SELECT $fields FROM {$this->_table}".$this->_where($filter);

		return $this->_pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 *
	 */
	function update(array $filter, array $properties)
	{
		foreach ($properties as $field => &$value)
		{
			$value = $this->_quoteIdentifier($field).'='.$this->_quoteValue($value);
		}

		return $this->_pdo->exec(
			'UPDATE '.$this->_table.' SET '.implode(' ', $properties).$this->_where($filter)
		);
	}

	/**
	 * @var \PDO
	 */
	private $_pdo;

	/**
	 * @var string
	 */
	private $_table;

	/**
	 * Quotes identifier(s).
	 *
	 * @param array|string $id
	 *
	 * @return array|string
	 */
	private function _quoteIdentifier($id)
	{
		if (is_array($id))
		{
			return array_map(
				array($this, __FUNCTION__),
				$id
			);
		}

		return '"'.str_replace('"', '', $id).'"';
	}

	/**
	 * Quotes value(s).
	 *
	 * @param array|string $value
	 *
	 * @return array|string
	 */
	private function _quoteValue($value)
	{
		if (is_array($value))
		{
			return array_map(
				array($this, __FUNCTION__),
				$value
			);
		}

		if ($value === null)
		{
			return 'NULL';
		}

		if ($value instanceof DateTime)
		{
			$value = $value->format('c');
		}

		return $this->_pdo->quote($value);
	}

	/**
	 *
	 */
	private function _where(array $criteria = null)
	{
		if (empty($criteria))
		{
			return '';
		}

		$wheres = array();
		foreach ($criteria as $field => $value)
		{
			$field = $this->_quoteIdentifier($field);
			$value = $this->_quoteValue($value);

			$wheres[] = "$field = $value";
		}
		return ' WHERE '.implode(' AND ', $wheres);
	}
}
