<?php
/**
 * IP Geo Block - Cron Class
 *
 * @package   IP_Geo_Block
 * @author    tokkonopapa <tokkonopapa@yahoo.com>
 * @license   GPL-3.0
 * @link      http://www.ipgeoblock.com/
 * @copyright 2013-2018 tokkonopapa
 */

class IP_Geo_Block_Cron {

	/**
	 * Cron scheduler.
	 *
	 */
	private static function schedule_cron_job( &$update, $db, $immediate = FALSE ) {
		wp_clear_scheduled_hook( IP_Geo_Block::CRON_NAME, array( $immediate ) );

		if ( $update['auto'] ) {
			$now = time();
			$next = $now + ( $immediate ? 0 : DAY_IN_SECONDS );

			if ( FALSE === $immediate ) {
				++$update['retry'];
				$cycle = DAY_IN_SECONDS * (int)$update['cycle'];

				if ( isset( $db['ipv4_last'] ) ) {
					// in case of Maxmind Legacy or IP2Location
					if ( $now - (int)$db['ipv4_last'] < $cycle &&
					     $now - (int)$db['ipv6_last'] < $cycle ) {
						$update['retry'] = 0;
						$next = max( (int)$db['ipv4_last'], (int)$db['ipv6_last'] ) +
							$cycle + rand( DAY_IN_SECONDS, DAY_IN_SECONDS * 6 );
					}
				} else {
					// in case of Maxmind GeoLite2
					if ( $now - (int)$db['ip_last'] < $cycle ) {
						$update['retry'] = 0;
						$next = (int)$db['ip_last'] +
							$cycle + rand( DAY_IN_SECONDS, DAY_IN_SECONDS * 6 );
					}
				}
			}

			wp_schedule_single_event( $next, IP_Geo_Block::CRON_NAME, array( $immediate ) );
		}
	}

	/**
	 * Database auto downloader.
	 *
	 * This function is called when:
	 *   1. Plugin is activated
	 *   2. WP Cron is kicked
	 * under the following condition:
	 *   A. Once per site when this plugin is activated on network wide
	 *   B. Multiple time for each blog when this plugin is individually activated
	 */
	public static function exec_update_db( $immediate = FALSE ) {
		$settings = IP_Geo_Block::get_option();
		$args = IP_Geo_Block::get_request_headers( $settings );

		// extract ip address from transient API to confirm the request source
		add_filter( IP_Geo_Block::PLUGIN_NAME . '-ip-addr', array( __CLASS__, 'extract_ip' ) );

		// download database files (higher priority order)
		foreach ( $providers = IP_Geo_Block_Provider::get_addons( $settings['providers'] ) as $provider ) {
			if ( $geo = IP_Geo_Block_API::get_instance( $provider, $settings ) ) {
				$res[ $provider ] = $geo->download( $settings[ $provider ], $args );

				// re-schedule cron job
				self::schedule_cron_job( $settings['update'], $settings[ $provider ], FALSE );

				// update provider settings
				self::update_settings( $settings, array( 'update', $provider ) );

				// skip to update settings in case of InfiniteWP that could be in a different country
				if ( isset( $_SERVER['HTTP_X_REQUESTED_FROM'] ) && FALSE !== strpos( $_SERVER['HTTP_X_REQUESTED_FROM'], 'InfiniteWP' ) )
					continue;

				// update matching rule immediately
				if ( $immediate && 'done' !== get_transient( IP_Geo_Block::CRON_NAME ) ) {
					$validate = IP_Geo_Block::get_geolocation( NULL, array( $provider ) );
					$validate = IP_Geo_Block::validate_country( 'cron', $validate, $settings );

					if ( 'ZZ' === $validate['code'] )
						continue;

					// matching rule should be reset when blocking happens 
					if ( 'passed' !== $validate['result'] )
						$settings['matching_rule'] = -1;

					// setup country code in whitelist if it needs to be initialized
					if ( -1 === (int)$settings['matching_rule'] ) {
						$settings['matching_rule'] = 0; // white list

						if ( FALSE === strpos( $settings['white_list'], $validate['code'] ) )
							$settings['white_list'] .= ( $settings['white_list'] ? ',' : '' ) . $validate['code'];

						// update option settings
						self::update_settings( $settings, array( 'matching_rule', 'white_list' ) );
					}

					// finished to update matching rule
					if ( -1 !== (int)$settings['matching_rule'] )
						set_transient( IP_Geo_Block::CRON_NAME, 'done', 5 * MINUTE_IN_SECONDS );
				}
			}
		}

		return isset( $res ) ? $res : NULL;
	}

