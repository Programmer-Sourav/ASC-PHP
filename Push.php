<?php
class Push {
	//notification title
	private $title;
	
	//notification message
	private $message;
	
	//notification image url
	private $image;
	
	private $type;
	
	private $user_id;
	private $int_project_id;
	
	//initializing values in this constructor
	function __construct($title, $message, $image, $user_id) {
		$this->title = $title;
		$this->message = $message;
		$this->image = $image;
		$this->user_id = $user_id;

	}
	
	//getting the push notification
	public function getPush() {
		$res = array();
		$res['data']['title'] = $this->title;
		$res['data']['message'] = $this->message;
		$res['data']['image'] = $this->image;
		$res['data']['user_id'] = $this->user_id;
		return $res;
	}
	
}
?>