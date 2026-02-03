<?php

namespace Drupal\event_reg\Controller;

use Drupal\Core\Controller\ControllerBase;

class EventRegConfigController extends ControllerBase {

  public function overview() {
    return [
      '#type' => 'container',
      '#markup' => '
        <h2>Event Registration</h2>
        <ul>
          <li><a href="/admin/config/event-reg/events">Event Configuration</a></li>
          <li><a href="/admin/config/event-reg/settings">Event Registration Settings</a></li>
          <li><a href="/admin/event-reg/registrations">View Registrations</a></li>
          <li><a href="/event/register" target="_blank">Open Registration Form (Public)</a></li>
        </ul>
      ',
    ];
  }

}