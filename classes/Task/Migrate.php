<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Migration task
 *
 * Usage:
 *
 * 1. Migrate:
 * ./minion.php --task=Migrate
 *
 * 2. Check if migration is needed:
 * ./minion.php --task=Migrate --checkonly=1
 *
 * Note: minion should be executable (chmod a+x minion)
 *
 * @link https://koseven.ga/documentation/minion/tasks
 */
class Task_Migrate extends Minion_Task {

	protected $_options = [
		'checkonly' => NULL,
		'debug' => NULL,
	];

	protected function _execute(array $params)
	{
		$migration_files = $this->list_migration_files();
		$applied_migrations = Settings::get('applied_migrations');
		if ($applied_migrations === NULL)
		{
			// Только что развернули проект, миграции уже заложены в schema.sql, поэтому считаем их учтенными
			return Settings::set('applied_migrations', $migration_files);
		}

		$files_to_apply = array_values(array_diff($migration_files, $applied_migrations));

		if ($params['checkonly'])
		{
			Minion_CLI::write(empty($files_to_apply) ? '' : 'MIGRATIONS NEEDED');
			die();
		}

		if ($params['debug'])
		{
			Minion_CLI::write('Нужно сделать '.count($files_to_apply).' '.
				Inflector::ru_plural_form(count($files_to_apply), ['миграцию', 'миграции', 'миграций']).
				(empty($files_to_apply) ? '' : ':'));
			foreach ($files_to_apply as $index => $migration_file)
			{
				Minion_CLI::write(($index + 1).'. '.$migration_file);
			}
			if ( ! empty($files_to_apply))
			{
				Minion_CLI::write("\nПрименяем SQL-файлы:");
			}
		}
		foreach ($files_to_apply as $index => $migration_file)
		{
			if ($params['debug'])
			{
				Minion_CLI::write(($index + 1).'. '.$migration_file);
			}
			try
			{
				if (preg_match('~\.sql$~', $migration_file))
				{
					$this->apply_sql_migration($migration_file);
				}
				else
				{
					$this->apply_php_migration($migration_file);
				}

				$applied_migrations = $this->list_applied_migrations();
				$applied_migrations[] = $migration_file;
				Settings::set('applied_migrations', array_values(array_unique($applied_migrations)));
			}
			catch (Exception $e)
			{
				if ($params['debug'])
				{
					Minion_CLI::write("  ОШИБКА: ".$e->getMessage());
					$user_input = Minion_CLI::read('  Пропустить файл и отметить его как примененный? [yN] #');
					if ($user_input == 'y')
					{
						Minion_CLI::write("  ПРОПУСК");
						continue;
					}
				}
				die;
				break;
			}
			if ($params['debug'])
			{
				Minion_CLI::write("  УСПЕШНО");
			}
		}

		// Cleaning up the removed files
		// TODO Удалять из $applied_migrations записи о миграциях, которые старше 3 месяцев от текущей даты
//		$applied_migrations = array_values(array_intersect($this->list_applied_migrations(), $migration_files));

		Settings::set('applied_migrations', $migration_files);
	}

	protected $migration_files_dir = __DIR__.'/../../.provision/sql';

	protected function list_applied_migrations()
	{
		$applied_migrations = Settings::get('applied_migrations', []);
		if (empty($applied_migrations) OR ! is_array($applied_migrations))
		{
			$applied_migrations = [];
		}

		return $applied_migrations;
	}

	protected function list_migration_files()
	{
		$migration_files = [];
		foreach (scandir($this->migration_files_dir) as $file)
		{
			if ( ! preg_match('~^migration\-[\-\_a-z0-9]+\.(sql|php)$~', $file))
			{
				continue;
			}
			$migration_files[] = $file;
		}

		return $migration_files;
	}

	protected function apply_sql_migration($file)
	{
		$db = Database::instance();

		$db->begin();
		try
		{
			$queries = file_get_contents($this->migration_files_dir.'/'.$file);

			// Trimming comments
			$queries = preg_replace('~\-\-[^\n]+\n~', '', $queries);

			foreach (explode(';', $queries) as $query)
			{
				$query = trim($query);
				if ( ! empty($query))
				{
					$db->query(NULL, DB::expr($query));
				}
			}

			$db->commit();
		}
		catch (Database_Exception $e)
		{
			$db->rollback();
			throw $e;
		}
	}

	protected function apply_php_migration($file)
	{
		try
		{
			include $this->migration_files_dir.'/'.$file;
		}
		catch (Database_Exception $e)
		{
			throw $e;
		}
	}
}
