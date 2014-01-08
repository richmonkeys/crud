<?php

namespace Crud\Action;

use \Cake\Utility\Inflector;

/**
 * Handles 'Index' Crud actions
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class Index extends Base {

/**
 * Default settings for 'index' actions
 *
 * `enabled` Is this crud action enabled or disabled
 *
 * `findMethod` The default `Model::find()` method for reading data
 *
 * `view` A map of the controller action and the view to render
 * If `NULL` (the default) the controller action name will be used
 *
 * @var array
 */
	protected $_settings = array(
		'enabled' => true,
		'findMethod' => 'all',
		'view' => null,
		'viewVar' => null,
		'serialize' => array(),
		'api' => array(
			'success' => array(
				'code' => 200
			),
			'error' => array(
				'code' => 400
			)
		)
	);

/**
 * Constant representing the scope of this action
 *
 * @var integer
 */
	const ACTION_SCOPE = Base::SCOPE_MODEL;

/**
 * Change the name of the view variable name
 * of the data when its sent to the view
 *
 * @param mixed $name
 * @return mixed
 */
	public function viewVar($name = null) {
		if (empty($name)) {
			return $this->config('viewVar') ?: Inflector::variable($this->_controller()->name);
		}

		return $this->config('viewVar', $name);
	}

/**
 * HTTP GET handler
 *
 * @return void
 */
	protected function _get() {
		$controller = $this->_controller();

		$success = true;
		$viewVar = $this->viewVar();

		$subject = $this->_trigger('beforePaginate', ['success' => $success, 'viewVar' => $viewVar]);
		$items = $controller->paginate($this->_model());
		$subject = $this->_trigger('afterPaginate', ['success' => $subject->success, 'viewVar' => $subject->viewVar, 'items' => $items]);

		$items = $subject->items;

		if ($items instanceof Iterator) {
			$items = iterator_to_array($items);
		}

		$controller->set(['success' => $subject->success, $subject->viewVar => $items]);
		$this->_trigger('beforeRender', $subject);
	}

}
