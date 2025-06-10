<?php
namespace Drupal\expense_app\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides a block showing personal expense claims.
 *
 * @Block(
 *   id = "personal_expense_overview_block",
 *   admin_label = @Translation("Personal Expense Overview")
 * )
 */
class PersonalExpenseOverviewBlock extends BlockBase implements ContainerFactoryPluginInterface {
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
    $storage = $this->entityTypeManager->getStorage('expense_claim');
    $claims = $storage->loadByProperties(['user_id' => \Drupal::currentUser()->id()]);
    $items = [];
    foreach ($claims as $claim) {
      $items[] = $claim->toLink()->toRenderable();
    }
    return [
      '#theme' => 'item_list',
      '#items' => $items,
    ];
  }
}
