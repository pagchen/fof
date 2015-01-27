<?php
/**
 * @package     FOF
 * @copyright   2010-2015 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Factory\Magic;

use FOF30\Controller\DataController;
use FOF30\Factory\Exception\ControllerNotFound;

defined('_JEXEC') or die;

/**
 * Creates a DataControler object instance based on the information provided by the fof.xml configuration file
 */
class ControllerFactory extends BaseFactory
{
	/**
	 * Create a new object instance
	 *
	 * @param   string  $name    The name of the class we're making
	 * @param   array   $config  The config parameters which override the fof.xml information
	 *
	 * @return  DataController  A new DataController object
	 */
	public function make($name = null, array $config = array())
	{
		if (empty($name))
		{
			throw new ControllerNotFound;
		}

		$defaultConfig = array(
			'name'           => $name,
			'default_task'   => $this->container->appConfig->get("views.$name.config.default_task"),
			'viewName'       => $this->container->appConfig->get("views.$name.config.viewName"),
			'modelName'      => $this->container->appConfig->get("views.$name.config.modelName"),
			'taskPrivileges' => $this->container->appConfig->get("views.$name.acl"),
			'cacheableTasks' => $this->container->appConfig->get("views.$name.config.cacheableTasks", array(
				'browse',
				'read'
			)),
			'taskMap'        => $this->container->appConfig->get("views.$name.taskmap")
		);

		$config = array_merge($defaultConfig, $config);

		$controller = new DataController($this->container, $config);

		$taskMap = $config['taskMap'];

		if (is_array($taskMap) && !empty($taskMap))
		{
			foreach ($taskMap as $virtualTask => $method)
			{
				$controller->registerTask($virtualTask, $method);
			}
		}

		return $controller;
	}
}