<?php

defined( 'ABSPATH' ) || die();

class BlogOrDieFuzzyTime {
	private $reference_timestamp;

	public function __construct( $reference_timestamp ) {
		$this->reference_timestamp = $reference_timestamp;
	}

	public function over_rough_period() {
		$periods        = array_filter( $this->elapsed_periods() );
		$longest_period = array_slice( $periods, 0, 1 );

		// I prefer: 'a day' over '1 days'
		if ( reset( $longest_period ) === '1' ) {
			$longest_period = [ substr( key( $longest_period ), 0, -1 ) => 'a' ];
		}

		$str = 'over ' . reset( $longest_period ) . ' ' . key( $longest_period );
		return str_replace( 'a hour', 'an hour', $str );
	}

	private function periods() {
		return [
			'years',
			'days',
			'hours',
			'minutes',
			'seconds',
		];
	}

	private function elapsed_periods() {
		$now  = new DateTime( current_time('mysql') );
		$then = new DateTime( "@$this->reference_timestamp" );

		$difference = $now->diff( $then )->format( '%y,%a,%h,%i,%s' );
		return array_combine( $this->periods(), explode( ',', $difference ) );
	}
}
