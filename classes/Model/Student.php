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

	public function describe_vof_fields()
	{
		return [
			'first_name' => [
				'title' => 'Имя',
				'type' => 'text',
				'rules' => [
					['not_empty'],
				],
			],
			'last_name' => [
				'title' => 'Фамилия',
				'type' => 'text',
				'rules' => [
					['not_empty'],
				],
			],
			'birthday' => [
				'title' => 'День рождения',
				'type' => 'text',
				'description' => 'В формате ГГГГ-ММ-ДД',
				'rules' => [
					['not_empty'],
					['regex', [':value', '~^\d{4}\-\d{2}\-\d{2}$~']],
				],
			],
			'phone' => [
				'title' => 'Телефон',
				'type' => 'text',
				'description' => 'Формат: +79261234567',
				'rules' => [
					['not_empty'],
					['phone'],
				],
			],
		];
	}

}