<?php

/*
 * Log information to the console.
 * Include timestamp, and any debug-data
 * our caller might pass us.
 */

function vipgoci_log( $str, $debug_data ) {
	echo '[ ' . date( 'c' ) . ' ]  ' .
		$str .
		'; ' .
		print_r(
			json_encode(
				$debug_data,
				JSON_PRETTY_PRINT
			),
			true
		) .
		"\n\r";
}


/*
 * Look at a patch given to use by our caller,
 * and figure out what lines of the target-file
 * were affected by the patch.
 *
 * This function will return an associative
 * array, were the keys represent every line
 * in the patch (except for the "@@" lines),
 * while the values represent line in the
 * raw committed line. Some keys might point
 * to empty values, in which case there is no
 * relation between the two.
 */

function vipgoci_patch_changed_lines(
	$repo_owner,
	$repo_name,
	$github_token,
	$pr_base_sha,
	$commit_id,
	$file_name
) {

	/*
	 * Fetch patch for all files of the Pull-Request
	 */
	$patch_arr = vipgoci_github_diffs_fetch(
		$repo_owner,
		$repo_name,
		$github_token,
		$pr_base_sha,
		$commit_id
	);

	/*
	 * Get patch for the relevant file
	 * our caller is interested in
	 */
	// FIXME: Detect if file is not part of the patch
	$lines_arr = explode(
		"\n",
		$patch_arr[ $file_name ]
	);

	$lines_changed = array();

	$i = 1;

	foreach ( $lines_arr as $line ) {
		preg_match_all(
			"/^@@\s+[-\+]([0-9]+,[0-9]+)\s+[-\+]([0-9]+,[0-9]+)\s+@@/",
			$line,
			$matches
		);

		if ( ! empty( $matches[0] ) ) {
			$start_end = explode(
				',',
				$matches[2][0]
			);


			$i = $start_end[0];

			$lines_changed[] = null;
		}

		else if ( empty( $matches[0] ) ) {
			if ( $line[0] == '-' ) {
				$lines_changed[] = null;
			}

			else if (
				( $line[0] == '+' ) ||
				( $line[0] == ' ' ) ||	// space
				( $line[0] == '	' ) ||	// tab
				( $line[0] == '\\' )	// a single \
			) {
				$lines_changed[] = $i++;
			}
		}
	}

	return $lines_changed;
}


/*
 * Filter out any issues in the code that were not
 * touched up on by the patch -- i.e., any issues
 * that existed prior to the change.
 */
function vipgoci_issues_filter_irrellevant(
	$file_issues_arr,
	$file_changed_lines
) {
	foreach (
		$file_issues_arr as
			$file_issue_line => $file_issue_val
	) {
		if ( ! in_array(
			$file_issue_line,
			$file_changed_lines
		) ) {
			unset(
				$file_issues_arr[
					$file_issue_line
				]
			);
		}
	}

	return $file_issues_arr;
}


/*
 * Get a specific item from in-memory cache based on
 * $cache_id_arr if $data is null, or if $data is not null,
 * add a specific item to cache.
 *
 * The data is stored in an associative array, with
 * key being an array (or anything else) -- $cache_id_arr --,
 * and used to identify the data up on retrieval.
 */

function vipgoci_cache( $cache_id_arr, $data = null ) {
	global $vipgoci_cache_buffer;

	$cache_id = json_encode(
		$cache_id_arr
	);


	if ( null === $data ) {
		if ( isset( $vipgoci_cache_buffer[ $cache_id ] ) ) {
			return $vipgoci_cache_buffer[ $cache_id ];
		}

		else {
			return false;
		}
	}

	$vipgoci_cache_buffer[ $cache_id ] = $data;

	return $data;
}


/*
 * Create a temporary file, and return the
 * full-path to the file.
 */

function vipgoci_save_temp_file( $file_name_prefix, $file_contents ) {
	/*
	 * Create temporary directory to save
	 * fetched files into
	 */
	$temp_file_name = $temp_file_save_status = tempnam(
		sys_get_temp_dir(),
		$file_name_prefix
	);

	if ( false !== $temp_file_name ) {
		$temp_file_save_status = file_put_contents(
			$temp_file_name,
			$file_contents
		);
	}

	// Detect possible errors when saving the temporary file
	if ( false === $temp_file_save_status ) {
		vipgoci_log(
			'Could not save file to disk, got ' .
			'an error. Exiting...',

			array(
				'temp_file_name' => $temp_file_name,
			)
		);

		exit( 254 );
	}

	return $temp_file_name;
}


/*
 * Return ASCII-art for GitHub, which will then
 * be turned into something more fancy. This is
 * intended to be called when preparing messages/comments
 * to be submitted to GitHub.
 */
function vipgoci_github_labels( $text_string ) {
	switch( strtolower( $text_string ) ) {
		case 'warning':
			return ':exclamation:';

		case 'error':
			return ':no_entry_sign:';
	}

	return '';
}
