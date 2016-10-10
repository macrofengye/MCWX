<?php
namespace Blog\event;

use Doctrine\Common\EventArgs;
use Doctrine\Common\EventManager;

class TestEvent {

	const preFoo = "preFoo";

	const postFoo = "postFoo";

	private $_evm;

	public $preFooInvoked = false;

	public $postFooInvoked = false;

	public function __construct(EventManager $evm) {
		$evm->addEventListener(array(
			self::preFoo,
			self::postFoo,
		), $this);
	}

	public function preFoo(EventArgs $e) {
		$obj = $e->obj;
		$this->preFooInvoked = true;
		$e->obj[0] = 9000;
	}

	public function postFoo(EventArgs $e) {
		$this->postFooInvoked = true;
		echo "2222";
	}
}

?>