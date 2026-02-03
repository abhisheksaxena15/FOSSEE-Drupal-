<?php

namespace Drupal\event_reg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  public function list() {

    $header = [
      'id' => $this->t('ID'),
      'name' => $this->t('Full Name'),
      'email' => $this->t('Email'),
      'college' => $this->t('College'),
      'department' => $this->t('Department'),
      'event' => $this->t('Event'),
      'date' => $this->t('Registered On'),
    ];

    $rows = [];

    $query = $this->database->select('event_reg_registration', 'r');
    $query->fields('r');
    $query->orderBy('created', 'DESC');

    $result = $query->execute();

    foreach ($result as $row) {

      $event = $this->database->select('event_reg_event', 'e')
        ->fields('e', ['event_name'])
        ->condition('id', $row->event_id)
        ->execute()
        ->fetchField();

      $rows[] = [
        'id' => $row->id,
        'name' => $row->full_name,
        'email' => $row->email,
        'college' => $row->college_name,
        'department' => $row->department,
        'event' => $event ?: '-',
        'date' => date('d M Y, h:i A', $row->created),
      ];
    }

    return [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No registrations found.'),
    ];
  }
}
