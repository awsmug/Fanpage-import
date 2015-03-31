<?php

namespace skip\v1_0_0;

/**
 * skip_id()
 *
 * Getting an unique ID.
 *
 * @package Skip\Tools
 * @since 1.0
 * @ignore
 */
function id( $length = 5 ){
	return substr( md5( rand() * time() ), 0, $length );
}

/**
 * skip_print( $value )
 *
 * Printing out variables on HTML frontend
 *
 * @param mixed $value The value to print out
 * @package Skip\Tools
 * @since 1.0
 * @ignore
 */
function p( $value ){
	echo '<pre>';
	print_r( $value );
	echo '</pre>';
}	

/**
 * skip_max_upload()
 * 
 * Get the max upload size in Bytes
 *
 * @return int $size Returns the max upload size in Bytes
 * @package Skip/Tools
 * @since 1.0
 */
function max_upload() {
	$size = ini_get( 'post_max_size' );
    $size = trim( $size );
    $last = strtolower( $size[strlen( $size )-1] );
	
    switch( $last ) {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $size *= 1024;
        case 'm':
            $size *= 1024;
        case 'k':
            $size *= 1024;
    }

    return $size;
}

/**
 * Marge arrays recursively and distinct
 * 
 * Merges any number of arrays / parameters recursively, replacing 
 * entries with string keys with values from latter arrays. 
 * If the entry or the next value to be assigned is an array, then it 
 * automagically treats both arguments as an array.
 * Numeric entries are appended, not replaced, but only if they are 
 * unique
 *
 * @param  array $array1 Initial array to merge.
 * @param  array ...     Variable list of arrays to recursively merge.
 *
 * @link   http://www.php.net/manual/en/function.array-merge-recursive.php#96201
 * @author Mark Roduner <mark.roduner@gmail.com>
 * @ignore
 */
function array_merge_recursive_distinct()
{
    $arrays = func_get_args();
    $base = array_shift($arrays);

    if(!is_array($base)) $base = empty($base) ? array() : array($base);

    foreach($arrays as $append) {
        if(!is_array($append)) $append = array($append);
        foreach($append as $key => $value) {
            if(!array_key_exists($key, $base) and !is_numeric($key)) {
                $base[$key] = $append[$key];
                continue;
            }
            if(is_array($value) or is_array($base[$key])) {
                $base[$key] = skip_array_merge_recursive_distinct($base[$key], $append[$key]);
            }
            else if(is_numeric($key))
            {
                if(!in_array($value, $base)) $base[] = $value;
            }
            else {
                $base[$key] = $value;
            }
        }
    }

    return $base;
}