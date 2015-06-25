<?php

namespace Uneak\RoutesManagerBundle\Routes;

use Symfony\Component\Routing\RouteCollection;
use Uneak\RoutesManagerBundle\Routes\NestedRouteManager;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Uneak\RoutesManagerBundle\Routes\FlattenRouteFactory;
use ReflectionObject;

class RoutesLoader extends FileLoader {

	protected $nRouteManager;
	protected $fRouteManager;
	protected $flattenRouteFactory;
	protected $rootPath;

	public function __construct(NestedRouteManager $nRouteManager, FlattenRouteManager $fRouteManager, FlattenRouteFactory $flattenRouteFactory, $rootPath) {
//        parent::__construct($locator);
		$this->nRouteManager = $nRouteManager;
		$this->fRouteManager = $fRouteManager;
		$this->flattenRouteFactory = $flattenRouteFactory;
		$this->rootPath = $rootPath;
	}

	public function load($resource, $type = null) {
		$routes = new RouteCollection();
		$nRoutes = $this->nRouteManager->getNestedRoutes();

		foreach ($nRoutes as $nRoute) {
			$flattenRoutes = $this->flattenRouteFactory->getFlattenRoutes($nRoute);
			$this->fRouteManager->addFlattenRoute($flattenRoutes);
			$registerRoutes = $this->flattenRouteFactory->getRoutes($flattenRoutes);
			foreach ($registerRoutes as $key => $route) {
				$routes->add($key, $route);
			}
			$reflection = new ReflectionObject($nRoute);
			$routes->addResource(new FileResource($reflection->getFileName()));
		}
		$routes->addPrefix($this->rootPath);

		return $routes;
	}

	public function supports($resource, $type = null) {
		return 'uneak_routesmanager' === $type;
	}

}
