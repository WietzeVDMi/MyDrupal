<?php
namespace Drupal\expense_app\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining an Expense Claim entity type.
 */
interface ExpenseClaimInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {
}
