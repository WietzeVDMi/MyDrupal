<?php
namespace Drupal\expense_app\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\expense_app\Entity\ExpenseClaim;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Expense Claim edit forms.
 */
class ExpenseClaimForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\expense_app\Entity\ExpenseClaim $entity */
    $entity = $this->entity;

    // Display OCR warnings.
    if (!$entity->isNew() && !$entity->get('ocr_errors')->isEmpty()) {
      $this->messenger()->addWarning($entity->get('ocr_errors')->value);
    }

    $form = parent::buildForm($form, $form_state);

    // Conditional form elements.
    $form['from_location']['#states'] = [
      'visible' => [
        ':input[name="type"]' => ['value' => 'reis'],
      ],
    ];
    $form['to_location']['#states'] = $form['from_location']['#states'];
    $form['trip_type']['#states'] = $form['from_location']['#states'];
    $form['transport_method']['#states'] = $form['from_location']['#states'];
    $form['distance']['#states'] = $form['from_location']['#states'];
    $form['km_rate']['#attributes']['readonly'] = 'readonly';
    $form['km_rate']['#states'] = $form['from_location']['#states'];

    $form['receipt']['#states'] = [
      'visible' => [
        ':input[name="type"]' => ['value' => 'bon'],
      ],
    ];
    $form['products']['#states'] = $form['receipt']['#states'];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    // Auto-fill km_rate.
    if ($entity->isNew()) {
      $rate = $this->config('expense_app.settings')->get('km_rate');
      $entity->set('km_rate', $rate);
    }

    // Reiskosten berekening.
    if ($entity->get('type')->target_id === 'reis') {
      $amount = $entity->get('distance')->value * $entity->get('km_rate')->value;
      if ($entity->get('trip_type')->value === 'retour') {
        $amount *= 2;
      }
      $entity->set('amount', $amount);
    }

    $status = parent::save($form, $form_state);
    $form_state->setRedirect('entity.expense_claim.canonical', ['expense_claim' => $entity->id()]);
    return $status;
  }
}
