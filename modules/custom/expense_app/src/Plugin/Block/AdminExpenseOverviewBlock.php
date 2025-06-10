<?php
namespace Drupal\expense_app\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;

/**
 * Provides a block listing all expense claims for admins.
 *
 * @Block(
 *   id = "admin_expense_overview_block",
 *   admin_label = @Translation("Admin Expense Overview")
 * )
 */
class AdminExpenseOverviewBlock extends BlockBase implements ContainerFactoryPluginInterface {
  /** @var EntityTypeManagerInterface */
  protected $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('entity_type.manager'));
  }

  public function build() {
    $claims = $this->entityTypeManager->getStorage('expense_claim')->loadMultiple();
    $items = [];
    foreach ($claims as $claim) {
      $items[] = $claim->toLink()->toRenderable();
    }
    return [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
  }

  public function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'administer expense claims');
  }
}
