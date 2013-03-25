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
 * @todo Handle createOrUpdate
 */
interface Manager
{

	//--------------------------------------
	// Table manipulation.
	//--------------------------------------

	/**
	 * @param string $name
	 * @param callable $callback
	 */
	function createTable($name, $callback);

	/**
	 * @param string $name
	 */
	function deleteTable($name);

	/**
	 * @param string $name
	 *
	 * @return Table
	 */
	//function getTable($name);

	/**
	 * @return string[]
	 */
	function getTables();

	//--------------------------------------
	// Data manipulation.
	//--------------------------------------

	/**
	 * @param string $table
	 * @param array $filter Properties that must match.
	 *
	 * @return integer The number of entries matching this filter.
	 */
	function count($table, array $filter = null);

	/**
	 * @param string  $table
	 * @param array[] $entries
	 *
	 * @return array[] For each entries an array with (at least)
	 *     generated properties (defaults, auto-increments, etc.).
	 */
	function create($table, array $entries);

	/**
	 * @param string $table
	 * @param array $filter Properties that must match.
	 *
	 * @return integer The number of deleted objets.
	 */
	function delete($table, array $filter);

	/**
	 * @param string $table
	 * @param array $filter Properties that must match.
	 * @param array $fields The name of the fields to get, “null” for
	 *     everything.
	 *
	 * @return array[] The entries found.
	 */
	function get($table, array $filter = null, array $fields = null);

	/**
	 * @param string $table
	 * @param array $filter Properties that must match.
	 * @param array[] $properties
	 *
	 * @return integer The number of updated entries.
	 */
	function update($table, array $filter, array $properties);
}
