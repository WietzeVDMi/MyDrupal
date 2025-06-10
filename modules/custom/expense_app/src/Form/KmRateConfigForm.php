<?php
namespace Drupal\expense_app\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for km rate.
 */
class KmRateConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['expense_app.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'expense_app_km_rate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('expense_app.settings');
    $form['km_rate'] = [
      '#type' => 'number',
      '#title' => $this->t('Kilometer rate'),
      '#step' => '.01',
      '#default_value' => $config->get('km_rate'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory()->getEditable('expense_app.settings')
      ->set('km_rate', $form_state->getValue('km_rate'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
