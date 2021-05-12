<?php

/**
 * Class ORM_Meta
 * Implementing Meta-interface for a class: any data with any keys can be stored in a single serialized column.
 */
trait ORM_Meta {

	protected $_meta_column = 'meta';

	protected function is_meta_column($column)
	{
		return ( ! isset($this->_table_columns[$column]) AND ! isset($this->_belongs_to[$column]) AND ! isset($this->_has_one[$column]) AND ! isset($this->_has_many[$column]));
	}

	public function set($column, $value)
	{
		if ( ! $this->is_meta_column($column) OR ! array_key_exists($this->_meta_column, $this->table_columns()))
		{
			return parent::set($column, $value);
		}

		$meta_column = $this->_object[$this->_meta_column];
		if ($meta_column === NULL)
		{
			$meta = [];
		}
		else
		{
			$meta = $this->_unserialize_value($meta_column);
		}
		if ( ! is_array($meta))
		{
			$meta = [];
		}

		$value_changed = ($value === NULL)
			? isset($meta[$column])
			: ( ! isset($meta[$column]) OR $meta[$column] !== $value);
		if ($value_changed)
		{
			if ($value !== NULL)
			{
				// Add / change
				$meta[$column] = $value;
			}
			else
			{
				// Remove
				unset($meta[$column]);
			}
			$this->_object[$this->_meta_column] = $this->_serialize_value($meta);
			$this->_changed[$this->_meta_column] = $this->_meta_column;
			$this->_saved = $this->_valid = FALSE;
		}

		return $this;
	}

	public function get($column)
	{
		if ( ! $this->is_meta_column($column))
		{
			return parent::get($column);
		}
		$meta = $this->_unserialize_value($this->_object[$this->_meta_column]);
		if ( ! is_array($meta) OR ! isset($meta[$column]))
		{
			return NULL;
		}

		return $meta[$column];
	}

	/**
	 * Processing meta-parameters just like the real ones
	 *
	 * @param bool $show_all
	 * @return array
	 */
	public function as_array($show_all = FALSE)
	{
		$result = parent::as_array($show_all);
		$meta_array = Arr::get($result, $this->_meta_column, []);
		$result = array_merge(is_array($meta_array) ? $meta_array : [], $result);
		unset($result[$this->_meta_column]);

		return $result;
	}

}
