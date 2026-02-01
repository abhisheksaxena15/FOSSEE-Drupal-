<?php

namespace Drupal\event_reg\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Admin form to configure events.
 */
class EventConfigForm extends FormBase {

  protected Connection $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'event_reg_event_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $form['event_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event Name'),
      '#required' => TRUE,
    ];

    $form['category'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#options' => [
        'Online Workshop' => 'Online Workshop',
        'Hackathon' => 'Hackathon',
        'Conference' => 'Conference',
        'One-day Workshop' => 'One-day Workshop',
      ],
      '#required' => TRUE,
    ];

    $form['event_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Event Date'),
      '#required' => TRUE,
    ];

    $form['registration_start'] = [
      '#type' => 'date',
      '#title' => $this->t('Registration Start Date'),
      '#required' => TRUE,
    ];

    $form['registration_end'] = [
      '#type' => 'date',
      '#title' => $this->t('Registration End Date'),
      '#required' => TRUE,
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Event'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    if ($form_state->getValue('registration_start') > $form_state->getValue('registration_end')) {
      $form_state->setErrorByName(
        'registration_end',
        $this->t('Registration end date must be after start date.')
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {

    $this->database->insert('event_reg_event')
      ->fields([
        'event_name' => $form_state->getValue('event_name'),
        'category' => $form_state->getValue('category'),
        'event_date' => strtotime($form_state->getValue('event_date')),
        'registration_start' => strtotime($form_state->getValue('registration_start')),
        'registration_end' => strtotime($form_state->getValue('registration_end')),
      ])
      ->execute();

    $this->messenger()->addStatus($this->t('Event has been created successfully.'));
  }

}
