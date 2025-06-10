<?php
namespace Drupal\expense_app\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;
use Drupal\expense_app\ExpenseClaimInterface;

/**
 * Defines the Expense Claim type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "expense_claim_type",
 *   label = @Translation("Expense claim type"),
 *   handlers = {
 *     "list_builder" = "Drupal\\Core\\Entity\\EntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\\Core\\Entity\\EntityTypeForm",
 *       "edit" = "Drupal\\Core\\Entity\\EntityTypeForm",
 *       "delete" = "Drupal\\Core\\Entity\\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "expense_claim_type",
 *   admin_permission = "administer expense claim types",
 *   bundle_of = "expense_claim",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/expense-claim-types/add",
 *     "edit-form" = "/admin/structure/expense-claim-types/manage/{expense_claim_type}",
 *     "delete-form" = "/admin/structure/expense-claim-types/manage/{expense_claim_type}/delete",
 *     "collection" = "/admin/structure/expense-claim-types"
 *   }
 * )
 */
class ExpenseClaimType extends ConfigEntityBundleBase {
  /** @var string */
  protected $id;

  /** @var string */
  protected $label;
}