	/**
	 * Update setting data according to the site type.
	 *
	 */
	private static function update_settings( $src, $keys = array() ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// for multisite (@since 3.0.0 in wp-admin/includes/plugin.php)
		if ( is_plugin_active_for_network( IP_GEO_BLOCK_BASE ) ) {
			global $wpdb;
			$blog_ids = $wpdb->get_col( "SELECT `blog_id` FROM `$wpdb->blogs`" );

			foreach ( $blog_ids as $id ) {
				switch_to_blog( $id );
				$dst = IP_Geo_Block::get_option();

				foreach ( $keys as $key ) {
					$dst[ $key ] = $src[ $key ];
				}

				update_option( IP_Geo_Block::OPTION_NAME, $dst );
				restore_current_blog();
			}
		}

		// for single site
		else {
			update_option( IP_Geo_Block::OPTION_NAME, $src );
		}
	}

	/**
	 * Extract ip address from transient API.
	 *
	 */
	public static function extract_ip() {
		return filter_var(
			$ip_adrs = get_transient( IP_Geo_Block::CRON_NAME ), FILTER_VALIDATE_IP
		) ? $ip_adrs : $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * Kick off a cron job to download database immediately in background on activation.
	 *
	 */
	public static function start_update_db( $settings, $force = FALSE ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		// the status is still inactive when this plugin is activated on dashboard.
		if ( ! ( is_plugin_active            ( IP_GEO_BLOCK_BASE )   ||            // @since 2.5.0
		         is_plugin_active_for_network( IP_GEO_BLOCK_BASE ) ) || $force ) { // @since 3.0.0
			set_transient( IP_Geo_Block::CRON_NAME, IP_Geo_Block::get_ip_address(), MINUTE_IN_SECONDS );
			self::schedule_cron_job( $settings['update'], NULL, TRUE );
		}
	}

	public static function stop_update_db() {
		wp_clear_scheduled_hook( IP_Geo_Block::CRON_NAME, array( FALSE ) ); // @since 2.1.0
	}

	/**
	 * Kick off a cron job to garbage collection for IP address cache.
	 *
	 */
	public static function exec_cache_gc( $settings ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		if ( is_plugin_active_for_network( IP_GEO_BLOCK_BASE ) ) {
			global $wpdb;
			$blog_ids = $wpdb->get_col( "SELECT `blog_id` FROM `$wpdb->blogs`" );

			foreach ( $blog_ids as $id ) {
				switch_to_blog( $id );
				IP_Geo_Block_Logs::delete_cache_expired( $settings['cache_time'] );
				restore_current_blog();
			}
		}

		// for single site
		else {
			IP_Geo_Block_Logs::delete_cache_expired( $settings['cache_time'] );
		}

		self::stop_cache_gc();
		self::start_cache_gc( $settings );
	}

	public static function start_cache_gc( $settings ) {
		if ( ! wp_next_scheduled( IP_Geo_Block::CACHE_NAME ) )
			wp_schedule_single_event( time() + $settings['cache_time_gc'], IP_Geo_Block::CACHE_NAME );
	}

	public static function stop_cache_gc() {
		wp_clear_scheduled_hook( IP_Geo_Block::CACHE_NAME ); // @since 2.1.0
	}

	/**
	 * Decompresses gz archive and output to the file.
	 *
	 * @param string $src full path to the downloaded file.
	 * @param string $dst full path to extracted file.
	 * @return TRUE or array of error code and message.
	 */
	private static function gzfile( $src, $dst ) {
		try {
			if ( FALSE === ( $gz = gzopen( $src, 'r' ) ) )
				throw new Exception(
					sprintf( __( 'Unable to read <code>%s</code>. Please check the permission.', 'ip-geo-block' ), $src )
				);

			if ( FALSE === ( $fp = @fopen( $dst, 'cb' ) ) )
				throw new Exception(
					sprintf( __( 'Unable to write <code>%s</code>. Please check the permission.', 'ip-geo-block' ), $filename )
				);

			if ( ! flock( $fp, LOCK_EX ) )
				throw new Exception(
					sprintf( __( 'Can\'t lock <code>%s</code>. Please try again after a while.', 'ip-geo-block' ), $filename )
				);

			ftruncate( $fp, 0 ); // truncate file

			// same block size in wp-includes/class-http.php
			while ( $data = gzread( $gz, 4096 ) ) {
				fwrite( $fp, $data, strlen( $data ) );
			}
		}

		catch ( Exception $e ) {
			$err = array(
				'code'    => $e->getCode(),
				'message' => $e->getMessage(),
			);
		}

		if ( ! empty( $fp ) ) {
			fflush( $fp );          // flush output before releasing the lock
			flock ( $fp, LOCK_UN ); // release the lock
			fclose( $fp );
		}

		return empty( $err ) ? TRUE : $err;
	}

	/**
	 * Download zip/gz file, uncompress and save it to specified file
	 *
	 * @param string $url URL of remote file to be downloaded.
	 * @param array $args request headers.
	 * @param string $filename full path to the downloaded file.
	 * @param int $modified time of last modified on the remote server.
	 * @return array status message.
	 */
	public static function download_zip( $url, $args, $files, $modified ) {
		require_once IP_GEO_BLOCK_PATH . 'classes/class-ip-geo-block-file.php';
		$fs = IP_Geo_Block_FS::init( 'download_zip' );

		// get extension
		$ext = strtolower( pathinfo( $url, PATHINFO_EXTENSION ) );
		if ( 'tar' === strtolower( pathinfo( pathinfo( $url, PATHINFO_FILENAME ), PATHINFO_EXTENSION ) ) )
			$ext = 'tar';

		// check file (1st parameter includes absolute path in case of array)
		$filename = is_array( $files ) ? $files[0] : (string)$files;
		if ( ! $fs->exists( $filename ) )
			$modified = 0;

		// set 'If-Modified-Since' request header
		$args += array(
			'headers'  => array(
				'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
				'Accept-Encoding' => 'gzip, deflate',
				'If-Modified-Since' => gmdate( DATE_RFC1123, (int)$modified ),
			),
		);

		// fetch file and get response code & message
		if ( isset( $args['method'] ) && 'GET' === $args['method'] )
			$src = wp_remote_get ( ( $url = esc_url_raw( $url ) ), $args );
		else
			$src = wp_remote_head( ( $url = esc_url_raw( $url ) ), $args );

		if ( is_wp_error( $src ) )
			return array(
				'code' => $src->get_error_code(),
				'message' => $src->get_error_message(),
			);

		$code = wp_remote_retrieve_response_code   ( $src );
		$mesg = wp_remote_retrieve_response_message( $src );
		$data = wp_remote_retrieve_header( $src, 'last-modified' );
		$modified = $data ? strtotime( $data ) : $modified;

		if ( 304 == $code )
			return array(
				'code' => $code,
				'message' => __( 'Your database file is up-to-date.', 'ip-geo-block' ),
				'filename' => $filename,
				'modified' => $modified,
			);

		elseif ( 200 != $code )
			return array(
				'code' => $code,
				'message' => $code.' '.$mesg,
			);

		try {
			// in case that the server which does not support HEAD method
			if ( isset( $args['method'] ) && 'GET' === $args['method'] ) {
				$data = wp_remote_retrieve_body( $src );

				if ( 'gz' === $ext ) {
					if ( function_exists( 'gzdecode') ) { // @since PHP 5.4.0
						if ( FALSE === $fs->put_contents( $filename, gzdecode( $data ) ) )
							throw new Exception(
								sprintf( __( 'Unable to write <code>%s</code>. Please check the permission.', 'ip-geo-block' ), $filename )
							);
					}

					else {
						$src = get_temp_dir() . basename( $url ); // $src should be removed
						$fs->put_contents( $src, $data );
						TRUE === ( $ret = self::gzfile( $src, $filename ) ) or $err = $ret;
					}
				}

				elseif ( 'tar' === $ext && class_exists( 'PharData', FALSE ) ) { // @since PECL phar 2.0.0
					$name = wp_remote_retrieve_header( $src, 'content-disposition' );
					$name = explode( 'filename=', $name );
					$name = array_pop( $name ); // e.g. GeoLite2-Country_20180102.tar.gz
					$src  = ( $tmp = get_temp_dir() ) . $name; // $src should be removed

					// CVE-2015-6833: A directory traversal when extracting ZIP files could be used to overwrite files
					// outside of intended area via a `..` in a ZIP archive entry that is mishandled by extractTo().
					if ( $fs->put_contents( $src, $data ) ) {
						$data = new PharData( $src, FilesystemIterator::SKIP_DOTS ); // get archives

						// make the list of contents to be extracted from archives.
						// when the list doesn't match the contents in archives, extractTo() may be crushed on windows.
						$dst = $data->getSubPathname(); // e.g. GeoLite2-Country_20180102
						foreach ( $files as $key => $val ) {
							$files[ $key ] = $dst.'/'.basename( $val );
						}

						// extract specific files from archives into temporary directory and copy it to the destination.
						$data->extractTo( $tmp .= $dst, $files /* NULL */, TRUE ); // $tmp should be removed

						// copy extracted files to Geolocation APIs directory
						$dst = dirname( $filename );
						foreach ( $files as $val ) {
							// should the destination be exclusive with LOCK_EX ?
							// $fs->put_contents( $dst.'/'.basename( $val ), $fs->get_contents( $tmp.'/'.$val ) );
							$fs->copy( $tmp.'/'.$val, $dst.'/'.basename( $val ), TRUE );
						}
					}
				}
			}

			// downloaded and unzip
			else {
				// download file
				$src = download_url( $url );

				if ( is_wp_error( $src ) )
					throw new Exception(
						$src->get_error_code() . ' ' . $src->get_error_message()
					);

				// unzip file
				if ( 'gz' === $ext ) {
					TRUE === ( $ret = self::gzfile( $src, $filename ) ) or $err = $ret;
				}

				elseif ( 'zip' === $ext && class_exists( 'ZipArchive', FALSE ) ) {
					$tmp = get_temp_dir(); // @since 2.5
					$ret = $fs->unzip_file( $src, $tmp ); // @since 2.5

					if ( is_wp_error( $ret ) )
						throw new Exception(
							$ret->get_error_code() . ' ' . $ret->get_error_message()
						);

					if ( FALSE === ( $data = $fs->get_contents( $tmp .= basename( $filename ) ) ) )
						throw new Exception(
							sprintf( __( 'Unable to read <code>%s</code>. Please check the permission.', 'ip-geo-block' ), $tmp )
						);

					if ( FALSE === $fs->put_contents( $filename, $data ) )
						throw new Exception(
							sprintf( __( 'Unable to write <code>%s</code>. Please check the permission.', 'ip-geo-block' ), $filename )
						);
				}

				else {
					throw new Exception( __( 'gz or zip is not supported on your system.', 'ip-geo-block' ) );
				}
			}
		}

		// error handler
		catch ( Exception $e ) {
			$err = array(
				'code'    => $e->getCode(),
				'message' => $e->getMessage(),
			);
		}

		! empty  ( $gz  ) and gzclose( $gz );
		! empty  ( $tmp ) and $fs->delete( $tmp, TRUE ); // should be removed recursively in case of directory
		is_string( $src ) && $fs->is_file( $src ) and $fs->delete( $src );

		return empty( $err ) ? array(
			'code' => $code,
			'message' => sprintf( __( 'Last update: %s', 'ip-geo-block' ), IP_Geo_Block_Util::localdate( $modified ) ),
			'filename' => $filename,
			'modified' => $modified,
		) : $err;
	}

}