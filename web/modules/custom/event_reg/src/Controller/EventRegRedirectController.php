<?php

namespace Drupal\event_reg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;

/***
 * Redirect controller for Event Registration config.
 */
class EventRegRedirectController extends ControllerBase {

  /**
   * Redirect to Event Configuration form.
   */
  public function redirectToConfig(): RedirectResponse {
    return new RedirectResponse('/admin/config/event-reg/events');
  }

}
