<?php

namespace Drupal\prueba_ilumno\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class JsonController
{

  /**
   * @return JsonResponse
   */
  public function content()
  {
    $rows = \Drupal::database()->select('example_users', 'e')
      ->fields('e')
      ->execute()
      ->fetchAll();

    return new JsonResponse(['data' => $rows]);
  }

}
