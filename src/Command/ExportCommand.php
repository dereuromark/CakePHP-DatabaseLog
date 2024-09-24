<?php

namespace DatabaseLog\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Text;

class ExportCommand extends Command {

	/**
	 * @param \Cake\Console\Arguments $args The command arguments.
	 * @param \Cake\Console\ConsoleIo $io The console io
	 * @return int|null|void The exit code or null for success
	 */
	public function execute(Arguments $args, ConsoleIo $io) {
		parent::execute($args, $io);

		$type = $args->getArgument('type');
		if ($type) {
			$types = Text::tokenize($type);
		} else {
			$types = $this->fetchTable('DatabaseLog.DatabaseLogs')->getTypes();
		}

		$limit = (int)$args->getOption('limit') ?: 100;

		foreach ($types as $type) {
			$query = $this->fetchTable('DatabaseLog.DatabaseLogs')->find();

			/** @var array<\DatabaseLog\Model\Entity\DatabaseLog> $logs */
			$logs = $query->where(['type' => $type])
				->limit($limit)
				->orderBy(['created' => 'DESC'])
				->all();
			$contentArray = [];
			foreach ($logs as $log) {
				$content = $this->fetchTable('DatabaseLog.DatabaseLogs')->format($log) . PHP_EOL;

				$contentArray[] = $content;
			}

			$content = implode(PHP_EOL, $contentArray);
			file_put_contents(LOGS . 'export-' . $type . '.txt', $content);

			$io->success('Exporting type ' . $type . ': ' . count($logs) . ' entries written to export-' . $type . '.txt');
		}

		if (!$types) {
			$io->out('Nothing to do...');
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
				'help' => 'Limit.',
				'short' => 'l',
			],
		])->setDescription('Export log entries. Optionally per type only.');

		return $parser;
	}

}
