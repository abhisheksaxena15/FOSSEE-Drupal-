<?php

namespace Drupal\event_reg\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventRegistrationListController extends ControllerBase {

  protected Connection $database;

  public function __construct(Connection $database) {
    $this->database = $database;
  }

  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('database')
    );
  }

  public function list(): array {

    $header = [
      'id' => $this->t('ID'),
      'name' => $this->t('Name'),
      'email' => $this->t('Email'),
      'college' => $this->t('College'),
      'department' => $this->t('Department'),
      'event' => $this->t('Event'),
      'date' => $this->t('Event Date'),
      'created' => $this->t('Registered On'),
    ];

    $query = $this->database->select('event_reg_registration', 'r');
    $query->join('event_reg_event', 'e', 'r.event_id = e.id');
    $query->fields('r', [
      'id',
      'full_name',
      'email',
      'college_name',
      'department',
      'created',
    ]);
    $query->fields('e', [
      'event_name',
      'event_date',
    ]);
    $query->orderBy('r.created', 'DESC');

    $rows = [];

    foreach ($query->execute() as $record) {
      $rows[] = [
        'id' => $record->id,
        'name' => $record->full_name,
        'email' => $record->email,
        'college' => $record->college_name,
        'department' => $record->department,
        'event' => $record->event_name,
        'date' => date('d M Y', $record->event_date),
        'created' => date('d M Y H:i', $record->created),
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
