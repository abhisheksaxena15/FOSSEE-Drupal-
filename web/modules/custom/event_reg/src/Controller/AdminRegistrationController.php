<?php

namespace Drupal\event_reg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class AdminRegistrationController extends ControllerBase {

  protected Connection $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  public function list(Request $request) {

  $selected_date = $request->query->get('event_date');
  $selected_event = $request->query->get('event_id');

  // ---------- FILTER FORM ----------
  $form['filters'] = [
    '#type' => 'container',
    '#attributes' => ['id' => 'registration-filter-wrapper'],
  ];

  // Event Date dropdown
  $form['filters']['event_date'] = [
    '#type' => 'select',
    '#title' => $this->t('Event Date'),
    '#options' => ['' => $this->t('- All Dates -')] + $this->getEventDates(),
    '#default_value' => $selected_date,
    '#ajax' => [
      'callback' => '::ajaxReload',
      'wrapper' => 'registration-table-wrapper',
    ],
  ];

  // Event dropdown
  $form['filters']['event_id'] = [
    '#type' => 'select',
    '#title' => $this->t('Event Name'),
    '#options' => ['' => $this->t('- All Events -')] + $this->getEventsByDate($selected_date),
    '#default_value' => $selected_event,
    '#ajax' => [
      'callback' => '::ajaxReload',
      'wrapper' => 'registration-table-wrapper',
    ],
  ];

  // ---------- TABLE ----------
  $header = [
    'name' => $this->t('Name'),
    'email' => $this->t('Email'),
    'college' => $this->t('College'),
    'department' => $this->t('Department'),
    'event' => $this->t('Event'),
    'date' => $this->t('Submitted'),
  ];

  $query = $this->database->select('event_reg_registration', 'r')
    ->fields('r')
    ->orderBy('created', 'DESC');

  if ($selected_event) {
    $query->condition('event_id', $selected_event);
  }

  if ($selected_date) {
    $query->join('event_reg_event', 'e', 'e.id = r.event_id');
    $query->condition('e.event_date', $selected_date);
  }

  $rows = [];
  foreach ($query->execute() as $row) {
    $event = $this->database->select('event_reg_event', 'e')
      ->fields('e', ['event_name'])
      ->condition('id', $row->event_id)
      ->execute()
      ->fetchField();

    $rows[] = [
      'name' => $row->full_name,
      'email' => $row->email,
      'college' => $row->college_name,
      'department' => $row->department,
      'event' => $event,
      'date' => date('d M Y H:i', $row->created),
    ];
  }

  $form['table'] = [
    '#type' => 'container',
    '#attributes' => ['id' => 'registration-table-wrapper'],
    'content' => [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No registrations found'),
    ],
  ];

  return $form;

  
}

protected function getEventDates(): array {
  $dates = $this->database->select('event_reg_event', 'e')
    ->fields('e', ['event_date'])
    ->distinct()
    ->execute()
    ->fetchCol();

  $options = [];
  foreach ($dates as $date) {
    $options[$date] = date('d M Y', $date);
  }
  return $options;
}

protected function getEventsByDate($date = NULL): array {
  $query = $this->database->select('event_reg_event', 'e')
    ->fields('e', ['id', 'event_name']);

  if ($date) {
    $query->condition('event_date', $date);
  }

  return $query->execute()->fetchAllKeyed();
}

public function ajaxReload(array &$form) {
  return $form['table'];
}


}
