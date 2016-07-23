<?php

class CustomGroup extends CustomFieldContainer {

	function __construct( $name, $location = 'options_page == acf-options' ) {
		$this->set( 'key', 'group_' . sha1($name) );
		$this->set( 'title', $name );
		$this->setLocation( $location );

		ACFED::addGroup( $this );
	}
	
	function setLocation( $location ) {
		if ( ! is_array( $location ) ) {
			$location = self::parseLocationString( $location );
		}

		$this->set('location', $location);
	}

	static function parseLocationString( $location_string ) {
		$groups = explode( 'OR', $location_string );

		foreach ( $groups as $g => $group ) {
			$group = trim( $group );
			$length = strlen( $group );
			
			if ( $group[0] == "(" && $group[$length - 1] == ")" ) {
				$group = substr( $group, 1, $length - 2 );
			}
			
			$rules = explode( 'AND', $group );
			
			foreach ( $rules as $r => $rule ) {
				$rule = trim( $rule );
				
				if ( preg_match_all( '/(.+)\s*(==)\s*(.*)/', $rule, $matches ) ) {
					$rules[$r] = array(
						'param' => trim( $matches[1][0] ),
						'operator' => trim( $matches[2][0] ),
						'value' => trim( $matches[3][0] ),
					);
					continue;
				}
				throw new Exception('Invalid location expression');
			}
			
			$groups[$g] = $rules;
		}

		return $groups;
	}
}


