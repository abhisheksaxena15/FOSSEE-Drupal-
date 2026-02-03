<?php

namespace Drupal\event_reg\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EventRegSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['event_reg.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'event_reg_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $config = $this->config('event_reg.settings');

    $form['notify_admin'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable admin email notification'),
      '#default_value' => $config->get('notify_admin'),
    ];

    $form['admin_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Admin email address'),
      '#default_value' => $config->get('admin_email') ?: \Drupal::config('system.site')->get('mail'),
      '#states' => [
        'visible' => [
          ':input[name="notify_admin"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {

    $this->config('event_reg.settings')
      ->set('notify_admin', $form_state->getValue('notify_admin'))
      ->set('admin_email', $form_state->getValue('admin_email'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}
