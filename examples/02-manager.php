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

require(__DIR__.'/../vendor/autoload.php');

use Rekodi\Manager\Memory as Manager;
use Rekodi\Filter;

//--------------------------------------

$manager = new Manager;

// Lets create a table.
$manager->createTable('albums', function ($table) {
	$table
		->string('title')->unique()
		->string('artist')
		;
});

// Let's create a bunch of entries.
$manager->create('albums', array(
	array(
		'title'  => 'Back in Black',
		'artist' => 'AC/DC',
	),
	array(
		'title'  => 'Thriller',
		'artist' => 'Michael Jackson',
	),
	array(
		'title'  => 'Bad',
		'artist' => 'Michael Jackson',
	),
));

// We can search all Michael Jackson manager.
$results = $manager->get(
	'albums',
	array('artist' => 'Michael Jackson'), // Filter.
	array('title')                        // Fields.
);
echo count($results), " album(s) retrieved:\n";
print_r($results);
echo "----\n";

// We can update an entry.
$n = $manager->update(
	'albums',
	array('title' => 'Back in Black'), // Filter.
	array('date'  => '1980-07-25')     // New properties.
);
echo "$n album(s) updated.\n";
echo "----\n";

// We can delete everything.
$n = $manager->delete(
	'albums',
	array() // Filter.
);
echo "$n album(s) deleted.\n";
