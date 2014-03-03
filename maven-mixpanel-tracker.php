<?php

/*
  Plugin Name: Maven Mixpanel Tracker
  Plugin URI:
  Description:
  Author: Site Mavens
  Version: 0.1
  Author URI:
 */

namespace MavenMixpanelTracker;

// Exit if accessed directly 
if ( ! defined( 'ABSPATH' ) ) exit;

//We need to load the library
require_once __DIR__ . '/lib/Mixpanel.php';

use Maven\Settings\OptionType,
	Maven\Settings\Option;

class Tracker extends \Maven\Tracking\BaseTracker {

	public function __construct( $args = array() ) {
		parent::__construct( 'Mixpanel' );

		$mixPanelToken = "";
		if ( $args && isset( $args[ 'token' ] ) ) {
			$mixPanelToken = $args[ 'token' ];
		}

		$defaultOptions = array(
			new Option(
					"mixpanelToken", "Mixpanel token", $mixPanelToken, '', OptionType::Input
			)
		);

		$this->addSettings( $defaultOptions );
		
	}

	public function addTransaction( \Maven\Tracking\ECommerceTransaction $transaction ) {

		return false;
	}

	public function addEvent( \Maven\Tracking\Event $event ) {

		if ( ! $this->getSetting( 'mixpanelToken' ) ) {
			throw new \Maven\Exceptions\RequiredException( 'Mixpanel Token required!' );
		}

		$props = $event->getProperties();
		
		// get the Mixpanel class instance, replace with your project token
		$mp = \Mixpanel::getInstance($this->getSetting( 'mixpanelToken'));

		// track an event
		$mp->track($event->getAction());

	}
	
	public function register( $trackers ){
		
		$trackers[$this->getTrackerKey()] = $this;
		
		return $trackers;
	}

}

$tracker = new Tracker();
\Maven\Core\HookManager::instance()->addFilter('maven/trackers/register', array($tracker,'register'));