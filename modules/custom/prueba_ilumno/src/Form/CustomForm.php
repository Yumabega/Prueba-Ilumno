<?php

namespace Drupal\prueba_ilumno\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CustomForm extends FormBase
{

  /**
   * @return string
   */
  public function getFormId()
  {
    return 'prueba_ilumno_custom_form';
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['result'] = [
      '#type' => 'markup',
      '#markup' => '<div class="result"></div>'
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#description' => $this->t('Enter the name'),
      '#required' => TRUE,
    ];

    $form['identification'] = array(
      '#type' => 'number',
      '#title' => $this->t('Identification'),
      '#description' => $this->t('Enter the identification'),
      '#required' => TRUE,
    );

    $form['birth_date'] = array(
      '#type' => 'date',
      '#title' => $this->t('Birt Date'),
      '#description' => $this->t('Enter the Birt Date. Example: DD/MM/YYYY'),
      '#required' => TRUE,
    );

    $form['range'] = array(
      '#type' => 'select',
      '#title' => $this->t('Range'),
      '#description' => $this->t('Enter the range'),
      '#required' => TRUE,
      '#options' => [
        '45' => $this->t('Administrador'),
        '50' => $this->t('WebMaster'),
        '30' => $this->t('Desarrollador'),
      ]
    );

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send data form'),
      '#ajax' => [
        'callback' => '::submitFormAjax',
      ],
    ];

    return $form;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return AjaxResponse
   */
  public function submitFormAjax(array $form, FormStateInterface $form_state)
  {
    $response = new AjaxResponse();

    $select = '.form-item input, .form-item select';
    $errors = $form_state->getErrors();
    $response->addCommand(new InvokeCommand($select, 'removeClass', ['error']));
    if (count($errors) > 0) {
      $msj = "Error in the following fields: <br/>";
      foreach ($errors as $index => $error) {
        $msj .= $error . "<br/>";
        $response->addCommand(new InvokeCommand('#edit-' . $index, 'addClass', ['error']));
      }
    } else {
      $msj = "Success saved form!";
      $response->addCommand(new InvokeCommand($select, 'val', ['']));
    }
    $response->addCommand(new HtmlCommand('.result', $msj));

    return $response;
  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    parent::validateForm($form, $form_state);

    $name = $form_state->getValue('name');
    $identification = $form_state->getValue('identification');
    $birth_date = $form_state->getValue('birth_date');
    $range = $form_state->getValue('range');

    if (!is_numeric($identification)) {
      $form_state->setErrorByName('identification', $this->t('identification must should be numeric'));
    }

    if (empty($name)) {
      $form_state->setErrorByName('name', $this->t('Name is required'));
    }

    if (empty($birth_date)) {
      $form_state->setErrorByName('birth_date', $this->t('Birth Date is required'));
    }

    if (empty($range)) {
      $form_state->setErrorByName('range', $this->t('Range is required'));
    }

  }

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    if (count($form_state->getErrors()) > 0) return;
    $values = $form_state->getValues();
    $values = [
      'name' => $values['name'],
      'identification' => $values['identification'],
      'birth_date' => $values['birth_date'],
      'range' => $values['range'],
    ];
    if ($values['range'] == '45') $values['state'] = 1;

    \Drupal::database()->insert('example_users')
      ->fields($values)
      ->execute();

    $messenger = \Drupal::messenger();
    $messenger->addMessage($this->t('Success saved form!'));
  }

}
