<?php defined('SYSPATH') or die('No direct script access.');

class Model_User extends Model_Auth_User {

	use ORM_Meta {
		as_array as protected meta_as_array;
	}

	protected $_table_name = 'users';

	protected $_primary_key = 'id';

	protected $_table_columns = [
		'id' => 'int',
		'username' => 'string',
		'password' => 'string',
		'email' => 'string',
		'full_name' => 'string',
		'role' => 'enum',
		'status' => 'enum',
		'meta' => 'string',
		'logins' => 'int',
		'last_login' => 'datetime',
		'created' => 'datetime',
	];

	protected $_has_one = [
		'user_token' => [
			'model' => 'User_Token',
			'foreign_key' => 'user_id',
		],
	];

	protected $_created_column = [
		'column' => 'created',
		'format' => 'Y-m-d H:i:s',
	];

	protected $_serialize_columns = ['meta'];

	public function describe_vof_fields()
	{
		return [
			'full_name' => [
				'title' => 'Имя Фамилия',
				'type' => 'text',
				'rules' => [
					['not_empty'],
				],
			],
			'email' => [
				'title' => 'Email',
				'type' => 'text',
				'rules' => [
					['not_empty'],
				],
			],
			'phone' => [
				'title' => 'Телефон',
				'type' => 'text',
				'description' => 'Формат: +79261234567',
				'rules' => [
					['phone'],
				],
			],
			'username' => [
				'title' => 'Логин',
				'type' => 'text',
				'rules' => [
					['not_empty'],
				],
			],
			'status' => [
				'title' => 'Статус',
				'type' => 'radio',
				'options' => [
					'active' => 'Активный',
					'inactive' => 'Неактивный',
					'blocked' => 'Заблокирован',
				],
				'classes' => 'layout_switch',
				'rules' => [
					['not_empty'],
				],
			],
			'role' => [
				'title' => 'Роль',
				'type' => 'select2',
				'options' => [
					'owner' => 'Владелец',
					'admin' => 'Админ',
					'manager' => 'Менеджер',
					'customer' => 'Клиент',
				],
				'std' => 'customer',
				'rules' => [
					['not_empty'],
				],
			],
		];
	}

	public function rules()
	{
		return array(
			'password' => [
				['not_empty'],
			],
			'email' => [
				['not_empty'],
				['email'],
				[[$this, 'unique'], ['email', ':value']],
			],
		);
	}

	public function filters()
	{
		return [
			'password' => [
				[[Auth::instance(), 'hash']],
			],
			'username' => [
				['trim'],
			],
			'email' => [
				['trim'],
			],
		];
	}

	protected $_meta_fields = [
		'slack_id',
	];

	public function values(array $values, array $expected = NULL)
	{
		$this->meta = array_intersect_key($values, array_combine($this->_meta_fields, $this->_meta_fields));

		return parent::values($values, $expected);
	}

	public function delete()
	{
		// user_tokens
		DB::delete('user_tokens')
			->where('user_id', '=', $this->id)
			->execute($this->_db);

		parent::delete();
	}

	public function unique_key($value)
	{
		return Valid::email($value) ? 'email' : 'username';
	}

	public function change_password(array $values, array &$errors = [])
	{
		$new_password = Arr::get($values, 'new_password');
		$retype_password = Arr::get($values, 'retype_password');

		if ( ! empty($new_password) AND strlen($new_password) < 4)
		{
			$errors['new_password'] = 'Пароль не может быть меньше 4 символов';
		}

		if ($new_password !== $retype_password)
		{
			$errors['retype_password'] = 'Пароли не совпадают';
		}

		return (empty($errors));
	}
}
