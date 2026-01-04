<?php

namespace DatabaseLog\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\I18n\Number;
use Cake\Log\Log;
use Cake\Utility\Text;

class ShowCommand extends Command {

	/**
	 * @return string
	 */
	public static function getDescription(): string {
		return 'List log entries.';
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
	 * @param \Cake\Console\Arguments $args The command arguments.
	 * @param \Cake\Console\ConsoleIo $io The console io
	 * @return int|null|void The exit code or null for success
	 */
	public function execute(Arguments $args, ConsoleIo $io) {
		parent::execute($args, $io);

		if ($args->getOption('test-entry')) {
			$errorType = (string)$args->getOption('test-entry');
			$context = [];
			$scope = null;
			if (strpos($errorType, ':') !== false) {
				[$errorType, $scope] = explode(':', $errorType, 2);
				$context['scope'] = $scope;
			}

			$this->log('A test message from CLI' . ($context ? 'with scope `' . $scope . '`' : ''), $errorType, $context);
		}

		if ($args->getOption('verbose')) {
			$dbType = $this->fetchTable('DatabaseLog.DatabaseLogs')->databaseType();
			$out = 'DB type: ' . $dbType;
			$dbSize = $this->fetchTable('DatabaseLog.DatabaseLogs')->databaseSize();
			if ($dbSize !== null) {
				$out .= ' (' . Number::toReadableSize($dbSize) . ')';
			}
			$io->out($out);
		}

		$query = $this->fetchTable('DatabaseLog.DatabaseLogs')->find();

		$type = $args->getArgument('type');
		if ($type) {
			$types = Text::tokenize($type);
			$query->where(['type IN' => $types]);
		}
		$limit = (string)$args->getOption('limit');
		$offset = null;
		if (!$limit) {
			$limit = 20;
		} elseif (str_contains($limit, ',')) {
			$elements = explode(',', $limit);
			$offset = (int)$elements[0];
			$limit = (int)$elements[1];
		}

		/** @var array<\DatabaseLog\Model\Entity\DatabaseLog> $logs */
		$logs = $query->orderBy(['created' => 'DESC'])
			->limit((int)$limit)
			->offset($offset)
			->all();

		foreach ($logs as $log) {
			$content = $log->created . ': ' . $log->type;
			$pieces = explode("\n", trim($log->message), 2);
			$shortMessage = Text::truncate(trim($pieces[0]), 100);
			$content .= ' - ' . $shortMessage;

			if ($log->type === 'error') {
				$io->error($content);
			} elseif ($log->type === 'warning' || $log->type === 'notice') {
				$io->warning($content);
			} else {
				$io->out($content);
			}

			$io->out($log->message, 2, ConsoleIo::VERBOSE);
		}
	}

	/**
	 * @return \Cake\Console\ConsoleOptionParser
	 */
	public function getOptionParser(): ConsoleOptionParser {
		$parser = parent::getOptionParser();
		$parser->addArgument('type', [
			'help' => 'Type or list of types (comma separated)',
		]);
		$parser->addOptions([
			'limit' => [
				'help' => 'Limit (and optional offset, comma separated).',
				'short' => 'l',
			],
			'test-entry' => [
				'help' => 'With test entry. Select e.g. `' . implode('`, `', Log::levels()) . '` etc. You can also define a context using `{level}:{context}` definition.',
				'short' => 't',
			],
		])->setDescription('List log entries. Optionally per type only. Use -v for DB related details.');

		return $parser;
	}

}
