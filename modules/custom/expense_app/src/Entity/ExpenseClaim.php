<?php
namespace Drupal\expense_app\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Expense Claim entity.
 *
 * @ContentEntityType(
 *   id = "expense_claim",
 *   label = @Translation("Expense Claim"),
 *   handlers = {
 *     "form" = {
 *       "default" = "Drupal\\expense_app\\Form\\ExpenseClaimForm"
 *     },
 *     "list_builder" = "Drupal\\Core\\Entity\\EntityListBuilder",
 *   },
 *   base_table = "expense_claim",
 *   data_table = "expense_claim_field_data",
 *   admin_permission = "administer expense claims",
 *   revisionable = TRUE,
 *   translatable = FALSE,
 *   bundle_entity_type = "expense_claim_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "uid" = "user_id",
 *     "label" = "title"
 *   },
 *   links = {
 *     "canonical" = "/expense_claim/{expense_claim}",
 *     "add-page" = "/expense_claim/add",
 *     "add-form" = "/expense_claim/add/{expense_claim_type}",
 *     "edit-form" = "/expense_claim/{expense_claim}/edit",
 *     "delete-form" = "/expense_claim/{expense_claim}/delete",
 *     "collection" = "/expense_claim/list"
 *   }
 * )
 */
class ExpenseClaim extends ContentEntityBase implements ExpenseClaimInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE);

    $fields['type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Type'))
      ->setSetting('target_type', 'expense_claim_type')
      ->setRequired(TRUE);

    $fields['amount'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Amount'))
      ->setSetting('scale', 2);

    $fields['status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Status'))
      ->setSettings([
        'allowed_values' => [
          'ingediend' => 'Ingediend',
          'goedgekeurd' => 'Goedgekeurd',
          'afgekeurd' => 'Afgekeurd',
          'feedback' => 'Feedback',
        ],
      ])
      ->setDefaultValue('ingediend');

    $fields['ocr_errors'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('OCR errors'));

    $fields['manual_mode'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Ik wil handmatig invoeren'))
      ->setDefaultValue(FALSE);

    $fields['products'] = BaseFieldDefinition::create('entity_reference_revisions')
      ->setLabel(t('Products'))
      ->setSetting('target_type', 'paragraph')
      ->setSetting('handler_settings', ['target_bundles' => ['product_line' => 'product_line']])
      ->setCardinality(BaseFieldDefinition::CARDINALITY_UNLIMITED);

    $fields['receipt'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Receipt'))
      ->setSetting('file_extensions', 'jpg jpeg png pdf');

    $fields['from_location'] = BaseFieldDefinition::create('string')
      ->setLabel(t('From'));
    $fields['to_location'] = BaseFieldDefinition::create('string')
      ->setLabel(t('To'));

    $fields['trip_type'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Trip type'))
      ->setSettings(['allowed_values' => ['enkel' => 'Enkel', 'retour' => 'Retour']]);

    $fields['transport_method'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Transport method'))
      ->setSettings(['allowed_values' => ['auto' => 'Auto', 'trein' => 'Trein', 'fiets' => 'Fiets', 'brommer' => 'Brommer']]);

    $fields['distance'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Distance'))
      ->setSetting('scale', 2);

    $fields['km_rate'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Km rate'))
      ->setSetting('scale', 2)
      ->setReadOnly(TRUE);

    $fields['comment'] = BaseFieldDefinition::create('comment')
      ->setLabel(t('Comments'))
      ->setSetting('default_mode', 'open')
      ->setSetting('comment_type', 'comment');

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'user');

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    // Skip OCR when manual mode is enabled or type is not bon.
    if ($this->get('manual_mode')->value || $this->get('type')->target_id !== 'bon') {
      return;
    }

    $file = $this->get('receipt')->entity;
    if ($file) {
      $path = $file->getFileUri();
      $process = proc_open(['tesseract', drupal_realpath($path), 'stdout'], [1 => ['pipe', 'w']], $pipes);
      if (is_resource($process)) {
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($process);
        if (preg_match('/([0-9]+(?:\.[0-9]{2}))/', $output, $matches)) {
          $this->set('amount', $matches[1]);
          $sum = 0;
          foreach ($this->get('products') as $item) {
            $paragraph = $item->entity;
            $sum += (float) $paragraph->get('field_product_amount')->value;
          }
          if (abs($sum - $matches[1]) > 0.01) {
            $this->set('ocr_errors', "Detected amount {$matches[1]} differs from product sum {$sum}");
          }
        }
      }
    }
  }
}
