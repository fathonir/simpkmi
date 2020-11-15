<?php

/**
 * Assign $ to $object properties
 * @param mixed $object Object target to assign
 * @param array $array Array source
 */
function assign_to($object, $array)
{
	foreach ($array as $key => $value)
	{
		if (property_exists($object, $key))
		{
			$object->{$key} = $value;
		}
	}
}
