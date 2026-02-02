<?php

namespace Drupal\event_reg\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Datetime\DrupalDateTime;


/**
 * Public event registration form.
 */
class EventRegistrationForm extends FormBase {

  protected Connection $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('database')
    );
  }

  public function getFormId(): string {
    return 'event_reg_registration_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {

    $now = \Drupal::time()->getRequestTime();

    // Check if any event is open for registration.
    $event = $this->database->select('event_reg_event', 'e')
  ->fields('e')
  ->condition('registration_start', $now, '<=')
  ->condition('registration_end', $now, '>=')
  ->range(0, 1)
  ->execute()
  ->fetchObject();


    // If registration is closed.
    if (!$event) {
      $form['message'] = [
        '#markup' => '<p><strong>Registration is currently closed.</strong></p>',
      ];
      return $form;
    }

    // Registration is open → show form.
    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#required' => TRUE,
    ];

    $form['college'] = [
      '#type' => 'textfield',
      '#title' => $this->t('College Name'),
      '#required' => TRUE,
    ];

    $form['department'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Department'),
      '#required' => TRUE,
    ];

    // Store event reference.
    $form['event_id'] = [
      '#type' => 'hidden',
      '#value' => $event->id,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Register'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Will be implemented in next step.
  }
}

