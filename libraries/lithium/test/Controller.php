<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 * @license       http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace lithium\test;

use \lithium\test\Reporter;
use \lithium\test\Dispatcher;
use \lithium\core\Libraries;

/**
 * Controller for reporting test results in html
 *
 */
class Controller extends \lithium\core\Object {

	/**
	 * undocumented function
	 *
	 * @param string $request
	 * @param string $params
	 * @param string $options
	 * @return void
	 */
	public function __invoke($request, $params, $options = array()) {
		error_reporting(E_ALL | E_STRICT | E_DEPRECATED);
		$group = '\\' . join('\\', $request->args);
		$report = Dispatcher::run($group , $request->query + array(
			'reporter' => 'html'
		));
		$filters = Libraries::locate('test.filter');
		$classes = Libraries::locate('tests', null, array(
			'filter' => '/cases|integration|functional/'
		));
		$menu = $report->reporter->menu($classes, array(
			'request' => $request, 'tree' => true
		));

		$template = Libraries::locate('test.reporter.template', 'layout', array(
			'filter' => false, 'type' => 'file', 'suffix' => '.html.php',
		));
		include $template;
	}
}

?>