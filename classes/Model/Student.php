<?php

class Model_Student extends ORM {

	protected $_table_name = 'students';

	protected $_primary_key = 'id';

	protected $_table_columns = [
		'id' => 'int',
		'first_name' => 'string',
		'last_name' => 'string',
		'birthday' => 'date',
		'phone' => 'string',
		'created' => 'datetime',
		'updated' => 'datetime',
	];

	protected $_created_column = [
		'column' => 'created',
		'format' => 'Y-m-d H:i:s',
	];

	protected $_updated_column = [
		'column' => 'updated',
		'format' => 'Y-m-d H:i:s',
	];

}