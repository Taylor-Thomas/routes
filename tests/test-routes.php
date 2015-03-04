<?php

class TestRoutes extends WP_UnitTestCase {

	function testThemeRoute(){
		$template = Routes::load('single.php');
		$this->assertTrue($template);
	}

	function testThemeRouteDoesntExist(){
		$template = Routes::load('singlefoo.php');
		$this->assertFalse($template);
	}

	function testFullPathRoute(){
		$hello = WP_CONTENT_DIR.'/plugins/hello.php';
		$template = Routes::load($hello);
		$this->assertTrue($template);
	}

	function testFullPathRouteDoesntExist(){
		$hello = WP_CONTENT_DIR.'/plugins/hello-foo.php';
		$template = Routes::load($hello);
		$this->assertFalse($template);
	}

	function testRouterClass(){
		$this->assertTrue(class_exists('AltoRouter'));
	}

	function testAppliedRoute(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		Routes::map('foo', function() use ($phpunit) {
			global $matches;
			$phpunit->assertTrue(true);
			$matches[] = true;
		});
		$this->go_to(home_url('foo'));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testRouteWithVariable() {
		$post_name = 'ziggy';
		$post = $this->factory->post->create(array('post_title' => 'Ziggy', 'post_name' => $post_name));
		global $matches;
		$matches = array();
		$phpunit = $this;
		Routes::map('mything/:slug', function($params) use ($phpunit) {
			global $matches;
			if ('ziggy' == $params['slug']) {
				$matches[] = true;
			}
		});
		$this->go_to(home_url('/mything/'.$post_name));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testRouteWithAltoVariable() {
		$post_name = 'ziggy';
		$post = $this->factory->post->create(array('post_title' => 'Ziggy', 'post_name' => $post_name));
		global $matches;
		$matches = array();
		$phpunit = $this;
		Routes::map('mything/[*:slug]', function($params) use ($phpunit) {
			global $matches;
			if ('ziggy' == $params['slug']) {
				$matches[] = true;
			}
		});
		$this->go_to(home_url('/mything/'.$post_name));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testRouteAgainstPostName(){
		$post_name = 'jared';
		$post = $this->factory->post->create(array('post_title' => 'Jared', 'post_name' => $post_name));
		global $matches;
		$matches = array();
		$phpunit = $this;
		Routes::map('randomthing/'.$post_name, function() use ($phpunit) {
			global $matches;
			$phpunit->assertTrue(true);
			$matches[] = true;
		});
		$this->go_to(home_url('/randomthing/'.$post_name));
		$this->matchRoutes();
		$this->assertEquals(1, count($matches));
	}

	function testFailedRoute(){
		$_SERVER['REQUEST_METHOD'] = 'GET';
		global $matches;
		$matches = array();
		$phpunit = $this;
		Routes::map('foo', function() use ($phpunit){
			$phpunit->assertTrue(false);
			$matches[] = true;
		});
		$this->go_to(home_url('bar'));
		$this->matchRoutes();
		$this->assertEquals(0, count($matches));
	}

	function testRouteWithClassCallback() {
		Routes::map('classroute', array('TestRoutes', 'testCallback'));
		$this->go_to(home_url('classroute'));
		$this->matchRoutes();
		global $matches;
		$this->assertEquals(1, count($matches));
	}

	function matchRoutes() {
		global $upstatement_routes;
		$upstatement_routes->init();
	}

	static function testCallback() {
		global $matches;
		$matches[] = true;
	}
}
