<?php

/**
 * @file
 * Contains \Drupal\product\Entity\Controller\ProductListBuilder.
 */

namespace Drupal\product\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\product\Response\QRImageResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

/**
 * Provides a list controller for product entity.
 *
 * @ingroup product
 */
class ProductListBuilder extends EntityListBuilder {

  /**
   * The url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;


  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('url_generator')
    );
  }

  /**
   * Constructs a new DictionaryProductListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type term.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage class.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The url generator.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, UrlGeneratorInterface $url_generator) {
    parent::__construct($entity_type, $storage);
    $this->urlGenerator = $url_generator;
  }


  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build['table'] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the product list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['product_title'] = $this->t('Title');
    $header['product_content'] = $this->t('Description');
    $header['product_link'] = $this->t('App Purchase Link');
    return $header + parent::buildHeader();
  }


  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\product\Entity\Term */
    $row['id'] = $entity->id();
    $row['product_title'] = Link::createFromRoute(
      $entity->product_title->value,
      'entity.product.canonical',
      ['product' => $entity->id()]
    );
    $row['product_content'] = strip_tags($entity->product_content->value);
    $row['product_link'] = $entity->product_link->value;
    return $row + parent::buildRow($entity);
  }


  /**
   * Main method that throw ImageResponse object to generate image.
   *
   * @return \Drupal\product\Response\QRImageResponse
   *   Make a QR image in JPEG format.
   */
  public function image($content) {
    return new QRImageResponse($content, $this->getLogoWidth(), $this->getLogoSize(), $this->getLogoMargin());
  }
}
