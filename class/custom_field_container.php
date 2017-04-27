<?php

class CustomFieldContainer extends CustomField {
	
	private $fields = array();
	private $field_name;

	function __construct( $name, $label, $type, $field_name = 'fields' ) {
		parent::__construct( $name, $label, $type );
		$this->field_name = $field_name;
	}

	function get( $key = null ) {
		if ( $key != null ) {
			if ( $key == $this->field_name ) {
				return $fields;
			}

			return parent::get( $key );
		}

		$data = parent::get();
		$data[ $this->field_name ] = $this->getFieldsData();
		return $data;
	}

	function getFields() {
		return $this->fields;
	}

	function getFieldsData() {
		$group_prefix = $this->get('key') . "_";
		$fields_data = array();

		foreach ( $this->fields as $field ) {
			$field_data        = $field->get();
			$field_data['key'] = $group_prefix . $field_data['key'];
			$fields_data[]     = $field_data;
		}

		return $fields_data;
	}

	function addField( $name, $label = null, $type = null ) {
		$field = null;

		if ( is_object( $name ) && get_class( $name ) == 'CustomField' ) {
			$field = $name;
		} elseif ( $label != null && $type != null ) {
			$field = new CustomField( $name, $label, $type );
		}

		if ( $field != null ) {
			$this->fields[] = $field;
		}

		return $field;
	}

	function addContainer( $name, $label, $type, $field_name = null ) {
		if ( $field_name == null ) {
			$field_name = ACFD::getContainerFieldName( $type );
		}

		$field = new CustomFieldContainer( $name, $label, $type, $field_name );
		$this->fields[] = $field;
		return $field;
	}

	function addModule( $module, $prefix = null ) {
		if ( $module == null ) {
			return $this;
		}

		if ( $prefix == null ) {
			$prefix = $this->get( 'name' ) . '_';
		}
		
		foreach ( $module->fields as $field ) {
			$this->addField( $field->copy( $prefix . $field->get( 'name' ) ) );
		}

		return $this;
	}
	
}

