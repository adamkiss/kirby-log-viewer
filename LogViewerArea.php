<?php

namespace AdamKiss\LogViewer;

use DateTimeZone;
use Kirby\Cms\App;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use Kirby\Filesystem\F;
use Kirby\Toolkit\Date;
use Kirby\Filesystem\Dir;
use Highlight\Highlighter;

class LogViewerArea
{
	private static ?Highlighter $hl = null;
	public const ID = 'adamkiss/kirby-log-viewer';

	public static function blueprint(App $kirby): array
	{
		return [
			'id' => static::ID,
			'breadcrumbLabel' => 'Kompletné logy',
			'label' => 'Logy',
			'icon' => 'server',
			'link' => 'logs',
			'menu' => true,

			'views' => [
				[
					'pattern' => 'logs',
					'action' => fn () => LogViewerArea::view(),
				],
				[
					'pattern' => 'logs/(:any)',
					'action' => fn (string $log) => LogViewerArea::view($log),
				]
			],
		];
	}

	public static function view(?string $week = null): array
	{
		$all = self::all();
		$week ??= A::first($all)['name'];
		$log = $all[$week];

		$content = F::read(kirby()->root('logs') . '/' . $log['file']);
		$parsed = self::parse($content);

		return [
			'component' => 'ak-log-viewer',
			'props' => [
				'log' => $log,
				'content' => $parsed,
				'all' => $all
			],
		];
	}

	public static function all(): array
	{
		$files = Dir::files(kirby()->root('logs'), ['.', '..', '.gitkeep']);
		$convert = function (string $log) {
			$name = Str::split($log, '.')[0];
			$start = new Date(str_replace('-', 'W', $name) . '1', option('adamkiss.kirby-log-viewer.timezone', new \DateTimeZone('Europe/Bratislava')));
			$end = new Date(str_replace('-', 'W', $name) . '7', option('adamkiss.kirby-log-viewer.timezone', new \DateTimeZone('Europe/Bratislava')));
			$range = sprintf(
				'%s - %s',
				$start->format('d.m.'),
				$end->format('d.m.Y')
			);

			return [
				'file' => $log,
				'name' => $name,
				'start' => $start,
				'end' => $end,
				'range' => $range,
			];
		};

		return A::keyBy(A::map(array_reverse($files), $convert), 'name');
	}

	public static function parse(string $logs): array
	{
		$__ = A::map(
			preg_split('/^(?=\[[\d\:\.\- \/]+\])/m', $logs),
			fn($line) => static::parseLine($line)
		);
		$__ = A::reduce($__, function($all, $el) {
			if (empty($all)) {
				return [$el];
			}

			$li = count($all) - 1;
			if ($el['time'] === $all[$li]['time']) {
				$all[$li]['class'] .= ' ' . $el['class'];
				$all[$li]['content'] .=  $el['content'];
			} else {
				$all [] = $el;
			}
			return $all;
		}, []);
		$__ = array_reverse($__);

		$grouped = A::reduce($__, function(array $all, $el) {
			$key = empty($el['time']) ? null : Str::split($el['time'], ' ')[0];
			if (A::has($all, $key)) {
				$all[$key] = [$el];
			} else {
				$all[$key][] = $el;
			}
			return $all;
		}, []);

		return $grouped;

	}

	public  static function parseLine(string $line) : array
	{
		$l = Str::match($line, '/^\[(?P<time>[\d\:\.\- \/]+)\]\s(?P<sev>\w+)\s(?P<content>[\s\S]*)$/s');
		if (is_null($l)) {
			return [
				'time' => null,
				'content' => $line,
				'class' => 'log-null',
			];
		}

		$content = match(true) {
			Str::startsWith($l['content'], '{') || Str::startsWith($l['content'], '[') =>
				"<br><div hl>" . static::getHighlighter()->highlight('json', $l['content'])->value . '</div>',
			Str::startsWith($l['content'], '(') =>
				preg_replace('/^\((.*?)\)(.*)$/', '<span class="tag">$1</span>$2',$l['content']),
			Str::contains($l['content'], 'Stack trace:') =>
				"<br>" . $l['content'],
			default =>
				"<span>{$l['content']}</span>"
		};
		$class = 'sev-' . Str::lower($l['sev']);

		return [
			'time' => $l['time'],
			'content' => implode('', [
				'<span class="punc">[</span>',
				"<time datetime=\"{$l['time']}\">{$l['time']}</time>",
				'<span class="punc">]</span> ',
				"<span class=\"sev {$class}\">{$l['sev']}</span> ",
				$content
			]),
			'class' => $class,
		];
	}

	public  static function getHighlighter() : Highlighter
	{
		if (is_null(static::$hl)) {
			static::$hl = new Highlighter();
		}

		return static::$hl;
	}
}
